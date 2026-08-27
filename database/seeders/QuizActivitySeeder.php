<?php

namespace Database\Seeders;

use App\Models\Enrollment;
use App\Models\Quiz;
use App\Models\QuizAttempt;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class QuizActivitySeeder extends Seeder
{
    /**
     * Plan of attempts per (user email => course slug => quiz title => [score, passed]).
     * Blank = the student took no attempt on that quiz for that course.
     */
    private const PLANS = [
        'student@lmsportal.com' => [
            'complete-web-development-bootcamp' => [
                'Mid-Course Quiz' => [[55, false], [82, true]],
                'Final Exam' => [[76, true]],
            ],
            'php-laravel-for-beginners' => [
                'Mid-Course Quiz' => [[90, true]],
                'Final Exam' => [[62, false], [100, true]],
            ],
            'javascript-es6-mastery' => [
                'Mid-Course Quiz' => [[55, false], [72, true]],
                'Final Exam' => [[58, false]],
            ],
            'mysql-database-design-administration' => [
                'Mid-Course Quiz' => [[50, false]],
                'Final Exam' => [[60, false]],
            ],
        ],
        'emma.wilson@example.com' => [
            'php-laravel-for-beginners' => [
                'Mid-Course Quiz' => [[78, true]],
                'Final Exam' => [[81, true]],
            ],
            'mysql-database-design-administration' => [
                'Mid-Course Quiz' => [[60, true]],
                'Final Exam' => [[65, false]],
            ],
            'mobile-app-development-with-flutter' => [
                'Mid-Course Quiz' => [[40, false]],
            ],
        ],
        'david.kim@example.com' => [
            'complete-web-development-bootcamp' => [
                'Mid-Course Quiz' => [[65, true]],
                'Final Exam' => [[66, false]],
            ],
            'javascript-es6-mastery' => [
                'Mid-Course Quiz' => [[92, true]],
                'Final Exam' => [[88, true]],
            ],
            'cloud-computing-with-aws' => [
                'Mid-Course Quiz' => [[62, true]],
                'Final Exam' => [[55, false]],
            ],
        ],
        'sofia.garcia@example.com' => [
            'python-for-data-science-analytics' => [
                'Mid-Course Quiz' => [[58, false]],
            ],
            'javascript-es6-mastery' => [
                'Mid-Course Quiz' => [[95, true]],
                'Final Exam' => [[90, true]],
            ],
            'uiux-design-fundamentals-with-figma' => [
                'Mid-Course Quiz' => [[45, false]],
            ],
        ],
        'james.patel@example.com' => [
            'mobile-app-development-with-flutter' => [
                'Mid-Course Quiz' => [[35, false]],
            ],
        ],
    ];

    public function run(): void
    {
        $eligible = Enrollment::with(['course.quizzes.questions.options'])
            ->whereHas('course.quizzes', fn ($q) => $q->where('type', '!=', 'assignment'))
            ->get();

        foreach ($eligible as $enrollment) {
            $user = User::find($enrollment->user_id);
            $coursePlan = self::PLANS[$user->email][$enrollment->course->slug] ?? [];

            if (empty($coursePlan)) {
                continue;
            }

            foreach ($enrollment->course->quizzes as $quiz) {
                if ($quiz->type === 'assignment') {
                    continue;
                }

                $attempts = $coursePlan[$quiz->title] ?? [];
                if (empty($attempts)) {
                    continue;
                }

                $this->createAttempts($user, $quiz, $attempts, $enrollment->progress);
            }
        }
    }

    private function createAttempts(User $user, Quiz $quiz, array $attempts, int $courseProgress): void
    {
        // Idempotency guard: skip if this student already has attempts on this quiz.
        if (QuizAttempt::withTrashed()->where('user_id', $user->id)->where('quiz_id', $quiz->id)->exists()) {
            return;
        }

        $questionIds = $quiz->questions->pluck('id')->values()->all();
        $optionsByQuestion = $quiz->questions->mapWithKeys(
            fn ($q) => [$q->id => $q->options->pluck('id', 'is_correct')->all()]
        );

        $daysAgo = match (true) {
            $courseProgress >= 100 => rand(2, 18),
            $courseProgress >= 50 => rand(1, 9),
            default => rand(0, 5),
        };

        foreach ($attempts as $i => [$score, $passed]) {
            $completedAt = now()->subDays($daysAgo)->subMinutes($i * 60 * 24 * 2);

            $answers = [];
            foreach ($questionIds as $index => $questionId) {
                $correctId = $optionsByQuestion[$questionId][1] ?? null;

                // Leave the first question unanswered on non-perfect attempts for realism.
                if ($index === 0 && $score < 100) {
                    continue;
                }

                if ($correctId !== null) {
                    $answers[$questionId] = $correctId;
                }
            }

            QuizAttempt::firstOrCreate(
                ['user_id' => $user->id, 'quiz_id' => $quiz->id, 'completed_at' => $completedAt->toDateTimeString()],
                [
                    'started_at' => $completedAt->copy()->subMinutes(rand(5, max(6, $quiz->duration_minutes ?? 10))),
                    'answers' => $answers,
                    'score' => $score,
                    'passed' => $passed,
                    'time_spent_seconds' => min(
                        ($quiz->duration_minutes ?? 45) * 60,
                        rand(240, max(300, ($quiz->duration_minutes ?? 0) * 60))
                    ),
                    'question_ids' => $quiz->shuffle_questions ? collect($questionIds)->shuffle()->values()->all() : $questionIds,
                    'completed_at' => $completedAt,
                ]
            );
        }
    }
}