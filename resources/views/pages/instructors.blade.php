@extends('layouts.app')

@section('title', 'Our Instructors')

@section('content')
    {{-- Page Header --}}
    <div class="relative overflow-hidden bg-gradient-to-br from-primary-950 via-primary-900 to-primary-800 py-14 md:py-20">
        {{-- Decorative layers --}}
        <div class="pointer-events-none absolute inset-0 select-none" aria-hidden="true">
            <div class="absolute -top-28 -right-24 w-[26rem] h-[26rem] rounded-full bg-primary-500/25 blur-3xl"></div>
            <div class="absolute -bottom-36 -left-20 w-96 h-96 rounded-full bg-accent-500/15 blur-3xl"></div>
            <div class="absolute inset-0 opacity-[0.06]" style="background-image: radial-gradient(circle at 1px 1px, white 1px, transparent 0); background-size: 26px 26px;"></div>
        </div>

        <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            {{-- Breadcrumb --}}
            <nav class="flex items-center gap-2 text-sm text-primary-300 mb-6">
                <a href="{{ route('home') }}" class="hover:text-white transition">Home</a>
                <span aria-hidden="true">/</span>
                <span class="text-white font-medium">Instructors</span>
            </nav>

            <div class="max-w-2xl">
                <h1 class="text-3xl md:text-5xl font-bold tracking-tight text-white mb-4">Meet Our Instructors</h1>
                <p class="text-base md:text-lg text-primary-200 leading-relaxed">Learn from industry experts who've shipped real products, led real teams, and taught thousands of students worldwide.</p>
            </div>

            {{-- Stats --}}
            <dl class="mt-10 grid grid-cols-2 md:flex md:items-stretch gap-y-6 gap-x-10 max-w-4xl">
                <div class="flex items-center gap-3.5">
                    <div class="w-11 h-11 shrink-0 rounded-xl bg-white/10 backdrop-blur grid place-items-center">
                        <svg class="w-6 h-6 text-accent-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19.128a9.38 9.38 0 002.625.372 9.337 9.337 0 004.121-.952 4.125 4.125 0 00-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 018.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0111.964-3.07M12 6.375a3.375 3.375 0 11-6.75 0 3.375 3.375 0 016.75 0z"/></svg>
                    </div>
                    <div>
                        <dd class="text-2xl font-bold text-white leading-none">{{ number_format($totals['instructors']) }}+</dd>
                        <dt class="text-[11px] font-semibold uppercase tracking-wider text-primary-300 mt-1.5">Instructors</dt>
                    </div>
                </div>
                <div class="flex items-center gap-3.5 md:border-l md:border-white/10 md:pl-10">
                    <div class="w-11 h-11 shrink-0 rounded-xl bg-white/10 backdrop-blur grid place-items-center">
                        <svg class="w-6 h-6 text-accent-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6.042A8.967 8.967 0 006 3.75c-1.052 0-2.062.18-3 .512v14.25A8.987 8.987 0 016 18c2.305 0 4.408.867 6 2.292m0-14.25a8.966 8.966 0 016-2.292c1.052 0 2.062.18 3 .512v14.25A8.987 8.987 0 0018 18a8.967 8.967 0 00-6 2.292m0-14.25v14.25"/></svg>
                    </div>
                    <div>
                        <dd class="text-2xl font-bold text-white leading-none">{{ number_format($totals['courses']) }}</dd>
                        <dt class="text-[11px] font-semibold uppercase tracking-wider text-primary-300 mt-1.5">Courses</dt>
                    </div>
                </div>
                <div class="flex items-center gap-3.5 md:border-l md:border-white/10 md:pl-10">
                    <div class="w-11 h-11 shrink-0 rounded-xl bg-white/10 backdrop-blur grid place-items-center">
                        <svg class="w-6 h-6 text-accent-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M4.26 10.147a60.438 60.438 0 00-.491 6.347A48.62 48.62 0 0112 20.904a48.62 48.62 0 018.232-4.41 60.46 60.46 0 00-.491-6.347m-15.482 0a50.636 50.636 0 00-2.658-.813A59.906 59.906 0 0112 3.493a59.903 59.903 0 0110.399 5.84c-.896.248-1.783.52-2.658.814m-15.482 0A50.717 50.717 0 0112 13.489a50.702 50.702 0 017.74-3.342M6.75 15a.75.75 0 100-1.5.75.75 0 000 1.5zm0 0v-3.675A55.378 55.378 0 0112 8.443m-7.007 11.55A5.981 5.981 0 006.75 15.75v-1.5"/></svg>
                    </div>
                    <div>
                        <dd class="text-2xl font-bold text-white leading-none">{{ number_format($totals['students']) }}</dd>
                        <dt class="text-[11px] font-semibold uppercase tracking-wider text-primary-300 mt-1.5">Learners</dt>
                    </div>
                </div>
                <div class="flex items-center gap-3.5 md:border-l md:border-white/10 md:pl-10">
                    <div class="w-11 h-11 shrink-0 rounded-xl bg-white/10 backdrop-blur grid place-items-center">
                        <svg class="w-6 h-6 text-accent-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M12 21a9.004 9.004 0 008.716-6.747M12 21a9.004 9.004 0 01-8.716-6.747M12 21c2.485 0 4.5-4.03 4.5-9S14.485 3 12 3m0 18c-2.485 0-4.5-4.03-4.5-9S9.515 3 12 3m0 0a8.997 8.997 0 017.843 4.582M12 3a8.997 8.997 0 00-7.843 4.582m15.686 0A11.953 11.953 0 0112 10.5c-2.998 0-5.74-1.1-7.843-2.918m15.686 0A8.959 8.959 0 0121 12c0 .778-.099 1.533-.284 2.253m0 0A17.919 17.919 0 0112 16.5c-3.162 0-6.133-.815-8.716-2.247m0 0A9.015 9.015 0 013 12c0-1.605.42-3.113 1.157-4.418"/></svg>
                    </div>
                    <div>
                        <dd class="text-2xl font-bold text-white leading-none">{{ $totals['countries'] }}+</dd>
                        <dt class="text-[11px] font-semibold uppercase tracking-wider text-primary-300 mt-1.5">Countries</dt>
                    </div>
                </div>
            </dl>
        </div>
    </div>

    <section class="py-12 bg-gray-50 min-h-[50vh]">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

            @if(!$topInstructors->isEmpty() && !request()->filled('search') && !request()->filled('page'))
                {{-- Top Rated --}}
                <div class="mb-12">
                    <div class="flex items-center gap-3 mb-6">
                        <svg class="w-6 h-6 text-accent-500" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                        <h2 class="text-xl font-bold text-gray-900">Most Popular This Month</h2>
                    </div>
                    <div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-6">
                        @foreach($topInstructors as $instructor)
                            <x-instructor-card :instructor="$instructor" compact />
                        @endforeach
                    </div>
                </div>
            @endif

            {{-- Toolbar --}}
            <form method="GET" action="{{ route('instructors') }}" id="inst-form" class="flex flex-col sm:flex-row items-stretch sm:items-center justify-between gap-4 mb-8">
                <h2 class="text-xl font-bold text-gray-900 shrink-0">All Instructors
                    <span class="text-sm font-medium text-gray-400 ml-1">({{ $instructors->total() }})</span>
                </h2>
                <div class="flex flex-col sm:flex-row gap-3 flex-1 sm:max-w-xl">
                    <div class="relative flex-1">
                        <svg class="absolute left-4 top-1/2 -translate-y-1/2 w-5 h-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                        <input type="text" name="search" value="{{ $search }}" placeholder="Search by name or expertise..."
                            class="w-full pl-11 pr-4 py-2.5 border border-gray-200 rounded-xl bg-white text-sm placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-primary-500 transition">
                    </div>
                    <select name="sort" onchange="document.getElementById('inst-form').submit()"
                        class="px-3 py-2.5 border border-gray-200 rounded-xl bg-white text-sm text-gray-700 focus:ring-2 focus:ring-primary-500 focus:border-primary-500 transition shrink-0">
                        <option value="popular" {{ request('sort', 'popular') === 'popular' ? 'selected' : '' }}>Most Students</option>
                        <option value="rating" {{ request('sort') === 'rating' ? 'selected' : '' }}>Highest Rated</option>
                        <option value="most_courses" {{ request('sort') === 'most_courses' ? 'selected' : '' }}>Most Courses</option>
                        <option value="newest" {{ request('sort') === 'newest' ? 'selected' : '' }}>Newest</option>
                        <option value="a_z" {{ request('sort') === 'a_z' ? 'selected' : '' }}>A – Z</option>
                    </select>
                    <button type="submit" class="sm:hidden px-4 py-2.5 bg-primary-600 text-white text-sm font-semibold rounded-xl">Search</button>
                </div>
            </form>

            @if($instructors->isNotEmpty())
                <div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-6">
                    @foreach($instructors as $instructor)
                        <x-instructor-card :instructor="$instructor" />
                    @endforeach
                </div>

                @if($instructors->hasPages())
                    <div class="mt-12">{{ $instructors->links() }}</div>
                @endif
            @else
                <div class="py-20 text-center bg-white rounded-2xl border border-gray-100">
                    <svg class="w-16 h-16 mx-auto text-gray-200 mb-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19.128a9.38 9.38 0 002.625.372 9.337 9.337 0 004.121-.952 4.125 4.125 0 00-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 018.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0111.964-3.07M12 6.375a3.375 3.375 0 11-6.75 0 3.375 3.375 0 016.75 0zm8.25 2.25a2.625 2.625 0 11-5.25 0 2.625 2.625 0 015.25 0z"/></svg>
                    <h3 class="text-lg font-semibold text-gray-900 mb-1">No instructors found</h3>
                    <p class="text-sm text-gray-500 mb-6">Try adjusting your search.</p>
                    <a href="{{ route('instructors') }}" class="inline-flex items-center px-5 py-2.5 bg-primary-600 hover:bg-primary-700 text-white text-sm font-semibold rounded-lg transition">View All Instructors</a>
                </div>
            @endif

            {{-- Become an instructor CTA --}}
            <div class="mt-14 bg-gradient-to-r from-accent-500 to-accent-600 rounded-2xl px-8 py-10 flex flex-col md:flex-row items-center justify-between gap-6">
                <div class="text-center md:text-left">
                    <h3 class="text-xl font-bold text-white mb-1">Want to teach on {{ $site['site_name'] ?? 'LMS Portal' }}?</h3>
                    <p class="text-accent-100 text-sm">Share your expertise with thousands of learners and earn from every enrollment.</p>
                </div>
                <a href="{{ route('contact') }}" class="shrink-0 px-6 py-3 bg-white text-accent-600 hover:bg-accent-50 font-bold rounded-xl text-sm transition shadow-lg">Become an Instructor</a>
            </div>
        </div>
    </section>
@endsection
