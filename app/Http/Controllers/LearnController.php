<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\Certificate;
use App\Models\Discussion;
use App\Models\Enrollment;
use App\Models\Lesson;
use App\Models\LessonNote;
use App\Models\LessonProgress;
use App\Models\LessonResource;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class LearnController extends Controller
{
    public function start(Course $course)
    {
        $this->authorizeAccess($course);

        $flat = $this->orderedLessons($course);

        abort_if($flat->isEmpty(), 404, 'This course has no lessons yet.');

        $completedIds = LessonProgress::where('user_id', auth()->id())
            ->whereIn('lesson_id', $flat->pluck('id'))
            ->pluck('lesson_id')
            ->flip();

        $lesson = $flat->first(fn ($l) => !$completedIds->has($l->id)) ?? $flat->first();

        return redirect()->route('learn.show', [$course, $lesson]);
    }

    public function show(Course $course, Lesson $lesson)
    {
        $this->authorizeAccess($course);

        $flat = $this->orderedLessons($course);
        $modules = $course->modules()->with('lessons')->get();

        abort_unless($flat->contains('id', $lesson->id), 404);

        $currentIdx = $flat->search(fn ($l) => $l->id === $lesson->id);
        $prev = $currentIdx > 0 ? $flat[$currentIdx - 1] : null;
        $next = $currentIdx < $flat->count() - 1 ? $flat[$currentIdx + 1] : null;

        $completedIds = LessonProgress::where('user_id', auth()->id())
            ->whereIn('lesson_id', $flat->pluck('id'))
            ->pluck('lesson_id')
            ->flip();

        $enrollment = Enrollment::where('user_id', auth()->id())->where('course_id', $course->id)->first();

        $notes = LessonNote::where('user_id', auth()->id())
            ->where('lesson_id', $lesson->id)
            ->latest()
            ->get();

        $questions = Discussion::where('course_id', $course->id)
            ->whereNull('parent_id')
            ->where(function ($q) use ($lesson) {
                $q->where('lesson_id', $lesson->id)->orWhereNull('lesson_id');
            })
            ->with(['user', 'replies.user'])
            ->latest()
            ->take(20)
            ->get();

        $totalLessons = $flat->count();
        $doneCount = $completedIds->count();

        // Exams / Tests / Assignments — unlocked once all lessons are completed
        $quizzes = $course->quizzes()
            ->withCount('questions')
            ->where('is_active', true)
            ->orderBy('id')
            ->get();

        $bestScores = [];
        $attemptCounts = [];

        if ($quizzes->isNotEmpty()) {
            $rows = \App\Models\QuizAttempt::where('user_id', auth()->id())
                ->whereIn('quiz_id', $quizzes->pluck('id'))
                ->whereNotNull('completed_at')
                ->selectRaw('quiz_id, MAX(score) as best, COUNT(*) as total')
                ->groupBy('quiz_id')
                ->get();

            $bestScores = $rows->pluck('best', 'quiz_id')->all();
            $attemptCounts = $rows->pluck('total', 'quiz_id')->all();
        }

        return view('pages.student.learn', compact(
            'course', 'lesson', 'modules', 'flat', 'currentIdx',
            'prev', 'next', 'completedIds', 'enrollment',
            'notes', 'questions', 'totalLessons', 'doneCount',
            'quizzes', 'bestScores', 'attemptCounts'
        ));
    }

    private function orderedLessons(Course $course)
    {
        return Course::query()
            ->whereKey($course->id)
            ->with(['modules.lessons' => fn ($q) => $q->orderBy('sort_order')])
            ->first()
            ->modules
            ->sortBy('sort_order')
            ->flatMap(fn ($m) => $m->lessons)
            ->values();
    }

    public function toggleComplete(Request $request, Course $course, Lesson $lesson)
    {
        $this->authorizeAccess($course);

        $existing = LessonProgress::where('user_id', auth()->id())->where('lesson_id', $lesson->id)->first();

        if ($existing) {
            if ($request->boolean('undo')) {
                $existing->delete();
            }
        } else {
            LessonProgress::create([
                'user_id' => auth()->id(),
                'lesson_id' => $lesson->id,
                'completed_at' => now(),
            ]);
        }

        $this->syncEnrollment($course);

        return back();
    }

    private function syncEnrollment(Course $course): void
    {
        $enrollment = Enrollment::where('user_id', auth()->id())->where('course_id', $course->id)->first();
        if (!$enrollment) {
            return;
        }

        $total = Lesson::whereHas('module', fn ($q) => $q->where('course_id', $course->id))->count();
        $done = $total > 0
            ? LessonProgress::whereIn('lesson_id', Lesson::whereHas('module', fn ($q) => $q->where('course_id', $course->id))->select('id'))
                ->where('user_id', auth()->id())
                ->count()
            : 0;

        $progress = $total > 0 ? (int) round($done / $total * 100) : 0;

        $enrollment->update([
            'progress' => $progress,
            'completed_at' => $progress >= 100 ? now() : null,
        ]);

        if ($progress >= 100 && !Certificate::where('user_id', auth()->id())->where('course_id', $course->id)->exists()) {
            Certificate::create([
                'code' => strtoupper(Str::random(4) . '-' . Str::random(4) . '-' . Str::random(4)),
                'user_id' => auth()->id(),
                'course_id' => $course->id,
                'issued_at' => now(),
            ]);
        }
    }

    public function storeNote(Request $request, Course $course, Lesson $lesson)
    {
        $this->authorizeAccess($course);

        $data = $request->validate(['content' => ['required', 'string', 'max:5000']]);

        LessonNote::create([
            'user_id' => auth()->id(),
            'lesson_id' => $lesson->id,
            'content' => $data['content'],
        ]);

        return back()->with('success', 'Note saved.');
    }

    public function destroyNote(LessonNote $note)
    {
        abort_unless($note->user_id === auth()->id(), 403);
        $note->delete();

        return back()->with('success', 'Note deleted.');
    }

    public function downloadResource(LessonResource $resource)
    {
        if ($resource->external_url) {
            return redirect()->away($resource->external_url);
        }

        abort_unless($resource->file_path && Storage::disk('public')->exists($resource->file_path), 404);

        return Storage::disk('public')->download($resource->file_path);
    }

    public function storeQuestion(Request $request, Course $course, Lesson $lesson)
    {
        $this->authorizeAccess($course);

        $data = $request->validate([
            'body' => ['required', 'string', 'max:2000'],
            'parent_id' => ['nullable', 'exists:discussions,id'],
        ]);

        Discussion::create([
            'user_id' => auth()->id(),
            'course_id' => $course->id,
            'lesson_id' => $lesson->id,
            'parent_id' => $data['parent_id'] ?? null,
            'body' => $data['body'],
        ]);

        return back()->with('success', $request->filled('parent_id') ? 'Reply posted.' : 'Question posted.');
    }

    public function destroyQuestion(Discussion $discussion)
    {
        $user = auth()->user();
        abort_unless($discussion->user_id === $user->id || $user->hasRole('admin'), 403);

        $discussion->delete();

        return back()->with('success', 'Deleted.');
    }

    private function authorizeAccess(Course $course): void
    {
        $user = auth()->user();

        $allowed = $user->hasRole('admin')
            || Enrollment::where('user_id', $user->id)->where('course_id', $course->id)->exists()
            || ($user->hasRole('instructor') && $course->instructor_id === $user->id);

        abort_unless($allowed, 403, 'Enroll in this course to access its lessons.');
    }
}
