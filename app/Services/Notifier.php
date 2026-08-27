<?php

namespace App\Services;

use App\Models\{AssignmentSubmission, Certificate, ContactMessage, Course, Lesson, LiveClass, QuizAttempt, User};
use App\Notifications\LmsNotification;
use Illuminate\Support\Facades\Notification as Facade;
use Illuminate\Support\Facades\Route;

class Notifier
{
    /**
     * Send a notification (in-app + email) to one or many users. Fails silently
     * (reported) so a notification problem never breaks the main flow.
     */
    public static function send(User|iterable $users, string $type, string $title, string $body = '', ?string $url = null, array $extra = []): void
    {
        $users = $users instanceof User ? [$users] : $users;

        try {
            Facade::send($users, new LmsNotification($type, $title, $body, $url, $extra));
        } catch (\Throwable $e) {
            report($e);
        }
    }

    // ---------------- Events ----------------

    /** Admin created a new course → notify the assigned instructor + all students. */
    public static function courseCreated(Course $course): void
    {
        // Notify the assigned instructor
        if ($instructor = $course->instructor) {
            self::send(
                $instructor,
                'course_created',
                "New course assigned to you: {$course->title}",
                "You've been assigned as the instructor for \"{$course->title}\". Start building the curriculum now.",
                route('admin.courses.show', $course),
            );
        }

        // Notify all students (users with student role)
        $students = User::role('student')->get();

        if ($students->isNotEmpty()) {
            self::send(
                $students,
                'course_published',
                "New course available: {$course->title}",
                "A new course \"{$course->title}\" has just been published. Check it out and enroll now!",
                route('courses.show', $course),
            );
        }
    }

    /** Admin updated a course → notify the instructor + enrolled students. */
    public static function courseUpdated(Course $course): void
    {
        // Notify the instructor
        if ($instructor = $course->instructor) {
            self::send(
                $instructor,
                'course_updated',
                "Course updated: {$course->title}",
                "The course \"{$course->title}\" has been updated. Review the changes.",
                route('admin.courses.show', $course),
            );
        }

        // Notify enrolled students
        $students = User::whereHas('enrollments', fn ($q) => $q->where('course_id', $course->id))
            ->get();

        if ($students->isNotEmpty()) {
            self::send(
                $students,
                'course_updated',
                "Course updated: {$course->title}",
                "The course you're enrolled in has been updated with new content or changes.",
                route('learn.start', $course),
            );
        }
    }

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

    // ---------------- Assignment Events ----------------

    /** Student submitted an assignment file → notify instructor. */
    public static function assignmentSubmitted(AssignmentSubmission $submission): void
    {
        $quiz = $submission->quiz;
        if (!$quiz) {
            return;
        }

        $course = $quiz->course()->withTrashed()->first();
        if ($course?->instructor) {
            self::send(
                $course->instructor,
                'assignment_submitted',
                "{$submission->user->name} submitted \"{$quiz->title}\"",
                'A new file submission has been uploaded for review.',
                Route::has('admin.courses.assignments.submissions')
                    ? route('admin.courses.assignments.submissions', [$course, $quiz])
                    : null,
            );
        }
    }

    /** Instructor graded an assignment → notify student. */
    public static function assignmentGraded(AssignmentSubmission $submission): void
    {
        $quiz = $submission->quiz;
        if (!$quiz) {
            return;
        }

        $course = $quiz->course()->withTrashed()->first();
        $score = $submission->marks !== null ? number_format($submission->marks, 1) . '%' : null;
        $passed = $submission->marks !== null && $submission->marks >= ($quiz->passing_score ?? 60);

        self::send(
            $submission->user,
            'assignment_result',
            ($passed ? 'Passed' : 'Graded') . ": {$quiz->title}",
            $score ? "Your grade: {$score}" : 'Your assignment has been graded.',
            Route::has('courses.assignments.show') && $course
                ? route('courses.assignments.show', [$course, $quiz])
                : null,
        );
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
