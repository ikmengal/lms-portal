<?php

namespace App\Support;

use App\Models\Course;
use App\Models\CourseModule;
use App\Models\Lesson;
use App\Models\User;
use Carbon\CarbonInterface;

/**
 * Date-based content drip: courses / modules / lessons become available
 * when their `unlocks_at` timestamp is reached. Admins and the course's own
 * instructor always bypass the lock so they can preview everything.
 */
class ContentDrip
{
    /**
     * Whether the given user can preview content regardless of unlock dates.
     */
    public static function bypassed(?User $user, Course $course): bool
    {
        if (! $user) {
            return false;
        }

        return $user->hasRole('admin')
            || ($user->hasRole('instructor') && $course->instructor_id === $user->id);
    }

    /**
     * The earliest moment a course becomes enrollable (null = always open).
     */
    public static function courseUnlockDate(Course $course): ?CarbonInterface
    {
        return $course->unlocks_at;
    }

    /**
     * True when the course has a future unlock date ("coming soon").
     */
    public static function courseComingSoon(Course $course): bool
    {
        return $course->unlocks_at && $course->unlocks_at->isFuture();
    }

    /**
     * Effective unlock date for a single lesson — the latest of
     * course / module / lesson level dates.
     */
    public static function lessonUnlockDate(Course $course, Lesson $lesson, ?CourseModule $module = null): ?CarbonInterface
    {
        $dates = array_values(array_filter([
            $course->unlocks_at,
            $module?->unlocks_at,
            $lesson->unlocks_at,
        ], fn ($d) => $d instanceof CarbonInterface));

        if (! $dates) {
            return null;
        }

        return max($dates);
    }

    /**
     * True when the lesson cannot be opened yet (ignoring admin/instructor bypass).
     */
    public static function lessonLocked(Course $course, Lesson $lesson, ?CourseModule $module = null, ?User $user = null): bool
    {
        if (static::bypassed($user, $course)) {
            return false;
        }

        $at = static::lessonUnlockDate($course, $lesson, $module);

        return (bool) ($at?->isFuture());
    }

    /**
     * True when any part of the course remains locked to the given user.
     */
    public static function hasLockedContent(Course $course, ?User $user = null): bool
    {
        if (static::bypassed($user, $course)) {
            return false;
        }

        $modules = $course->modules()->with('lessons')->get();

        foreach ($modules as $module) {
            foreach ($module->lessons as $lesson) {
                if (static::lessonLocked($course, $lesson, $module, $user)) {
                    return true;
                }
            }
        }

        return false;
    }
}