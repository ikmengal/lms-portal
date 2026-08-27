@extends('layouts.app')
@php
    $lang = content_lang();
    $availableLangs = array_values(array_unique(array_merge(
        [$course->language_code],
        array_keys($lesson->translations ?? []),
        array_keys($course->translations ?? [])
    )));
@endphp
@section('title', $lesson->localize('title', $lang) . ' — ' . $course->localize('title', $lang))
@section('content')
    <div class="max-w-7xl mx-auto space-y-6 mt-2">
        {{-- Breadcrumb --}}
        <div class="flex items-center gap-2 text-sm text-gray-400 flex-wrap">
            <a href="{{ route('dashboard') }}" class="inline-flex items-center gap-1 font-medium text-primary-600 hover:text-primary-700 transition">
                <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18"/></svg>
                Dashboard
            </a>
            <span>/</span>
            <a href="{{ route('courses.show', $course->slug) }}" class="hover:text-primary-600 transition">{{ Str::limit($course->localize('title', $lang), 40) }}</a>
            <span>/</span>
            <span class="text-gray-600 font-medium truncate max-w-[200px]">{{ Str::limit($lesson->localize('title', $lang), 40) }}</span>
            @php
                $langInfo = \language_info($lang);
            @endphp
            @if(count($availableLangs) > 1)
                <div class="flex items-center gap-1.5 ml-auto">
                    @foreach($availableLangs as $lc)
                        @php
                            $li = \language_info($lc);
                        @endphp
                        @if($li)
                            @php
                                $active = ($lang ?? $course->language_code) === $lc;
                            @endphp
                            <a href="{{ request()->fullUrlWithQuery(['lang' => $lc]) }}"
                                class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full border text-[11px] font-semibold transition {{ $active ? 'bg-primary-600 border-primary-600 text-white' : 'bg-white border-gray-200 text-gray-600 hover:border-primary-300' }}">
                                {{ $li['flag'] }} {{ $li['native'] }}
                            </a>
                        @endif
                    @endforeach
                </div>
            @endif
        </div>

        <div class="grid lg:grid-cols-[1fr_360px] gap-6 items-start">
            {{-- Main player column --}}
            <div class="space-y-5 min-w-0">
                {{-- Video Player --}}
                <div class="bg-black rounded-xl overflow-hidden shadow-sm aspect-video grid place-items-center relative">
                    @if($isLocked)
                        <div class="text-center px-6 py-10 text-white/80">
                            <div class="w-20 h-20 mx-auto mb-4 rounded-2xl bg-white/10 grid place-items-center">
                                <svg class="w-10 h-10 text-violet-300" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 1a4.5 4.5 0 00-4.5 4.5V9H5a2 2 0 00-2 2v6a2 2 0 002 2h10a2 2 0 002-2v-6a2 2 0 00-2-2h-.5V5.5A4.5 4.5 0 0010 1zm3 8V5.5a3 3 0 10-6 0V9h6z" clip-rule="evenodd"/></svg>
                            </div>
                            <p class="text-lg font-bold text-white mb-1">Lesson Locked</p>
                            <p class="text-sm text-white/70 mb-1">This lesson is part of the scheduled drip content.</p>
                            <p class="text-xs text-violet-300 font-medium inline-flex items-center gap-2 flex-wrap justify-center">
                                <span>Unlocks</span>
                                <b class="text-white font-bold">{{ $nextUnlockAt?->format('M j, Y g:i A') }}</b>
                                <x-unlock-countdown :unlockAt="$nextUnlockAt" class="text-violet-300 bg-violet-400/10 border border-violet-300/20 rounded-full px-2 py-0.5" />
                            </p>
                        </div>
                    @elseif($lesson->video_url && preg_match('/(?:youtube\.com\/(?:watch\?v=|embed\/)|youtu\.be\/)([\w-]{11})/', $lesson->video_url, $m))
                        <iframe class="w-full h-full" src="https://www.youtube.com/embed/{{ $m[1] }}" title="{{ $lesson->localize('title', $lang) }}" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>
                    @elseif($lesson->video_url && preg_match('/vimeo\.com\/(\d+)/', $lesson->video_url, $m))
                        <iframe class="w-full h-full" src="https://player.vimeo.com/video/{{ $m[1] }}" title="{{ $lesson->localize('title', $lang) }}" frameborder="0" allow="autoplay; fullscreen; picture-in-picture" allowfullscreen></iframe>
                    @elseif($lesson->video_url)
                        <video id="lesson-video" controls class="w-full h-full bg-black" data-lesson-id="{{ $lesson->id }}" data-course-id="{{ $course->id }}" data-save-url="{{ route('learn.video-progress.save', [$course, $lesson]) }}" data-get-url="{{ route('learn.video-progress.get', [$course, $lesson]) }}" data-resume-position="{{ $videoProgress?->watched_seconds ?? 0 }}" {{ $completedIds->has($lesson->id) ? '' : 'autoplay' }}>
                            <source src="{{ $lesson->video_url }}" />
                            Your browser does not support the video tag.
                        </video>
                    @else
                        <div class="text-center px-6 py-10 text-white/70">
                            <svg class="w-14 h-14 mx-auto mb-3 text-white/40" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.2"><path stroke-linecap="round" stroke-linejoin="round" d="M5.25 5.653c0-.856.917-1.398 1.667-.986l11.54 6.348a1.125 1.125 0 010 1.971l-11.54 6.347a1.125 1.125 0 01-1.667-.985V5.653z"/></svg>
                            <p class="text-sm font-medium text-white/80 mb-1">{{ $lesson->localize('title', $lang) }}</p>
                            <p class="text-xs text-white/50">Video will be available soon. You can still complete this lesson and continue.</p>
                        </div>
                    @endif
                </div>

                {{-- Lesson header + Mark complete + Prev/Next --}}
                <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-5 space-y-4">
                    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                        <div class="min-w-0">
                            <p class="text-xs font-semibold uppercase tracking-wide text-primary-700">Lesson {{ $currentIdx + 1 }} of {{ $totalLessons }} · {{ $lesson->duration_minutes }} min</p>
                            <h1 class="text-xl font-bold text-gray-900 mt-1 truncate">{{ $lesson->localize('title', $lang) }}</h1>
                            @if($isLocked)
                                <p class="text-xs text-violet-600 font-medium mt-1 flex items-center gap-1.5 flex-wrap">Unlocks
                                    <b class="font-bold">{{ $nextUnlockAt?->format('M j, Y g:i A') }}</b>
                                    <x-unlock-countdown :unlockAt="$nextUnlockAt" class="text-violet-600" /> —
                                    complete the scheduled lessons to stay on track.</p>
                            @endif
                        </div>
                        @if($isLocked)
                            <span class="shrink-0 inline-flex items-center gap-2 px-5 py-2.5 rounded-lg text-sm font-semibold bg-gray-100 text-gray-400 cursor-not-allowed">
                                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 1a4.5 4.5 0 00-4.5 4.5V9H5a2 2 0 00-2 2v6a2 2 0 002 2h10a2 2 0 002-2v-6a2 2 0 00-2-2h-.5V5.5A4.5 4.5 0 0010 1zm3 8V5.5a3 3 0 10-6 0V9h6z" clip-rule="evenodd"/></svg>
                                Locked
                            </span>
                        @else
                        <form method="POST" action="{{ route('learn.complete', [$course, $lesson]) }}" class="shrink-0">
                            @csrf
                            <input type="hidden" name="undo" value="{{ $completedIds->has($lesson->id) ? 1 : 0 }}">
                            <button type="submit" class="inline-flex items-center gap-2 px-5 py-2.5 rounded-lg text-sm font-semibold transition shadow-sm {{ $completedIds->has($lesson->id) ? 'bg-secondary-50 text-secondary-700 border border-secondary-300 hover:bg-secondary-100' : 'bg-secondary-500 hover:bg-secondary-600 text-white' }}">
                                @if($completedIds->has($lesson->id))
                                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5"/></svg>
                                    Completed — undo?
                                @else
                                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5"/></svg>
                                    Mark as Complete
                                @endif
                            </button>
                        </form>
                        @endif
                    </div>
                    <div class="flex items-center justify-between gap-3 pt-1 border-t border-gray-50">
                        @if($prev)
                            @if($lockState[$prev->id]['locked'] ?? false)
                                <span class="inline-flex items-center gap-2 text-sm font-medium text-gray-300 cursor-not-allowed min-w-0" title="Unlocks {{ $lockState[$prev->id]['unlocks_at']?->format('M j, Y \a\t g:i A') }}">
                                    <svg class="w-4 h-4 shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 1a4.5 4.5 0 00-4.5 4.5V9H5a2 2 0 00-2 2v6a2 2 0 002 2h10a2 2 0 002-2v-6a2 2 0 00-2-2h-.5V5.5A4.5 4.5 0 0010 1zm3 8V5.5a3 3 0 10-6 0V9h6z" clip-rule="evenodd"/></svg>
                                    <span class="truncate">{{ Str::limit($prev->localize('title', $lang), 34) }}</span>
                                </span>
                            @else
                            <a href="{{ route('learn.show', [$course, $prev]) }}" class="inline-flex items-center gap-2 text-sm font-medium text-gray-500 hover:text-primary-600 transition min-w-0">
                                <svg class="w-4 h-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18"/></svg>
                                <span class="truncate">{{ Str::limit($prev->localize('title', $lang), 34) }}</span>
                            </a>
                            @endif
                        @else
                            <span></span>
                        @endif
                        @if($next)
                            @if($lockState[$next->id]['locked'] ?? false)
                                <span class="inline-flex items-center gap-2 text-sm font-medium text-gray-300 cursor-not-allowed min-w-0 sm:flex-row-reverse text-right" title="Unlocks {{ $lockState[$next->id]['unlocks_at']?->format('M j, Y \a\t g:i A') }}">
                                    <span class="truncate">{{ Str::limit($next->localize('title', $lang), 34) }}</span>
                                    <svg class="w-4 h-4 shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 1a4.5 4.5 0 00-4.5 4.5V9H5a2 2 0 00-2 2v6a2 2 0 002 2h10a2 2 0 002-2v-6a2 2 0 00-2-2h-.5V5.5A4.5 4.5 0 0010 1zm3 8V5.5a3 3 0 10-6 0V9h6z" clip-rule="evenodd"/></svg>
                                </span>
                            @else
                            <a href="{{ route('learn.show', [$course, $next]) }}" class="inline-flex items-center gap-2 text-sm font-medium text-gray-500 hover:text-primary-600 transition min-w-0 sm:flex-row-reverse text-right">
                                <svg class="w-4 h-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5L21 12m0 0l-7.5 7.5M21 12H3"/></svg>
                                <span class="truncate">{{ Str::limit($next->localize('title', $lang), 34) }}</span>
                            </a>
                            @endif
                        @elseif(!$completedIds->has($lesson->id))
                            <span class="text-xs text-gray-400">Final lesson of this course 🎓</span>
                        @endif
                    </div>
                </div>

                {{-- Tabs: Overview / Notes / Resources / Q&A --}}
                <div x-data="{ tab: 'overview' }" class="bg-white rounded-xl border border-gray-100 shadow-sm overflow-hidden">
                    <div class="border-b border-gray-100 px-4 pt-3">
                        <div class="flex gap-5 overflow-x-auto">
                            <button @click="tab = 'overview'" :class="tab === 'overview' ? 'border-primary-600 text-primary-600' : 'border-transparent text-gray-500 hover:text-gray-700'" class="pb-3 text-sm font-medium border-b-2 transition whitespace-nowrap">Overview</button>
                            <button @click="tab = 'notes'" :class="tab === 'notes' ? 'border-primary-600 text-primary-600' : 'border-transparent text-gray-500 hover:text-gray-700'" class="pb-3 text-sm font-medium border-b-2 transition whitespace-nowrap">Notes ({{ $notes->count() }})</button>
                            <button @click="tab = 'resources'" :class="tab === 'resources' ? 'border-primary-600 text-primary-600' : 'border-transparent text-gray-500 hover:text-gray-700'" class="pb-3 text-sm font-medium border-b-2 transition whitespace-nowrap">Resources ({{ $lesson->resources->count() }})</button>
                            <button @click="tab = 'qa'" :class="tab === 'qa' ? 'border-primary-600 text-primary-600' : 'border-transparent text-gray-500 hover:text-gray-700'" class="pb-3 text-sm font-medium border-b-2 transition whitespace-nowrap">Q&amp;A ({{ $questions->count() }})</button>
                        </div>
                    </div>

                    <div class="p-5">
                        {{-- Overview --}}
                        <div x-show="tab === 'overview'">
                            @if($lesson->localize('description', $lang))
                                <p class="text-sm text-gray-600 leading-relaxed whitespace-pre-line">{{ $lesson->localize('description', $lang) }}</p>
                            @else
                                <p class="text-sm text-gray-500">In this lesson we cover <strong>{{ strtolower($lesson->localize('title', $lang)) }}</strong> as part of the <strong>{{ $course->localize('title', $lang) }}</strong> course. Estimated time: about {{ $lesson->duration_minutes }} minutes.</p>
                            @endif
                        </div>

                        {{-- Notes --}}
                        <div x-show="tab === 'notes'" style="display:none;" class="space-y-4">
                            @if($isLocked)
                                <p class="p-4 bg-gray-50 border border-gray-100 rounded-lg text-sm text-gray-400 text-center">Notes unlock with this lesson <x-unlock-countdown :unlockAt="$nextUnlockAt" class="text-gray-400" /> ({{ $nextUnlockAt?->format('M j, Y g:i A') }}).</p>
                            @else
                            <form method="POST" action="{{ route('learn.notes.store', [$course, $lesson]) }}" class="space-y-2">
                                @csrf
                                <textarea name="content" rows="3" required placeholder="Write your note for this lesson... e.g. key points, timestamps, questions"
                                    class="w-full px-4 py-2.5 border border-gray-200 rounded-lg text-sm focus:ring-2 focus:ring-primary-500 focus:border-primary-500"></textarea>
                                <div class="flex justify-end">
                                    <button type="submit" class="px-4 py-2 bg-primary-600 hover:bg-primary-700 text-white text-sm font-medium rounded-lg transition">Save Note</button>
                                </div>
                            </form>
                            @endif
                            <ul class="space-y-3">
                                @forelse($notes as $note)
                                    <li class="bg-gray-50 rounded-lg p-4 group">
                                        <p class="text-sm text-gray-700 whitespace-pre-line">{{ $note->content }}</p>
                                        <div class="flex items-center justify-between mt-2">
                                            <span class="text-xs text-gray-400">{{ $note->created_at->diffForHumans() }}</span>
                                            <form method="POST" action="{{ route('learn.notes.destroy', $note) }}" onsubmit="return confirm('Delete this note?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="text-xs text-red-400 opacity-0 group-hover:opacity-100 hover:text-red-600 transition">delete</button>
                                            </form>
                                        </div>
                                    </li>
                                @empty
                                    <li class="text-sm text-gray-400 py-4 text-center">No notes yet — capture your thoughts while watching.</li>
                                @endforelse
                            </ul>
                        </div>

                        {{-- Resources --}}
                        <div x-show="tab === 'resources'" style="display:none;">
                            <ul class="divide-y divide-gray-50 -mx-1">
                                @forelse($lesson->resources as $resource)
                                    <li class="py-3 px-1 flex items-center justify-between gap-3">
                                        <div class="flex items-center gap-3 min-w-0">
                                            <span class="w-9 h-9 rounded-lg bg-accent-50 text-accent-700 grid place-items-center shrink-0">
                                                <svg class="w-4.5 h-4.5 w-[18px]" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m2.25 0H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z"/></svg>
                                            </span>
                                            <div class="min-w-0">
                                                <p class="text-sm font-medium text-gray-800 truncate">{{ $resource->title }}</p>
                                                <p class="text-xs text-gray-400">{{ $resource->external_url ? 'External link' : 'Downloadable file' }}</p>
                                            </div>
                                        </div>
                                        @if($isLocked)
                                            <span class="shrink-0 inline-flex items-center gap-1.5 px-3.5 py-2 bg-gray-50 text-gray-400 text-xs font-semibold rounded-lg">
                                                <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 1a4.5 4.5 0 00-4.5 4.5V9H5a2 2 0 00-2 2v6a2 2 0 002 2h10a2 2 0 002-2v-6a2 2 0 00-2-2h-.5V5.5A4.5 4.5 0 0010 1zm3 8V5.5a3 3 0 10-6 0V9h6z" clip-rule="evenodd"/></svg>
                                                Locked
                                            </span>
                                        @else
                                        <a href="{{ route('learn.resources.download', $resource) }}" {{ $resource->external_url ? 'target=_blank rel=noopener' : '' }}
                                            class="shrink-0 inline-flex items-center gap-1.5 px-3.5 py-2 bg-primary-50 hover:bg-primary-100 text-primary-700 text-xs font-semibold rounded-lg transition">
                                            <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5M16.5 12L12 16.5m0 0L7.5 12m4.5 4.5V3"/></svg>
                                            Get
                                        </a>
                                        @endif
                                    </li>
                                @empty
                                    <li class="text-sm text-gray-400 py-4 text-center">No resources attached to this lesson.</li>
                                @endforelse
                            </ul>
                        </div>

                        {{-- Q&A / Discussion --}}
                        <div x-show="tab === 'qa'" style="display:none;" class="space-y-5">
                            @if($isLocked)
                                <p class="p-4 bg-gray-50 border border-gray-100 rounded-lg text-sm text-gray-400 text-center">Discussion opens with this lesson <x-unlock-countdown :unlockAt="$nextUnlockAt" class="text-gray-400" /> ({{ $nextUnlockAt?->format('M j, Y g:i A') }}).</p>
                            @else
                            <form method="POST" action="{{ route('learn.questions.store', [$course, $lesson]) }}" class="space-y-2">
                                @csrf
                                <textarea name="body" rows="2" required placeholder="Ask a question about this lesson..."
                                    class="w-full px-4 py-2.5 border border-gray-200 rounded-lg text-sm focus:ring-2 focus:ring-primary-500 focus:border-primary-500"></textarea>
                                <div class="flex justify-end">
                                    <button type="submit" class="px-4 py-2 bg-primary-600 hover:bg-primary-700 text-white text-sm font-medium rounded-lg transition">Post Question</button>
                                </div>
                            </form>
                            @endif

                            <ul class="space-y-5">
                                @forelse($questions as $question)
                                    @php
                                        $isInstructor = $question->user && ($question->user->hasRole('instructor') || $question->user->hasRole('admin'));
                                        $upvoted = $question->isUpvotedBy(auth()->user());
                                        $upvoteCount = $question->upvoteCount();
                                        $canAnswer = auth()->user()->hasRole('admin') || $course->instructor_id === auth()->id();
                                    @endphp
                                    <li class="space-y-3">
                                        <div class="flex gap-3">
                                            <div class="w-8 h-8 rounded-full {{ $isInstructor ? 'bg-accent-500' : 'bg-primary-600' }} text-white grid place-items-center text-xs font-bold shrink-0">{{ strtoupper(substr($question->user?->name ?? 'U', 0, 1)) }}</div>
                                            <div class="min-w-0 flex-1">
                                                <div class="rounded-xl p-4 {{ $question->is_answered ? 'bg-green-50 border border-green-200' : 'bg-gray-50' }}">
                                                    <div class="flex items-start gap-2">
                                                        <div class="flex-1 min-w-0">
                                                            <p class="text-sm text-gray-800 whitespace-pre-line">{{ $question->body }}</p>
                                                            <div class="flex items-center gap-2 mt-2 flex-wrap">
                                                                <span class="text-xs {{ $isInstructor ? 'text-accent-600 font-semibold' : 'text-gray-400' }}">{{ $question->user?->name ?? 'User' }}</span>
                                                                @if($isInstructor)
                                                                    <span class="inline-flex items-center px-1.5 py-0.5 rounded text-[10px] font-bold bg-accent-100 text-accent-700">Instructor</span>
                                                                @endif
                                                                @if($question->is_answered)
                                                                    <span class="inline-flex items-center gap-1 px-1.5 py-0.5 rounded text-[10px] font-bold bg-green-100 text-green-700">
                                                                        <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5"/></svg>
                                                                        Answered
                                                                    </span>
                                                                @endif
                                                                <span class="text-xs text-gray-400">{{ $question->created_at->diffForHumans() }}</span>
                                                            </div>
                                                        </div>
                                                        {{-- Upvote --}}
                                                        <form method="POST" action="{{ route('learn.questions.upvote', $question) }}" class="shrink-0">
                                                            @csrf
                                                            <button type="submit" class="flex flex-col items-center gap-0.5 px-2 py-1 rounded-lg transition {{ $upvoted ? 'text-primary-600 bg-primary-50' : 'text-gray-400 hover:text-primary-600 hover:bg-gray-100' }}">
                                                                <svg class="w-4 h-4 {{ $upvoted ? 'fill-primary-600' : '' }}" fill="{{ $upvoted ? 'currentColor' : 'none' }}" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M4.5 15.75l7.5-7.5 7.5 7.5"/></svg>
                                                                <span class="text-[11px] font-semibold">{{ $upvoteCount }}</span>
                                                            </button>
                                                        </form>
                                                    </div>
                                                </div>

                                                {{-- Replies --}}
                                                @if($question->replies->isNotEmpty())
                                                    <div class="mt-3 space-y-3 sm:ml-6">
                                                        @foreach($question->replies as $reply)
                                                            @php $replyIsInstructor = $reply->user && ($reply->user->hasRole('instructor') || $reply->user->hasRole('admin')); @endphp
                                                            <div class="flex gap-2.5">
                                                                <div class="w-7 h-7 rounded-full {{ $replyIsInstructor ? 'bg-accent-500' : 'bg-gray-300' }} {{ $replyIsInstructor ? 'text-white' : 'text-gray-700' }} grid place-items-center text-[10px] font-bold shrink-0">{{ strtoupper(substr($reply->user?->name ?? 'U', 0, 1)) }}</div>
                                                                <div class="bg-white border border-gray-100 rounded-xl p-3 flex-1 min-w-0">
                                                                    <p class="text-sm text-gray-700 whitespace-pre-line">{{ $reply->body }}</p>
                                                                    <div class="flex items-center gap-2 mt-1.5">
                                                                        <span class="text-xs {{ $replyIsInstructor ? 'text-accent-600 font-semibold' : 'text-gray-400' }}">{{ $reply->user?->name ?? 'User' }}</span>
                                                                        @if($replyIsInstructor)
                                                                            <span class="inline-flex items-center px-1.5 py-0.5 rounded text-[10px] font-bold bg-accent-100 text-accent-700">Instructor</span>
                                                                        @endif
                                                                        <span class="text-xs text-gray-400">{{ $reply->created_at->diffForHumans() }}</span>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        @endforeach
                                                    </div>
                                                @endif

                                                {{-- Reply form --}}
                                                <details class="mt-2 sm:ml-6">
                                                    <summary class="text-xs font-medium text-primary-600 cursor-pointer hover:text-primary-700 transition w-fit">Reply</summary>
                                                    <form method="POST" action="{{ route('learn.questions.store', [$course, $lesson]) }}" class="flex gap-2 mt-2">
                                                        @csrf
                                                        <input type="hidden" name="parent_id" value="{{ $question->id }}">
                                                        <input type="text" name="body" required placeholder="Write a reply..." class="flex-1 px-3 py-2 border border-gray-200 rounded-lg text-sm focus:ring-2 focus:ring-primary-500 focus:border-primary-500" />
                                                        <button type="submit" class="px-3 py-2 bg-primary-600 hover:bg-primary-700 text-white text-xs font-medium rounded-lg transition">Send</button>
                                                    </form>
                                                </details>

                                                {{-- Mark as answered (instructor/admin only) --}}
                                                @if($canAnswer && !$question->is_answered)
                                                    <form method="POST" action="{{ route('learn.questions.answer', $question) }}" class="mt-2 sm:ml-6">
                                                        @csrf
                                                        <button type="submit" class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-medium text-green-700 bg-green-50 hover:bg-green-100 border border-green-200 rounded-lg transition">
                                                            <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5"/></svg>
                                                            Mark as Answered
                                                        </button>
                                                    </form>
                                                @elseif($canAnswer && $question->is_answered)
                                                    <form method="POST" action="{{ route('learn.questions.answer', $question) }}" class="mt-2 sm:ml-6">
                                                        @csrf
                                                        <button type="submit" class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-medium text-gray-500 bg-gray-50 hover:bg-gray-100 border border-gray-200 rounded-lg transition">
                                                            <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                                                            Unmark Answered
                                                        </button>
                                                    </form>
                                                @endif
                                            </div>
                                        </div>
                                    </li>
                                @empty
                                    <li class="text-sm text-gray-400 py-4 text-center">No questions yet — be the first to ask!</li>
                                @endforelse
                            </ul>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Curriculum Sidebar --}}
            <aside x-data="{ panel: 'curriculum' }" class="bg-white rounded-xl border border-gray-100 shadow-sm overflow-hidden lg:sticky lg:top-20 max-h-[calc(100vh-6rem)] flex flex-col">

                {{-- Back to Dashboard --}}
                <div class="p-4 border-b border-gray-100 bg-gray-50/60">
                    <a href="{{ route('dashboard') }}"
                        class="w-full inline-flex items-center justify-center gap-2 px-4 py-2.5 bg-white border border-gray-200 text-sm font-semibold text-gray-700 rounded-xl hover:bg-gray-100 hover:border-gray-300 transition shadow-sm">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18"/></svg>
                        Back to Dashboard
                    </a>
                </div>

                {{-- Progress --}}
                <div class="p-5 border-b border-gray-100">
                    <a href="{{ route('courses.show', $course->slug) }}" class="text-sm font-bold text-gray-900 hover:text-primary-600 transition line-clamp-2">{{ $course->title }}</a>
                    <div class="flex items-center justify-between mt-3 text-xs text-gray-400">
                        <span>{{ $doneCount }}/{{ $totalLessons }} lessons done</span>
                        <span class="font-semibold text-primary-600">{{ $certProgressPct }}%</span>
                    </div>
                    <div class="h-2 bg-gray-100 rounded-full mt-2 overflow-hidden">
                        <div class="h-full bg-gradient-to-r from-primary-500 to-secondary-500 rounded-full transition-all duration-500" style="width: {{ $certProgressPct }}%"></div>
                    </div>
                    @php $level = \App\Services\GamificationService::currentLevel(auth()->user()); @endphp
                    <div class="flex items-center justify-between mt-3 pt-3 border-t border-dashed border-gray-100">
                        <a href="{{ route('gamification.index') }}" class="inline-flex items-center gap-1.5 text-[11px] font-semibold text-secondary-600 bg-secondary-50 px-2 py-1 rounded-md hover:bg-secondary-100 transition">
                            <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 20 20"><path d="M11.48 3.499a.562.562 0 011.04 0l2.125 5.111a.563.563 0 00.475.345l5.518.442c.499.04.701.663.321.988l-4.204 3.602a.563.563 0 00-.182.557l1.285 5.385a.562.562 0 01-.84.61l-4.725-2.885a.563.563 0 00-.586 0L6.982 20.54a.562.562 0 01-.84-.61l1.285-5.386a.562.562 0 00-.182-.557l-4.204-3.602a.563.563 0 01.321-.988l5.518-.442a.563.563 0 00.475-.345L11.48 3.5z"/></svg>
                            Level {{ $level }}
                        </a>
                        <span class="text-[11px] font-semibold text-primary-600">{{ number_format(auth()->user()->xp) }} XP</span>
                    </div>
                </div>

                {{-- Sidebar Tabs: Curriculum / Achievements --}}
                <div class="flex gap-1.5 px-3 pt-2.5 border-b border-gray-100 bg-gray-50/40">
                    <button type="button" @click="panel = 'curriculum'"
                        :class="panel === 'curriculum' ? 'bg-white border-gray-200 text-primary-700 shadow-sm' : 'border-transparent text-gray-500 hover:text-gray-700'"
                        class="flex-1 inline-flex items-center justify-center gap-1.5 px-3 py-2 text-xs font-bold rounded-t-lg border-b-2 border-b-transparent transition">
                        <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6.042A8.967 8.967 0 006 3.75c-1.052 0-2.062.18-3 .512v14.25A8.987 8.987 0 016 18c2.305 0 4.408.867 6 2.292m0-14.25a8.966 8.966 0 016-2.292c1.052 0 2.062.18 3 .512v14.25A8.987 8.987 0 0018 18a8.967 8.967 0 00-6 2.292m0-14.25v14.25"/></svg>
                        Curriculum
                    </button>
                    <button type="button" @click="panel = 'dashboard'"
                        :class="panel === 'dashboard' ? 'bg-white border-gray-200 text-primary-700 shadow-sm' : 'border-transparent text-gray-500 hover:text-gray-700'"
                        class="flex-1 inline-flex items-center justify-center gap-1.5 px-3 py-2 text-xs font-bold rounded-t-lg border-b-2 border-b-transparent transition">
                        <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M3 13.125C3 12.504 3.504 12 4.125 12h2.25c.621 0 1.125.504 1.125 1.125v6.75C7.5 20.496 6.996 21 6.375 21h-2.25A1.125 1.125 0 013 19.875v-6.75zM9.75 8.625c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125v11.25c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 01-1.125-1.125V8.625zM16.5 4.125c0-.621.504-1.125 1.125-1.125h2.25C20.496 3 21 3.504 21 4.125v15.75c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 01-1.125-1.125V4.125z"/></svg>
                        Achievements
                        @if($badges->isNotEmpty())
                            <span class="px-1.5 py-0.5 rounded-full bg-accent-100 text-accent-700 text-[10px] font-bold">{{ $badges->count() }}</span>
                        @endif
                    </button>
                </div>

                {{-- Achievements panel: Certificate / Exams & Tests / Badges --}}
                <div x-show="panel === 'dashboard'" class="flex-1 min-h-0 overflow-y-auto" style="display:none;">

                {{-- Certificate --}}
                <div class="p-5 border-b border-gray-100">
                    <div class="flex items-center justify-between mb-3">
                        <p class="text-xs font-bold uppercase tracking-wider text-gray-500">Certificate</p>
                        @if($certificate)
                            <span class="text-[11px] font-medium px-2 py-0.5 rounded-full bg-secondary-50 text-secondary-700">Earned</span>
                        @else
                            <span class="text-[11px] font-medium px-2 py-0.5 rounded-full bg-gray-100 text-gray-500">{{ $certProgressPct >= 100 ? 'Ready' : 'In progress' }}</span>
                        @endif
                    </div>
                    <div class="rounded-xl bg-gradient-to-br from-secondary-50 to-accent-50 border border-secondary-100 p-4 flex items-center gap-3">
                        <div class="w-11 h-11 rounded-xl bg-white border border-secondary-100 grid place-items-center shrink-0 shadow-sm">
                            <svg class="w-6 h-6 text-secondary-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m2.25 0H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z"/></svg>
                        </div>
                        <div class="min-w-0 flex-1">
                            @if($certificate)
                                <p class="text-sm font-semibold text-secondary-700">Certificate earned!</p>
                                <p class="text-[11px] text-gray-500 leading-snug">Issued {{ $certificate->issued_at->format('M j, Y') }}</p>
                                <a href="{{ route('certificates.show', $certificate) }}" target="_blank" class="inline-flex items-center gap-1 text-[11px] font-semibold text-secondary-700 hover:text-secondary-800 transition mt-1">
                                    View &amp; Download
                                    <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5L21 12m0 0l-7.5 7.5M21 12H3"/></svg>
                                </a>
                            @else
                                <p class="text-sm font-semibold text-gray-800">{{ $certProgressPct }}% complete</p>
                                <div class="h-1.5 bg-white rounded-full mt-2 overflow-hidden">
                                    <div class="h-full bg-secondary-500 rounded-full transition-all duration-500" style="width: {{ $certProgressPct }}%"></div>
                                </div>
                                <p class="text-[11px] text-gray-500 mt-1.5 leading-snug">{{ $totalLessons - $doneCount }} lesson{{ $totalLessons - $doneCount > 1 ? 's' : '' }} left to earn your certificate</p>
                            @endif
                        </div>
                    </div>
                </div>

                {{-- Exams & Tests --}}
                @if($quizzes->isNotEmpty())
                    @php $examsUnlocked = $doneCount >= $totalLessons; @endphp
                    <div class="border-b border-gray-100">
                        <div class="px-5 py-3 flex items-center justify-between">
                            <p class="text-xs font-bold uppercase tracking-wider text-gray-500">Exams &amp; Tests</p>
                            <span class="text-[11px] font-medium px-2 py-0.5 rounded-full {{ $examsUnlocked ? 'bg-secondary-50 text-secondary-700' : 'bg-gray-100 text-gray-500' }}">
                                {{ $examsUnlocked ? 'Unlocked' : 'Locked' }}
                            </span>
                        </div>

                        @if(!$examsUnlocked)
                            <div class="mx-5 mb-3 p-3 bg-gray-50 border border-gray-100 rounded-xl text-center">
                                <p class="text-[11px] text-gray-500 leading-relaxed">
                                    <svg class="w-5 h-5 mx-auto text-gray-300 mb-1" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M16.5 10.5V6.75a4.5 4.5 0 10-9 0v3.75m-.75 11.25h10.5a2.25 2.25 0 002.25-2.25v-6.75a2.25 2.25 0 00-2.25-2.25H6.75a2.25 2.25 0 00-2.25 2.25v6.75a2.25 2.25 0 002.25 2.25z"/></svg>
                                    Complete all lessons to unlock {{ $quizzes->count() }} {{ $quizzes->count() === 1 ? 'test' : 'tests & exams' }}.
                                    <strong class="text-gray-600">{{ $totalLessons - $doneCount }}</strong> lesson{{ $totalLessons - $doneCount > 1 ? 's' : '' }} remaining.
                                </p>
                            </div>
                        @endif

                        <ul class="px-2 pb-4 space-y-1">
                            @foreach($quizzes as $quiz)
                                @php
                                    $isAssignment = $quiz->type === 'assignment';
                                    if ($isAssignment) {
                                        $sub = $quiz->submissionFor(auth()->id());
                                        $subStatus = $sub?->status;
                                        $subGraded = $sub?->isGraded();
                                        $subMarks = $sub?->marks;
                                        $subPassed = $subGraded && $subMarks !== null && $subMarks >= $quiz->passing_score;
                                        $completed = $subGraded;
                                    } else {
                                        $best = $bestScores[$quiz->id] ?? null;
                                        $attempts = $attemptCounts[$quiz->id] ?? 0;
                                        $passed = $best !== null && $best >= $quiz->passing_score;
                                        $completed = $best !== null;
                                    }
                                    $title = $quiz->type === 'final_exam' ? 'Final Exam' : ($isAssignment ? 'Assignment' : 'Quiz');
                                @endphp
                                <li>
                                    <div class="flex items-center gap-2.5 px-3 py-2 mx-1 rounded-lg {{ $examsUnlocked && $completed ? 'bg-secondary-50/70' : '' }}">
                                        <span class="w-8 h-8 rounded-lg grid place-items-center shrink-0 {{ $quiz->type === 'final_exam' ? 'bg-red-100 text-red-600' : ($isAssignment ? 'bg-accent-100 text-accent-700' : 'bg-primary-100 text-primary-700') }}">
                                            @if($quiz->type === 'final_exam')
                                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M4.26 10.147a60.438 60.438 0 00-.491 6.347A48.62 48.62 0 0112 20.904a48.62 48.62 0 018.232-4.41 60.46 60.46 0 00-.491-6.347m-15.482 0a50.636 50.636 0 00-2.658-.813A59.906 59.906 0 0112 3.493a59.903 59.903 0 0110.399 5.84c-.896.248-1.783.52-2.658.814m-15.482 0A50.717 50.717 0 0112 13.489a50.702 50.702 0 017.74-3.342M6.75 15a.75.75 0 100-1.5.75.75 0 000 1.5zm0 0v-3.675A55.378 55.378 0 0112 8.443m-7.007 11.55A5.981 5.981 0 006.75 15.75v-1.5"/></svg>
                                            @elseif($isAssignment)
                                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z"/></svg>
                                            @else
                                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12c0 1.268-.63 2.39-1.593 3.068a3.745 3.745 0 01-1.043 3.296 3.745 3.745 0 01-3.296 1.043A3.745 3.745 0 0112 21c-1.268 0-2.39-.63-3.068-1.593a3.746 3.746 0 01-3.296-1.043 3.745 3.745 0 01-1.043-3.296A3.745 3.745 0 013 12c0-1.268.63-2.39 1.593-3.068a3.746 3.746 0 011.043-3.296 3.746 3.746 0 013.296-1.043A3.746 3.746 0 0112 3c1.268 0 2.39.63 3.068 1.593a3.746 3.746 0 013.296 1.043 3.746 3.746 0 011.043 3.296A3.745 3.745 0 0121 12z"/></svg>
                                            @endif
                                        </span>
                                        <div class="min-w-0 flex-1">
                                            <p class="text-sm font-medium {{ $examsUnlocked ? 'text-gray-800' : 'text-gray-400' }} truncate">{{ $quiz->title }}</p>
                                            <p class="text-[11px] text-gray-400">
                                                @if(!$examsUnlocked)
                                                    {{ $title }} · {{ $quiz->questions_count }} questions
                                                @elseif($isAssignment)
                                                    @if($sub)
                                                        @if($subGraded)
                                                            Score: <strong class="{{ $subPassed ? 'text-secondary-600' : 'text-red-500' }}">{{ number_format($subMarks, 0) }}%</strong>
                                                        @else
                                                            {{ ucfirst($subStatus) }}
                                                        @endif
                                                    @else
                                                        Not submitted
                                                    @endif
                                                    @if($quiz->due_date)
                                                        · Due {{ $quiz->due_date->diffForHumans() }}
                                                    @endif
                                                @else
                                                    @if($best !== null)
                                                        Score: <strong class="{{ $passed ? 'text-secondary-600' : 'text-red-500' }}">{{ number_format($best, 0) }}%</strong>
                                                        · {{ $attempts }} attempt{{ $attempts > 1 ? 's' : '' }}
                                                    @else
                                                        Not attempted yet
                                                    @endif
                                                @endif
                                            </p>
                                        </div>
                                        @if($examsUnlocked && !$isAssignment && $best !== null)
                                            <span class="shrink-0 px-2 py-0.5 rounded-full text-[10px] font-bold {{ $passed ? 'bg-secondary-100 text-secondary-700' : 'bg-red-100 text-red-600' }}">
                                                {{ $passed ? 'PASSED' : 'FAILED' }}
                                            </span>
                                        @elseif($examsUnlocked && $isAssignment && $sub && $subGraded)
                                            <span class="shrink-0 px-2 py-0.5 rounded-full text-[10px] font-bold {{ $subPassed ? 'bg-secondary-100 text-secondary-700' : 'bg-red-100 text-red-600' }}">
                                                {{ $subPassed ? 'PASSED' : 'FAILED' }}
                                            </span>
                                        @endif
                                        @if($examsUnlocked)
                                            <a href="{{ $isAssignment ? route('courses.assignments.show', [$course, $quiz]) : route('courses.tests.show', [$course, $quiz]) }}"
                                                class="shrink-0 inline-flex items-center px-3 py-1.5 text-[11px] font-bold rounded-lg transition {{ $completed ? 'bg-white border border-gray-200 text-gray-700 hover:bg-gray-50' : 'bg-primary-600 text-white hover:bg-primary-700 shadow-sm' }}">
                                                {{ $isAssignment ? ($sub ? 'View' : 'Submit') : (($best !== null) ? 'Retake' : 'Start') }}
                                            </a>
                                        @else
                                            <span class="shrink-0 inline-flex items-center gap-1 px-3 py-1.5 text-[11px] font-bold rounded-lg bg-gray-100 text-gray-400 cursor-not-allowed" title="Complete all lessons to unlock">
                                                <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 1a4.5 4.5 0 00-4.5 4.5V9H5a2 2 0 00-2 2v6a2 2 0 002 2h10a2 2 0 002-2v-6a2 2 0 00-2-2h-.5V5.5A4.5 4.5 0 0010 1zm3 8V5.5a3 3 0 10-6 0V9h6z" clip-rule="evenodd"/></svg>
                                                Locked
                                            </span>
                                        @endif
                                    </div>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                {{-- Badges --}}
                @if($badges->isNotEmpty())
                    <div class="p-5 border-b border-gray-100">
                        <div class="flex items-center justify-between mb-3">
                            <p class="text-xs font-bold uppercase tracking-wider text-gray-500">Badges Earned</p>
                            <a href="{{ route('gamification.index') }}" class="text-[11px] font-semibold text-primary-600 hover:text-primary-700 transition">{{ $badges->count() }}+ →</a>
                        </div>
                        <div class="flex flex-wrap gap-2.5">
                            @foreach($badges as $badge)
                                <div class="group relative" title="{{ $badge->name }}">
                                    <div class="w-10 h-10 rounded-xl grid place-items-center shadow-sm border hover:scale-110 transition cursor-pointer" style="background: {{ $badge->color }}20; border-color: {{ $badge->color }}40">
                                        <x-badge-icon :icon="$badge->icon" :color="$badge->color" class="w-5 h-5" />
                                    </div>
                                    <div class="absolute bottom-full left-1/2 -translate-x-1/2 mb-1.5 px-2 py-1 bg-gray-900 text-white text-[10px] font-medium rounded-md whitespace-nowrap opacity-0 group-hover:opacity-100 transition pointer-events-none shadow-lg z-10">
                                        {{ $badge->name }}
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif
                </div>

                {{-- Curriculum panel: Modules & Lessons --}}
                <nav x-show="panel === 'curriculum'" class="overflow-y-auto divide-y divide-gray-50 flex-1 min-h-0">
                    @foreach($modules as $module)
                        <div x-data="{ open: {{ $module->lessons->contains('id', $lesson->id) ? 'true' : 'false' }} }">
                            <button type="button" @click="open = !open" class="w-full flex items-center justify-between gap-2 px-4 py-3 text-left hover:bg-gray-50 transition">
                                <div class="min-w-0">
                                    <p class="text-sm font-semibold text-gray-800 truncate">{{ $module->localize('title', $lang) }}</p>
                                    <p class="text-[11px] text-gray-400">{{ $module->lessons->count() }} lessons · {{ $module->lessons->sum('duration_minutes') }} min</p>
                                </div>
                                @if($module->unlocks_at && $module->unlocks_at->isFuture())
                                    <span class="shrink-0 inline-flex items-center gap-1 text-[11px] font-semibold text-violet-600 bg-violet-50 px-2 py-0.5 rounded-md" title="Module unlocks {{ $module->unlocks_at->format('M j, Y \a\t g:i A') }}">
                                        <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 1a4.5 4.5 0 00-4.5 4.5V9H5a2 2 0 00-2 2v6a2 2 0 002 2h10a2 2 0 002-2v-6a2 2 0 00-2-2h-.5V5.5A4.5 4.5 0 0010 1zm3 8V5.5a3 3 0 10-6 0V9h6z" clip-rule="evenodd"/></svg>
                                        <x-unlock-countdown :unlockAt="$module->unlocks_at" class="text-violet-600" />
                                    </span>
                                @endif
                                <svg :class="open && 'rotate-180'" class="w-4 h-4 text-gray-400 transition-transform shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 8.25l-7.5 7.5-7.5-7.5"/></svg>
                            </button>
                            <ul x-show="open" style="display:none;" class="pb-2">
                                @foreach($module->lessons as $l)
                                    @php $lLocked = $lockState[$l->id]['locked'] ?? false; @endphp
                                    <li>
                                        @if($lLocked)
                                            <span class="flex items-center gap-2.5 px-4 py-2 pl-6 text-sm transition text-gray-300 cursor-not-allowed border-l-2 border-transparent" title="Unlocks {{ $lockState[$l->id]['unlocks_at']?->format('M j, Y \a\t g:i A') }}">
                                                <span class="w-5 h-5 rounded-full border-2 border-gray-200 grid place-items-center shrink-0">
                                                    <svg class="w-2.5 h-2.5 text-gray-300" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 1a4.5 4.5 0 00-4.5 4.5V9H5a2 2 0 00-2 2v6a2 2 0 002 2h10a2 2 0 002-2v-6a2 2 0 00-2-2h-.5V5.5A4.5 4.5 0 0010 1zm3 8V5.5a3 3 0 10-6 0V9h6z" clip-rule="evenodd"/></svg>
                                                </span>
                                                <span class="truncate flex-1">{{ $l->localize('title', $lang) }}</span>
                                                <x-unlock-countdown :unlockAt="$lockState[$l->id]['unlocks_at']" class="text-[11px] text-gray-400" />
                                            </span>
                                        @else
                                        <a href="{{ route('learn.show', [$course, $l]) }}"
                                            class="flex items-center gap-2.5 px-4 py-2 pl-6 text-sm transition {{ $l->id === $lesson->id ? 'bg-primary-50 text-primary-700 font-semibold border-l-2 border-primary-600' : 'text-gray-600 hover:bg-gray-50 border-l-2 border-transparent' }}">
                                            @if($completedIds->has($l->id))
                                                <span class="w-5 h-5 rounded-full bg-secondary-500 grid place-items-center shrink-0">
                                                    <svg class="w-3 h-3 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3"><path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5"/></svg>
                                                </span>
                                            @else
                                                <span class="w-5 h-5 rounded-full border-2 border-gray-300 grid place-items-center shrink-0"></span>
                                            @endif
                                            <span class="truncate flex-1">{{ $l->localize('title', $lang) }}</span>
                                            <span class="text-[11px] text-gray-400 shrink-0">{{ $l->duration_minutes }}m</span>
                                        </a>
                                        @endif
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                    @endforeach
                </nav>
            </aside>
        </div>
    </div>

    @if($lesson->video_url && preg_match('/^(https?:\/\/)(?!.*(?:youtube|vimeo)).+/i', $lesson->video_url))
    <script>
    document.addEventListener('DOMContentLoaded', function () {
        const video = document.getElementById('lesson-video');
        if (!video) return;

        let savedPosition = parseInt(video.dataset.resumePosition || '0', 10);
        let resumed = false;
        let lastSaved = 0;
        const SAVE_INTERVAL = 10;

        video.addEventListener('loadedmetadata', function () {
            if (savedPosition > 0 && video.duration > 0) {
                const seekTo = Math.min(savedPosition, video.duration - 1);
                if (seekTo > 2) {
                    video.currentTime = seekTo;
                }
            }
            resumed = true;
        });

        video.addEventListener('timeupdate', function () {
            if (!resumed || !video.duration) return;
            const now = Math.floor(video.currentTime);
            if (now - lastSaved >= SAVE_INTERVAL) {
                lastSaved = now;
                navigator.sendBeacon(video.dataset.saveUrl, new FormData(Object.entries({
                    watched_seconds: now,
                    duration: Math.floor(video.duration),
                    _token: '{{ csrf_token() }}',
                })));
            }
        });

        video.addEventListener('pause', function () {
            if (!resumed || !video.duration) return;
            const now = Math.floor(video.currentTime);
            if (now !== lastSaved) {
                lastSaved = now;
                navigator.sendBeacon(video.dataset.saveUrl, new FormData(Object.entries({
                    watched_seconds: now,
                    duration: Math.floor(video.duration),
                    _token: '{{ csrf_token() }}',
                })));
            }
        });

        video.addEventListener('ended', function () {
            navigator.sendBeacon(video.dataset.saveUrl, new FormData(Object.entries({
                watched_seconds: Math.floor(video.duration),
                duration: Math.floor(video.duration),
                _token: '{{ csrf_token() }}',
            })));
        });
    });
    </script>
    @endif

    <script>
    // Live countdown for scheduled (drip) module & lesson unlocks
    (function () {
        function pad(n) { return String(n).padStart(2, '0'); }

        function format(secs) {
            if (secs <= 0) return 'Open now';
            const d = Math.floor(secs / 86400);
            const h = Math.floor((secs % 86400) / 3600);
            const m = Math.floor((secs % 3600) / 60);
            const s = secs % 60;
            return (d > 0 ? d + 'd ' : '') + h + 'h ' + pad(m) + 'm ' + pad(s) + 's';
        }

        function tick() {
            let expired = false;
            document.querySelectorAll('[data-countdown]').forEach(function (el) {
                const target = new Date(el.dataset.countdown).getTime();
                const diff = Math.max(0, Math.floor((target - Date.now()) / 1000));
                el.textContent = format(diff);
                if (diff <= 0 && !el.dataset.done) {
                    el.dataset.done = '1';
                    expired = true;
                }
            });
            if (expired) {
                setTimeout(function () { window.location.reload(); }, 2500);
            }
        }

        tick();
        setInterval(tick, 1000);
    })();
    </script>
@endsection
