@extends('layouts.app')

@section('title', $post->title)

@section('content')
    <article>
        {{-- Header --}}
        <div class="bg-gradient-to-r from-primary-900 to-primary-800 pt-14 pb-32">
            <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
                <nav class="flex items-center gap-2 text-sm text-primary-300 mb-8 flex-wrap">
                    <a href="{{ route('home') }}" class="hover:text-white transition">Home</a>
                    <span>/</span>
                    <a href="{{ route('blog') }}" class="hover:text-white transition">Blog</a>
                    <span>/</span>
                    <span class="text-white truncate">{{ Str::limit($post->title, 50) }}</span>
                </nav>

                <span class="inline-block px-3 py-1 bg-accent-500/20 text-accent-300 border border-accent-400/30 text-xs font-bold uppercase tracking-wide rounded-full mb-5">{{ $post->category }}</span>
                <h1 class="text-3xl md:text-4xl font-bold text-white leading-tight mb-6">{{ $post->title }}</h1>

                <div class="flex flex-wrap items-center gap-x-6 gap-y-3 text-sm text-primary-200">
                    <div class="flex items-center gap-2.5">
                        @if($post->author?->avatar_url)
                            <img src="{{ $post->author->avatar_url }}" alt="" class="w-9 h-9 rounded-full object-cover ring-2 ring-white/20">
                        @else
                            <div class="w-9 h-9 bg-white/15 rounded-full grid place-items-center text-white text-xs font-bold">{{ $post->author?->initials ?? 'LP' }}</div>
                        @endif
                        <div>
                            <p class="font-semibold text-white leading-tight">{{ $post->author?->name ?? 'LMS Portal Team' }}</p>
                            @if($post->author?->hasRole('instructor'))
                                <p class="text-xs text-primary-300">Instructor</p>
                            @endif
                        </div>
                    </div>
                    <span class="flex items-center gap-1.5">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 012.25-2.25h13.5A2.25 2.25 0 0121 7.5v11.25m-18 0A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75m-18 0v-7.5A2.25 2.25 0 015.25 9h13.5A2.25 2.25 0 0121 11.25v7.5"/></svg>
                        {{ $post->published_at->format('M d, Y') }}
                    </span>
                    <span class="flex items-center gap-1.5">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        {{ $post->reading_time }} min read
                    </span>
                    <span class="flex items-center gap-1.5">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178z"/><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                        {{ number_format($post->views) }} views
                    </span>
                </div>
            </div>
        </div>

        {{-- Body --}}
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 -mt-24 pb-16">
            {{-- Cover image / gradient banner --}}
            <div class="rounded-2xl overflow-hidden shadow-xl border border-gray-100 aspect-[21/9] bg-gradient-to-br from-primary-400 via-primary-600 to-primary-800 grid place-items-center relative">
                @if($post->image_url)
                    <img src="{{ $post->image_url }}" alt="{{ $post->title }}" class="absolute inset-0 w-full h-full object-cover">
                @else
                    <svg class="w-16 h-16 text-white/30" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M12 7.5h1.5m-1.5 3h1.5m-7.5 3h7.5m-7.5 3h7.5m3-9h3.375c.621 0 1.125.504 1.125 1.125V18a2.25 2.25 0 01-2.25 2.25M16.5 7.5V18a2.25 2.25 0 002.25 2.25M16.5 7.5V4.875c0-.621-.504-1.125-1.125-1.125H4.125C3.504 3.75 3 4.254 3 4.875V18a2.25 2.25 0 002.25 2.25h13.5M6 7.5h3v3H6v-3z"/></svg>
                @endif
            </div>

            <div class="bg-white rounded-b-2xl shadow-sm border border-t-0 border-gray-100 px-6 sm:px-10 py-10">
                {{-- Excerpt lead --}}
                <p class="text-lg text-gray-600 leading-relaxed font-medium border-l-4 border-accent-400 pl-5 mb-8">{{ $post->excerpt }}</p>

                {{-- Content (## -> h2) --}}
                <div class="space-y-4">
                    @foreach(preg_split('/\n\n+/', trim($post->content)) as $block)
                        @if(str_starts_with($block, '## '))
                            <h2 class="text-xl font-bold text-gray-900 !mt-10 mb-3 flex items-start gap-2.5">
                                <span class="w-1.5 h-6 bg-accent-400 rounded-full shrink-0 mt-0.5"></span>
                                {{ substr($block, 3) }}
                            </h2>
                        @elseif(str_starts_with($block, '- '))
                            <ul class="space-y-2 my-3">
                                @foreach(preg_split('/\n/', $block) as $item)
                                    <li class="flex items-start gap-2.5 text-gray-600 leading-relaxed">
                                        <svg class="w-4 h-4 text-secondary-500 mt-1 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5"/></svg>
                                        <span>{{ preg_replace('/\*\*(.*?)\*\*/', '$1', preg_replace('/^- /', '', $item)) }}</span>
                                    </li>
                                @endforeach
                            </ul>
                        @else
                            <p class="text-gray-600 leading-relaxed">{!! preg_replace('/\*\*(.*?)\*\*/', '<strong class="text-gray-900 font-semibold">$1</strong>', e($block)) !!}</p>
                        @endif
                    @endforeach
                </div>

                {{-- Share + tags --}}
                <div class="mt-12 pt-8 border-t border-gray-100 flex flex-col sm:flex-row items-center justify-between gap-4">
                    <span class="text-sm font-medium text-gray-500">Found this helpful? Share it:</span>
                    <div class="flex items-center gap-2">
                        <a href="https://twitter.com/intent/tweet?url={{ urlencode(url()->current()) }}&text={{ urlencode($post->title) }}" target="_blank" rel="noopener" aria-label="Share on X" class="w-9 h-9 bg-gray-100 hover:bg-primary-600 hover:text-white text-gray-500 rounded-lg grid place-items-center transition">
                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M18.244 2.25h3.308l-7.227 8.26 8.502 11.24H16.17l-5.214-6.817L4.99 21.75H1.68l7.73-8.835L1.254 2.25H8.08l4.713 6.231zm-1.161 17.52h1.833L7.084 4.126H5.117z"/></svg>
                        </a>
                        <a href="https://www.facebook.com/sharer/sharer.php?u={{ urlencode(url()->current()) }}" target="_blank" rel="noopener" aria-label="Share on Facebook" class="w-9 h-9 bg-gray-100 hover:bg-primary-600 hover:text-white text-gray-500 rounded-lg grid place-items-center transition">
                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M24 4.557c-.883.392-1.832.656-2.828.775 1.017-.609 1.798-1.574 2.165-2.724-.951.564-2.005.974-3.127 1.195-.897-.957-2.178-1.555-3.594-1.555-3.179 0-5.515 2.966-4.797 6.045-4.091-.205-7.719-2.165-10.148-5.144-1.29 2.213-.669 5.108 1.523 6.574-.806-.026-1.566-.247-2.229-.616-.054 2.281 1.581 4.415 3.949 4.89-.693.188-1.452.232-2.224.084.626 1.956 2.444 3.379 4.6 3.419-2.07 1.623-4.678 2.348-7.29 2.04 2.179 1.397 4.768 2.212 7.548 2.212 9.142 0 14.307-7.721 13.995-14.646.962-.695 1.797-1.562 2.457-2.549z"/></svg>
                        </a>
                        <a href="https://www.linkedin.com/sharing/share-offsite/?url={{ urlencode(url()->current()) }}" target="_blank" rel="noopener" aria-label="Share on LinkedIn" class="w-9 h-9 bg-gray-100 hover:bg-primary-600 hover:text-white text-gray-500 rounded-lg grid place-items-center transition">
                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M20.447 20.452h-3.554v-5.569c0-1.328-.027-3.037-1.852-3.037-1.853 0-2.136 1.445-2.136 2.939v5.667H9.351V9h3.414v1.561h.046c.477-.9 1.637-1.85 3.37-1.85 3.601 0 4.267 2.37 4.267 5.455v6.286zM5.337 7.433c-1.144 0-2.063-.926-2.063-2.065 0-1.138.92-2.063 2.063-2.063 1.14 0 2.064.925 2.064 2.063 0 1.139-.925 2.065-2.064 2.065zm1.782 13.019H3.555V9h3.564v11.452zM22.225 0H1.771C.792 0 0 .774 0 1.729v20.542C0 23.227.792 24 1.771 24h20.451C23.2 24 24 23.227 24 22.271V1.729C24 .774 23.2 0 22.222 0h.003z"/></svg>
                        </a>
                    </div>
                </div>
            </div>

            {{-- Related posts --}}
            @if($related->isNotEmpty())
                <section class="mt-14">
                    <h2 class="text-2xl font-bold text-gray-900 mb-6">Keep Reading</h2>
                    <div class="grid sm:grid-cols-3 gap-6">
                        @foreach($related as $rel)
                            <a href="{{ route('blog.show', $rel) }}" class="group bg-white rounded-2xl border border-gray-100 shadow-sm hover:shadow-lg hover:border-primary-200 transition-all duration-300 overflow-hidden flex flex-col">
                                <div class="aspect-[16/9] bg-gradient-to-br from-primary-400 via-primary-600 to-primary-800 grid place-items-center relative overflow-hidden">
                                    @if($rel->image_url)
                                        <img src="{{ $rel->image_url }}" alt="{{ $rel->title }}" class="absolute inset-0 w-full h-full object-cover group-hover:scale-105 transition-transform duration-300">
                                    @else
                                        <svg class="w-8 h-8 text-white/40 group-hover:scale-110 transition-transform duration-300" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M12 7.5h1.5m-1.5 3h1.5m-7.5 3h7.5m-7.5 3h7.5m3-9h3.375c.621 0 1.125.504 1.125 1.125V18a2.25 2.25 0 01-2.25 2.25M16.5 7.5V18a2.25 2.25 0 002.25 2.25M16.5 7.5V4.875c0-.621-.504-1.125-1.125-1.125H4.125C3.504 3.75 3 4.254 3 4.875V18a2.25 2.25 0 002.25 2.25h13.5M6 7.5h3v3H6v-3z"/></svg>
                                    @endif
                                </div>
                                <div class="p-4 flex-1 flex flex-col">
                                    <span class="text-[10px] font-bold uppercase tracking-wide text-primary-600 mb-1.5">{{ $rel->category }}</span>
                                    <h3 class="text-sm font-bold text-gray-900 group-hover:text-primary-700 transition leading-snug line-clamp-2 flex-1">{{ $rel->title }}</h3>
                                    <p class="text-xs text-gray-400 mt-3">{{ $rel->published_at->format('M d, Y') }} • {{ $rel->reading_time }} min read</p>
                                </div>
                            </a>
                        @endforeach
                    </div>
                </section>
            @endif

            {{-- CTA --}}
            <div class="mt-12 bg-gradient-to-r from-primary-600 to-primary-800 rounded-2xl px-8 py-10 flex flex-col md:flex-row items-center justify-between gap-6">
                <div class="text-center md:text-left">
                    <h3 class="text-xl font-bold text-white mb-1">Enjoyed this article?</h3>
                    <p class="text-primary-200 text-sm">Go deeper with expert-led courses on this topic and more.</p>
                </div>
                <div class="flex flex-wrap justify-center gap-3 shrink-0">
                    <a href="{{ route('courses.index') }}" class="px-6 py-3 bg-white text-primary-700 hover:bg-primary-50 font-bold rounded-xl text-sm transition shadow">Explore Courses</a>
                    <a href="{{ route('blog') }}" class="px-6 py-3 bg-primary-700/40 hover:bg-primary-700/60 text-white font-semibold rounded-xl text-sm transition border border-white/20">More Articles</a>
                </div>
            </div>

            {{-- More from blog --}}
            @if($moreArticles->isNotEmpty())
                <div class="mt-10 bg-white rounded-2xl border border-gray-100 shadow-sm p-6">
                    <h3 class="text-xs font-bold uppercase tracking-wider text-gray-400 mb-4">More from the Blog</h3>
                    <ul class="space-y-3">
                        @foreach($moreArticles as $more)
                            <li>
                                <a href="{{ route('blog.show', $more) }}" class="flex items-center gap-3 text-sm font-medium text-gray-700 hover:text-primary-700 transition group">
                                    <svg class="w-4 h-4 text-gray-300 group-hover:text-primary-600 transition shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5L21 12m0 0l-7.5 7.5M21 12H3"/></svg>
                                    {{ $more->title }}
                                </a>
                            </li>
                        @endforeach
                    </ul>
                </div>
            @endif
        </div>
    </article>
@endsection
