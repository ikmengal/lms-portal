<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\LiveClass;
use App\Models\LiveClassAttendance;
use App\Services\Notifier;
use Illuminate\Http\Request;

class LiveClassController extends Controller
{
    private function authorizeCourse(Course $course): void
    {
        $user = auth()->user();
        abort_unless($user->hasRole('admin') || $course->instructor_id === $user->id, 403);
    }

    public function index(Course $course)
    {
        $this->authorizeCourse($course);

        return view('pages.admin.courses.live-classes', [
            'course' => $course,
            'classes' => $course->liveClasses()->orderBy('scheduled_at')->get(),
        ]);
    }

    public function store(Request $request, Course $course)
    {
        $this->authorizeCourse($course);

        $validated = $this->validated($request);

        $class = $course->liveClasses()->create($validated);

        // Immediate announcement to all enrolled students.
        Notifier::liveClassScheduled($class);

        return back()->with('success', 'Live class scheduled and students notified.');
    }

    public function update(Request $request, Course $course, LiveClass $liveClass)
    {
        $this->authorizeCourse($course);
        abort_unless($liveClass->course_id === $course->id, 404);

        // Rescheduling re-arms reminders.
        $data = $this->validated($request);
        if ($liveClass->scheduled_at != $data['scheduled_at']) {
            $data['reminder_24h_sent_at'] = null;
            $data['reminder_15m_sent_at'] = null;
        }

        $liveClass->update($data);
        Notifier::liveClassScheduled($liveClass->refresh());

        return back()->with('success', 'Live class updated. Students notified of the new time.');
    }

    public function destroy(Course $course, LiveClass $liveClass)
    {
        $this->authorizeCourse($course);
        abort_unless($liveClass->course_id === $course->id, 404);

        $liveClass->delete();

        return back()->with('success', 'Live class removed.');
    }

    public function attendance(Course $course, LiveClass $liveClass)
    {
        $this->authorizeCourse($course);
        abort_unless($liveClass->course_id === $course->id, 404);

        $liveClass->load(['attendances.user', 'course.enrollments.user']);

        $enrolledStudents = $course->enrollments()->with('user')->get()->pluck('user');
        $attendedStudentIds = $liveClass->attendances->pluck('user_id');
        $absentStudents = $enrolledStudents->reject(fn ($u) => $attendedStudentIds->contains($u->id));

        return view('pages.admin.courses.live-class-attendance', [
            'course' => $course,
            'class' => $liveClass,
            'attended' => $liveClass->attendances,
            'absent' => $absentStudents,
        ]);
    }

    private function validated(Request $request): array
    {
        return $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:2000'],
            'join_url' => ['required', 'url', 'max:2048'],
            'scheduled_at' => ['required', 'date', 'after:now'],
            'duration_minutes' => ['nullable', 'integer', 'min:5', 'max:600'],
        ]) + ['duration_minutes' => 60];
    }
}
