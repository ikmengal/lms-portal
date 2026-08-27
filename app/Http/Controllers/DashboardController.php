<?php

namespace App\Http\Controllers;

use Illuminate\Routing\Controllers\HasMiddleware; // 1. Import this
use Illuminate\Routing\Controllers\Middleware;    // 2. Import this
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Models\{
    ContactMessage,
    Certificate,
    QuizAttempt,
    Enrollment,
    Course,
    User
};

class DashboardController extends Controller
{
    public static function middleware(): array
    {
        return [
            new Middleware('auth', only: ['auth']),
        ];
    }

    public function index()
    {
        $user = Auth::user();
        $role = $user->getRoleNames()->first();

        return match ($role) {
            'admin' => $this->adminDashboard(),
            'instructor' => $this->instructorDashboard(),
            default => $this->studentDashboard(),
        };
    }

    private function adminDashboard()
    {
        $user = Auth::user();

        $stats = [
            'totalUsers' => User::count(),
            'newUsersToday' => User::whereDate('created_at', today())->count(),
            'totalCourses' => Course::count(),
            'activeCourses' => Course::where('price', '>', 0)->count(),
            'instructors' => User::role('instructor')->count(),
            'students' => User::role('student')->count(),
            'admins' => User::role('admin')->count(),
            'enrollments' => Enrollment::count(),
            'enrollmentsToday' => Enrollment::whereDate('created_at', today())->count(),
        ];

        $recentUsers = User::latest()->take(8)->get();

        $recentMessages = ContactMessage::latest()->take(4)->get();
        $unreadMessages = ContactMessage::where('is_read', false)->count();

        return view('pages.admin.dashboard', compact(
            'stats', 'recentUsers', 'recentMessages', 'unreadMessages'
        ));
    }

    private function instructorDashboard()
    {
        $user = Auth::user();

        $courses = Course::where('instructor_id', $user->id)
            ->withCount(['enrollments', 'modules', 'reviews'])
            ->withAvg('reviews', 'rating')
            ->latest()
            ->get();

        $courseIds = $courses->pluck('id');

        $enrollments = Enrollment::whereIn('course_id', $courseIds)->get();
        $totalStudents = (int) Enrollment::whereIn('course_id', $courseIds)->distinct('user_id')->count('user_id');
        $completedCount = $enrollments->filter(fn ($e) => $e->isCompleted())->count();
        $avgProgress = $enrollments->count() ? round($enrollments->avg('progress'), 1) : 0;

        $revenueTotal = $courses->sum(fn ($c) => ($c->price ?? 0) * $c->enrollments_count);
        $revenueThisMonth = $courses->sum(fn ($c) => ($c->price ?? 0) * $c->enrollments
            ->where('created_at', '>=', now()->startOfMonth())->count());
        $revenueLastMonth = $courses->sum(fn ($c) => ($c->price ?? 0) * $c->enrollments
            ->whereBetween('created_at', [now()->subMonthNoOverflow()->startOfMonth(), now()->startOfMonth()])->count());

        $ratedCourses = $courses->filter(fn ($c) => $c->reviews_avg_rating !== null);
        $avgRating = $ratedCourses->count()
            ? round($ratedCourses->avg(fn ($c) => (float) $c->reviews_avg_rating), 1)
            : null;

        $recentEnrollments = Enrollment::whereIn('course_id', $courseIds)
            ->with(['user', 'course'])
            ->latest()
            ->take(6)
            ->get();

        $topCourses = $courses->sortByDesc('enrollments_count')->take(5)->values();

        $students = $enrollments ?? [];

        $stats = [
            'totalCourses' => $courses->count(),
            'publishedLessons' => Course::query()->whereIn('id', $courseIds)
                ->with('modules.lessons')->get()
                ->sum(fn ($c) => $c->modules->sum(fn ($m) => $m->lessons->count())),
            'totalStudents' => $totalStudents,
            'completedCount' => $completedCount,
            'avgProgress' => $avgProgress,
            'avgRating' => $avgRating,
            'totalReviews' => $courses->sum('reviews_count'),
            'revenueTotal' => $revenueTotal,
            'revenueThisMonth' => $revenueThisMonth,
            'revenueLastMonth' => $revenueLastMonth,
            'revenueGrowth' => $revenueLastMonth > 0
                ? round((($revenueThisMonth - $revenueLastMonth) / $revenueLastMonth) * 100, 1)
                : null,
            'totalEarnings' => 0,
        ];

        return view('pages.instructor.dashboard', get_defined_vars());
    }

