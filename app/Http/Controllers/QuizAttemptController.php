<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\Quiz;
use App\Models\QuizAttempt;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class QuizAttemptController extends Controller
{
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

        $attempts = QuizAttempt::where('user_id', $user->id)->where('quiz_id', $quiz->id)->latest()->get();
        $bestAttempt = $attempts->whereNotNull('completed_at')->sortByDesc('score')->first();
        $lastAttempt = $attempts->first();

        return view('pages.student.take-test', compact(
            'course',
            'quiz',
            'enrollment',
            'bestAttempt',
            'lastAttempt'
        ));
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
            'answers' => ['required', 'array'],
            'answers.*' => ['integer'],
        ]);

        $quiz->load('questions.options');

        [$score, $earned, $total, $results] = $this->grade($quiz, $validated['answers']);

        $attempt = QuizAttempt::create([
            'user_id' => $user->id,
            'quiz_id' => $quiz->id,
            'answers' => $validated['answers'],
            'score' => $score,
            'passed' => $score >= $quiz->passing_score,
            'completed_at' => now(),
        ]);

        return redirect()
            ->route('courses.tests.show', [$course, $quiz])
            ->with('attempt_results', [
                'attempt_id' => $attempt->id,
                'score' => $score,
                'earned' => $earned,
                'total' => $total,
                'passed' => $attempt->passed,
                'details' => $results,
            ])
            ->with('success', 'Test submitted successfully. Your score: ' . number_format($score, 1) . '%');
    }

    private function grade(Quiz $quiz, array $answers): array
    {
        $earned = 0;
        $total = 0;
        $details = [];

        foreach ($quiz->questions as $question) {
            $total += $question->points;
            $selected = $answers[$question->id] ?? null;

            $correctOptionIds = $question->options->where('is_correct')->pluck('id');
            $isCorrect = $selected !== null && $correctOptionIds->contains((int) $selected);

            if ($isCorrect) {
                $earned += $question->points;
            }

            $details[$question->id] = [
                'selected' => (int) $selected,
                'correct_ids' => $correctOptionIds->all(),
                'is_correct' => $isCorrect,
            ];
        }

        return [$total > 0 ? round($earned / $total * 100, 2) : 0.0, $earned, $total, $details];
    }
}
