<?php

namespace App\Services;

use App\Models\Badge;
use App\Models\Enrollment;
use App\Models\QuizAttempt;
use App\Models\User;
use App\Models\UserBadge;
use App\Models\UserStreak;
use App\Models\XpTransaction;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class GamificationService
{
    private const XP_LESSON_COMPLETE = 10;
    private const XP_QUIZ_PASSED = 25;
    private const XP_COURSE_COMPLETED = 100;
    private const XP_DAILY_STREAK = 5;
    private const XP_FIRST_ENROLLMENT = 15;
    private const XP_DISCUSSION_POST = 5;

    public static function awardXp(User $user, int $amount, string $type, string $description, $subject = null): void
    {
        if ($amount <= 0) return;

        DB::transaction(function () use ($user, $amount, $type, $description, $subject) {
            XpTransaction::create([
                'user_id' => $user->id,
                'amount' => $amount,
                'type' => $type,
                'description' => $description,
                'subject_type' => $subject ? get_class($subject) : null,
                'subject_id' => $subject?->id,
            ]);

            DB::table('users')->where('id', $user->id)->increment('xp', $amount);
        });

        self::checkBadges($user);
    }

    public static function totalXp(User $user): int
    {
        return $user->xp ?? 0;
    }

    public static function currentLevel(User $user): int
    {
        $xp = self::totalXp($user);
        return (int) floor(sqrt($xp / 50)) + 1;
    }

    public static function xpForNextLevel(User $user): int
    {
        $level = self::currentLevel($user);
        return ($level * $level - ($level - 1) * ($level - 1)) * 50;
    }

    public static function xpProgressInLevel(User $user): int
    {
        $xp = self::totalXp($user);
        $level = self::currentLevel($user);
        $baseXp = ($level - 1) * ($level - 1) * 50;
        $nextXp = $level * $level * 50;
        $range = $nextXp - $baseXp;
        $current = $xp - $baseXp;
        return $range > 0 ? min(100, (int) round($current / $range * 100)) : 0;
    }

    public static function recordLessonComplete(User $user, $lesson): void
    {
        self::awardXp($user, self::XP_LESSON_COMPLETE, 'lesson_complete', 'Completed lesson: ' . $lesson->title, $lesson);
        self::touchStreak($user);
    }

    public static function recordQuizPassed(User $user, QuizAttempt $attempt): void
    {
        self::awardXp($user, self::XP_QUIZ_PASSED, 'quiz_passed', 'Passed quiz: ' . $attempt->quiz->title ?? 'Quiz', $attempt);
        self::touchStreak($user);
    }

    public static function recordCourseCompleted(User $user, Enrollment $enrollment): void
    {
        self::awardXp($user, self::XP_COURSE_COMPLETED, 'course_completed', 'Completed course: ' . $enrollment->course->title, $enrollment);
        self::touchStreak($user);
    }

    public static function recordFirstEnrollment(User $user): void
    {
        $count = $user->enrollments()->count();
        if ($count === 1) {
            self::awardXp($user, self::XP_FIRST_ENROLLMENT, 'first_enrollment', 'Enrolled in first course');
        }
    }

    public static function recordDiscussionPost(User $user, $discussion): void
    {
        self::awardXp($user, self::XP_DISCUSSION_POST, 'discussion_post', 'Posted a question/reply', $discussion);
    }

    public static function touchStreak(User $user): void
    {
        $streak = UserStreak::firstOrCreate(['user_id' => $user->id]);
        $today = Carbon::today();

        if ($streak->last_active_date === null) {
            $streak->update(['current_streak' => 1, 'longest_streak' => 1, 'last_active_date' => $today]);
            return;
        }

        if ($streak->last_active_date->toDateString() === $today->toDateString()) {
            return;
        }

        $diff = $streak->last_active_date->diffInDays($today);

        if ($diff === 1) {
            $newStreak = $streak->current_streak + 1;
            $streak->update([
                'current_streak' => $newStreak,
                'longest_streak' => max($streak->longest_streak, $newStreak),
                'last_active_date' => $today,
            ]);
            if ($newStreak > 1 && $newStreak % 5 === 0) {
                self::awardXp($user, self::XP_DAILY_STREAK, 'streak_bonus', $newStreak . '-day learning streak!');
            }
        } else {
            $streak->update(['current_streak' => 1, 'last_active_date' => $today]);
        }
    }

    public static function leaderboard(int $limit = 50): \Illuminate\Support\Collection
    {
        return User::where('xp', '>', 0)
            ->select('id', 'name', 'avatar', 'xp')
            ->orderByDesc('xp')
            ->limit($limit)
            ->get();
    }

    public static function rank(User $user): int
    {
        return User::where('xp', '>', $user->xp ?? 0)->count() + 1;
    }

    public static function checkBadges(User $user): array
    {
        $earned = [];
        $badges = Badge::where('is_active', true)->get();

        foreach ($badges as $badge) {
            if ($user->badges()->where('badge_id', $badge->id)->exists()) continue;

            $eligible = match ($badge->slug) {
                'first-steps' => $user->xp >= 10,
                'lesson-lover' => self::completedLessonCount($user) >= 5,
                'knowledge-seeker' => self::completedLessonCount($user) >= 25,
                'lesson-master' => self::completedLessonCount($user) >= 50,
                'quiz-champion' => $user->quizAttempts()->where('passed', true)->count() >= 1,
                'quiz-master' => $user->quizAttempts()->where('passed', true)->count() >= 10,
                'course-graduate' => $user->enrollments()->whereNotNull('completed_at')->count() >= 1,
                'course-collector' => $user->enrollments()->whereNotNull('completed_at')->count() >= 5,
                'streak-warrior' => self::longestStreak($user) >= 7,
                'streak-legend' => self::longestStreak($user) >= 30,
                'curious-mind' => $user->discussions()->count() >= 5,
                'helpful-voice' => $user->discussions()->whereNull('parent_id')->count() >= 10,
                'rising-star' => self::totalXp($user) >= 500,
                'xp-legend' => self::totalXp($user) >= 5000,
                'perfect-score' => $user->quizAttempts()->where('score', 100)->exists(),
                default => false,
            };

            if ($eligible) {
                UserBadge::create([
                    'user_id' => $user->id,
                    'badge_id' => $badge->id,
                    'earned_at' => now(),
                ]);

                if ($badge->xp_reward > 0) {
                    self::awardXp($user, $badge->xp_reward, 'badge_reward', 'Earned badge: ' . $badge->name);
                }

                $earned[] = $badge;
            }
        }

        return $earned;
    }

    private static function completedLessonCount(User $user): int
    {
        return $user->lessonProgress()->count();
    }

    private static function longestStreak(User $user): int
    {
        $streak = UserStreak::where('user_id', $user->id)->first();
        return $streak?->longest_streak ?? 0;
    }
}
