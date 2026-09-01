<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\{
    Storage, DB
};
use App\Support\ContentDrip;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use App\Services\{
    GamificationService,
    Notifier
};
use App\Models\{
    DiscussionUpvote,
    LessonResource,
    LessonProgress,
    VideoProgress,
    CourseModule,
    Certificate,
    QuizAttempt,
    Discussion,
    LessonNote,
    Enrollment,
    Course,
    Lesson
};

class LearnController extends Controller
{
    public function start(Course $course)
    {
        $this->authorizeAccess($course);

        $flat = $this->orderedLessons($course);

        abort_if($flat->isEmpty(), 404, 'This course has no lessons yet.');

        $enrollment = Enrollment::where('user_id', auth()->id())->where('course_id', $course->id)->first();
        $lockState = $this->lockState($course, $flat);

        $unlocked = fn ($l) => !($lockState[$l->id]['locked'] ?? false);

        if ($enrollment && $enrollment->last_lesson_id && $flat->contains('id', $enrollment->last_lesson_id)) {
            $lesson = $flat->first(fn ($l) => $l->id === $enrollment->last_lesson_id);
        } else {
            $completedIds = LessonProgress::where('user_id', auth()->id())
                ->whereIn('lesson_id', $flat->pluck('id'))
                ->pluck('lesson_id')
                ->flip();

            $lesson = $flat->first(fn ($l) => $unlocked($l) && !$completedIds->has($l->id))
                ?? $flat->first($unlocked)
                ?? $flat->first();
        }

        return redirect()->route('learn.show', [$course, $lesson]);
    }

