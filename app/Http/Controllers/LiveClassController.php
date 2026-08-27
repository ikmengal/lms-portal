<?php

namespace App\Http\Controllers;

use App\Models\{Course, LiveClass, LiveClassAttendance};
use Illuminate\Http\Request;

class LiveClassController extends Controller
{
    public function index(Request $request)
    {
        $user = auth()->user();

        $enrolledCourseIds = $user->enrollments()->pluck('course_id');

        $classes = LiveClass::whereIn('course_id', $enrolledCourseIds)
            ->with('course')
            ->orderBy('scheduled_at', 'desc')
            ->get();

        $tab = $request->get('tab', 'upcoming');

        $upcoming = $classes->filter(fn ($c) => $c->isUpcoming());
        $past = $classes->filter(fn ($c) => !$c->isUpcoming());

        return view('pages.student.live-classes', [
            'upcoming' => $upcoming,
            'past' => $past,
            'tab' => $tab,
        ]);
    }

    public function join(LiveClass $liveClass)
    {
        $user = auth()->user();

        abort_unless(
            $user->enrollments()->where('course_id', $liveClass->course_id)->exists(),
            403,
        );

        LiveClassAttendance::updateOrCreate(
            ['live_class_id' => $liveClass->id, 'user_id' => $user->id],
            ['joined_at' => now()],
        );

        return redirect()->away($liveClass->join_url);
    }

    public function leave(LiveClass $liveClass)
    {
        $user = auth()->user();

        $attendance = LiveClassAttendance::where('live_class_id', $liveClass->id)
            ->where('user_id', $user->id)
            ->whereNull('left_at')
            ->first();

        if ($attendance) {
            $attendance->update([
                'left_at' => now(),
                'duration_seconds' => $attendance->joined_at->diffInSeconds(now()),
            ]);
        }

        return back()->with('success', 'Attendance recorded.');
    }
}
