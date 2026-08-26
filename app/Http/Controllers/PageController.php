<?php

namespace App\Http\Controllers;

use App\Models\BlogPost;
use App\Models\Certificate;
use App\Models\ContactMessage;
use App\Models\Course;
use App\Models\CourseCategory;
use App\Models\Enrollment;
use App\Models\Review;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PageController extends Controller
{
    public function about()
    {
        $stats = [
            'courses' => Course::count(),
            'instructors' => Course::distinct('instructor_id')->count('instructor_id'),
            'students' => Enrollment::distinct('user_id')->count('user_id'),
            'certificates' => Certificate::count(),
            'categories' => CourseCategory::where('is_active', true)->count(),
            'avgRating' => round((float) Review::avg('rating'), 1),
        ];

        return view('pages.about', compact('stats'));
    }

    public function contact()
    {
        return view('pages.contact');
    }

    public function contactSubmit(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:100'],
            'email' => ['required', 'email', 'max:150'],
            'subject' => ['required', 'string', 'max:150'],
            'message' => ['required', 'string', 'min:10', 'max:3000'],
        ], [
            'message.min' => 'Your message must be at least 10 characters.',
        ]);

        $message = ContactMessage::create($validated);

        // Notify every admin via email + in-app notification.
        try {
            \Illuminate\Support\Facades\Notification::send(
                User::role('admin')->get(),
                new \App\Notifications\NewContactMessageNotification($message)
            );
        } catch (\Throwable $e) {
            report($e); // Never block the contact form because of a mail failure.
        }

        return back()->with('success', 'Thanks for reaching out! Our team will get back to you within 24 hours.');
    }

    public function categories(Request $request)
    {
        $search = trim((string) $request->get('search'));
        $sort = $request->get('sort', 'popular');

        $query = CourseCategory::where('is_active', true)
            ->withCount(['courses as courses_count' => fn ($q) => $q->whereNull('deleted_at')]);

        if ($search !== '') {
            $query->where('name', 'like', "%{$search}%");
        }

        match ($sort) {
            'a_z' => $query->orderBy('name'),
            'newest' => $query->latest(),
            default => $query->orderByDesc('courses_count')->orderBy('name'),
        };

        $categories = $query->get();

        $totals = [
            'categories' => CourseCategory::where('is_active', true)->count(),
            'courses' => Course::count(),
            'enrollments' => Enrollment::count(),
        ];

        // Cycle through palette colors for card icons
        $palette = ['purple', 'blue', 'orange', 'red', 'green', 'indigo', 'yellow', 'primary', 'accent', 'secondary'];
        $categories->each(fn ($cat, $i) => $cat->color = $palette[$i % count($palette)]);

        return view('pages.categories', compact('categories', 'totals', 'search', 'sort'));
    }

    public function instructors(Request $request)
    {
        $search = trim((string) $request->get('search'));

        $query = User::role('instructor')
            ->whereNull('deleted_at')
            ->withCount(['courses as courses_count' => fn ($q) => $q->whereNull('courses.deleted_at')])
            ->addSelect([
                'students_count' => Enrollment::selectRaw('COUNT(DISTINCT user_id)')
                    ->whereIn('course_id', Course::query()->select('id')->whereColumn('instructor_id', 'users.id')),
                'reviews_count' => Review::selectRaw('COUNT(*)')
                    ->whereIn('course_id', Course::query()->select('id')->whereColumn('instructor_id', 'users.id')),
                'avg_rating' => Review::selectRaw('AVG(rating)')
                    ->whereIn('course_id', Course::query()->select('id')->whereColumn('instructor_id', 'users.id')),
            ]);

        if ($search !== '') {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('bio', 'like', "%{$search}%");
            });
        }

        match ($request->get('sort')) {
            'rating' => $query->orderByDesc('avg_rating'),
            'a_z' => $query->orderBy('name'),
            'most_courses' => $query->orderByDesc('courses_count'),
            'newest' => $query->latest(),
            default => $query->orderByDesc('students_count'),
        };

        $instructors = $query->paginate(9)->withQueryString();

        $topInstructors = (clone $query)->orderByDesc('students_count')->take(3)->get();

        $totals = [
            'instructors' => User::role('instructor')->whereNull('deleted_at')->count(),
            'courses' => Course::count(),
            'students' => Enrollment::distinct('user_id')->count('user_id'),
            'countries' => 40,
        ];

        return view('pages.instructors', compact('instructors', 'topInstructors', 'totals', 'search'));
    }

    public function instructorShow(User $instructor, Request $request)
    {
        abort_unless($instructor->hasRole('instructor'), 404);

        $baseCourseQuery = Course::query()
            ->with(['categoryTerm', 'levelTerm'])
            ->withCount(['enrollments as students_count', 'reviews as reviews_count'])
            ->withAvg('reviews as avg_rating', 'rating')
            ->where('instructor_id', $instructor->id);

        $totalCourses = (clone $baseCourseQuery)->count();
        $totalStudents = Enrollment::whereIn(
            'course_id',
            Course::where('instructor_id', $instructor->id)->select('id')
        )->distinct('user_id')->count('user_id');

        $courseIds = Course::where('instructor_id', $instructor->id)->pluck('id');
        $avgRating = Review::whereIn('course_id', $courseIds)->count()
            ? round((float) Review::whereIn('course_id', $courseIds)->avg('rating'), 1)
            : 0;
        $reviewsCount = Review::whereIn('course_id', $courseIds)->count();

        $coursesQuery = (clone $baseCourseQuery);
        match ($request->get('sort')) {
            'price_low' => $coursesQuery->orderBy('price'),
            'price_high' => $coursesQuery->orderByDesc('price'),
            'rating' => $coursesQuery->orderByDesc('avg_rating'),
            'a_z' => $coursesQuery->orderBy('title'),
            default => $coursesQuery->orderByDesc('students_count'),
        };
        $courses = $coursesQuery->paginate(6)->withQueryString();

        $latestReviews = Review::whereIn('course_id', $courseIds)
            ->with(['user:id,name', 'course:id,title,slug'])
            ->latest()
            ->take(3)
            ->get();

        return view('pages.instructor-profile', compact(
            'instructor',
            'courses',
            'totalCourses',
            'totalStudents',
            'avgRating',
            'reviewsCount',
            'latestReviews'
        ));
    }

    public function blog(Request $request)
    {
        $postsQuery = BlogPost::published()
            ->with('author:id,name')
            ->orderByDesc('published_at');

        $featured = null;
        if (! $request->filled('category') && ! $request->filled('search') && ! $request->filled('page')) {
            $featured = (clone $postsQuery)->first();
        }

        $posts = (clone $postsQuery)
            ->when($featured, fn ($q) => $q->whereKeyNot($featured->getKey()))
            ->when($category = $request->get('category'), fn ($q) => $q->where('category', $category))
            ->when($search = trim((string) $request->get('search')), function ($q) use ($search) {
                $q->where(function ($qq) use ($search) {
                    $qq->where('title', 'like', "%{$search}%")
                        ->orWhere('excerpt', 'like', "%{$search}%")
                        ->orWhere('content', 'like', "%{$search}%");
                });
            })
            ->paginate(6)
            ->withQueryString();

        $categories = BlogPost::published()
            ->select('category', DB::raw('COUNT(*) as posts_count'))
            ->groupBy('category')
            ->orderBy('category')
            ->get();

        $popular = BlogPost::published()->orderByDesc('views')->take(4)->get();

        return view('pages.blog', compact('featured', 'posts', 'categories', 'popular'));
    }

    public function blogShow(BlogPost $post)
    {
        abort_unless($post->published_at && $post->published_at->lte(now()), 404);

        $post->increment('views');

        $related = BlogPost::published()
            ->whereKeyNot($post->getKey())
            ->where(function ($q) use ($post) {
                $q->where('category', $post->category)
                    ->orWhere('author_id', $post->author_id);
            })
            ->orderByRaw('CASE WHEN category = ? THEN 0 ELSE 1 END', [$post->category])
            ->orderByDesc('published_at')
            ->take(3)
            ->get();

        if ($related->isEmpty()) {
            $related = BlogPost::published()
                ->whereKeyNot($post->getKey())
                ->latest('published_at')
                ->take(3)
                ->get();
        }

        $moreArticles = BlogPost::published()
            ->whereKeyNot($post->getKey())
            ->inRandomOrder()
            ->take(2)
            ->get(['id', 'title', 'slug']);

        return view('pages.blog-detail', compact('post', 'related', 'moreArticles'));
    }

    public function faq()
    {
        $faqs = [
            'Getting Started' => [
                [
                    'q' => 'How do I create an account?',
                    'a' => 'Click the "Sign Up Free" button in the top navigation, fill in your name, email and password, then verify your email address. Your account is activated instantly after verification — no credit card required.',
                ],
                [
                    'q' => 'Do I need any prior experience to start learning?',
                    'a' => 'No. Most of our courses are designed to take you from absolute zero. Each course clearly lists its prerequisites and difficulty level (Beginner, Intermediate, Advanced), so you can pick the right starting point.',
                ],
                [
                    'q' => 'Can I learn on my phone or tablet?',
                    'a' => 'Yes. The platform is fully responsive and works in any modern mobile browser. Your progress syncs automatically across devices — start a lesson on your laptop and continue on your phone.',
                ],
            ],
            'Courses & Learning' => [
                [
                    'q' => 'How long do I have access to a course?',
                    'a' => 'Forever. Once you enroll in a course, you get lifetime access — including all future updates, new lessons, and downloadable resources the instructor adds.',
                ],
                [
                    'q' => 'Do courses include hands-on projects?',
                    'a' => 'Yes. Nearly every course includes real-world projects, exercises, and downloadable source files. Many also include quizzes and a final exam so you can validate your knowledge before claiming your certificate.',
                ],
                [
                    'q' => 'What if a course is too easy or too hard for me?',
                    'a' => 'Use the level filters (Beginner / Intermediate / Advanced) on the Courses page to find your fit. You can also preview the curriculum of every course before enrolling to see exactly what is covered.',
                ],
                [
                    'q' => 'Can I ask the instructor questions?',
                    'a' => 'Absolutely. Every lesson has a discussion section where you can post questions and get answers from the instructor and fellow learners.',
                ],
            ],
            'Payments & Pricing' => [
                [
                    'q' => 'Which payment methods do you accept?',
                    'a' => 'We accept all major credit and debit cards, PayPal, and bank transfers for enterprise plans. All payments are processed over encrypted connections and we never store your card details.',
                ],
                [
                    'q' => 'Is there a free plan?',
                    'a' => 'Yes. The Free plan gives you access to our entire library of free courses plus community support. Paid courses can be purchased individually or unlocked fully with Pro.',
                ],
                [
                    'q' => 'What is your refund policy?',
                    'a' => 'We offer a no-questions-asked refund within 30 days of purchase if you are not satisfied with a course. Contact our support team with your receipt and we will process it within 2–3 business days.',
                ],
            ],
            'Certificates' => [
                [
                    'q' => 'Do I receive a certificate when I complete a course?',
                    'a' => 'Yes. Once you complete 100% of a course\'s lessons and pass its final exam, your certificate is generated automatically with a unique verification code that anyone can verify online.',
                ],
                [
                    'q' => 'How can employers verify my certificate?',
                    'a' => 'Every certificate carries a unique ID (e.g. LMS-XXXXXXXXXX). Anyone can verify its authenticity instantly by entering the ID on our Certificates page — no account needed.',
                ],
                [
                    'q' => 'Are the certificates accredited?',
                    'a' => 'Our certificates demonstrate completion of rigorous, industry-aligned training. They are widely recognized by employers, though they are not equivalent to government-accredited degrees.',
                ],
            ],
            'Account & Support' => [
                [
                    'q' => 'How do I reset my password?',
                    'a' => 'Click "Log In" then "Forgot password?" and enter your email. We will send you a secure link to choose a new password. The link expires after 60 minutes for security.',
                ],
                [
                    'q' => 'How do I delete my account?',
                    'a' => 'Go to Profile → Settings → Danger Zone → Delete Account. Please note this permanently removes your enrollments, progress, and certificates, and cannot be undone.',
                ],
                [
                    'q' => 'How fast does support respond?',
                    'a' => 'Our team replies to most messages within 24 hours on business days. Priority support for Pro and Enterprise members targets a 4-hour response time.',
                ],
            ],
        ];

        $totalFaqs = collect($faqs)->sum(fn ($group) => count($group));

        return view('pages.faq', compact('faqs', 'totalFaqs'));
    }

    public function pricing()
    {
        $plans = [
            'free' => [
                'name' => 'Free',
                'tagline' => 'Start learning today',
                'monthly' => 0,
                'yearly' => 0,
                'highlighted' => false,
                'cta' => 'Get Started Free',
                'features' => [
                    'Access to all free courses',
                    'Community discussion access',
                    'Basic progress tracking',
                    'Course completion badges',
                    'Learn on any device',
                ],
                'excluded' => ['Certificates of completion', 'Offline downloads', 'Priority support'],
            ],
            'pro' => [
                'name' => 'Pro',
                'tagline' => 'Everything to master new skills',
                'monthly' => 19,
                'yearly' => 190,
                'highlighted' => true,
                'cta' => 'Start 7-Day Free Trial',
                'features' => [
                    'Unlimited access to ALL courses',
                    'Certificates of completion',
                    'Downloadable resources & projects',
                    'Quizzes, exams & graded assignments',
                    'Direct Q&A with instructors',
                    'Offline viewing',
                    'Priority email support',
                ],
                'excluded' => [],
            ],
            'team' => [
                'name' => 'Enterprise',
                'tagline' => 'Upskill your whole organization',
                'monthly' => null,
                'yearly' => null,
                'highlighted' => false,
                'cta' => 'Contact Sales',
                'features' => [
                    'Everything in Pro, for your team',
                    'Centralized admin dashboard',
                    'Custom learning paths',
                    'Team analytics & reporting',
                    'SSO & advanced security',
                    'Dedicated success manager',
                    'Custom course development',
                ],
                'excluded' => [],
            ],
        ];

        $comparison = [
            ['feature' => 'Free course library', 'free' => true, 'pro' => true, 'team' => true],
            ['feature' => 'Full premium course library', 'free' => false, 'pro' => true, 'team' => true],
            ['feature' => 'Certificates of completion', 'free' => false, 'pro' => true, 'team' => true],
            ['feature' => 'Final exams & graded quizzes', 'free' => false, 'pro' => true, 'team' => true],
            ['feature' => 'Downloadable resources', 'free' => false, 'pro' => true, 'team' => true],
            ['feature' => 'Offline viewing', 'free' => false, 'pro' => true, 'team' => true],
            ['feature' => 'Instructor Q&A priority', 'free' => 'Lowest', 'pro' => 'Standard', 'team' => 'Priority'],
            ['feature' => 'Team management dashboard', 'free' => false, 'pro' => false, 'team' => true],
            ['feature' => 'Learning analytics & reports', 'free' => false, 'pro' => false, 'team' => true],
            ['feature' => 'SSO / SAML integration', 'free' => false, 'pro' => false, 'team' => true],
            ['feature' => 'Dedicated success manager', 'free' => false, 'pro' => false, 'team' => true],
        ];

        $stats = [
            'learners' => Enrollment::distinct('user_id')->count('user_id'),
            'courses' => Course::count(),
            'satisfaction' => round((float) Review::avg('rating') * 20),
        ];

        return view('pages.pricing', compact('plans', 'comparison', 'stats'));
    }

    public function certificates()
    {
        $stats = [
            'issued' => Certificate::count(),
            'holders' => Certificate::distinct('user_id')->count('user_id'),
            'courses' => Course::count(),
        ];

        $recent = Certificate::with(['user:id,name', 'course:id,title,slug'])
            ->latest('issued_at')
            ->take(6)
            ->get();

        return view('pages.certificates', compact('stats', 'recent'));
    }

    public function certificateLookup(Request $request)
    {
        $validated = $request->validate([
            'code' => ['required', 'string', 'max:50'],
        ]);

        $code = strtoupper(trim($validated['code']));

        return redirect()->route('certificates.verify', ['code' => $code]);
    }
}
