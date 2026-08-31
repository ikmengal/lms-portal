<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use App\Services\Notifier;
use App\Models\{
    AssignmentSubmission,
    Course,
    Quiz
};

class AssignmentController extends Controller
{
    private function authorizeCourse(Course $course): void
    {
        $user = auth()->user();
        abort_unless($user->hasRole('admin') || $course->instructor_id === $user->id, 403);
    }

    public function index(Course $course)
    {
        $this->authorizeCourse($course);

        $assignments = $course->quizzes()
            ->where('type', 'assignment')
            ->withCount('submissions')
            ->with('submissions')
            ->latest()
            ->get();

        return view('pages.admin.courses.assignments.index', get_defined_vars());
    }

    public function create(Course $course)
    {
        $this->authorizeCourse($course);

        return view('pages.admin.courses.assignments.form', [
            'course' => $course,
            'assignment' => new Quiz([
                'type' => 'assignment',
                'is_active' => true,
                'max_file_size_mb' => 10,
                'allowed_extensions' => 'pdf,doc,docx,png,jpg,jpeg',
            ]),
        ]);
    }

    public function store(Request $request, Course $course)
    {
        $this->authorizeCourse($course);

        $data = $this->validated($request);

        $quiz = $course->quizzes()->create($data);

        return redirect()
            ->route('admin.courses.assignments.index', $course)
            ->with('success', 'Assignment created successfully.');
    }

    public function edit(Course $course, Quiz $quiz)
    {
        $this->authorizeCourse($course);
        abort_unless($quiz->course_id === $course->id && $quiz->isAssignment(), 404);

        // return view('pages.admin.courses.assignments.form', compact('course', 'quiz'));
        return view('pages.admin.courses.assignments.form', get_defined_vars());
    }

    public function update(Request $request, Course $course, Quiz $quiz)
    {
        $this->authorizeCourse($course);
        abort_unless($quiz->course_id === $course->id && $quiz->isAssignment(), 404);

        $data = $this->validated($request);
        $quiz->update($data);

        return redirect()
            ->route('admin.courses.assignments.index', $course)
            ->with('success', 'Assignment updated successfully.');
    }

    public function destroy(Course $course, Quiz $quiz)
    {
        $this->authorizeCourse($course);
        abort_unless($quiz->course_id === $course->id && $quiz->isAssignment(), 404);

        $quiz->delete();

        return back()->with('success', 'Assignment deleted.');
    }

    public function submissions(Course $course, Quiz $quiz)
    {
        $this->authorizeCourse($course);
        abort_unless($quiz->course_id === $course->id && $quiz->isAssignment(), 404);

        $submissions = AssignmentSubmission::where('quiz_id', $quiz->id)
            ->with('user')
            ->latest('submitted_at')
            ->get();

        return view('pages.admin.courses.assignments.submissions', get_defined_vars());
    }

    public function showGrade(Course $course, Quiz $quiz, AssignmentSubmission $submission)
    {
        $this->authorizeCourse($course);
        abort_unless($quiz->course_id === $course->id && $quiz->isAssignment(), 404);
        abort_unless($submission->quiz_id === $quiz->id, 404);

        $submission->load('user');

        return view('pages.admin.courses.assignments.grade', get_defined_vars());
    }

    public function grade(Request $request, Course $course, Quiz $quiz, AssignmentSubmission $submission)
    {
        $this->authorizeCourse($course);
        abort_unless($quiz->course_id === $course->id && $quiz->isAssignment(), 404);
        abort_unless($submission->quiz_id === $quiz->id, 404);

        $validated = $request->validate([
            'marks' => ['required', 'numeric', 'min:0', 'max:100'],
            'feedback' => ['nullable', 'string', 'max:5000'],
        ]);

        $submission->update([
            'marks' => $validated['marks'],
            'feedback' => $validated['feedback'] ?? null,
            'status' => 'graded',
            'graded_at' => now(),
            'graded_by' => auth()->id(),
        ]);

        Notifier::assignmentGraded($submission);

        return redirect()
            ->route('admin.courses.assignments.submissions', [$course, $quiz])
            ->with('success', 'Submission graded successfully.');
    }

    private function validated(Request $request): array
    {
        return $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:2000'],
            'due_date' => ['nullable', 'date', 'after:now'],
            'max_file_size_mb' => ['required', 'integer', 'min:1', 'max:50'],
            'allowed_extensions' => ['nullable', 'string', 'max:255'],
            'passing_score' => ['required', 'integer', 'min:1', 'max:100'],
            'is_active' => ['nullable', 'boolean'],
        ]) + [
            'is_active' => false,
            'max_file_size_mb' => 10,
        ];
    }
}
