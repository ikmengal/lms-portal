<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\Quiz;
use App\Models\QuizAttempt;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class QuizAttemptController extends Controller
{
    /** Grace period (seconds) after the deadline in which a submission is still accepted. */
    private const GRACE_SECONDS = 60;

    public function show(Course $course, Quiz $quiz)
    {
        abort_unless($quiz->course_id === $course->id || $quiz->trashed(), 404);
        abort_unless($quiz->is_active && !$quiz->trashed(), 404);

        $user = Auth::user();
        $enrollment = $user->enrollments()->where('course_id', $course->id)->first();
        $isAdmin = $user->hasRole('admin');

        // Only enrolled students (and admins previewing) can take tests
        if (!$isAdmin) {
            abort_unless($enrollment, 403, 'Enroll in this course to take its tests.');
        }

        $quiz->load('questions.options');

        $history = QuizAttempt::where('user_id', $user->id)
            ->where('quiz_id', $quiz->id)
            ->whereNotNull('completed_at')
            ->latest('completed_at')
            ->get();

        $bestAttempt = $history->sortByDesc('score')->first();
        $lastAttempt = $history->first();
        $attemptsUsed = $history->count();
        $attemptsLeft = $quiz->attemptsLeftFor($user->id);

        // Attempts exhausted?
        if (!$isAdmin && $attemptsLeft === 0) {
            return view('pages.student.take-test', compact(
                'course', 'quiz', 'enrollment', 'bestAttempt', 'lastAttempt', 'history', 'attemptsUsed', 'attemptsLeft'
            ));
        }

        // Admins preview only — no attempt records or timers.
        if ($isAdmin) {
            return view('pages.student.take-test', [
                ...compact('course', 'quiz', 'enrollment', 'bestAttempt', 'lastAttempt', 'history', 'attemptsUsed', 'attemptsLeft'),
                'attempt' => null,
                'remainingSeconds' => null,
                'preview' => true,
            ]);
        }

        // Resume a running attempt, or start a fresh one when explicitly requested.
        $attempt = QuizAttempt::where('user_id', $user->id)
            ->where('quiz_id', $quiz->id)
            ->whereNull('completed_at')
            ->latest()
            ->first();

        if ($attempt && $this->isExpired($quiz, $attempt)) {
            // Abandoned past-deadline attempt — discard so the student can restart cleanly.
            $attempt->delete();
            $attempt = null;
        }

        $starting = false;

        if (!$attempt && request('start')) {
            $attempt = QuizAttempt::create([
                'user_id' => $user->id,
                'quiz_id' => $quiz->id,
                'started_at' => now(),
                'question_ids' => $this->orderedQuestionIds($quiz),
                'answers' => [],
            ]);
            $starting = true;
        }

        // No running attempt yet — show the intro/start screen.
        if (!$attempt) {
            return view('pages.student.take-test', [
                ...compact('course', 'quiz', 'enrollment', 'bestAttempt', 'lastAttempt', 'history', 'attemptsUsed', 'attemptsLeft'),
                'attempt' => null,
                'remainingSeconds' => null,
                'preview' => false,
            ]);
        }

        $attempt->forceFill(['question_ids' => $attempt->question_ids ?: $this->orderedQuestionIds($quiz)])->save();

        // Present questions in this attempt's stored (possibly randomized) order.
        $questions = collect($attempt->question_ids ?? [])
            ->map(fn ($id) => $quiz->questions->firstWhere('id', $id))
            ->filter()
            ->values();

        if ($questions->isEmpty()) {
            $questions = $quiz->questions;
        }

        // Randomize answer-option order per attempt.
        if ($quiz->shuffle_options) {
            $questions->each(fn ($q) => $q->setRelation('options', $q->options->shuffle()->values()));
        }

        $remainingSeconds = $quiz->duration_minutes
            ? max(0, $quiz->duration_minutes * 60 - $this->secondsElapsed($attempt))
            : null;

        return view('pages.student.take-test', [
            ...compact('course', 'quiz', 'enrollment', 'bestAttempt', 'lastAttempt', 'history', 'attemptsUsed', 'attemptsLeft'),
            'attempt' => $attempt,
            'attemptQuestions' => $questions,
            'remainingSeconds' => $remainingSeconds,
            'preview' => false,
            'justStarted' => $starting,
        ]);
    }

    public function submit(Request $request, Course $course, Quiz $quiz)
    {
        abort_unless($quiz->course_id === $course->id && $quiz->is_active, 404);

        $user = Auth::user();
        $enrollment = $user->enrollments()->where('course_id', $course->id)->first();

        if (!$user->hasRole('admin')) {
            abort_unless($enrollment, 403, 'Enroll in this course to take its tests.');
        }

        $validated = $request->validate([
            'answers' => ['nullable', 'array'],
            'answers.*' => ['nullable'],
            'answers.*.*' => ['integer'],
        ]);

        $quiz->load('questions.options');

        // Locate the running attempt.
        $attempt = QuizAttempt::where('user_id', $user->id)
            ->where('quiz_id', $quiz->id)
            ->whereNull('completed_at')
            ->latest()
            ->first();

        if ($attempt && $this->isHardExpired($quiz, $attempt)) {
            $attempt->delete();
            return redirect()
                ->route('courses.tests.show', [$course, $quiz])
                ->with('error', "Time's up! This attempt expired and was discarded. Start again if retakes remain.");
        }

        if (!$attempt) {
            if (!$user->hasRole('admin') && $quiz->attemptsLeftFor($user->id) === 0) {
                return redirect()->route('courses.tests.show', [$course, $quiz])
                    ->with('error', 'You have used all available attempts for this test.');
            }

            $attempt = QuizAttempt::create([
                'user_id' => $user->id,
                'quiz_id' => $quiz->id,
                'started_at' => now(),
                'question_ids' => $this->orderedQuestionIds($quiz),
                'answers' => [],
            ]);
        }

        [$score, $earned, $total, $results] = $this->grade($quiz, $validated['answers'] ?? []);

        // Cap recorded time at the allowed duration (grace-period submissions don't over-report).
        if ($attempt->started_at) {
            $allowed = $quiz->duration_minutes ? $quiz->duration_minutes * 60 : null;
            $timeSpent = $this->secondsElapsed($attempt);

            if ($allowed !== null) {
                $timeSpent = min(max($timeSpent, 0), $allowed + self::GRACE_SECONDS);
            }
        } else {
            $timeSpent = null;
        }

        $attempt->update([
            'answers' => $validated['answers'] ?? [],
            'score' => $score,
            'passed' => $score >= $quiz->passing_score,
            'time_spent_seconds' => $timeSpent,
            'completed_at' => now(),
        ]);

        $message = ($score >= $quiz->passing_score ? 'Passed! ' : '') . 'Auto-graded score: ' . number_format($score, 1) . '%'
            . ($quiz->duration_minutes ? ' · Time used: ' . $attempt->formattedTimeSpent() : '');

        \App\Services\Notifier::quizResult($attempt->refresh());

        return redirect()
            ->route('courses.tests.show', [$course, $quiz])
            ->with('attempt_results', [
                'attempt_id' => $attempt->id,
                'score' => $score,
                'earned' => $earned,
                'total' => $total,
                'passed' => $attempt->passed,
                'time_spent' => $attempt->formattedTimeSpent(),
                'details' => $results,
            ])
            ->with($attempt->passed ? 'success' : 'error', $message);
    }

    /** Full quiz history for the signed-in student. */
    public function history()
    {
        $user = Auth::user();

        $base = QuizAttempt::query()
            ->where('user_id', $user->id)
            ->whereNotNull('completed_at');

        $stats = [
            'total' => (clone $base)->count(),
            'passed' => (clone $base)->where('passed', true)->count(),
            'avg_score' => round((float) (clone $base)->avg('score'), 1),
            'best_score' => round((float) (clone $base)->max('score'), 1),
        ];

        $attempts = (clone $base)
            ->with(['quiz.course'])
            ->orderByDesc('completed_at')
            ->paginate(15);

        return view('pages.student.quiz-history', compact('attempts', 'stats'));
    }

    private function orderedQuestionIds(Quiz $quiz): array
    {
        $ids = $quiz->questions->pluck('id')->all();

        return $quiz->shuffle_questions ? collect($ids)->shuffle()->all() : $ids;
    }

    private function secondsElapsed(QuizAttempt $attempt): int
    {
        $from = $attempt->started_at ?? $attempt->created_at;

        return max(0, $from ? now()->getTimestamp() - $from->getTimestamp() : 0);
    }

    private function isExpired(Quiz $quiz, QuizAttempt $attempt): bool
    {
        if (!$quiz->duration_minutes) {
            return false;
        }

        return $this->secondsElapsed($attempt) > $quiz->duration_minutes * 60 + self::GRACE_SECONDS;
    }

    private function isHardExpired(Quiz $quiz, QuizAttempt $attempt): bool
    {
        if (!$quiz->duration_minutes) {
            return false;
        }

        return $this->secondsElapsed($attempt) > $quiz->duration_minutes * 60 + self::GRACE_SECONDS + 120;
    }

    /**
     * Auto-grader. Answers are normalized to arrays of option ids per question.
     *
     * @return array{0: float, 1: int, 2: int, 3: array}
     */
    private function grade(Quiz $quiz, array $answers): array
    {
        $earned = 0;
        $total = 0;
        $details = [];

        foreach ($quiz->questions as $question) {
            $total += $question->points;

            // Normalize raw input to a list of option ids (handles both radio & checkbox input).
            $raw = $answers[$question->id] ?? null;
            $selected = collect(is_array($raw) ? $raw : [$raw])
                ->filter(fn ($v) => $v !== null && $v !== '')
                ->map(fn ($v) => (int) $v)
                ->unique()
                ->values();

            $correctOptionIds = $question->options->where('is_correct')->pluck('id')->map(fn ($v) => (int) $v);

            $isCorrect = match ($question->type) {
                'multiple_answers' => $selected->isNotEmpty()
                    && $selected->sort()->values()->all() === $correctOptionIds->sort()->values()->all(),
                default => $selected->count() === 1 && $correctOptionIds->contains($selected->first()),
            };

            if ($isCorrect) {
                $earned += $question->points;
            }

            $details[$question->id] = [
                'selected' => $selected->all(),
                'correct_ids' => $correctOptionIds->all(),
                'is_correct' => $isCorrect,
                'points_earned' => $isCorrect ? $question->points : 0,
            ];
        }

        return [$total > 0 ? round($earned / $total * 100, 2) : 0.0, $earned, $total, $details];
    }
}
