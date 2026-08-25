@extends('layouts.app')

@section('title', $instructor->name . ' — Instructor')

@section('content')
    {{-- Profile Banner --}}
    <section class="relative h-44 md:h-56 overflow-hidden bg-gradient-to-br from-primary-950 via-primary-900 to-primary-800">
        @if($instructor->banner_url)
            <img src="{{ $instructor->banner_url }}" alt="" class="absolute inset-0 w-full h-full object-cover">
            <div class="absolute inset-0 bg-gradient-to-b from-primary-950/70 via-primary-900/25 to-primary-950/70"></div>
        @else
            <div class="pointer-events-none absolute inset-0 select-none" aria-hidden="true">
                <div class="absolute -top-24 -right-20 w-80 h-80 rounded-full bg-primary-500/25 blur-3xl"></div>
                <div class="absolute -bottom-28 -left-16 w-72 h-72 rounded-full bg-accent-500/15 blur-3xl"></div>
                <div class="absolute inset-0 opacity-[0.06]" style="background-image: radial-gradient(circle at 1px 1px, white 1px, transparent 0); background-size: 26px 26px;"></div>
            </div>
        @endif

        <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pt-6">
            {{-- Breadcrumb --}}
            <nav class="flex items-center gap-2 text-sm text-primary-200">
                <a href="{{ route('home') }}" class="hover:text-white transition">Home</a>
                <span aria-hidden="true">/</span>
                <a href="{{ route('instructors') }}" class="hover:text-white transition">Instructors</a>
                <span aria-hidden="true">/</span>
                <span class="text-white font-medium truncate">{{ $instructor->name }}</span>
            </nav>
        </div>
    </section>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 -mt-16 pb-16">
        <div class="bg-white rounded-2xl shadow-lg border border-gray-100 p-6 sm:p-10 mb-10">
            <div class="flex flex-col md:flex-row md:items-start gap-6 md:gap-9">
                {{-- Avatar — overlaps banner edge --}}
                <div class="shrink-0 -mt-14 md:-mt-20">
                    @if($instructor->avatar_url)
                        <img src="{{ $instructor->avatar_url }}" alt="{{ $instructor->name }}" class="w-28 h-28 md:w-36 md:h-36 rounded-full object-cover ring-4 ring-white shadow-xl">
                    @else
                        <div class="w-28 h-28 md:w-36 md:h-36 rounded-full bg-gradient-to-br from-primary-500 to-primary-800 ring-4 ring-white shadow-xl grid place-items-center text-white text-3xl font-bold tracking-wide">
                            {{ $instructor->initials }}
                        </div>
                    @endif
                </div>

                <div class="flex-1 min-w-0">
                    <div class="flex flex-wrap items-center gap-x-4 gap-y-2.5">
                        <h1 class="text-3xl md:text-4xl font-bold tracking-tight text-gray-900">{{ $instructor->name }}</h1>
                        <span class="inline-flex items-center gap-1.5 px-3 py-1 bg-secondary-50 text-secondary-700 text-xs font-bold uppercase tracking-wide rounded-full border border-secondary-100">
                            <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M6.267 3.455a3.066 3.066 0 001.745-.723 3.066 3.066 0 013.976 0 3.066 3.066 0 001.745.723 3.066 3.066 0 012.812 2.812c.051.643.304 1.254.723 1.745a3.066 3.066 0 010 3.976 3.066 3.066 0 00-.723 1.745 3.066 3.066 0 01-2.812 2.812 3.066 3.066 0 00-1.745.723 3.066 3.066 0 01-3.976 0 3.066 3.066 0 00-1.745-.723 3.066 3.066 0 01-2.812-2.812 3.066 3.066 0 00-.723-1.745 3.066 3.066 0 010-3.976 3.066 3.066 0 00.723-1.745 3.066 3.066 0 012.812-2.812zm7.44 5.252a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
                            Verified Instructor
                        </span>
                    </div>

                    @if($avgRating > 0)
                        <div class="flex flex-wrap items-center gap-x-3 gap-y-2 mt-3.5">
                            <div class="flex items-center gap-0.5" aria-hidden="true">
                                @for($i = 1; $i <= 5; $i++)
                                    <svg class="w-[18px] h-[18px] {{ $i <= floor($avgRating) ? 'text-accent-400' : 'text-gray-200' }}" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                                @endfor
                            </div>
                            <span class="font-semibold text-gray-900">{{ number_format($avgRating, 1) }} instructor rating</span>
                            <span class="text-gray-400 text-sm">({{ number_format($reviewsCount) }} reviews)</span>
                        </div>
                    @endif

                    @if(!empty($instructor->bio))
                        <p class="text-gray-600 leading-relaxed mt-5 max-w-3xl whitespace-pre-line">{{ $instructor->bio }}</p>
                    @endif

                    <div class="mt-6 flex flex-wrap items-center gap-2.5 text-sm">
                        <span class="inline-flex items-center gap-2 px-3.5 py-1.5 bg-gray-50 border border-gray-100 rounded-full text-gray-600 font-medium">
                            <svg class="w-4 h-4 text-primary-600 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6.042A8.967 8.967 0 006 3.75c-1.052 0-2.062.18-3 .512v14.25A8.987 8.987 0 016 18c2.305 0 4.408.867 6 2.292m0-14.25a8.966 8.966 0 016-2.292c1.052 0 2.062.18 3 .512v14.25A8.987 8.987 0 0018 18a8.967 8.967 0 00-6 2.292m0-14.25v14.25"/></svg>
                            {{ $totalCourses }} Course{{ $totalCourses === 1 ? '' : 's' }}
                        </span>
                        <span class="inline-flex items-center gap-2 px-3.5 py-1.5 bg-gray-50 border border-gray-100 rounded-full text-gray-600 font-medium">
                            <svg class="w-4 h-4 text-primary-600 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19.128a9.38 9.38 0 002.625.372 9.337 9.337 0 004.121-.952 4.125 4.125 0 00-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 018.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0111.964-3.07M12 6.375a3.375 3.375 0 11-6.75 0 3.375 3.375 0 016.75 0z"/></svg>
                            {{ number_format($totalStudents) }} Students
                        </span>
                        <span class="inline-flex items-center gap-2 px-3.5 py-1.5 bg-gray-50 border border-gray-100 rounded-full text-gray-600 font-medium">
                            <svg class="w-4 h-4 text-primary-600 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 012.25-2.25h13.5A2.25 2.25 0 0121 7.5v11.25m-18 0A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75m-18 0v-7.5A2.25 2.25 0 015.25 9h13.5A2.25 2.25 0 0121 11.25v7.5"/></svg>
                            Joined {{ $instructor->created_at->format('M Y') }}
                        </span>
                    </div>
                </div>
            </div>

            {{-- Stats band --}}
            <dl class="grid grid-cols-2 md:grid-cols-4 gap-4 mt-8 pt-8 border-t border-gray-100">
                <div class="rounded-xl border border-gray-100 bg-gray-50/60 px-4 py-4 text-center transition hover:border-primary-200 hover:bg-primary-50/50">
                    <dd class="text-2xl font-bold text-primary-700 leading-none">{{ number_format($totalStudents) }}</dd>
                    <dt class="text-xs font-medium text-gray-500 uppercase tracking-wide mt-2">Total Students</dt>
                </div>
                <div class="rounded-xl border border-gray-100 bg-gray-50/60 px-4 py-4 text-center transition hover:border-accent-200 hover:bg-accent-50/60">
                    <dd class="text-2xl font-bold text-accent-600 leading-none">{{ number_format($avgRating, 1) }}</dd>
                    <dt class="text-xs font-medium text-gray-500 uppercase tracking-wide mt-2">Avg Rating</dt>
                </div>
                <div class="rounded-xl border border-gray-100 bg-gray-50/60 px-4 py-4 text-center transition hover:border-secondary-200 hover:bg-secondary-50/60">
                    <dd class="text-2xl font-bold text-secondary-700 leading-none">{{ number_format($totalCourses) }}</dd>
                    <dt class="text-xs font-medium text-gray-500 uppercase tracking-wide mt-2">Courses</dt>
                </div>
                <div class="rounded-xl border border-gray-100 bg-gray-50/60 px-4 py-4 text-center transition hover:border-purple-200 hover:bg-purple-50/60">
                    <dd class="text-2xl font-bold text-purple-600 leading-none">{{ number_format($reviewsCount) }}</dd>
                    <dt class="text-xs font-medium text-gray-500 uppercase tracking-wide mt-2">Reviews</dt>
                </div>
            </dl>
        </div>

        <div class="grid lg:grid-cols-3 gap-10">
            {{-- Courses --}}
            <div class="lg:col-span-2">
                <form method="GET" id="sort-courses-form" class="flex items-center justify-between mb-6 gap-4 flex-wrap">
                    <h2 class="text-xl font-bold text-gray-900">Courses by {{ strtok($instructor->name, ' ') }}
                        <span class="text-sm font-medium text-gray-400 ml-1">({{ $courses->total() }})</span>
                    </h2>
                    <select name="sort" onchange="document.getElementById('sort-courses-form').submit()"
                        class="px-3 py-2 border border-gray-200 rounded-lg text-sm text-gray-700 bg-white focus:ring-2 focus:ring-primary-500 focus:border-primary-500 transition">
                        <option value="popular" {{ request('sort', 'popular') === 'popular' ? 'selected' : '' }}>Most Popular</option>
                        <option value="rating" {{ request('sort') === 'rating' ? 'selected' : '' }}>Highest Rated</option>
                        <option value="price_low" {{ request('sort') === 'price_low' ? 'selected' : '' }}>Price: Low to High</option>
                        <option value="price_high" {{ request('sort') === 'price_high' ? 'selected' : '' }}>Price: High to Low</option>
                        <option value="a_z" {{ request('sort') === 'a_z' ? 'selected' : '' }}>A – Z</option>
                    </select>
                </form>

                @if($courses->isNotEmpty())
                    <div class="grid sm:grid-cols-2 gap-6">
                        @foreach($courses as $course)
                            <x-course-card
                                :title="$course->title"
                                :category="$course->category ?? 'General'"
                                :instructor="$course->instructor?->name ?? ''"
                                :rating="$course->avg_rating ? round((float) $course->avg_rating, 1) : 0"
                                :reviews="$course->reviews_count"
                                :students="number_format($course->students_count)"
                                :duration="$course->duration_hours . ' hours'"
                                :price="$course->price > 0 ? '$' . number_format($course->price, 2) : 'Free'"
                                :image="$course->thumbnail ? asset('storage/' . $course->thumbnail) : null"
                                :level="$course->level ?? 'Beginner'"
                                :bestseller="$course->students_count >= 10"
                                :slug="route('courses.show', $course)"
                            />
                        @endforeach
                    </div>

                    @if($courses->hasPages())
                        <div class="mt-10">{{ $courses->links() }}</div>
                    @endif
                @else
                    <div class="py-16 text-center bg-white rounded-2xl border border-gray-100">
                        <h3 class="font-semibold text-gray-900 mb-1">No courses published yet</h3>
                        <p class="text-sm text-gray-500">Check back soon — new courses are on the way.</p>
                    </div>
                @endif
            </div>

            {{-- Sidebar --}}
            <aside class="space-y-8">
                {{-- Latest reviews --}}
                @if($latestReviews->isNotEmpty())
                    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6">
                        <h3 class="font-bold text-gray-900 mb-5 flex items-center gap-2">
                            <svg class="w-5 h-5 text-accent-400" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                            What Students Say
                        </h3>
                        <div class="space-y-5">
                            @foreach($latestReviews as $review)
                                <div class="border-b border-gray-50 last:border-0 last:pb-0 pb-4">
                                    <div class="flex items-center gap-2 mb-1.5">
                                        <div class="flex items-center gap-0.5">
                                            @for($i = 1; $i <= 5; $i++)
                                                <svg class="w-3.5 h-3.5 {{ $i <= $review->rating ? 'text-accent-400' : 'text-gray-200' }}" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                                            @endfor
                                        </div>
                                        <span class="text-xs font-medium text-gray-700">{{ $review->user?->name ?? 'Anonymous' }}</span>
                                    </div>
                                    <p class="text-sm text-gray-500 leading-relaxed line-clamp-3">{{ Str::limit($review->comment, 140) }}</p>
                                    @if($review->course)
                                        <a href="{{ route('courses.show', $review->course) }}" class="mt-1 inline-block text-xs font-medium text-primary-600 hover:text-primary-700 transition">{{ Str::limit($review->course->title, 40) }}</a>
                                    @endif
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif

                {{-- CTA --}}
                <div class="bg-gradient-to-br from-primary-600 to-primary-800 rounded-2xl p-8 text-center">
                    <div class="w-14 h-14 mx-auto bg-white/15 rounded-2xl grid place-items-center mb-4">
                        <svg class="w-7 h-7 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6.042A8.967 8.967 0 006 3.75c-1.052 0-2.062.18-3 .512v14.25A8.987 8.987 0 016 18c2.305 0 4.408.867 6 2.292m0-14.25a8.966 8.966 0 016-2.292c1.052 0 2.062.18 3 .512v14.25A8.987 8.987 0 0018 18a8.967 8.967 0 00-6 2.292m0-14.25v14.25"/></svg>
                    </div>
                    <h3 class="text-lg font-bold text-white mb-2">Start Learning Today</h3>
                    <p class="text-sm text-primary-200 mb-5">Join {{ number_format($totalStudents) }} students already learning from {{ strtok($instructor->name, ' ') }}.</p>
                    <a href="{{ route('courses.index') }}" class="inline-flex w-full justify-center items-center px-5 py-3 bg-white text-primary-700 font-bold rounded-xl text-sm transition hover:bg-primary-50">Browse All Courses</a>
                </div>

                {{-- Back link --}}
                <a href="{{ route('instructors') }}" class="flex items-center justify-center gap-2 text-sm font-medium text-gray-500 hover:text-primary-600 transition">
                    <svg class="w-4 h-4 rotate-180" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5L21 12m0 0l-7.5 7.5M21 12H3"/></svg>
                    Back to all instructors
                </a>
            </aside>
        </div>
    </div>
@endsection