    public function show(Course $course, Lesson $lesson)
    {
        $this->authorizeAccess($course);

        $flat = $this->orderedLessons($course);
        $modules = $course->modules()->with('lessons')->get();
        $lockState = $this->lockState($course, $flat);

        abort_unless($flat->contains('id', $lesson->id), 404);

        $currentIdx = $flat->search(fn ($l) => $l->id === $lesson->id);
        $prev = $currentIdx > 0 ? $flat[$currentIdx - 1] : null;
        $next = $currentIdx < $flat->count() - 1 ? $flat[$currentIdx + 1] : null;

        $isLocked = $lockState[$lesson->id]['locked'] ?? false;
        $nextUnlockAt = $isLocked ? ($lockState[$lesson->id]['unlocks_at'] ?? null) : null;

        $completedIds = LessonProgress::where('user_id', auth()->id())
            ->whereIn('lesson_id', $flat->pluck('id'))
            ->pluck('lesson_id')
            ->flip();

        $enrollment = Enrollment::where('user_id', auth()->id())->where('course_id', $course->id)->first();

        if ($enrollment) {
            $enrollment->update(['last_lesson_id' => $lesson->id]);
        }

        $videoProgress = VideoProgress::where('user_id', auth()->id())->where('lesson_id', $lesson->id)->first();

        $notes = LessonNote::where('user_id', auth()->id())
            ->where('lesson_id', $lesson->id)
            ->latest()
            ->get();

        $questions = Discussion::where('course_id', $course->id)
            ->whereNull('parent_id')
            ->where(function ($q) use ($lesson) {
                $q->where('lesson_id', $lesson->id)->orWhereNull('lesson_id');
            })
            ->with(['user', 'replies.user', 'upvotes', 'answeredBy'])
            ->latest()
            ->take(20)
            ->get();

        $totalLessons = $flat->count();
        $doneCount = $completedIds->count();

        $certificate = Certificate::where('user_id', auth()->id())
            ->where('course_id', $course->id)
            ->first();

        $badges = auth()->user()->badges()
            ->orderByPivot('earned_at', 'desc')
            ->take(9)
            ->get();

        $certProgressPct = $totalLessons ? (int) round($doneCount / $totalLessons * 100) : 0;

        // Exams / Tests / Assignments — unlocked once all lessons are completed
        $quizzes = $course->quizzes()
            ->withCount('questions')
            ->where('is_active', true)
            ->orderBy('id')
            ->get();

        $bestScores = [];
        $attemptCounts = [];

        if ($quizzes->isNotEmpty()) {
            $rows = QuizAttempt::where('user_id', auth()->id())
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
            'prev', 'next', 'completedIds', 'enrollment', 'videoProgress',
            'notes', 'questions', 'totalLessons', 'doneCount',
            'quizzes', 'bestScores', 'attemptCounts', 'lockState',
            'isLocked', 'nextUnlockAt', 'certificate', 'badges', 'certProgressPct'
        ));
    }

    /**
     * Lock state for every lesson in the course: whether it is closed to the
     * current user and when it becomes available.
     */
    private function lockState(Course $course, $flat): array
    {
        $map = [];
        foreach ($flat as $lesson) {
            $module = CourseModule::find($lesson->course_module_id);
            $map[$lesson->id] = [
                'locked' => ContentDrip::lessonLocked($course, $lesson, $module, auth()->user()),
                'unlocks_at' => ContentDrip::lessonUnlockDate($course, $lesson, $module),
            ];
        }

        return $map;
    }

    private function lessonModule(Course $course, Lesson $lesson): ?CourseModule
    {
        return $course->modules()->find($lesson->course_module_id);
    }

    /**
     * Block actions on lessons whose content drip date has not been reached.
     */
    private function ensureLessonUnlocked(Course $course, Lesson $lesson): void
    {
        if (ContentDrip::lessonLocked($course, $lesson, $this->lessonModule($course, $lesson), auth()->user())) {
            abort(403, 'This lesson is locked and will be available on its scheduled date.');
        }
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
        $this->ensureLessonUnlocked($course, $lesson);

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

            GamificationService::recordLessonComplete(auth()->user(), $lesson);
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
            $certificate = Certificate::create([
                'code' => Certificate::generateCode(),
                'user_id' => auth()->id(),
                'course_id' => $course->id,
                'issued_at' => now(),
            ]);

            Notifier::certificateIssued($certificate);
            GamificationService::recordCourseCompleted(auth()->user(), $enrollment);
        }
    }

    public function storeNote(Request $request, Course $course, Lesson $lesson)
    {
        $this->authorizeAccess($course);
        $this->ensureLessonUnlocked($course, $lesson);

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

        abort_unless($resource->file_path && Storage::disk('upload')->exists($resource->file_path), 404);

        return Storage::disk('upload')->download($resource->file_path);
    }

    public function storeQuestion(Request $request, Course $course, Lesson $lesson)
    {
        $this->authorizeAccess($course);
        $this->ensureLessonUnlocked($course, $lesson);

        $data = $request->validate([
            'body' => ['required', 'string', 'max:2000'],
            'parent_id' => ['nullable', 'exists:discussions,id'],
        ]);

        $discussion = Discussion::create([
            'user_id' => auth()->id(),
            'course_id' => $course->id,
            'lesson_id' => $lesson->id,
            'parent_id' => $data['parent_id'] ?? null,
            'body' => $data['body'],
        ]);

        GamificationService::recordDiscussionPost(auth()->user(), $discussion);

        if (!empty($data['parent_id'])) {
            $parent = Discussion::with('user')->find($data['parent_id']);
            if ($parent && $parent->user_id !== auth()->id()) {
                Notifier::send(
                    $parent->user,
                    'reply',
                    auth()->user()->name . ' replied to your question',
                    Str::limit($data['body'], 100),
                    route('learn.show', [$course, $lesson]) . '#qa',
                );
            }
        }

        return back()->with('success', $request->filled('parent_id') ? 'Reply posted.' : 'Question posted.');
    }

    public function destroyQuestion(Discussion $discussion)
    {
        $user = auth()->user();
        abort_unless($discussion->user_id === $user->id || $user->hasRole('admin'), 403);

        $discussion->delete();

        return back()->with('success', 'Deleted.');
    }

    public function toggleUpvote(Discussion $discussion)
    {
        $user = auth()->user();

        $existing = DiscussionUpvote::where('discussion_id', $discussion->id)
            ->where('user_id', $user->id)
            ->first();

        if ($existing) {
            $existing->delete();
        } else {
            DiscussionUpvote::create([
                'discussion_id' => $discussion->id,
                'user_id' => $user->id,
            ]);
        }

        return back();
    }

    public function markAnswered(Discussion $discussion)
    {
        $user = auth()->user();

        $course = $discussion->course;
        abort_unless(
            $course->instructor_id === $user->id || $user->hasRole('admin'),
            403,
        );

        $discussion->update([
            'is_answered' => !$discussion->is_answered,
            'answered_by' => $discussion->is_answered ? null : $user->id,
        ]);

        return back()->with('success', $discussion->is_answered ? 'Marked as answered.' : 'Unmarked as answered.');
    }

    public function saveVideoProgress(Request $request, Course $course, Lesson $lesson)
    {
        $this->authorizeAccess($course);
        $this->ensureLessonUnlocked($course, $lesson);

        $data = $request->validate([
            'watched_seconds' => 'required|integer|min:0',
            'duration' => 'required|integer|min:1',
        ]);

        $percentage = min(100, round($data['watched_seconds'] / $data['duration'] * 100, 2));

        VideoProgress::updateOrCreate(
            ['user_id' => auth()->id(), 'lesson_id' => $lesson->id],
            [
                'watched_seconds' => $data['watched_seconds'],
                'percentage' => $percentage,
                'last_position_at' => now(),
            ]
        );

        return response()->json(['ok' => true, 'percentage' => $percentage]);
    }

    public function getVideoProgress(Course $course, Lesson $lesson)
    {
        $this->authorizeAccess($course);
        $this->ensureLessonUnlocked($course, $lesson);

        $progress = VideoProgress::where('user_id', auth()->id())
            ->where('lesson_id', $lesson->id)
            ->first();

        return response()->json([
            'watched_seconds' => $progress?->watched_seconds ?? 0,
            'percentage' => $progress?->percentage ?? 0,
        ]);
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
