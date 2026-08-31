<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Support\Facades\Storage;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\Notifier;
use App\Models\{
    CourseModule,
    LessonResource,
    Course,
    Lesson
};

class CourseContentController extends Controller
{
    private function authorizeCourse(Course $course): void
    {
        $user = auth()->user();
        abort_unless($user->hasRole('admin') || $course->instructor_id === $user->id, 403);
    }

    private function authorizeModule(CourseModule $module): void
    {
        $this->authorizeCourse(Course::findOrFail($module->course_id));
    }

    private function authorizeLesson(Lesson $lesson): void
    {
        $this->authorizeModule(CourseModule::findOrFail($lesson->course_module_id));
    }

    public function index(Course $course)
    {
        $this->authorizeCourse($course);
        $course->load(['modules.lessons', 'quizzes']);

        return view('pages.admin.courses.curriculum', [
            'course' => $course,
            'nextModuleOrder' => ((int) $course->modules->max('sort_order')) + 1,
            'totalLessons' => $course->modules->sum(fn ($m) => $m->lessons->count()),
        ]);
    }

    public function storeModule(Request $request, Course $course)
    {
        $this->authorizeCourse($course);
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
        ]);

        CourseModule::create([
            'course_id' => $course->id,
            'title' => $validated['title'],
            'sort_order' => (int) $course->modules()->max('sort_order') + 1,
        ]);

        return back()->with('success', 'Chapter added successfully.');
    }

    public function updateModule(Request $request, CourseModule $module)
    {
        $this->authorizeModule($module);
        $validated = $request->validate(['title' => ['required', 'string', 'max:255']]);

        $module->update($validated);

        return back()->with('success', 'Chapter renamed successfully.');
    }

    public function moveModule(Request $request, CourseModule $module)
    {
        $this->authorizeModule($module);
        $validated = $request->validate(['direction' => ['required', 'in:up,down']]);

        $swapWith = Course::find($module->course_id)
            ->modules()
            ->where('sort_order', $module->sort_order + ($validated['direction'] === 'up' ? -1 : 1))
            ->first();

        if ($swapWith) {
            [$module->sort_order, $swapWith->sort_order] = [$swapWith->sort_order, $module->sort_order];
            $module->save();
            $swapWith->save();
        }

        return back();
    }

    public function destroyModule(CourseModule $module)
    {
        $this->authorizeModule($module);
        $module->delete();

        return back()->with('success', 'Chapter deleted.');
    }

    public function storeLesson(Request $request, CourseModule $module)
    {
        $this->authorizeModule($module);
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'duration_minutes' => ['required', 'integer', 'min:0', 'max:600'],
            'video_url' => ['nullable', 'url', 'max:2048'],
        ]);

        Lesson::create([
            'course_module_id' => $module->id,
            'title' => $validated['title'],
            'duration_minutes' => $validated['duration_minutes'],
            'video_url' => $validated['video_url'] ?? null,
            'sort_order' => (int) $module->lessons()->max('sort_order') + 1,
        ]);

        Notifier::newLesson($module->course, $module->lessons()->latest('id')->first());

        return back()->with('success', 'Lesson added successfully.');
    }

    public function updateLesson(Request $request, Lesson $lesson)
    {
        $this->authorizeLesson($lesson);
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'duration_minutes' => ['required', 'integer', 'min:0', 'max:600'],
            'video_url' => ['nullable', 'url', 'max:2048'],
        ]);

        $lesson->update($validated);

        return back()->with('success', 'Lesson updated successfully.');
    }

    public function destroyLesson(Lesson $lesson)
    {
        $this->authorizeLesson($lesson);
        $lesson->delete();

        return back()->with('success', 'Lesson deleted.');
    }

    public function storeResource(Request $request, Lesson $lesson)
    {
        $this->authorizeLesson($lesson);

        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'file' => ['nullable', 'file', 'max:20480'],
            'external_url' => ['nullable', 'url', 'max:2048'],
        ]);

        abort_if(empty($validated['external_url']) && !$request->hasFile('file'), 422, 'Provide a file or a link.');

        LessonResource::create([
            'lesson_id' => $lesson->id,
            'title' => $validated['title'],
            'file_path' => $request->hasFile('file')
                ? $request->file('file')->store('lessons/resources', 'upload')
                : null,
            'external_url' => $validated['external_url'] ?? null,
        ]);

        return back()->with('success', 'Resource added.');
    }

    public function destroyResource(LessonResource $resource)
    {
        $this->authorizeLesson(Lesson::withTrashed()->findOrFail($resource->lesson_id));

        if ($resource->file_path) {
            Storage::disk('upload')->delete($resource->file_path);
        }

        $resource->delete();

        return back()->with('success', 'Resource removed.');
    }
}
