<?php

namespace Database\Seeders;

use App\Models\Course;
use App\Models\Enrollment;
use App\Models\LiveClass;
use App\Models\LiveClassAttendance;
use Illuminate\Database\Seeder;

class LiveClassesSeeder extends Seeder
{
    public function run(): void
    {
        foreach (Course::all() as $course) {
            $past = [
                [
                    'title' => 'Live Q&A — Getting Started',
                    'description' => 'Open session where we kick off the course, clarify the roadmap and answer any questions about the first module.',
                    'days_ago' => 9,
                    'minutes' => 60,
                ],
                [
                    'title' => 'Capstone Workshop & Office Hours',
                    'description' => 'Live help session for the final project. Bring your questions and get direct feedback on your work in progress.',
                    'days_ago' => 2,
                    'minutes' => 45,
                ],
            ];

            $upcoming = [
                [
                    'title' => 'Live Orientation & Study Plan',
                    'description' => 'A live orientation covering how to get the most out of the course, how to schedule your study plan and what to expect each week.',
                    'days_ahead' => 2,
                    'minutes' => 60,
                ],
                [
                    'title' => 'Project Showcase & Community Call',
                    'description' => 'Students present their completed projects. A great chance to get feedback and see how others approached the same challenges.',
                    'days_ahead' => 10,
                    'minutes' => 90,
                ],
            ];

            foreach ($past as $data) {
                $scheduledAt = now()->subDays($data['days_ago'])->setTime(17, 0);
                $cls = LiveClass::firstOrCreate(
                    ['course_id' => $course->id, 'title' => $data['title']],
                    [
                        'description' => $data['description'],
                        'join_url' => 'https://meet.google.com/lms-' . $course->id . '-' . strtolower(preg_replace('/[^A-Za-z0-9]/', '', $data['title'])),
                        'scheduled_at' => $scheduledAt,
                        'duration_minutes' => $data['minutes'],
                        'reminder_24h_sent_at' => $scheduledAt->copy()->subDay(),
                        'reminder_15m_sent_at' => $scheduledAt->copy()->subMinutes(15),
                    ]
                );

                $this->seedAttendance($cls);
            }

            foreach ($upcoming as $data) {
                LiveClass::firstOrCreate(
                    ['course_id' => $course->id, 'title' => $data['title']],
                    [
                        'description' => $data['description'],
                        'join_url' => 'https://meet.google.com/lms-' . $course->id . '-' . strtolower(preg_replace('/[^A-Za-z0-9]/', '', $data['title'])),
                        'scheduled_at' => now()->addDays($data['days_ahead'])->setTime(18, 0),
                        'duration_minutes' => $data['minutes'],
                        'reminder_24h_sent_at' => null,
                        'reminder_15m_sent_at' => null,
                    ]
                );
            }
        }
    }

    private function seedAttendance(LiveClass $liveClass): void
    {
        $students = Enrollment::query()
            ->where('course_id', $liveClass->course_id)
            ->where('progress', '>', 0)
            ->pluck('user_id');

        foreach ($students as $userId) {
            $joinedAt = $liveClass->scheduled_at->copy()->addMinutes(rand(0, 5));
            $duration = min($liveClass->duration_minutes * 60, rand(20 * 60, $liveClass->duration_minutes * 60));

            LiveClassAttendance::firstOrCreate(
                ['live_class_id' => $liveClass->id, 'user_id' => $userId],
                [
                    'joined_at' => $joinedAt,
                    'left_at' => $joinedAt->copy()->addSeconds($duration),
                    'duration_seconds' => $duration,
                ]
            );
        }
    }
}