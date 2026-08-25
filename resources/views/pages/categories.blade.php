@extends('layouts.app')

@php
    $colorMap = [
        'primary' => ['bg' => 'bg-primary-100', 'text' => 'text-primary-600'],
        'accent' => ['bg' => 'bg-accent-100', 'text' => 'text-accent-600'],
        'secondary' => ['bg' => 'bg-secondary-100', 'text' => 'text-secondary-600'],
        'purple' => ['bg' => 'bg-purple-100', 'text' => 'text-purple-600'],
        'blue' => ['bg' => 'bg-blue-100', 'text' => 'text-blue-600'],
        'orange' => ['bg' => 'bg-orange-100', 'text' => 'text-orange-600'],
        'red' => ['bg' => 'bg-red-100', 'text' => 'text-red-600'],
        'green' => ['bg' => 'bg-green-100', 'text' => 'text-green-600'],
        'indigo' => ['bg' => 'bg-indigo-100', 'text' => 'text-indigo-600'],
        'yellow' => ['bg' => 'bg-yellow-100', 'text' => 'text-yellow-600'],
    ];
@endphp

@section('title', 'Course Categories')

@section('content')
    {{-- Page Header --}}
    <div class="bg-gradient-to-r from-primary-900 to-primary-800 py-14">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <h1 class="text-3xl md:text-4xl font-bold text-white mb-2">Browse by Category</h1>
            <p class="text-primary-200">Explore our full library organized by subject — find exactly what you want to learn.</p>
            <div class="mt-4 flex items-center gap-2 text-sm text-primary-300">
                <a href="{{ route('home') }}" class="hover:text-white transition">Home</a>
                <span>/</span>
                <span class="text-white">Categories</span>
            </div>

            {{-- Quick Stats --}}
            <div class="mt-8 grid grid-cols-3 gap-4 max-w-lg">
                <div class="bg-white/10 backdrop-blur rounded-xl px-4 py-3 text-center">
                    <div class="text-2xl font-bold text-white">{{ number_format($totals['categories']) }}</div>
                    <div class="text-xs text-primary-200 uppercase tracking-wide">Categories</div>
                </div>
                <div class="bg-white/10 backdrop-blur rounded-xl px-4 py-3 text-center">
                    <div class="text-2xl font-bold text-white">{{ number_format($totals['courses']) }}</div>
                    <div class="text-xs text-primary-200 uppercase tracking-wide">Courses</div>
                </div>
                <div class="bg-white/10 backdrop-blur rounded-xl px-4 py-3 text-center">
                    <div class="text-2xl font-bold text-white">{{ number_format($totals['enrollments']) }}</div>
                    <div class="text-xs text-primary-200 uppercase tracking-wide">Enrollments</div>
                </div>
            </div>
        </div>
    </div>

    {{-- Toolbar --}}
    <section class="py-12 bg-gray-50 min-h-[50vh]">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <form method="GET" action="{{ route('categories') }}" id="cat-form" class="flex flex-col sm:flex-row items-stretch sm:items-center justify-between gap-4 mb-8">
                <div class="relative flex-1 max-w-md">
                    <svg class="absolute left-4 top-1/2 -translate-y-1/2 w-5 h-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                    <input type="text" name="search" value="{{ $search }}" placeholder="Search categories..."
                        class="w-full pl-11 pr-4 py-3 border border-gray-200 rounded-xl bg-white text-sm placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-primary-500 transition">
                </div>
                <div class="flex items-center gap-3">
                    <label class="text-sm text-gray-500 hidden sm:block">Sort:</label>
                    <select name="sort" onchange="document.getElementById('cat-form').submit()"
                        class="px-3 py-2.5 border border-gray-200 rounded-xl bg-white text-sm text-gray-700 focus:ring-2 focus:ring-primary-500 focus:border-primary-500 transition">
                        <option value="popular" {{ $sort === 'popular' ? 'selected' : '' }}>Most Popular</option>
                        <option value="a_z" {{ $sort === 'a_z' ? 'selected' : '' }}>A – Z</option>
                        <option value="newest" {{ $sort === 'newest' ? 'selected' : '' }}>Newest</option>
                    </select>
                    <button type="submit" class="sm:hidden px-4 py-2.5 bg-primary-600 text-white text-sm font-semibold rounded-xl">Go</button>
                </div>
            </form>

            @if($categories->isNotEmpty())
                <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 xl:grid-cols-4 gap-6">
                    @foreach($categories as $category)
                        @php
                            $c = $colorMap[$category->color] ?? $colorMap['primary'];
                            $abbr = collect(explode(' ', $category->name))
                                ->map(fn ($w) => mb_substr($w, 0, 1))
                                ->take(2)
                                ->implode('');
                        @endphp
                        <a href="{{ route('courses.index', ['category' => $category->slug]) }}"
                           class="group relative block p-6 bg-white rounded-2xl border border-gray-100 shadow-sm hover:shadow-lg hover:border-primary-200 hover:-translate-y-1 transition-all duration-300">
                            <span class="absolute top-4 right-4 px-2.5 py-1 {{ $c['bg'] }} {{ $c['text'] }} text-xs font-bold rounded-full">
                                {{ $category->courses_count }}
                            </span>
                            <div class="flex items-start gap-4">
                                <div class="w-14 h-14 shrink-0 {{ $c['bg'] }} rounded-xl flex items-center justify-center group-hover:scale-105 transition-transform duration-300">
                                    <span class="text-base font-bold {{ $c['text'] }}">{{ strtoupper($abbr) }}</span>
                                </div>
                                <div class="min-w-0 pt-1">
                                    <h3 class="font-semibold text-gray-900 group-hover:text-primary-700 transition truncate">{{ $category->name }}</h3>
                                    <p class="text-sm text-gray-400 mt-0.5">
                                        {{ $category->courses_count }} course{{ $category->courses_count === 1 ? '' : 's' }}
                                    </p>
                                </div>
                            </div>
                            <div class="mt-4 flex items-center justify-between border-t border-gray-50 pt-4">
                                <span class="text-xs text-gray-400">Explore category</span>
                                <svg class="w-4 h-4 text-gray-300 group-hover:text-primary-600 group-hover:translate-x-1 transition-all" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5L21 12m0 0l-7.5 7.5M21 12H3"/></svg>
                            </div>
                        </a>
                    @endforeach
                </div>
            @else
                <div class="py-20 text-center">
                    <svg class="w-16 h-16 mx-auto text-gray-200 mb-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607z"/></svg>
                    <h3 class="text-lg font-semibold text-gray-900 mb-1">No categories found</h3>
                    <p class="text-sm text-gray-500 mb-6">Try a different search term.</p>
                    <a href="{{ route('categories') }}" class="inline-flex items-center px-5 py-2.5 bg-primary-600 hover:bg-primary-700 text-white text-sm font-semibold rounded-lg transition">View All Categories</a>
                </div>
            @endif

            {{-- CTA --}}
            <div class="mt-14 bg-gradient-to-r from-primary-600 to-primary-800 rounded-2xl px-8 py-10 flex flex-col md:flex-row items-center justify-between gap-6">
                <div>
                    <h3 class="text-xl font-bold text-white mb-1">Can't find what you're looking for?</h3>
                    <p class="text-primary-200 text-sm">Suggest a new category or browse our complete course catalog.</p>
                </div>
                <div class="flex gap-3 shrink-0">
                    <a href="{{ route('contact') }}" class="px-6 py-3 bg-white text-primary-700 hover:bg-primary-50 font-semibold rounded-xl text-sm transition shadow">Suggest a Topic</a>
                    <a href="{{ route('courses.index') }}" class="px-6 py-3 bg-primary-700/40 hover:bg-primary-700/60 text-white font-semibold rounded-xl text-sm transition border border-white/20">All Courses</a>
                </div>
            </div>
        </div>
    </section>
@endsection
