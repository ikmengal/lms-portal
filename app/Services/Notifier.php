<?php

namespace App\Services;

use App\Models\{Certificate, ContactMessage, Course, Lesson, LiveClass, QuizAttempt, User};
use App\Notifications\InAppNotification;
use Illuminate\Support\Facades\Notification as Facade;
use Illuminate\Support\Facades\Route;

class Notifier
{
    /**
     * Send an in-app notification to one or many users. Fails silently
     * (reported) so a notification problem never breaks the main flow.
     */
    public static function send(User|iterable $users, string $type, string $title, string $body = '', ?string $url = null, array $extra = []): void
    {
        $users = $users instanceof User ? [$users] : $users;

        try {
            Facade::send($users, new InAppNotification($type, $title, $body, $url, $extra));
        } catch (\Throwable $e) {
            report($e);
        }
    }

    // ---------------- Events ----------------

    /** Student enrolled in a course → welcome the student, alert the instructor. */
    public static function courseEnrolled(User $student, Course $course): void
    {
        self::send($student, 'welcome', "Welcome to {$course->title}!", 'Start learning now — your lessons are ready.', route('learn.start', $course));

        if ($instructor = $course->instructor) {
            self::send(
                $instructor,
                'enrollment',
                'New student enrolled',
                "{$student->name} enrolled in \"{$course->title}\".",
                Route::has('admin.courses.tests.index') && $instructor->hasRole('admin')
                    ? route('admin.courses.curriculum', $course)
                    : route('instructor.students'),
            );
        }
    }

    /** A new lesson was published → notify every enrolled student. */
    public static function newLesson(Course $course, Lesson $lesson): void
    {
        $students = User::whereHas('enrollments', fn ($q) => $q->where('course_id', $course->id))
            ->get();

        self::send(
            $students,
            'lesson',
            "New lesson in {$course->title}",
            "\"{$lesson->title}\" was just added to your course.",
            route('learn.show', [$course, $lesson]),
        );
    }

    /** Quiz/assignment auto-graded result → student; instructor alerted for assignments. */
    public static function quizResult(QuizAttempt $attempt): void
    {
        $quiz = $attempt->quiz;
        if (!$quiz) {
            return;
        }

        $isAssignment = $quiz->type === 'assignment';
        $score = number_format((float) $attempt->score, 1);
        $passed = (bool) $attempt->passed;

        self::send(
            $attempt->user,
            $isAssignment ? 'assignment_result' : 'quiz_result',
            ($passed ? 'Passed' : 'Completed') . ": {$quiz->title}",
            'Your score: ' . $score . '% · pass mark ' . $quiz->passing_score . '%.',
            $quiz->course_id ? route('courses.tests.show', [$quiz->course_id, $quiz]) : null,
        );

        // Assignments go to instructors for review/feedback.
        if ($isAssignment) {
            $course = $quiz->course()->withTrashed()->first();
            if ($course?->instructor) {
                self::send(
                    $course->instructor,
                    'assignment_submitted',
                    "{$attempt->user->name} submitted \"{$quiz->title}\"",
                    'Scored ' . $score . '% (' . ($passed ? 'pass' : 'fail') . '). Review the attempt for feedback.',
                    $quiz->course_id ? route('courses.tests.show', [$quiz->course_id, $quiz]) : null,
                );
            }
        }
    }

    /** Certificate issued → student. */
    public static function certificateIssued(Certificate $certificate): void
    {
        self::send(
            $certificate->user,
            'certificate',
            '🎓 Certificate issued!',
            "You completed \"{$certificate->course->title}\". Certificate ID: {$certificate->code}",
            $certificate->verificationUrl(),
        );
    }

    /** A live class was scheduled → enrolled students. */
    public static function liveClassScheduled(LiveClass $class): void
    {
        self::send(
            self::enrolledStudents($class->course),
            'live_class',
            'Live class scheduled: ' . $class->title,
            $class->course->title . ' · ' . $class->scheduled_at->format('D, M j g:i A'),
            $class->join_url,
        );
    }

    /** Reminder windows (24h / 15min before start). */
    public static function liveClassReminder(LiveClass $class, string $when): void
    {
        self::send(
            self::enrolledStudents($class->course),
            'live_class_reminder',
            "Live class {$when}: {$class->title}",
            $class->course->title . ' starts ' . $class->scheduled_at->format('g:i A') . '. Join on time!',
            $class->join_url,
        );
    }

    private static function enrolledStudents(Course $course)
    {
        return User::whereHas('enrollments', fn ($q) => $q->where('course_id', $course->id))->get();
    }
}
