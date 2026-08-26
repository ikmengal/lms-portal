<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\{Certificate, Course, Enrollment, LessonProgress, Payment, QuizAttempt, User};

class AnalyticsController extends Controller
{
    public function index()
    {
        $now = now();
        $thirtyDaysAgo = $now->copy()->subDays(30);
        $monthStart = $now->copy()->startOfMonth();

        // ---------- Students ----------
        $studentIds = User::role('student')->pluck('id');
        $totalStudents = $studentIds->count();
        $newStudents30d = User::role('student')->where('created_at', '>=', $thirtyDaysAgo)->count();

        // Active = completed a lesson OR enrolled within the last 30 days (union).
        $activeStudents = LessonProgress::whereIn('user_id', $studentIds)->where('completed_at', '>=', $thirtyDaysAgo)
            ->union(
                Enrollment::query()->select('user_id')->whereIn('user_id', $studentIds)->where('created_at', '>=', $thirtyDaysAgo)
            )
            ->distinct()->get(['user_id'])->count();

        // ---------- Enrollments & completion ----------
        $totalEnrollments = Enrollment::count();
        $enrollmentsThisMonth = Enrollment::where('created_at', '>=', $monthStart)->count();
        $completedEnrollments = Enrollment::where('progress', '>=', 100)->count();
        $completionRate = $totalEnrollments > 0 ? round($completedEnrollments / $totalEnrollments * 100, 1) : 0;
        $avgProgress = round((float) Enrollment::avg('progress'), 1);

        // Progress distribution buckets (for engagement section)
        $bucketCounts = Enrollment::selectRaw("
                SUM(CASE WHEN progress = 0 THEN 1 ELSE 0 END) as not_started,
                SUM(CASE WHEN progress BETWEEN 1 AND 49 THEN 1 ELSE 0 END) as early,
                SUM(CASE WHEN progress BETWEEN 50 AND 99 THEN 1 ELSE 0 END) as mid,
                SUM(CASE WHEN progress >= 100 THEN 1 ELSE 0 END) as done
            ")->first();
        $progressBuckets = [
            ['label' => 'Not started', 'count' => (int) ($bucketCounts->not_started ?? 0), 'color' => 'bg-gray-300'],
            ['label' => 'Early (1-49%)', 'count' => (int) ($bucketCounts->early ?? 0), 'color' => 'bg-accent-400'],
            ['label' => 'Mid (50-99%)', 'count' => (int) ($bucketCounts->mid ?? 0), 'color' => 'bg-primary-400'],
            ['label' => 'Completed', 'count' => (int) ($bucketCounts->done ?? 0), 'color' => 'bg-secondary-500'],
        ];

        // ---------- Revenue ----------
        $revenueQuery = Payment::where('status', 'paid');
        $totalRevenue = (float) (clone $revenueQuery)->sum('amount');
        $revenueThisMonth = (float) (clone $revenueQuery)->where('paid_at', '>=', $monthStart)->sum('amount');
        $paymentsCount = (clone $revenueQuery)->count();

        $monthlyRevenue = collect(range(5, 0))->map(function ($i) use ($revenueQuery) {
            $start = now()->subMonths($i)->startOfMonth();
            $end = now()->subMonths($i)->endOfMonth();

            return [
                'label' => $start->format('M'),
                'revenue' => (float) (clone $revenueQuery)->whereBetween('paid_at', [$start, $end])->sum('amount'),
                'enrollments' => Enrollment::whereBetween('created_at', [$start, $end])->count(),
            ];
        });
        $maxMonthlyRevenue = max($monthlyRevenue->max('revenue'), 1);

        // ---------- Instructor earnings (top 5 by revenue from their courses) ----------
        $instructorEarnings = Payment::query()
            ->where('payments.status', 'paid')
            ->join('courses', 'courses.id', '=', 'payments.course_id')
            ->join('users as instructors', 'instructors.id', '=', 'courses.instructor_id')
            ->groupBy('instructors.id', 'instructors.name')
            ->orderByDesc('revenue')
            ->limit(5)
            ->get([
                'instructors.id',
                'instructors.name',
                \Illuminate\Support\Facades\DB::raw('SUM(payments.amount) as revenue'),
                \Illuminate\Support\Facades\DB::raw('COUNT(DISTINCT courses.id) as courses_count'),
                \Illuminate\Support\Facades\DB::raw('COUNT(payments.id) as sales_count'),
            ]);
        $maxInstructorRevenue = max($instructorEarnings->max('revenue') ?? 0, 1);

        // ---------- Most popular courses (top 6 by enrollments) ----------
        $popularCourses = Course::query()
            ->withCount(['enrollments'])
            ->withAvg('reviews as avg_rating', 'rating')
            ->addSelect([
                'revenue' => Payment::selectRaw('COALESCE(SUM(amount),0)')
                    ->where('status', 'paid')
                    ->whereColumn('course_id', 'courses.id'),
                'instructor_name' => User::select('name')->whereColumn('id', 'courses.instructor_id'),
            ])
            ->withTrashed()
            ->orderByDesc('enrollments_count')
            ->limit(6)
            ->get();
        $maxCourseEnrollments = max($popularCourses->max('enrollments_count') ?? 0, 1);

        // ---------- Quiz performance ----------
        $attemptsQuery = QuizAttempt::whereNotNull('completed_at');
        $totalAttempts = (clone $attemptsQuery)->count();
        $avgQuizScore = round((float) (clone $attemptsQuery)->avg('score'), 1);
        $passedAttempts = (clone $attemptsQuery)->where('passed', true)->count();
        $passRate = $totalAttempts > 0 ? round($passedAttempts / $totalAttempts * 100, 1) : 0;

        $quizPerformanceByQuiz = \App\Models\Quiz::withTrashed()
            ->withCount(['attempts as attempts_count' => fn ($q) => $q->whereNotNull('completed_at')])
            ->addSelect([
                'quizzes.*',
                'avg_score' => QuizAttempt::selectRaw('AVG(score)')
                    ->whereColumn('quiz_id', 'quizzes.id')
                    ->whereNotNull('completed_at'),
            ])
            ->orderByDesc('avg_score')
            ->limit(15)
            ->get()
            ->filter(fn ($q) => $q->attempts_count > 0)
            ->take(5)
            ->values();

        return view('pages.admin.analytics', compact(
            'totalStudents', 'newStudents30d', 'activeStudents',
            'totalEnrollments', 'enrollmentsThisMonth', 'completedEnrollments', 'completionRate', 'avgProgress',
            'progressBuckets', 'totalRevenue', 'revenueThisMonth', 'paymentsCount', 'monthlyRevenue', 'maxMonthlyRevenue',
            'instructorEarnings', 'maxInstructorRevenue',
            'popularCourses', 'maxCourseEnrollments',
            'totalAttempts', 'avgQuizScore', 'passRate', 'quizPerformanceByQuiz'
        ));
    }
}
