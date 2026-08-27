<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class NotificationsSeeder extends Seeder
{
    public function run(): void
    {
        $student = User::where('email', 'student@lmsportal.com')->first();
        $admin = User::where('email', 'admin@lmsportal.com')->first();

        $notifications = [];

        if ($student) {
            $notifications[] = [
                'type' => 'App\Notifications\LmsNotification',
                'notifiable_type' => User::class,
                'notifiable_id' => $student->id,
                'data' => json_encode([
                    'type' => 'welcome',
                    'title' => 'Welcome to LMS Portal! 🎉',
                    'body' => 'Your account is ready. Enroll in your first course and start learning today.',
                    'url' => route('courses.index'),
                ]),
                'read_at' => now()->subDays(14),
            ];

            $notifications[] = [
                'type' => 'App\Notifications\LmsNotification',
                'notifiable_type' => User::class,
                'notifiable_id' => $student->id,
                'data' => json_encode([
                    'type' => 'course_completed',
                    'title' => 'Course Completed 🎓',
                    'body' => 'Congratulations! You completed the Complete Web Development Bootcamp. Your certificate is ready to download.',
                    'url' => route('certificates.index'),
                ]),
                'read_at' => now()->subDays(6),
            ];

            $notifications[] = [
                'type' => 'App\Notifications\LmsNotification',
                'notifiable_type' => User::class,
                'notifiable_id' => $student->id,
                'data' => json_encode([
                    'type' => 'badge',
                    'title' => 'New Badge Earned 🏅',
                    'body' => 'You earned the Rising Star badge by reaching 500 XP. Keep it up!',
                    'url' => route('gamification.index'),
                ]),
                'read_at' => null,
            ];

            $notifications[] = [
                'type' => 'App\Notifications\InAppNotification',
                'notifiable_type' => User::class,
                'notifiable_id' => $student->id,
                'data' => json_encode([
                    'type' => 'quiz_result',
                    'title' => 'Quiz Result: Perfect Score 💯',
                    'body' => 'You scored 100% on the PHP & Laravel Final Exam. Excellent work!',
                    'url' => route('quiz.history'),
                ]),
                'read_at' => null,
            ];
        }

        if ($admin) {
            $notifications[] = [
                'type' => 'App\Notifications\NewContactMessageNotification',
                'notifiable_type' => User::class,
                'notifiable_id' => $admin->id,
                'data' => json_encode([
                    'contact_message_id' => 1,
                    'name' => 'Hassan Raza',
                    'email' => 'hassan.raza@example.com',
                    'subject' => 'Question about the web development bootcamp',
                    'preview' => 'Hi, I\'m a complete beginner and I wanted to ask whether the Complete Web Development Bootcamp is suitable for someone with zero programming experience...',
                    'url' => route('admin.messages.index'),
                ]),
                'read_at' => null,
            ];

            $notifications[] = [
                'type' => 'App\Notifications\InAppNotification',
                'notifiable_type' => User::class,
                'notifiable_id' => $admin->id,
                'data' => json_encode([
                    'type' => 'payment',
                    'title' => 'New Payment Received 💳',
                    'body' => 'A student started the Cloud Computing with AWS course using the SUMMER15 coupon.',
                    'url' => route('admin.analytics'),
                ]),
                'read_at' => null,
            ];
        }

        foreach ($notifications as $notification) {
            $notification['id'] = (string) Str::uuid();
            $notification['created_at'] = now()->subDays(rand(1, 20));
            $notification['updated_at'] = $notification['created_at'];

            $exists = DB::table('notifications')
                ->where('type', $notification['type'])
                ->where('notifiable_id', $notification['notifiable_id'])
                ->where('data', $notification['data'])
                ->exists();

            if (!$exists) {
                DB::table('notifications')->insert($notification);
            }
        }
    }
}