<?php

namespace Database\Seeders;

use App\Models\AssignmentSubmission;
use App\Models\Quiz;
use App\Models\User;
use Illuminate\Database\Seeder;

class AssignmentActivitySeeder extends Seeder
{
    /**
     * Submissions: user email => course slug => [status, marks, feedback]
     * Not all enrolled students submit, and not all submissions are graded yet.
     */
    private const PLANS = [
        'student@lmsportal.com' => [
            'complete-web-development-bootcamp' => [
                'Portfolio Website Project',
                'graded', 92,
                'Excellent work! Clean semantic HTML, responsive CSS and a working PHP contact form. I love that you deployed to a live URL. A few small notes: add a 404 page and meta descriptions for SEO.',
            ],
            'php-laravel-for-beginners' => [
                'Laravel CRUD Web App',
                'graded', 88,
                'Really solid CRUD app. Validation and authorization are both in place. Consider extracting the search logic into a repository or a form request class next time.',
            ],
            'mysql-database-design-administration' => [
                'E-Commerce Database Design',
                'submitted', null,
                null,
            ],
        ],
        'emma.wilson@example.com' => [
            'php-laravel-for-beginners' => [
                'Laravel CRUD Web App',
                'submitted', null,
                null,
            ],
            'mysql-database-design-administration' => [
                'E-Commerce Database Design',
                'late', 74,
                'Submitted two days late — please review deadlines carefully. The schema itself is well normalized and your EXPLAIN plans are excellent.',
            ],
        ],
        'david.kim@example.com' => [
            'complete-web-development-bootcamp' => [
                'Portfolio Website Project',
                'graded', 78,
                'Good structure and the project works end to end. Make sure the site is fully responsive on small screens and add alt text to all images.',
            ],
            'javascript-es6-mastery' => [
                'Weather App with ES6 Modules',
                'graded', 95,
                'Outstanding — clean module structure, proper error handling and great use of async/await. The localStorage persistence is a nice touch.',
            ],
        ],
        'sofia.garcia@example.com' => [
            'javascript-es6-mastery' => [
                'Weather App with ES6 Modules',
                'graded', 100,
                'Perfect submission. Well organized, fully functional and beautifully commented. Keep this energy!',
            ],
        ],
        'james.patel@example.com' => [
            'complete-web-development-bootcamp' => [
                'Portfolio Website Project',
                'late', null,
                null,
            ],
        ],
    ];

    public function run(): void
    {
        foreach (self::PLANS as $email => $courses) {
            $user = User::where('email', $email)->first();
            if (!$user) {
                continue;
            }

            foreach ($courses as $courseSlug => [$quizTitle, $status, $marks, $feedback]) {
                $submission = $this->findAssignment($courseSlug, $quizTitle);
                if (!$submission) {
                    continue;
                }

                $inst = User::where('email', 'instructor@lmsportal.com')->first();

                $submittedAt = $submission->due_date?->copy();
                if ($submittedAt === null) {
                    $submittedAt = now()->subDays(rand(1, 5));
                } elseif ($status === 'late') {
                    $submittedAt->addDays(rand(1, 3))->addHours(rand(2, 12));
                } else {
                    $submittedAt->subDays(rand(1, 5))->subHours(rand(0, 10));
                }

                $isGraded = in_array($status, ['graded', 'late']) && $marks !== null;

                AssignmentSubmission::firstOrCreate(
                    ['quiz_id' => $submission->id, 'user_id' => $user->id],
                    [
                        'file_path' => 'assignments/' . \Illuminate\Support\Str::slug($submission->title) . '-' . $user->id . '.pdf',
                        'file_original_name' => \Illuminate\Support\Str::slug($submission->title) . '.pdf',
                        'status' => $status,
                        'marks' => $isGraded ? $marks : null,
                        'feedback' => $isGraded ? $feedback : null,
                        'submitted_at' => $submittedAt,
                        'graded_at' => $isGraded ? $submittedAt->copy()->addDays(rand(1, 3)) : null,
                        'graded_by' => $isGraded ? $inst?->id : null,
                    ]
                );
            }
        }
    }

    private function findAssignment(string $courseSlug, string $quizTitle): ?Quiz
    {
        return Quiz::query()
            ->where('type', 'assignment')
            ->where('title', $quizTitle)
            ->whereHas('course', fn ($q) => $q->where('slug', $courseSlug))
            ->with('course')
            ->first();
    }
}