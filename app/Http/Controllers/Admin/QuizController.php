<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\Quiz;
use App\Models\QuizQuestion;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class QuizController extends Controller
{
    private function authorizeCourse(Course $course): void
    {
        $user = auth()->user();
        abort_unless($user->hasRole('admin') || $course->instructor_id === $user->id, 403);
    }

    public function index(Course $course)
    {
        $this->authorizeCourse($course);
        $course->load(['quizzes.questions']);

        return view('pages.admin.courses.tests.index', [
            'course' => $course,
            'quizzes' => $course->quizzes()->withCount('questions')->with('attempts')->get(),
        ]);
    }

    public function create(Course $course)
    {
        $this->authorizeCourse($course);
        return view('pages.admin.courses.tests.form', [
            'course' => $course,
            'quiz' => new Quiz(['type' => 'quiz', 'passing_score' => 60, 'shuffle_options' => true, 'is_active' => true]),
        ]);
    }

    public function store(Request $request, Course $course)
    {
        $this->authorizeCourse($course);
        $data = $this->validated($request);
        $questions = $this->normalizedQuestions($request);

        DB::transaction(function () use ($course, $data, $questions) {
            $quiz = $course->quizzes()->create($data);

            foreach ($questions as $i => $q) {
                $question = $quiz->questions()->create([
                    'question' => $q['question'],
                    'type' => $q['type'],
                    'points' => $q['points'],
                    'sort_order' => $i,
                ]);

                foreach ($q['options'] as $opt) {
                    $question->options()->create([
                        'option_text' => $opt['text'],
                        'is_correct' => $opt['is_correct'],
                    ]);
                }
            }
        });

        return redirect()
            ->route('admin.courses.tests.index', $course)
            ->with('success', ucfirst(str_replace('_', ' ', $data['type'])) . ' created successfully.');
    }

    public function edit(Course $course, Quiz $quiz)
    {
        $this->authorizeCourse($course);
        abort_unless($quiz->course_id === $course->id, 404);

        $quiz->load('questions.options');

        return view('pages.admin.courses.tests.form', get_defined_vars());
    }

    public function update(Request $request, Course $course, Quiz $quiz)
    {
        $this->authorizeCourse($course);
        abort_unless($quiz->course_id === $course->id, 404);

        $data = $this->validated($request);
        $questions = $this->normalizedQuestions($request);

        DB::transaction(function () use ($quiz, $data, $questions) {
            $quiz->update($data);

            // Replace questions wholesale (attempts keep their stored answers/score)
            $quiz->questions()->delete();

            foreach ($questions as $i => $q) {
                $question = $quiz->questions()->create([
                    'question' => $q['question'],
                    'type' => $q['type'],
                    'points' => $q['points'],
                    'sort_order' => $i,
                ]);

                foreach ($q['options'] as $opt) {
                    $question->options()->create([
                        'option_text' => $opt['text'],
                        'is_correct' => $opt['is_correct'],
                    ]);
                }
            }
        });

        return redirect()
            ->route('admin.courses.tests.index', $course)
            ->with('success', 'Test updated successfully.');
    }

    public function destroy(Course $course, Quiz $quiz)
    {
        $this->authorizeCourse($course);
        abort_unless($quiz->course_id === $course->id, 404);

        $label = ucfirst(str_replace('_', ' ', $quiz->type));
        $quiz->delete();

        return back()->with('success', $label . ' deleted.');
    }

    private function validated(Request $request): array
    {
        return $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'type' => ['required', 'in:quiz,assignment,final_exam'],
            'description' => ['nullable', 'string', 'max:2000'],
            'passing_score' => ['required', 'integer', 'min:1', 'max:100'],
            'duration_minutes' => ['nullable', 'integer', 'min:1', 'max:600'],
            'max_attempts' => ['nullable', 'integer', 'min:1', 'max:99'],
            'shuffle_questions' => ['nullable', 'boolean'],
            'shuffle_options' => ['nullable', 'boolean'],
            'is_active' => ['nullable', 'boolean'],
        ]) + [
            'is_active' => false,
            'shuffle_questions' => false,
            'shuffle_options' => false,
        ];
    }

    private function normalizedQuestions(Request $request): array
    {
        $raw = $request->input('questions', []);

        $questions = collect($raw)
            ->filter(fn ($q) => filled($q['question'] ?? null))
            ->map(function ($q) {
                $type = in_array($q['type'] ?? null, array_keys(QuizQuestion::TYPES), true)
                    ? $q['type']
                    : 'multiple_choice';

                // True/False questions always have exactly two fixed options.
                if ($type === 'true_false') {
                    $options = [
                        ['text' => 'True', 'is_correct' => (($q['correct'] ?? '') === 'true')],
                        ['text' => 'False', 'is_correct' => (($q['correct'] ?? '') === 'false')],
                    ];
                } else {
                    $options = collect($q['options'] ?? [])
                        ->filter(fn ($o) => filled($o['text'] ?? null))
                        ->values()
                        ->map(fn ($o) => [
                            'text' => trim($o['text']),
                            'is_correct' => !empty($o['is_correct']),
                        ])
                        ->all();
                }

                $correctCount = collect($options)->where('is_correct', true)->count();

                return [
                    'question' => trim($q['question']),
                    'type' => $type,
                    'points' => max(1, (int) ($q['points'] ?? 1)),
                    'options' => $options,
                    'correct_count' => $correctCount,
                ];
            })
            ->filter(function ($q) {
                if ($q['type'] === 'true_false') {
                    return $q['correct_count'] === 1; // must pick True or False
                }

                if ($q['type'] === 'multiple_answers') {
                    return count($q['options']) >= 2 && $q['correct_count'] >= 1;
                }

                return count($q['options']) >= 2 && $q['correct_count'] === 1;
            })
            ->map(function ($q) {
                unset($q['correct_count']);

                return $q;
            })
            ->values()
            ->all();

        if (empty($questions)) {
            throw ValidationException::withMessages([
                'questions' => 'Add at least one valid question. Each needs 2+ options and the correct answer(s) marked.',
            ]);
        }

        return $questions;
    }
}