    public function earnings()
    {
        $user = Auth::user();

        $courses = Course::where('instructor_id', $user->id)
            ->withCount(['enrollments', 'reviews'])
            ->withAvg('reviews', 'rating')
            ->orderByDesc(
                Enrollment::selectRaw('count(*)')
                    ->whereColumn('course_id', 'courses.id')
            )
            ->get();

        $perCourse = $courses->map(fn ($c) => (object) [
            'id' => $c->id,
            'title' => $c->title,
            'slug' => $c->slug,
            'price' => $c->price ?? 0,
            'students' => $c->enrollments_count,
            'revenue' => ($c->price ?? 0) * $c->enrollments_count,
            'rating' => $c->reviews_avg_rating ? round((float) $c->reviews_avg_rating, 1) : null,
        ])->values();

        $totalRevenue = $perCourse->sum('revenue');

        $monthly = collect(range(5, 0))->map(function ($i) use ($courses) {
            $start = now()->subMonths($i)->startOfMonth();
            $end = now()->subMonths($i)->endOfMonth();
            $revenue = $courses->sum(fn ($c) => ($c->price ?? 0) * $c->enrollments
                ->whereBetween('created_at', [$start, $end])->count());

            return (object) [
                'label' => $start->format('M'),
                'revenue' => $revenue,
            ];
        });

        $records = Enrollment::whereHas('course', fn ($q) => $q->where('instructor_id', $user->id))
            ->with(['user', 'course'])
            ->latest()
            ->paginate(10)
            ->through(fn ($e) => (object) [
                'student' => $e->user?->name ?? 'Deleted user',
                'course' => $e->course?->title,
                'amount' => $e->course?->price ?? 0,
                'date' => $e->created_at,
                'progress' => $e->progress,
            ]);

        $thisMonth = now()->startOfMonth();
        $stats = [
            'total' => $totalRevenue,
            'thisMonth' => $records->getCollection()->toBase()
                ->sum(fn ($r) => $r->date >= $thisMonth ? $r->amount : 0),
            'maxMonthly' => max($monthly->max('revenue'), 1),
            'avgPerSale' => $records->isEmpty() || $totalRevenue == 0
                ? 0
                : round($totalRevenue / max(Enrollment::whereHas('course', fn ($q) => $q->where('instructor_id', $user->id))->count(), 1), 2),
        ];

        return view('pages.instructor.earnings', compact('perCourse', 'monthly', 'records', 'stats'));
    }

    public function students(Request $request)
    {
        $user = Auth::user();

        $myCourseIds = Course::where('instructor_id', $user->id)->pluck('id');

        $query = Enrollment::whereIn('course_id', $myCourseIds)
            ->with(['user', 'course']);

        if ($request->filled('search')) {
            $term = '%' . $request->search . '%';
            $query->whereHas('user', fn ($q) => $q->where('name', 'like', $term)->orWhere('email', 'like', $term));
        }

        if ($request->filled('course')) {
            $query->where('course_id', $request->course);
        }

        if ($request->filled('status')) {
            match ($request->status) {
                'completed' => $query->whereNotNull('completed_at'),
                'active' => $query->whereNull('completed_at')->where('progress', '>', 0),
                default => $query->whereNull('completed_at')->where('progress', 0),
            };
        }

        $enrollments = $query->latest()->paginate(12)->withQueryString();

        $allEnrollments = Enrollment::whereIn('course_id', $myCourseIds)->get();
        $studentStats = [
            'total' => $allEnrollments->count(),
            'unique' => $allEnrollments->unique('user_id')->count(),
            'completed' => $allEnrollments->filter(fn ($e) => $e->isCompleted())->count(),
            'justStarted' => $allEnrollments->where('progress', 0)->count(),
        ];

        $myCourses = Course::where('instructor_id', $user->id)->orderBy('title')->get(['id', 'title']);

        return view('pages.instructor.students', compact('enrollments', 'studentStats', 'myCourses'));
    }

