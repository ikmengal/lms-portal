@extends('layouts.app')

@section('title', 'Blog')

@php
    $catColors = [
        'Learning Tips' => 'bg-primary-50 text-primary-700',
        'Career Advice' => 'bg-accent-50 text-accent-700',
        'Student Stories' => 'bg-secondary-50 text-secondary-700',
        'Design' => 'bg-purple-50 text-purple-700',
        'Industry' => 'bg-blue-50 text-blue-700',
    ];
    $activeCategory = request('category');
@endphp

@section('content')
    {{-- Page Header --}}
    <div class="bg-gradient-to-r from-primary-900 to-primary-800 py-14">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
            <span class="text-sm font-semibold text-accent-400 uppercase tracking-widest">The {{ $site['site_name'] ?? 'LMS Portal' }} Blog</span>
            <h1 class="text-3xl md:text-4xl font-bold text-white mt-2 mb-3">Insights to Accelerate Your Learning</h1>
            <p class="text-lg text-primary-200 max-w-2xl mx-auto">Study strategies, career advice, and success stories from our community of learners and instructors.</p>
        </div>
    </div>

    <section class="py-12 bg-gray-50 min-h-[50vh]">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

            {{-- Search --}}
            <form method="GET" action="{{ route('blog') }}" id="blog-form" class="flex flex-col md:flex-row items-stretch md:items-center gap-4 mb-10">
                <div class="relative flex-1 max-w-xl">
                    <svg class="absolute left-4 top-1/2 -translate-y-1/2 w-5 h-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                    @if($activeCategory)<input type="hidden" name="category" value="{{ $activeCategory }}">@endif
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Search articles..."
                        class="w-full pl-11 pr-4 py-3 border border-gray-200 rounded-xl bg-white text-sm placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-primary-500 transition">
                </div>
                <button type="submit" class="md:hidden px-5 py-3 bg-primary-600 text-white font-semibold rounded-xl">Search</button>

                <div class="flex items-center gap-2 overflow-x-auto pb-1 md:pb-0" x-data>
                    <a href="{{ route('blog') }}" class="whitespace-nowrap px-4 py-2 rounded-full text-xs font-semibold transition border {{ !$activeCategory ? 'bg-primary-600 border-primary-600 text-white' : 'bg-white border-gray-200 text-gray-600 hover:border-primary-300 hover:text-primary-700' }}">
                        All Posts
                    </a>
                    @foreach($categories as $cat)
                        <a href="{{ route('blog', ['category' => $cat->category]) }}"
                           class="whitespace-nowrap px-4 py-2 rounded-full text-xs font-semibold transition border {{ $activeCategory === $cat->category ? 'bg-primary-600 border-primary-600 text-white' : 'bg-white border-gray-200 text-gray-600 hover:border-primary-300 hover:text-primary-700' }}">
                            {{ $cat->category }} ({{ $cat->posts_count }})
                        </a>
                    @endforeach
                </div>
            </form>

            <div class="grid lg:grid-cols-3 gap-10">
                {{-- Main column --}}
                <div class="lg:col-span-2 space-y-8">

                    @if($featured)
                        {{-- Featured Post --}}
                        <a href="{{ route('blog.show', $featured) }}" class="group block bg-white rounded-2xl border border-gray-100 shadow-sm hover:shadow-lg hover:border-primary-200 transition-all duration-300 overflow-hidden">
                            <div class="grid md:grid-cols-2">
                                <div class="relative min-h-[220px] bg-gradient-to-br from-primary-500 via-primary-600 to-primary-800 flex items-center justify-center overflow-hidden">
                                    @if($featured->image_url)
                                        <img src="{{ $featured->image_url }}" alt="{{ $featured->title }}" class="absolute inset-0 w-full h-full object-cover group-hover:scale-105 transition-transform duration-300">
                                    @else
                                        <svg class="w-14 h-14 text-white/40 group-hover:scale-110 transition-transform duration-300" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M12 7.5h1.5m-1.5 3h1.5m-7.5 3h7.5m-7.5 3h7.5m3-9h3.375c.621 0 1.125.504 1.125 1.125V18a2.25 2.25 0 01-2.25 2.25M16.5 7.5V18a2.25 2.25 0 002.25 2.25M16.5 7.5V4.875c0-.621-.504-1.125-1.125-1.125H4.125C3.504 3.75 3 4.254 3 4.875V18a2.25 2.25 0 002.25 2.25h13.5M6 7.5h3v3H6v-3z"/></svg>
                                    @endif
                                    <span class="absolute top-4 left-4 px-3 py-1 bg-accent-500 text-white text-xs font-bold rounded-full uppercase tracking-wide">Featured</span>
                                </div>
                                <div class="p-7 flex flex-col justify-center">
                                    <span class="inline-flex self-start px-3 py-1 rounded-full text-[11px] font-bold uppercase tracking-wide mb-3 {{ $catColors[$featured->category] ?? 'bg-gray-100 text-gray-600' }}">{{ $featured->category }}</span>
                                    <h2 class="text-2xl font-bold text-gray-900 group-hover:text-primary-700 transition leading-snug mb-3">{{ $featured->title }}</h2>
                                    <p class="text-sm text-gray-500 leading-relaxed line-clamp-3">{{ Str::limit($featured->excerpt, 180) }}</p>
                                    <div class="mt-5 flex items-center gap-3 text-xs text-gray-400">
                                        <div class="w-7 h-7 bg-primary-100 text-primary-700 rounded-full grid place-items-center text-[11px] font-bold">{{ $featured->author?->initials ?? 'LP' }}</div>
                                        <span class="font-medium text-gray-600">{{ $featured->author?->name ?? 'LMS Team' }}</span>
                                        <span>•</span>
                                        <span>{{ $featured->published_at->format('M d, Y') }}</span>
                                        <span>•</span>
                                        <span>{{ $featured->reading_time }} min read</span>
                                    </div>
                                </div>
                            </div>
                        </a>
                    @endif

                    @if($posts->isNotEmpty())
                        <div class="grid sm:grid-cols-2 gap-6">
                            @foreach($posts as $post)
                                <article class="group flex flex-col bg-white rounded-2xl border border-gray-100 shadow-sm hover:shadow-lg hover:border-primary-200 transition-all duration-300 overflow-hidden">
                                    <a href="{{ route('blog.show', $post) }}" class="relative aspect-[16/9] bg-gradient-to-br from-primary-400 via-primary-600 to-primary-800 grid place-items-center overflow-hidden">
                                        @if($post->image_url)
                                            <img src="{{ $post->image_url }}" alt="{{ $post->title }}" class="absolute inset-0 w-full h-full object-cover group-hover:scale-105 transition-transform duration-300">
                                        @else
                                            <svg class="w-10 h-10 text-white/40 group-hover:scale-110 transition-transform duration-300" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M12 7.5h1.5m-1.5 3h1.5m-7.5 3h7.5m-7.5 3h7.5m3-9h3.375c.621 0 1.125.504 1.125 1.125V18a2.25 2.25 0 01-2.25 2.25M16.5 7.5V18a2.25 2.25 0 002.25 2.25M16.5 7.5V4.875c0-.621-.504-1.125-1.125-1.125H4.125C3.504 3.75 3 4.254 3 4.875V18a2.25 2.25 0 002.25 2.25h13.5M6 7.5h3v3H6v-3z"/></svg>
                                        @endif
                                        <span class="absolute top-3 left-3 px-2.5 py-1 bg-white/90 backdrop-blur text-[11px] font-bold rounded-md text-gray-700 uppercase tracking-wide">{{ $post->category }}</span>
                                    </a>
                                    <div class="p-5 flex-1 flex flex-col">
                                        <h3 class="font-bold text-gray-900 group-hover:text-primary-700 transition leading-snug mb-2">
                                            <a href="{{ route('blog.show', $post) }}">{{ $post->title }}</a>
                                        </h3>
                                        <p class="text-sm text-gray-500 leading-relaxed line-clamp-2 flex-1">{{ $post->excerpt }}</p>
                                        <div class="mt-4 pt-4 border-t border-gray-50 flex items-center gap-2 text-xs text-gray-400">
                                            <div class="w-6 h-6 bg-primary-100 text-primary-700 rounded-full grid place-items-center text-[10px] font-bold shrink-0">{{ $post->author?->initials ?? 'LP' }}</div>
                                            <span class="font-medium text-gray-600 truncate">{{ $post->author?->name ?? 'LMS Team' }}</span>
                                            <span>•</span>
                                            <span class="shrink-0">{{ $post->published_at->format('M d') }}</span>
                                            <span class="ml-auto shrink-0">{{ $post->reading_time }} min</span>
                                        </div>
                                    </div>
                                </article>
                            @endforeach
                        </div>

                        @if($posts->hasPages())
                            <div>{{ $posts->links() }}</div>
                        @endif
                    @else
                        <div class="py-20 text-center bg-white rounded-2xl border border-gray-100">
                            <svg class="w-16 h-16 mx-auto text-gray-200 mb-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607z"/></svg>
                            <h3 class="text-lg font-semibold text-gray-900 mb-1">No articles found</h3>
                            <p class="text-sm text-gray-500 mb-6">Try a different search or category.</p>
                            <a href="{{ route('blog') }}" class="inline-flex items-center px-5 py-2.5 bg-primary-600 hover:bg-primary-700 text-white text-sm font-semibold rounded-lg transition">View All Articles</a>
                        </div>
                    @endif
                </div>

                {{-- Sidebar --}}
                <aside class="space-y-8">
                    {{-- Popular posts --}}
                    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6">
                        <h3 class="font-bold text-gray-900 mb-5 flex items-center gap-2">
                            <svg class="w-5 h-5 text-accent-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M15.362 5.214A8.252 8.252 0 0112 21 8.25 8.25 0 016.038 7.048 8.287 8.287 0 009 9.6a8.983 8.983 0 013.361-6.867 8.21 8.21 0 003 2.48z"/><path stroke-linecap="round" stroke-linejoin="round" d="M12 18a3.75 3.75 0 00.495-7.467 5.99 5.99 0 00-1.925 3.546 5.974 5.974 0 01-2.133-1A3.75 3.75 0 0012 18z"/></svg>
                            Trending Articles
                        </h3>
                        <div class="space-y-4">
                            @foreach($popular as $i => $post)
                                <a href="{{ route('blog.show', $post) }}" class="group flex gap-4">
                                    <span class="text-2xl font-bold text-gray-200 group-hover:text-primary-300 transition w-7 shrink-0">{{ str_pad($i + 1, 2, '0', STR_PAD_LEFT) }}</span>
                                    <div class="min-w-0">
                                        <h4 class="text-sm font-semibold text-gray-800 group-hover:text-primary-700 transition leading-snug line-clamp-2">{{ $post->title }}</h4>
                                        <p class="text-xs text-gray-400 mt-1">{{ number_format($post->views) }} views • {{ $post->reading_time }} min read</p>
                                    </div>
                                </a>
                            @endforeach
                        </div>
                    </div>

                    {{-- Newsletter CTA --}}
                    <div class="bg-gradient-to-br from-primary-600 to-primary-800 rounded-2xl p-7 text-center">
                        <div class="w-12 h-12 mx-auto bg-white/15 rounded-xl grid place-items-center mb-4">
                            <svg class="w-6 h-6 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M21.75 6.75v10.5a2.25 2.25 0 01-2.25 2.25h-15a2.25 2.25 0 01-2.25-2.25V6.75m19.5 0A2.25 2.25 0 0019.5 4.5h-15a2.25 2.25 0 00-2.25 2.25m19.5 0v.243a2.25 2.25 0 01-1.07 1.916l-7.5 4.615a2.25 2.25 0 01-2.36 0L3.32 8.91a2.25 2.25 0 01-1.07-1.916V6.75"/></svg>
                        </div>
                        <h3 class="text-lg font-bold text-white mb-2">Never Miss an Article</h3>
                        <p class="text-sm text-primary-200 mb-5">Get learning tips and career advice in your inbox every week.</p>
                        <a href="{{ route('contact') }}" class="inline-flex w-full justify-center items-center px-5 py-3 bg-white text-primary-700 font-bold rounded-xl text-sm transition hover:bg-primary-50">Subscribe via Contact Page</a>
                    </div>

                    {{-- Explore courses --}}
                    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6">
                        <h3 class="font-bold text-gray-900 mb-4">Ready to Learn?</h3>
                        <p class="text-sm text-gray-500 mb-5">Turn reading into skills with our expert-led courses.</p>
                        <a href="{{ route('courses.index') }}" class="flex items-center justify-between px-4 py-3 bg-primary-50 text-primary-700 rounded-xl text-sm font-semibold hover:bg-primary-100 transition">
                            Browse Courses
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5L21 12m0 0l-7.5 7.5M21 12H3"/></svg>
                        </a>
                    </div>
                </aside>
            </div>
        </div>
    </section>
@endsection
