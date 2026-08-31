<?php

namespace App\Http\Controllers;

use App\Models\AssignmentSubmission;
use App\Models\Course;
use App\Models\Quiz;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class AssignmentController extends Controller
{
    public function show(Course $course, Quiz $quiz)
    {
        abort_unless($quiz->course_id === $course->id, 404);
        abort_unless($quiz->is_active && !$quiz->trashed() && $quiz->isAssignment(), 404);

        $user = Auth::user();
        $enrollment = $user->enrollments()->where('course_id', $course->id)->first();

        if (!$user->hasRole('admin')) {
            abort_unless($enrollment, 403, 'Enroll in this course to access assignments.');
        }

        $submission = $quiz->submissionFor($user->id);

        $allowedExtensions = collect(explode(',', $quiz->allowed_extensions ?? 'pdf,doc,docx,png,jpg,jpeg'))
            ->map(fn ($ext) => trim(strtolower($ext)))
            ->values();

        return view('pages.student.assignments.show', get_defined_vars('course', 'quiz', 'submission', 'allowedExtensions'));
    }

    public function submit(Request $request, Course $course, Quiz $quiz)
    {
        abort_unless($quiz->course_id === $course->id && $quiz->isAssignment(), 404);

        $user = Auth::user();
        $enrollment = $user->enrollments()->where('course_id', $course->id)->first();

        if (!$user->hasRole('admin')) {
            abort_unless($enrollment, 403, 'Enroll in this course to submit assignments.');
        }

        if ($quiz->isOverdue()) {
            return back()->with('error', 'The deadline for this assignment has passed. Submissions are no longer accepted.');
        }

        $allowedExtensions = collect(explode(',', $quiz->allowed_extensions ?? 'pdf,doc,docx,png,jpg,jpeg'))
            ->map(fn ($ext) => trim(strtolower($ext)))
            ->values();

        $validated = $request->validate([
            'file' => [
                'required',
                'file',
                'max:' . ($quiz->max_file_size_mb ?? 10) * 1024,
                'mimes:' . $allowedExtensions->implode(','),
            ],
        ]);

        $file = $request->file('file');
        $fileName = time() . '_' . $file->getClientOriginalName();
        $filePath = $file->storeAs(
            'assignments/' . $quiz->id . '/' . $user->id,
            $fileName,
            'local'
        );

        $submission = AssignmentSubmission::updateOrCreate(
            ['quiz_id' => $quiz->id, 'user_id' => $user->id],
            [
                'file_path' => $filePath,
                'file_original_name' => $file->getClientOriginalName(),
                'status' => 'submitted',
                'submitted_at' => now(),
                'marks' => null,
                'feedback' => null,
                'graded_at' => null,
                'graded_by' => null,
            ]
        );

        \App\Services\Notifier::assignmentSubmitted($submission);

        return back()->with('success', 'Assignment submitted successfully.');
    }

    public function download(AssignmentSubmission $submission)
    {
        $user = Auth::user();

        abort_unless(
            $user->hasRole('admin') || $user->id === $submission->user_id,
            403
        );

        abort_unless(Storage::disk('local')->exists($submission->file_path), 404);

        return Storage::disk('local')->download(
            $submission->file_path,
            $submission->file_original_name
        );
    }

    public function history()
    {
        $user = Auth::user();

        $submissions = AssignmentSubmission::where('user_id', $user->id)
            ->with('quiz.course', 'gradedBy')
            ->latest('submitted_at')
            ->paginate(15);

        return view('pages.student.assignments.history', compact('submissions'));
    }
}