    private function studentDashboard()
    {
        $user = Auth::user();

        $enrolledCourses = $user->enrollments()->with(['course.instructor', 'course.categoryTerm'])->latest('updated_at')->get();
        $certificates = $user->certificates()->with('course')->latest('issued_at')->get();
        $wishlist = $user->wishlists()->with(['course.instructor'])->latest()->get();

        $completedCount = $enrolledCourses->filter(fn ($e) => $e->isCompleted())->count();
        $learningHours = (int) round($enrolledCourses->sum(
            fn ($e) => ($e->course->duration_hours ?? 0) * ($e->progress / 100)
        ));

        $stats = [
            'enrolledCount' => $enrolledCourses->count(),
            'inProgressCount' => $enrolledCourses->filter(fn ($e) => !$e->isCompleted() && $e->progress > 0)->count(),
            'notStartedCount' => $enrolledCourses->where('progress', 0)->count(),
            'completedCount' => $completedCount,
            'certificates' => $certificates->count(),
            'wishlist' => $wishlist->count(),
            'avgProgress' => $enrolledCourses->count() ? round($enrolledCourses->avg('progress')) : 0,
            'learningHours' => $learningHours,
        ];

        // Continue learning: most recently active in-progress course
        $continueLearning = $enrolledCourses
            ->first(fn ($e) => !$e->isCompleted());

        // Recent activity timeline
        $activities = collect();

        foreach ($user->enrollments()->with('course')->latest()->take(5)->get() as $e) {
            $activities->push([
                'type' => $e->isCompleted() ? 'certificate' : 'enrolled',
                'text' => ($e->isCompleted() ? 'Completed ' : 'Enrolled in ') . '<strong>' . e($e->course?->title ?? 'a course') . '</strong>',
                'meta' => $e->isCompleted() && $e->completed_at ? $e->completed_at : $e->created_at,
                'href' => $e->course ? route('courses.show', $e->course->slug) : null,
            ]);
        }

        foreach (QuizAttempt::where('user_id', $user->id)->with('quiz.course')->latest('completed_at')->take(6)->get() as $attempt) {
            if (!$attempt->quiz) continue;
            $activities->push([
                'type' => $attempt->passed ? 'passed' : 'failed',
                'text' => ($attempt->passed ? 'Passed' : 'Failed') . ' <strong>' . e($attempt->quiz->title) . '</strong> with ' . number_format($attempt->score, 0) . '%',
                'meta' => $attempt->completed_at ?? $attempt->created_at,
                'href' => $attempt->quiz->course ? route('courses.show', $attempt->quiz->course->slug) : null,
            ]);
        }

        foreach ($certificates->take(4) as $certificate) {
            $activities->push([
                'type' => 'certificate',
                'text' => 'Earned a certificate for <strong>' . e($certificate->course?->title ?? 'a course') . '</strong>',
                'meta' => $certificate->issued_at,
                'href' => route('certificates.show', $certificate),
            ]);
        }

        foreach ($user->reviews()->with('course')->latest()->take(4)->get() as $review) {
            $activities->push([
                'type' => 'review',
                'text' => 'Rated <strong>' . e($review->course?->title ?? 'a course') . '</strong> ' . str_repeat('★', $review->rating),
                'meta' => $review->created_at,
                'href' => $review->course ? route('courses.show', $review->course->slug) : null,
            ]);
        }

        $recentActivity = $activities
            ->sortByDesc(fn ($a) => $a['meta']?->timestamp ?? 0)
            ->take(8)
            ->values();

        $gamification = [
            'xp' => $user->xp ?? 0,
            'level' => \App\Services\GamificationService::currentLevel($user),
            'xpProgress' => \App\Services\GamificationService::xpProgressInLevel($user),
            'streak' => $user->streak?->current_streak ?? 0,
            'rank' => \App\Services\GamificationService::rank($user),
            'recentBadges' => $user->badges()->orderByPivot('earned_at', 'desc')->take(3)->get(),
        ];

        return view('pages.student.dashboard', compact(
            'stats', 'enrolledCourses', 'certificates', 'wishlist', 'continueLearning', 'recentActivity', 'gamification'
        ));
    }

    public function certificate(Certificate $certificate)
    {
        abort_unless($certificate->user_id === Auth::id(), 403);

        $certificate->load(['course.instructor', 'user']);

        return view('pages.student.certificate', compact('certificate'));
    }

    public function downloadCertificate(Certificate $certificate)
    {
        abort_unless($certificate->user_id === Auth::id(), 403);

        $certificate->load(['course.instructor', 'user']);

        $dompdf = new \Dompdf\Dompdf(['isRemoteEnabled' => false]);
        $dompdf->loadHtml(view('pages.student.certificate-pdf', ['certificate' => $certificate])->render());
        $dompdf->setPaper('A4', 'landscape');
        $dompdf->render();

        return response($dompdf->output(), 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'attachment; filename="certificate-' . $certificate->code . '.pdf"',
        ]);
    }

    public function verify(string $code)
    {
        $certificate = Certificate::with(['user', 'course.instructor'])
            ->where('code', strtoupper(trim($code)))
            ->first();

        $enrollment = $certificate
            ? Enrollment::where('user_id', $certificate->user_id)
                ->where('course_id', $certificate->course_id)
                ->first()
            : null;

        return view('pages.verify-certificate', compact('certificate', 'enrollment'));
    }

    public function users()
    {
        $users = User::with('roles')->latest()->paginate(10);

        return view('pages.admin.users', compact('users'));
    }
}
