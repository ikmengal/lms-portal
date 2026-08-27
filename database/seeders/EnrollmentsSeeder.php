<?php

namespace Database\Seeders;

use App\Models\Certificate;
use App\Models\Course;
use App\Models\Enrollment;
use App\Models\User;
use Illuminate\Database\Seeder;

class EnrollmentsSeeder extends Seeder
{
    public function run(): void
    {
        $students = [
            'emma.wilson@example.com' => [
                ['php-laravel-for-beginners', 100],
                ['mysql-database-design-administration', 45],
                ['mobile-app-development-with-flutter', 10],
            ],
            'david.kim@example.com' => [
                ['complete-web-development-bootcamp', 60],
                ['javascript-es6-mastery', 100],
                ['cloud-computing-with-aws', 25],
            ],
            'sofia.garcia@example.com' => [
                ['python-for-data-science-analytics', 30],
                ['uiux-design-fundamentals-with-figma', 15],
                ['javascript-es6-mastery', 100],
            ],
            'james.patel@example.com' => [
                ['complete-web-development-bootcamp', 0],
                ['mobile-app-development-with-flutter', 10],
            ],
        ];

        foreach ($students as $email => $enrollments) {
            $user = User::where('email', $email)->first();
            if (!$user) {
                continue;
            }

            foreach ($enrollments as [$slug, $progress]) {
                $course = Course::where('slug', $slug)->first();
                if (!$course) {
                    continue;
                }

                $enrollment = Enrollment::firstOrCreate(
                    ['user_id' => $user->id, 'course_id' => $course->id],
                    [
                        'progress' => $progress,
                        'completed_at' => $progress >= 100
                            ? now()->subDays(rand(6, 45))
                            : null,
                    ]
                );

                if ($enrollment->isCompleted()) {
                    Certificate::firstOrCreate(
                        ['user_id' => $user->id, 'course_id' => $course->id],
                        [
                            'code' => Certificate::generateCode(),
                            'issued_at' => $enrollment->completed_at ?? now(),
                        ]
                    );
                }
            }
        }
    }
}