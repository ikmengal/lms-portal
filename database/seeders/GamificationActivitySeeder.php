<?php

namespace Database\Seeders;

use App\Models\Enrollment;
use App\Models\LessonProgress;
use App\Models\QuizAttempt;
use App\Models\User;
use App\Models\UserStreak;
use App\Services\GamificationService;
use Illuminate\Database\Seeder;

class GamificationActivitySeeder extends Seeder
{
    private const XP_LESSON = 10;
    private const XP_QUIZ = 25;
    private const XP_COURSE = 100;
    private const XP_FIRST_ENROLLMENT = 15;
    private const XP_DISCUSSION = 5;

    private const STREAKS = [
        'student@lmsportal.com' => [12, 12, 'today'],
        'emma.wilson@example.com' => [6, 9, 'today'],
        'david.kim@example.com' => [7, 7, 'today'],
        'sofia.garcia@example.com' => [4, 4, 'today'],
        'james.patel@example.com' => [1, 1, 'today'],
    ];

    public function run(): void
    {
        $this->seedStreaks();
        $this->awardActivityXp();
        $this->awardBadges();
    }

    private function seedStreaks(): void
    {
        foreach (self::STREAKS as $email => [$current, $longest, $active]) {
            $user = User::where('email', $email)->first();
            if (!$user) {
                continue;
            }

            UserStreak::updateOrCreate(
                ['user_id' => $user->id],
                [
                    'current_streak' => $current,
                    'longest_streak' => $longest,
                    'last_active_date' => $active === 'today' ? now()->toDateString() : now()->subDays(2)->toDateString(),
                ]
            );
        }
    }

    private function awardActivityXp(): void
    {
        // Idempotency guard: re-running db:seed must not double-award XP.
        if (\App\Models\XpTransaction::exists()) {
            return;
        }

        foreach (User::role('student')->get() as $user) {
            $lessons = LessonProgress::where('user_id', $user->id)->count();
            $quizzesPassed = QuizAttempt::where('user_id', $user->id)->where('passed', true)->count();
            $coursesCompleted = Enrollment::where('user_id', $user->id)->whereNotNull('completed_at')->count();
            $discussions = $user->discussions()->count();

            if ($lessons > 0) {
                GamificationService::awardXp($user, $lessons * self::XP_LESSON, 'lesson_complete', 'Completed ' . $lessons . ' lessons');
            }

            if ($quizzesPassed > 0) {
                GamificationService::awardXp($user, $quizzesPassed * self::XP_QUIZ, 'quiz_passed', 'Passed ' . $quizzesPassed . ' quizzes');
            }

            if ($coursesCompleted > 0) {
                GamificationService::awardXp($user, $coursesCompleted * self::XP_COURSE, 'course_completed', 'Completed ' . $coursesCompleted . ' course(s)');
            }

            if (Enrollment::where('user_id', $user->id)->exists()) {
                GamificationService::awardXp($user, self::XP_FIRST_ENROLLMENT, 'first_enrollment', 'Enrolled in your first course');
            }

            if ($discussions > 0) {
                GamificationService::awardXp($user, $discussions * self::XP_DISCUSSION, 'discussion_post', 'Posted in ' . $discussions . ' discussion(s)');
            }
        }

        // Instructors earn XP for answering student questions in discussions.
        foreach (User::role('instructor')->get() as $instructor) {
            $replies = $instructor->discussions()->count();
            if ($replies > 0) {
                GamificationService::awardXp($instructor, $replies * self::XP_DISCUSSION, 'discussion_post', 'Helped students in discussions');
            }
        }
    }

    private function awardBadges(): void
    {
        $users = User::whereHas('roles')->get();

        foreach ($users as $user) {
            GamificationService::checkBadges($user);
        }
    }
}