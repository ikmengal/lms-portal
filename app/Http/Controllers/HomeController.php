<?php

namespace App\Http\Controllers;

use Illuminate\Database\Query\Builder;
use App\Models\{
    CourseCategory,
    Enrollment,
    Course,
    Review
};


class HomeController extends Controller
{
    public function index()
    {
        $baseCourseQuery = Course::query()
            ->with(['instructor:id,name', 'categoryTerm', 'levelTerm'])
            ->withCount(['enrollments as students_count', 'reviews as reviews_count'])
            ->withAvg('reviews as avg_rating', 'rating');

        $featuredCourses = (clone $baseCourseQuery)
            ->orderByDesc('avg_rating')
            ->orderByDesc('students_count')
            ->take(6)
            ->get();

        $trendingCourses = (clone $baseCourseQuery)
            ->orderByDesc('students_count')
            ->take(3)
           ->get();

        $newCourses = (clone $baseCourseQuery)
            ->latest()
            ->take(3)
            ->get();

        $categories = CourseCategory::where('is_active', true)
            ->withCount(['courses as courses_count' => fn ($q) => $q->whereNull('deleted_at')])
            ->having('courses_count', '>', 0)
            ->orderByDesc('courses_count')
            ->take(8)
            ->get();

        $stats = [
            'courses' => Course::count(),
            'instructors' => Course::distinct('instructor_id')->count('instructor_id'),
            'students' => Enrollment::distinct('user_id')->count('user_id'),
            'avgRating' => round((float) Review::avg('rating'), 1),
        ];

        return view('pages.home', compact(
            'featuredCourses', 'trendingCourses', 'newCourses', 'categories', 'stats'
        ));
    }
}
