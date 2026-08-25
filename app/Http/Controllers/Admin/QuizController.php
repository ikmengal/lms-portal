<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\Quiz;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

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
            'quiz' => new Quiz(['type' => 'quiz', 'passing_score' => 60, 'is_active' => true]),
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

        return view('pages.admin.courses.tests.form', compact('course', 'quiz'));
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
            'is_active' => ['nullable', 'boolean'],
        ]) + ['is_active' => false];
    }

    private function normalizedQuestions(Request $request): array
    {
        $raw = $request->input('questions', []);

        $questions = collect($raw)
            ->filter(fn ($q) => filled($q['question'] ?? null))
            ->map(function ($q) {
                $options = collect($q['options'] ?? [])
                    ->filter(fn ($o) => filled($o['text'] ?? null))
                    ->values()
                    ->map(fn ($o) => [
                        'text' => trim($o['text']),
                        'is_correct' => !empty($o['is_correct']),
                    ])
                    ->all();

                return [
                    'question' => trim($q['question']),
                    'points' => max(1, (int) ($q['points'] ?? 1)),
                    'options' => $options,
                ];
            })
            ->filter(fn ($q) => count($q['options']) >= 2 && collect($q['options'])->contains('is_correct', true))
            ->values()
            ->all();

        if (empty($questions)) {
            throw \Illuminate\Validation\ValidationException::withMessages([
                'questions' => 'Each question needs at least 2 options and one marked correct.',
            ]);
        }

        return $questions;
    }
}
