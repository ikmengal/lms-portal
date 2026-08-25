<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\CourseCategory;
use App\Models\CourseLevel;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class CourseController extends Controller
{
    public function index(Request $request)
    {
        $user = auth()->user();

        $courses = Course::with(['instructor', 'enrollments', 'categoryTerm', 'levelTerm'])
            ->when(!$user->hasRole('admin'), fn ($q) => $q->where('instructor_id', $user->id))
            ->when($request->filled('search'), fn ($q) => $q->where('title', 'like', '%' . $request->search . '%'))
            ->when($request->filled('category'), fn ($q) => $q->where('course_category_id', $request->category))
            ->when($request->filled('level'), fn ($q) => $q->where('course_level_id', $request->level))
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return view('pages.admin.courses.index', [
            'courses' => $courses,
            'categories' => $this->categories(),
            'levels' => $this->levels(),
        ]);
    }

    public function show(Course $course)
    {
        $this->authorizeCourse($course);

        $course->load([
            'instructor',
            'modules.lessons',
            'quizzes.questions',
            'enrollments.user',
        ]);

        return view('pages.admin.courses.show', [
            'course' => $course,
            'studentsCount' => $course->enrollments->count(),
            'lecturesCount' => $course->modules->sum(fn ($m) => $m->lessons->count()),
            'totalMinutes' => $course->modules->sum(fn ($m) => $m->lessons->sum('duration_minutes')),
            'recentStudents' => $course->enrollments()->with('user')->latest()->take(8)->get(),
        ]);
    }

    public function create()
    {
        return view('pages.admin.courses.create', [
            'categories' => $this->categories(),
            'levels' => $this->levels(),
            'instructors' => User::role('instructor')->orderBy('name')->get(['id', 'name']),
        ]);
    }

    public function store(Request $request)
    {
        $validated = $this->validateCourse($request);
        $validated['instructor_id'] = $this->resolveInstructorId($request);

        $validated['slug'] = $this->uniqueSlug($validated['title']);

        if ($request->hasFile('thumbnail')) {
            $validated['thumbnail'] = $request->file('thumbnail')->store('courses/thumbnails', 'public');
        }

        Course::create($validated);

        return redirect()->route('admin.courses.index')->with('success', 'Course created successfully.');
    }

    public function edit(Course $course)
    {
        $this->authorizeCourse($course);

        return view('pages.admin.courses.edit', [
            'course' => $course,
            'categories' => $this->categories(),
            'levels' => $this->levels(),
            'instructors' => User::role('instructor')->orderBy('name')->get(['id', 'name']),
        ]);
    }

    public function update(Request $request, Course $course)
    {
        $this->authorizeCourse($course);

        $validated = $this->validateCourse($request);
        $validated['instructor_id'] = $this->resolveInstructorId($request, $course);

        if (Str::slug($validated['title']) !== $course->slug) {
            $validated['slug'] = $this->uniqueSlug($validated['title'], $course->id);
        }

        if ($request->hasFile('thumbnail')) {
            if ($course->thumbnail) {
                Storage::disk('public')->delete($course->thumbnail);
            }
            $validated['thumbnail'] = $request->file('thumbnail')->store('courses/thumbnails', 'public');
        }

        $course->update($validated);

        return redirect()->route('admin.courses.index')->with('success', 'Course updated successfully.');
    }

    public function destroy(Course $course)
    {
        $this->authorizeCourse($course);

        if ($course->thumbnail) {
            Storage::disk('public')->delete($course->thumbnail);
        }

        $course->delete();

        return redirect()->route('admin.courses.index')->with('success', 'Course moved to trash. You can restore it anytime.');
    }

    private function authorizeCourse(Course $course): void
    {
        $user = auth()->user();
        abort_unless($user->hasRole('admin') || $course->instructor_id === $user->id, 403);
    }

    private function resolveInstructorId(Request $request, ?Course $course = null): ?int
    {
        $user = auth()->user();

        if (!$user->hasRole('admin')) {
            return $user->id;
        }

        return $request->input('instructor_id') ?: $course?->instructor_id;
    }

    public function trash(Request $request)
    {
        $courses = Course::onlyTrashed()
            ->with(['instructor', 'enrollments'])
            ->when($request->filled('search'), fn ($q) => $q->where('title', 'like', '%' . $request->search . '%'))
            ->latest('deleted_at')
            ->paginate(10)
            ->withQueryString();

        return view('pages.admin.courses.trash', compact('courses'));
    }

    public function restore(int $id)
    {
        $course = Course::withTrashed()->findOrFail($id);
        $course->restore();

        return redirect()->route('admin.courses.trash')->with('success', 'Course restored successfully.');
    }

    public function forceDelete(int $id)
    {
        $course = Course::onlyTrashed()->findOrFail($id);

        if ($course->thumbnail) {
            Storage::disk('public')->delete($course->thumbnail);
        }

        $course->forceDelete();

        return redirect()->route('admin.courses.trash')->with('success', 'Course permanently deleted.');
    }

    private function validateCourse(Request $request): array
    {
        return $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:10000'],
            'course_category_id' => [
                'required',
                Rule::exists('course_categories', 'id')->whereNull('deleted_at'),
            ],
            'course_level_id' => [
                'required',
                Rule::exists('course_levels', 'id')->whereNull('deleted_at'),
            ],
            'duration_hours' => ['required', 'integer', 'min:0', 'max:2000'],
            'price' => ['required', 'numeric', 'min:0', 'max:99999.99'],
            'instructor_id' => ['nullable', 'exists:users,id'],
            'thumbnail' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
        ]);
    }

    private function categories()
    {
        return CourseCategory::ordered()->get();
    }

    private function levels()
    {
        return CourseLevel::ordered()->get();
    }

    private function uniqueSlug(string $title, ?int $ignoreId = null): string
    {
        $base = Str::slug($title) ?: 'course';
        $slug = $base;
        $i = 1;

        while (Course::withTrashed()->where('slug', $slug)->when($ignoreId, fn ($q) => $q->where('id', '!=', $ignoreId))->exists()) {
            $slug = $base . '-' . ++$i;
        }

        return $slug;
    }
}
