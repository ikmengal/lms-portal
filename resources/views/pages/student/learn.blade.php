@extends('layouts.app')
@section('title', $lesson->title . ' — ' . $course->title)
@section('content')
    <div class="max-w-7xl mx-auto space-y-6 mt-2">
        {{-- Breadcrumb --}}
        <div class="flex items-center gap-2 text-sm text-gray-400 flex-wrap">
            <a href="{{ route('dashboard') }}" class="hover:text-primary-600 transition">Dashboard</a>
            <span>/</span>
            <a href="{{ route('courses.show', $course->slug) }}" class="hover:text-primary-600 transition">{{ Str::limit($course->title, 40) }}</a>
            <span>/</span>
            <span class="text-gray-600 font-medium truncate max-w-[200px]">{{ Str::limit($lesson->title, 40) }}</span>
        </div>

        <div class="grid lg:grid-cols-[1fr_360px] gap-6 items-start">
            {{-- Main player column --}}
            <div class="space-y-5 min-w-0">
                {{-- Video Player --}}
                <div class="bg-black rounded-xl overflow-hidden shadow-sm aspect-video grid place-items-center relative">
                    @if($lesson->video_url && preg_match('/(?:youtube\.com\/(?:watch\?v=|embed\/)|youtu\.be\/)([\w-]{11})/', $lesson->video_url, $m))
                        <iframe class="w-full h-full" src="https://www.youtube.com/embed/{{ $m[1] }}" title="{{ $lesson->title }}" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>
                    @elseif($lesson->video_url && preg_match('/vimeo\.com\/(\d+)/', $lesson->video_url, $m))
                        <iframe class="w-full h-full" src="https://player.vimeo.com/video/{{ $m[1] }}" title="{{ $lesson->title }}" frameborder="0" allow="autoplay; fullscreen; picture-in-picture" allowfullscreen></iframe>
                    @elseif($lesson->video_url)
                        <video controls class="w-full h-full bg-black" {{ $completedIds->has($lesson->id) ? '' : 'autoplay' }}>
                            <source src="{{ $lesson->video_url }}" />
                            Your browser does not support the video tag.
                        </video>
                    @else
                        <div class="text-center px-6 py-10 text-white/70">
                            <svg class="w-14 h-14 mx-auto mb-3 text-white/40" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.2"><path stroke-linecap="round" stroke-linejoin="round" d="M5.25 5.653c0-.856.917-1.398 1.667-.986l11.54 6.348a1.125 1.125 0 010 1.971l-11.54 6.347a1.125 1.125 0 01-1.667-.985V5.653z"/></svg>
                            <p class="text-sm font-medium text-white/80 mb-1">{{ $lesson->title }}</p>
                            <p class="text-xs text-white/50">Video will be available soon. You can still complete this lesson and continue.</p>
                        </div>
                    @endif
                </div>

                {{-- Lesson header + Mark complete + Prev/Next --}}
                <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-5 space-y-4">
                    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                        <div class="min-w-0">
                            <p class="text-xs font-semibold uppercase tracking-wide text-primary-700">Lesson {{ $currentIdx + 1 }} of {{ $totalLessons }} · {{ $lesson->duration_minutes }} min</p>
                            <h1 class="text-xl font-bold text-gray-900 mt-1 truncate">{{ $lesson->title }}</h1>
                        </div>
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
                    </div>
                    <div class="flex items-center justify-between gap-3 pt-1 border-t border-gray-50">
                        @if($prev)
                            <a href="{{ route('learn.show', [$course, $prev]) }}" class="inline-flex items-center gap-2 text-sm font-medium text-gray-500 hover:text-primary-600 transition min-w-0">
                                <svg class="w-4 h-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18"/></svg>
                                <span class="truncate">{{ Str::limit($prev->title, 34) }}</span>
                            </a>
                        @else
                            <span></span>
                        @endif
                        @if($next)
                            <a href="{{ route('learn.show', [$course, $next]) }}" class="inline-flex items-center gap-2 text-sm font-medium text-gray-500 hover:text-primary-600 transition min-w-0 sm:flex-row-reverse text-right">
                                <svg class="w-4 h-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5L21 12m0 0l-7.5 7.5M21 12H3"/></svg>
                                <span class="truncate">{{ Str::limit($next->title, 34) }}</span>
                            </a>
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
                            @if($lesson->description)
                                <p class="text-sm text-gray-600 leading-relaxed whitespace-pre-line">{{ $lesson->description }}</p>
                            @else
                                <p class="text-sm text-gray-500">In this lesson we cover <strong>{{ strtolower($lesson->title) }}</strong> as part of the <strong>{{ $course->title }}</strong> course. Estimated time: about {{ $lesson->duration_minutes }} minutes.</p>
                            @endif
                        </div>

                        {{-- Notes --}}
                        <div x-show="tab === 'notes'" style="display:none;" class="space-y-4">
                            <form method="POST" action="{{ route('learn.notes.store', [$course, $lesson]) }}" class="space-y-2">
                                @csrf
                                <textarea name="content" rows="3" required placeholder="Write your note for this lesson... e.g. key points, timestamps, questions"
                                    class="w-full px-4 py-2.5 border border-gray-200 rounded-lg text-sm focus:ring-2 focus:ring-primary-500 focus:border-primary-500"></textarea>
                                <div class="flex justify-end">
                                    <button type="submit" class="px-4 py-2 bg-primary-600 hover:bg-primary-700 text-white text-sm font-medium rounded-lg transition">Save Note</button>
                                </div>
                            </form>
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
                                        <a href="{{ route('learn.resources.download', $resource) }}" {{ $resource->external_url ? 'target=_blank rel=noopener' : '' }}
                                            class="shrink-0 inline-flex items-center gap-1.5 px-3.5 py-2 bg-primary-50 hover:bg-primary-100 text-primary-700 text-xs font-semibold rounded-lg transition">
                                            <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5M16.5 12L12 16.5m0 0L7.5 12m4.5 4.5V3"/></svg>
                                            Get
                                        </a>
                                    </li>
                                @empty
                                    <li class="text-sm text-gray-400 py-4 text-center">No resources attached to this lesson.</li>
                                @endforelse
                            </ul>
                        </div>

                        {{-- Q&A / Discussion --}}
                        <div x-show="tab === 'qa'" style="display:none;" class="space-y-5">
                            <form method="POST" action="{{ route('learn.questions.store', [$course, $lesson]) }}" class="space-y-2">
                                @csrf
                                <textarea name="body" rows="2" required placeholder="Ask a question about this lesson..."
                                    class="w-full px-4 py-2.5 border border-gray-200 rounded-lg text-sm focus:ring-2 focus:ring-primary-500 focus:border-primary-500"></textarea>
                                <div class="flex justify-end">
                                    <button type="submit" class="px-4 py-2 bg-primary-600 hover:bg-primary-700 text-white text-sm font-medium rounded-lg transition">Post Question</button>
                                </div>
                            </form>

                            <ul class="space-y-5">
                                @forelse($questions as $question)
                                    <li class="space-y-3">
                                        <div class="flex gap-3">
                                            <div class="w-8 h-8 rounded-full bg-primary-600 text-white grid place-items-center text-xs font-bold shrink-0">{{ strtoupper(substr($question->user?->name ?? 'U', 0, 1)) }}</div>
                                            <div class="min-w-0 flex-1">
                                                <div class="bg-gray-50 rounded-xl p-4">
                                                    <p class="text-sm text-gray-800 whitespace-pre-line">{{ $question->body }}</p>
                                                    <p class="text-xs text-gray-400 mt-2">{{ $question->user?->name ?? 'User' }} · {{ $question->created_at->diffForHumans() }}</p>
                                                </div>

                                                {{-- Replies --}}
                                                @if($question->replies->isNotEmpty())
                                                    <div class="mt-3 space-y-3 sm:ml-6">
                                                        @foreach($question->replies as $reply)
                                                            <div class="flex gap-2.5">
                                                                <div class="w-7 h-7 rounded-full bg-gray-300 text-gray-700 grid place-items-center text-[10px] font-bold shrink-0">{{ strtoupper(substr($reply->user?->name ?? 'U', 0, 1)) }}</div>
                                                                <div class="bg-white border border-gray-100 rounded-xl p-3 flex-1 min-w-0">
                                                                    <p class="text-sm text-gray-700 whitespace-pre-line">{{ $reply->body }}</p>
                                                                    <p class="text-xs text-gray-400 mt-1.5">{{ $reply->user?->name ?? 'User' }} · {{ $reply->created_at->diffForHumans() }}</p>
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
            <aside class="bg-white rounded-xl border border-gray-100 shadow-sm overflow-hidden lg:sticky lg:top-20 max-h-[calc(100vh-6rem)] flex flex-col">
                <div class="p-5 border-b border-gray-100">
                    <a href="{{ route('courses.show', $course->slug) }}" class="text-sm font-bold text-gray-900 hover:text-primary-600 transition line-clamp-2">{{ $course->title }}</a>
                    <div class="flex items-center justify-between mt-3 text-xs text-gray-400">
                        <span>{{ $doneCount }}/{{ $totalLessons }} lessons done</span>
                        <span class="font-semibold text-primary-600">{{ $totalLessons ? round($doneCount / $totalLessons * 100) : 0 }}%</span>
                    </div>
                    <div class="h-2 bg-gray-100 rounded-full mt-2 overflow-hidden">
                        <div class="h-full bg-gradient-to-r from-primary-500 to-secondary-500 rounded-full transition-all duration-500" style="width: {{ $totalLessons ? round($doneCount / $totalLessons * 100) : 0 }}%"></div>
                    </div>
                </div>

                {{-- Exams & Tests (unlocked only when course is completed) --}}
                @if($quizzes->isNotEmpty())
                    <div class="border-t border-gray-100">
                        <div class="px-4 py-3 flex items-center justify-between">
                            <p class="text-xs font-bold uppercase tracking-wider text-gray-500">Exams & Tests</p>
                            <span class="text-[11px] font-medium px-2 py-0.5 rounded-full {{ $doneCount >= $totalLessons ? 'bg-secondary-50 text-secondary-700' : 'bg-gray-100 text-gray-500' }}">
                                {{ $doneCount >= $totalLessons ? 'Unlocked' : 'Locked' }}
                            </span>
                        </div>

                        @if($doneCount < $totalLessons)
                            <div class="mx-4 mb-4 p-3.5 bg-gray-50 border border-gray-100 rounded-xl text-center">
                                <svg class="w-7 h-7 mx-auto text-gray-300" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M16.5 10.5V6.75a4.5 4.5 0 10-9 0v3.75m-.75 11.25h10.5a2.25 2.25 0 002.25-2.25v-6.75a2.25 2.25 0 00-2.25-2.25H6.75a2.25 2.25 0 00-2.25 2.25v6.75a2.25 2.25 0 002.25 2.25z"/></svg>
                                <p class="text-xs text-gray-500 mt-2 leading-relaxed">Complete all lessons to unlock the {{ $quizzes->count() === 1 ? str_replace('_', ' ', $quizzes->first()->type) : $quizzes->count() . ' exams, tests & assignments' }}.</p>
                                <p class="text-[11px] text-gray-400 mt-1">{{ $totalLessons - $doneCount }} lesson{{ $totalLessons - $doneCount > 1 ? 's' : '' }} remaining</p>
                            </div>
                        @else
                            <ul class="pb-3 space-y-1 px-2">
                                @foreach($quizzes as $quiz)
                                    @php
                                        $best = $bestScores[$quiz->id] ?? null;
                                        $attempts = $attemptCounts[$quiz->id] ?? 0;
                                        $passed = $best !== null && $best >= $quiz->passing_score;
                                    @endphp
                                    <li>
                                        <div class="flex items-center gap-2.5 px-2 py-2 mx-1 rounded-lg {{ $passed ? 'bg-secondary-50/60' : '' }}">
                                            <span class="w-8 h-8 rounded-lg grid place-items-center shrink-0 {{ $quiz->type === 'final_exam' ? 'bg-red-100 text-red-600' : ($quiz->type === 'assignment' ? 'bg-accent-100 text-accent-700' : 'bg-primary-100 text-primary-700') }}">
                                                @if($quiz->type === 'final_exam')
                                                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M4.26 10.147a60.438 60.438 0 00-.491 6.347A48.62 48.62 0 0112 20.904a48.62 48.62 0 018.232-4.41 60.46 60.46 0 00-.491-6.347m-15.482 0a50.636 50.636 0 00-2.658-.813A59.906 59.906 0 0112 3.493a59.903 59.903 0 0110.399 5.84c-.896.248-1.783.52-2.658.814m-15.482 0A50.717 50.717 0 0112 13.489a50.702 50.702 0 017.74-3.342M6.75 15a.75.75 0 100-1.5.75.75 0 000 1.5zm0 0v-3.675A55.378 55.378 0 0112 8.443m-7.007 11.55A5.981 5.981 0 006.75 15.75v-1.5"/></svg>
                                                @elseif($quiz->type === 'assignment')
                                                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z"/></svg>
                                                @else
                                                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12c0 1.268-.63 2.39-1.593 3.068a3.745 3.745 0 01-1.043 3.296 3.745 3.745 0 01-3.296 1.043A3.745 3.745 0 0112 21c-1.268 0-2.39-.63-3.068-1.593a3.746 3.746 0 01-3.296-1.043 3.745 3.745 0 01-1.043-3.296A3.745 3.745 0 013 12c0-1.268.63-2.39 1.593-3.068a3.746 3.746 0 011.043-3.296 3.746 3.746 0 013.296-1.043A3.746 3.746 0 0112 3c1.268 0 2.39.63 3.068 1.593a3.746 3.746 0 013.296 1.043 3.746 3.746 0 011.043 3.296A3.745 3.745 0 0121 12z"/></svg>
                                                @endif
                                            </span>
                                            <div class="min-w-0 flex-1">
                                                <p class="text-sm font-medium text-gray-800 truncate">{{ $quiz->title }}</p>
                                                <p class="text-[11px] text-gray-400">
                                                    @if($best !== null)
                                                        Score: <strong class="{{ $passed ? 'text-secondary-600' : 'text-red-500' }}">{{ number_format($best, 0) }}%</strong>
                                                        · {{ $attempts }} attempt{{ $attempts > 1 ? 's' : '' }}
                                                    @else
                                                        Not attempted yet
                                                    @endif
                                                </p>
                                            </div>
                                            @if($best !== null)
                                                <span class="shrink-0 px-2 py-1 rounded-full text-[10px] font-bold {{ $passed ? 'bg-secondary-100 text-secondary-700' : 'bg-red-100 text-red-600' }}">
                                                    {{ $passed ? 'PASSED' : 'FAILED' }}
                                                </span>
                                            @endif
                                            <a href="{{ route('courses.tests.show', [$course, $quiz]) }}"
                                                class="shrink-0 inline-flex items-center px-3 py-1.5 text-[11px] font-bold rounded-lg transition {{ $best !== null ? 'bg-white border border-gray-200 text-gray-700 hover:bg-gray-50' : 'bg-primary-600 text-white hover:bg-primary-700 shadow-sm' }}">
                                                {{ $best !== null ? 'Retake' : 'Start' }}
                                            </a>
                                        </div>
                                    </li>
                                @endforeach
                            </ul>
                        @endif
                    </div>
                @endif

                <nav class="overflow-y-auto divide-y divide-gray-50 flex-1">
                    @foreach($modules as $module)
                        <div x-data="{ open: {{ $module->lessons->contains('id', $lesson->id) ? 'true' : 'false' }} }">
                            <button type="button" @click="open = !open" class="w-full flex items-center justify-between gap-2 px-4 py-3 text-left hover:bg-gray-50 transition">
                                <div class="min-w-0">
                                    <p class="text-sm font-semibold text-gray-800 truncate">{{ $module->title }}</p>
                                    <p class="text-[11px] text-gray-400">{{ $module->lessons->count() }} lessons · {{ $module->lessons->sum('duration_minutes') }} min</p>
                                </div>
                                <svg :class="open && 'rotate-180'" class="w-4 h-4 text-gray-400 transition-transform shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 8.25l-7.5 7.5-7.5-7.5"/></svg>
                            </button>
                            <ul x-show="open" style="display:none;" class="pb-2">
                                @foreach($module->lessons as $l)
                                    <li>
                                        <a href="{{ route('learn.show', [$course, $l]) }}"
                                            class="flex items-center gap-2.5 px-4 py-2 pl-6 text-sm transition {{ $l->id === $lesson->id ? 'bg-primary-50 text-primary-700 font-semibold border-l-2 border-primary-600' : 'text-gray-600 hover:bg-gray-50 border-l-2 border-transparent' }}">
                                            @if($completedIds->has($l->id))
                                                <span class="w-5 h-5 rounded-full bg-secondary-500 grid place-items-center shrink-0">
                                                    <svg class="w-3 h-3 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3"><path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5"/></svg>
                                                </span>
                                            @else
                                                <span class="w-5 h-5 rounded-full border-2 border-gray-300 grid place-items-center shrink-0"></span>
                                            @endif
                                            <span class="truncate flex-1">{{ $l->title }}</span>
                                            <span class="text-[11px] text-gray-400 shrink-0">{{ $l->duration_minutes }}m</span>
                                        </a>
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                    @endforeach
                </nav>
            </aside>
        </div>
    </div>
@endsection
