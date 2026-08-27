@extends('layouts.app')

@php
    $activeCategory = request('category');
    $activeLevels = (array) request('levels', []);
    $activePrice = request('price');
    $activeRating = request('rating');
    $activeLanguage = request('language');
    $activeDuration = request('duration');
    $searchTerm = request('search');
    $hasFilters = $activeCategory || $activeLevels || $activePrice || $activeRating || $activeLanguage || $activeDuration || $searchTerm;
@endphp

@section('title', 'All Courses')

@section('content')
    {{-- Page Header --}}
    <div class="bg-gradient-to-r from-primary-900 to-primary-800 py-12">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <h1 class="text-3xl font-bold text-white mb-2">Explore Our Courses</h1>
            <p class="text-primary-200">Find the perfect course from our growing library</p>
            <div class="mt-4 flex items-center gap-2 text-sm text-primary-300">
                <a href="{{ route('home') }}" class="hover:text-white transition">Home</a>
                <span>/</span>
                <span class="text-white">All Courses</span>
            </div>

            {{-- Search Bar --}}
            <form method="GET" action="{{ route('courses.index') }}" class="mt-6 max-w-xl bg-white rounded-xl p-2 shadow-lg flex items-center">
                @foreach(['category' => $activeCategory, 'price' => $activePrice, 'rating' => $activeRating, 'language' => $activeLanguage, 'duration' => $activeDuration] as $hk => $hv)
                    @if($hv)<input type="hidden" name="{{ $hk }}" value="{{ $hv }}">@endif
                @endforeach
                @foreach($activeLevels as $lv)<input type="hidden" name="levels[]" value="{{ $lv }}">@endforeach
                <svg class="w-5 h-5 text-gray-400 ml-3 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                <input type="text" name="search" value="{{ $searchTerm }}" placeholder="Search by course, instructor or category..."
                    class="flex-1 px-4 py-3 text-gray-900 placeholder-gray-400 focus:outline-none text-sm">
                <button type="submit" class="px-6 py-3 bg-primary-600 hover:bg-primary-700 text-white font-semibold rounded-lg transition whitespace-nowrap text-sm">Search</button>
            </form>
        </div>
    </div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12" x-data="{ mobileFilters: false }">

        <div class="flex flex-col lg:flex-row gap-8">

            {{-- Filters Sidebar --}}
            <aside class="lg:w-72 shrink-0" :class="mobileFilters ? 'fixed inset-0 z-50 bg-white p-6 overflow-y-auto lg:static lg:p-0' : 'hidden lg:block'">
                <div class="flex items-center justify-between mb-6 lg:hidden">
                    <h2 class="text-lg font-bold text-gray-900">Filters</h2>
                    <button @click="mobileFilters = false" class="p-2 text-gray-500 hover:text-gray-700">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>

                <form method="GET" action="{{ route('courses.index') }}" id="filters-form" class="space-y-6 bg-gray-50 rounded-xl p-6">
                    <input type="hidden" name="search" value="{{ $searchTerm }}">
                    <input type="hidden" name="sort" value="{{ request('sort') }}">

                    {{-- Category Filter --}}
                    <div>
                        <h3 class="font-semibold text-gray-900 mb-3">Category</h3>
                        <div class="space-y-2 max-h-56 overflow-y-auto pr-1">
                            <label class="flex items-center gap-2 cursor-pointer group">
                                <input type="radio" name="category" value="" {{ !$activeCategory ? 'checked' : '' }} onchange="document.getElementById('filters-form').submit()" class="w-4 h-4 text-primary-600 border-gray-300 focus:ring-primary-500">
                                <span class="text-sm text-gray-600 group-hover:text-gray-900 transition">All Categories</span>
                            </label>
                            @foreach($categories as $cat)
                                <label class="flex items-center justify-between gap-2 cursor-pointer group">
                                    <span class="flex items-center gap-2 min-w-0">
                                        <input type="radio" name="category" value="{{ $cat->slug }}" {{ $activeCategory === $cat->slug ? 'checked' : '' }} onchange="document.getElementById('filters-form').submit()" class="w-4 h-4 text-primary-600 border-gray-300 focus:ring-primary-500 shrink-0">
                                        <span class="text-sm text-gray-600 group-hover:text-gray-900 transition truncate">{{ $cat->name }}</span>
                                    </span>
                                    <span class="text-[11px] text-gray-400">{{ $cat->courses_count }}</span>
                                </label>
                            @endforeach
                        </div>
                    </div>

                    {{-- Level Filter --}}
                    <div>
                        <h3 class="font-semibold text-gray-900 mb-3">Level</h3>
                        <div class="space-y-2">
                            @foreach($levels as $level)
                                <label class="flex items-center gap-2 cursor-pointer group">
                                    <input type="checkbox" name="levels[]" value="{{ $level }}" {{ in_array($level, $activeLevels) ? 'checked' : '' }} onchange="document.getElementById('filters-form').submit()" class="w-4 h-4 text-primary-600 border-gray-300 rounded focus:ring-primary-500">
                                    <span class="text-sm text-gray-600 group-hover:text-gray-900 transition">{{ $level }}</span>
                                </label>
                            @endforeach
                        </div>
                    </div>

                    {{-- Price Filter --}}
                    <div>
                        <h3 class="font-semibold text-gray-900 mb-3">Price</h3>
                        <div class="space-y-2">
                            @foreach(['' => 'All Prices', 'free' => 'Free', 'paid' => 'Paid'] as $val => $label)
                                <label class="flex items-center gap-2 cursor-pointer group">
                                    <input type="radio" name="price" value="{{ $val }}" {{ $activePrice === $val ? 'checked' : '' }} onchange="document.getElementById('filters-form').submit()" class="w-4 h-4 text-primary-600 border-gray-300 focus:ring-primary-500">
                                    <span class="text-sm text-gray-600 group-hover:text-gray-900 transition">{{ $label }}</span>
                                </label>
                            @endforeach
                        </div>
                    </div>

                    {{-- Rating Filter --}}
                    <div>
                        <h3 class="font-semibold text-gray-900 mb-3">Rating</h3>
                        <div class="space-y-2">
                            <label class="flex items-center gap-2 cursor-pointer group">
                                <input type="radio" name="rating" value="" {{ !$activeRating ? 'checked' : '' }} onchange="document.getElementById('filters-form').submit()" class="w-4 h-4 text-primary-600 border-gray-300 focus:ring-primary-500">
                                <span class="text-sm text-gray-600 group-hover:text-gray-900 transition">Any rating</span>
                            </label>
                            @foreach([4.5, 4.0, 3.5, 3.0] as $r)
                                <label class="flex items-center gap-2 cursor-pointer group">
                                    <input type="radio" name="rating" value="{{ $r }}" {{ $activeRating == $r ? 'checked' : '' }} onchange="document.getElementById('filters-form').submit()" class="w-4 h-4 text-primary-600 border-gray-300 focus:ring-primary-500">
                                    <span class="flex items-center gap-1">
                                        @for($i = 1; $i <= 5; $i++)
                                            <svg class="w-3.5 h-3.5 {{ $i <= floor($r) ? 'text-accent-400' : 'text-gray-200' }}" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                                        @endfor
                                        <span class="text-xs text-gray-500">& up</span>
                                    </span>
                                </label>
                            @endforeach
                        </div>
                    </div>

                    {{-- Language Filter --}}
                    <div>
                        <h3 class="font-semibold text-gray-900 mb-3">Language</h3>
                        <select name="language" onchange="document.getElementById('filters-form').submit()"
                            class="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm text-gray-700 bg-white focus:ring-2 focus:ring-primary-500 focus:border-primary-500 transition">
                            <option value="">All Languages</option>
                            @foreach($languages as $lang)
                                <option value="{{ $lang }}" {{ $activeLanguage === $lang ? 'selected' : '' }}>{{ $lang }}</option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Duration Filter --}}
                    <div>
                        <h3 class="font-semibold text-gray-900 mb-3">Duration</h3>
                        <div class="space-y-2">
                            @foreach(['' => 'Any duration', 'short' => 'Short (< 10 hours)', 'medium' => 'Medium (10–30 hours)', 'long' => 'Long (> 30 hours)'] as $val => $label)
                                <label class="flex items-center gap-2 cursor-pointer group">
                                    <input type="radio" name="duration" value="{{ $val }}" {{ $activeDuration === $val ? 'checked' : '' }} onchange="document.getElementById('filters-form').submit()" class="w-4 h-4 text-primary-600 border-gray-300 focus:ring-primary-500">
                                    <span class="text-sm text-gray-600 group-hover:text-gray-900 transition">{{ $label }}</span>
                                </label>
                            @endforeach
                        </div>
                    </div>

                    <a href="{{ route('courses.index') }}" class="block w-full py-2.5 text-center text-gray-500 hover:text-gray-700 font-medium text-sm transition">Clear All Filters</a>
                </form>
            </aside>

            {{-- Course Grid --}}
            <div class="flex-1 min-w-0">
                {{-- Top Bar --}}
                <form method="GET" action="{{ route('courses.index') }}" id="sort-form" class="flex items-center justify-between mb-6 gap-4 flex-wrap">
                    @foreach(['category' => $activeCategory, 'price' => $activePrice, 'rating' => $activeRating, 'language' => $activeLanguage, 'duration' => $activeDuration] as $hk => $hv)
                        @if($hv)<input type="hidden" name="{{ $hk }}" value="{{ $hv }}">@endif
                    @endforeach
                    @foreach($activeLevels as $lv)<input type="hidden" name="levels[]" value="{{ $lv }}">@endforeach
                    <input type="hidden" name="search" value="{{ $searchTerm }}">

                    <div class="flex items-center gap-4">
                        <button type="button" @click="mobileFilters = true" class="lg:hidden px-4 py-2 border border-gray-300 rounded-lg text-sm font-medium text-gray-700 hover:bg-gray-50 transition">
                            <svg class="w-4 h-4 inline mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M10.5 6h9.75M10.5 6a1.5 1.5 0 11-3 0m3 0a1.5 1.5 0 10-3 0M3.75 6H7.5m3 12h9.75m-9.75 0a1.5 1.5 0 01-3 0m3 0a1.5 1.5 0 00-3 0m-3.75 0H7.5m9-6h3.75m-3.75 0a1.5 1.5 0 01-3 0m3 0a1.5 1.5 0 00-3 0m-9.75 0h9.75"/></svg>
                            Filters
                        </button>
                        <p class="text-sm text-gray-500"><span class="font-semibold text-gray-900">{{ $courses->total() }}</span> course{{ $courses->total() === 1 ? '' : 's' }} found</p>
                    </div>
                    <div class="flex items-center gap-3">
                        <label class="text-sm text-gray-500 hidden sm:block">Sort by:</label>
                        <select name="sort" onchange="document.getElementById('sort-form').submit()"
                            class="px-3 h-[42px] border border-gray-200 rounded-lg text-sm text-gray-700 bg-white focus:ring-2 focus:ring-primary-500 focus:border-primary-500 transition">
                            <option value="" {{ !request('sort') ? 'selected' : '' }}>Most Popular</option>
                            <option value="newest" {{ request('sort') === 'newest' ? 'selected' : '' }}>Newest</option>
                            <option value="rating" {{ request('sort') === 'rating' ? 'selected' : '' }}>Highest Rated</option>
                            <option value="price_low" {{ request('sort') === 'price_low' ? 'selected' : '' }}>Price: Low to High</option>
                            <option value="price_high" {{ request('sort') === 'price_high' ? 'selected' : '' }}>Price: High to Low</option>
                            <option value="a_z" {{ request('sort') === 'a_z' ? 'selected' : '' }}>A – Z</option>
                        </select>
                    </div>
                </form>

                {{-- Active filter chips --}}
                @if($hasFilters)
                    <div class="flex flex-wrap items-center gap-2 mb-6">
                        @if($searchTerm)
                            <span class="inline-flex items-center gap-1 px-3 py-1 bg-primary-50 text-primary-700 text-xs font-medium rounded-full">"{{ Str::limit($searchTerm, 25) }}"</span>
                        @endif
                        @foreach($categories->where('slug', $activeCategory) as $cat)
                            <span class="inline-flex items-center gap-1 px-3 py-1 bg-primary-50 text-primary-700 text-xs font-medium rounded-full">{{ $cat->name }}</span>
                        @endforeach
                        @foreach($activeLevels as $lv)
                            <span class="inline-flex items-center gap-1 px-3 py-1 bg-primary-50 text-primary-700 text-xs font-medium rounded-full">{{ $lv }}</span>
                        @endforeach
                        @if($activePrice === 'free')<span class="inline-flex items-center gap-1 px-3 py-1 bg-secondary-50 text-secondary-700 text-xs font-medium rounded-full">Free</span>@endif
                        @if($activePrice === 'paid')<span class="inline-flex items-center gap-1 px-3 py-1 bg-accent-50 text-accent-700 text-xs font-medium rounded-full">Paid</span>@endif
                        @if($activeRating)<span class="inline-flex items-center gap-1 px-3 py-1 bg-accent-50 text-accent-700 text-xs font-medium rounded-full">{{ $activeRating }}★ & up</span>@endif
                        @if($activeLanguage)<span class="inline-flex items-center gap-1 px-3 py-1 bg-primary-50 text-primary-700 text-xs font-medium rounded-full">{{ $activeLanguage }}</span>@endif
                        @if($activeDuration)<span class="inline-flex items-center gap-1 px-3 py-1 bg-primary-50 text-primary-700 text-xs font-medium rounded-full">{{ ['short' => '< 10h', 'medium' => '10–30h', 'long' => '> 30h'][$activeDuration] ?? '' }}</span>@endif
                        <a href="{{ route('courses.index') }}" class="inline-flex items-center gap-1 px-3 py-1 text-xs font-medium text-red-500 hover:text-red-600 transition">Clear all ×</a>
                    </div>
                @endif

                {{-- Course Grid --}}
                @if($courses->isNotEmpty())
                    <div class="grid sm:grid-cols-2 xl:grid-cols-3 gap-6">
                        @foreach($courses as $course)
                            <x-course-card
                                :title="$course->title"
                                :category="$course->category ?? 'General'"
                                :instructor="$course->instructor?->name ?? 'Instructor'"
                                :rating="$course->avg_rating ? round((float) $course->avg_rating, 1) : 0"
                                :reviews="$course->reviews_count"
                                :students="number_format($course->students_count)"
                                :duration="$course->duration_hours . ' hours'"
                                :price="$course->price > 0 ? '$' . number_format($course->price, 2) : 'Free'"
                                :image="$course->thumbnail ? asset('assets/upload/' . $course->thumbnail) : null"
                                :level="$course->level ?? 'Beginner'"
                                :subtitle="$course->subtitle"
                                :languages="array_merge([$course->language_code], array_keys($course->translations ?? []))"
                                :bestseller="$course->students_count >= 10"
                                :comingSoon="$course->unlocks_at && $course->unlocks_at->isFuture()"
                                :comingSoonDate="$course->unlocks_at?->isFuture() ? $course->unlocks_at->format('M j, Y') : null"
                                :slug="route('courses.show', $course)"
                            />
                        @endforeach
                    </div>

                    {{-- Pagination --}}
                    @if($courses->hasPages())
                        <div class="mt-12">
                            {{ $courses->links() }}
                        </div>
                    @endif
                @else
                    <div class="py-20 text-center">
                        <svg class="w-16 h-16 mx-auto text-gray-200 mb-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607z"/></svg>
                        <h3 class="text-lg font-semibold text-gray-900 mb-1">No courses found</h3>
                        <p class="text-sm text-gray-500 mb-6">Try adjusting your search or removing some filters.</p>
                        <a href="{{ route('courses.index') }}" class="inline-flex items-center px-5 py-2.5 bg-primary-600 hover:bg-primary-700 text-white text-sm font-semibold rounded-lg transition">View All Courses</a>
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection
