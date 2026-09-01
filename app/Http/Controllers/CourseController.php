<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Support\ContentDrip;
use App\Models\{
    Certificate,
    QuizAttempt,
    Enrollment,
    Wishlist,
    Course
};

class CourseController extends Controller
{
    public function index(Request $request)
    {
        $query = Course::query()
            ->with(['instructor:id,name', 'categoryTerm', 'levelTerm'])
            ->withCount(['enrollments as students_count'])
            ->withCount(['reviews as reviews_count'])
            ->withAvg('reviews as avg_rating', 'rating');

        // ---- Search ----
        if ($search = trim((string) $request->get('search'))) {
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%")
                    ->orWhereHas('instructor', fn ($iq) => $iq->where('name', 'like', "%{$search}%"))
                    ->orWhereHas('categoryTerm', fn ($cq) => $cq->where('name', 'like', "%{$search}%"));
            });
        }

        // ---- Category ----
        if ($categorySlug = $request->get('category')) {
            $query->whereHas('categoryTerm', fn ($q) => $q->where('slug', $categorySlug));
        }

        // ---- Level (multi) ----
        if ($levels = array_filter((array) $request->get('levels', []))) {
            $query->whereHas('levelTerm', fn ($q) => $q->whereIn('name', $levels));
        }

        // ---- Price ----
        match ($request->get('price')) {
            'free' => $query->where('price', '<=', 0),
            'paid' => $query->where('price', '>', 0),
            default => null,
        };

        // ---- Language ----
        if ($language = $request->get('language')) {
            $query->where('language', $language);
        }

        // ---- Duration ranges (hours) ----
        match ($request->get('duration')) {
            'short' => $query->where('duration_hours', '<', 10),
            'medium' => $query->whereBetween('duration_hours', [10, 30]),
            'long' => $query->where('duration_hours', '>', 30),
            default => null,
        };

        // ---- Minimum rating (on aggregated average) ----
        if ($minRating = $request->get('rating')) {
            $query->having('avg_rating', '>=', (float) $minRating);
        }

        // ---- Sorting ----
        match ($request->get('sort')) {
            'popular' => $query->orderByDesc('students_count'),
            'rating' => $query->orderByDesc('avg_rating'),
            'price_low' => $query->orderBy('price'),
            'price_high' => $query->orderByDesc('price'),
            'newest' => $query->latest(),
            'a_z' => $query->orderBy('title'),
            default => $query->orderByDesc('students_count'),
        };

        $courses = $query->paginate(9)->withQueryString();

        // ---- Filter UI data ----
        $categories = CourseCategory::where('is_active', true)
            ->withCount(['courses as courses_count' => fn ($q) => $q->whereNull('deleted_at')])
            ->orderBy('name')
            ->get();

        $levels = CourseLevel::where('is_active', true)
            ->whereIn('name', ['Beginner', 'Intermediate', 'Advanced'])
            ->orderBy('id')
            ->pluck('name');

        $languages = Course::select('language')->distinct()->orderBy('language')->pluck('language');

        return view('pages.courses', compact(
            'courses', 'categories', 'levels', 'languages'
        ));
    }

    public function show(string $slug)
    {
        $course = Course::with(['instructor', 'modules.lessons', 'reviews.user'])
            ->when(is_numeric($slug), function ($q) use ($slug) {
                $q->where(fn ($qq) => $qq->where('slug', $slug)->orWhere('id', (int) $slug));
            }, fn ($q) => $q->where('slug', $slug))
            ->firstOrFail();

        $studentsCount = $course->enrollments()->count();
        $reviewsCount = $course->reviews()->count();
        $avgRating = $reviewsCount ? round($course->reviews()->avg('rating'), 1) : 0;

        $ratingDistribution = [];
        for ($stars = 5; $stars >= 1; $stars--) {
            $count = $course->reviews->where('rating', $stars)->count();
            $ratingDistribution[$stars] = [
                'count' => $count,
                'pct' => $reviewsCount ? (int) round($count / $reviewsCount * 100) : 0,
            ];
        }

        $instructorCourses = 0;
        $instructorStudents = 0;
        if ($course->instructor) {
            $instructorCourses = Course::where('instructor_id', $course->instructor->id)->count();
            $instructorStudents = Enrollment::whereIn(
                'course_id',
                Course::where('instructor_id', $course->instructor->id)->pluck('id')
            )->distinct('user_id')->count('user_id');
        }

        $totalLectures = $course->modules->sum(fn ($m) => $m->lessons->count());
        $totalMinutes = $course->modules->sum(fn ($m) => $m->lessons->sum('duration_minutes'));

        $enrollment = null;
        $certificate = null;

        if (auth()->check()) {
            $enrollment = Enrollment::where('user_id', auth()->id())
                ->where('course_id', $course->id)
                ->first();
            $certificate = Certificate::where('user_id', auth()->id())
                ->where('course_id', $course->id)
                ->first();
        }

        $quizzes = $course->quizzes()->withCount('questions')->orderBy('id')->get();

        $bestScores = [];
        $attemptCounts = [];
        if (auth()->check() && ($enrollment || auth()->user()->hasRole('admin'))) {
            $rows = QuizAttempt::where('user_id', auth()->id())
                ->whereIn('quiz_id', $quizzes->pluck('id'))
                ->whereNotNull('completed_at')
                ->selectRaw('quiz_id, MAX(score) as best, COUNT(*) as total')
                ->groupBy('quiz_id')
                ->get();
            $bestScores = $rows->pluck('best', 'quiz_id')->all();
            $attemptCounts = $rows->pluck('total', 'quiz_id')->all();
        }

        $wishlisted = auth()->check()
            && Wishlist::where('user_id', auth()->id())->where('course_id', $course->id)->exists();

        $comingSoon = ContentDrip::courseComingSoon($course);
        $unlocksAt = $course->unlocks_at;

        return view('pages.course-detail', compact(
            'course',
            'enrollment',
            'certificate',
            'studentsCount',
            'reviewsCount',
            'avgRating',
            'ratingDistribution',
            'instructorCourses',
            'instructorStudents',
            'totalLectures',
            'totalMinutes',
            'quizzes',
            'bestScores',
            'attemptCounts',
            'wishlisted',
            'comingSoon',
            'unlocksAt'
        ));
    }

    public function toggleWishlist(Course $course)
    {
        $added = Wishlist::toggle(auth()->user(), $course);

        return back()->with('success', $added ? 'Course saved to your wishlist.' : 'Course removed from your wishlist.');
    }
}
