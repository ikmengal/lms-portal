@extends('layouts.app')
@php
    $lang = content_lang();
    $availableLangs = array_values(array_unique(array_merge(
        [$course->language_code],
        array_keys($course->translations ?? [])
    )));
@endphp
@section('title', $course->localize('title', $lang))
@section('content')
    {{-- Course Hero --}}
    <div class="bg-gradient-to-r from-primary-900 to-primary-800 py-12">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center gap-2 text-sm text-primary-300 mb-4">
                <a href="{{ route('home') }}" class="hover:text-white transition">Home</a>
                <span>/</span>
                <a href="{{ route('courses.index') }}" class="hover:text-white transition">Courses</a>
                <span>/</span>
                <span class="text-white">{{ $course->localize('title', $lang) }}</span>
            </div>
        </div>
    </div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12" x-data="{ activeTab: 'overview', showAllModules: false }">

        <div class="flex flex-col lg:flex-row gap-10">

            {{-- Main Content --}}
            <div class="flex-1">

                {{-- Course Header --}}
                <div class="mb-8">
                    <div class="flex flex-wrap gap-2 mb-3">
                        @if($course->category)
                            <span class="px-3 py-1 bg-primary-100 text-primary-700 text-xs font-semibold rounded-full">{{ $course->category }}</span>
                        @endif
                        <span class="px-3 py-1 bg-gray-100 text-gray-600 text-xs font-semibold rounded-full">{{ $course->level }}</span>
                        @if($enrollment?->isCompleted())
                            <span class="px-3 py-1 bg-secondary-100 text-secondary-700 text-xs font-semibold rounded-full inline-flex items-center gap-1">
                                <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                Completed
                            </span>
                        @elseif($enrollment)
                            <span class="px-3 py-1 bg-accent-100 text-accent-700 text-xs font-semibold rounded-full">In Progress &middot; {{ $enrollment->progress }}%</span>
                        @endif
                    </div>
                    <div class="flex items-start justify-between gap-4 mb-4">
                        <h1 class="text-3xl md:text-4xl font-bold text-gray-900">{{ $course->localize('title', $lang) }}</h1>
                        @auth
                            <form method="POST" action="{{ route('courses.wishlist', $course) }}" class="shrink-0 pt-1">
                                @csrf
                                <button type="submit" title="{{ $wishlisted ? 'Remove from wishlist' : 'Save to wishlist' }}"
                                    class="p-2.5 rounded-full border transition {{ $wishlisted ? 'border-red-200 bg-red-50 text-red-500' : 'border-gray-200 bg-white text-gray-400 hover:text-red-500 hover:border-red-200' }}">
                                    <svg class="w-5 h-5 {{ $wishlisted ? 'fill-current' : '' }}" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M21 8.25c0-2.485-2.099-4.5-4.688-4.5-1.935 0-3.597 1.126-4.312 2.733-.715-1.607-2.377-2.733-4.313-2.733C5.1 3.75 3 5.765 3 8.25c0 7.22 9 12 9 12s9-4.78 9-12z"/></svg>
                                </button>
                            </form>
                        @endauth
                    </div>
                    @if($course->localize('subtitle', $lang))
                        <p class="text-base text-primary-700 font-medium mb-4">{{ $course->localize('subtitle', $lang) }}</p>
                    @endif
                    @if($course->localize('description', $lang))
                        <p class="text-lg text-gray-600 leading-relaxed mb-6">{{ $course->localize('description', $lang) }}</p>
                    @endif

                    @if(count($availableLangs) > 1)
                        <div class="flex flex-wrap items-center gap-2 mb-6">
                            <span class="text-xs font-semibold text-gray-500 uppercase tracking-wide">Language:</span>
                            <div class="flex flex-wrap items-center gap-1.5">
                                @foreach($availableLangs as $lc)
                                    @php
                                        $li = \language_info($lc);
                                    @endphp
                                    @if($li)
                                        @php
                                            $active = ($lang ?? $course->language_code) === $lc;
                                        @endphp
                                        <a href="{{ request()->fullUrlWithQuery(['lang' => $lc]) }}"
                                            class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full border text-xs font-semibold transition {{ $active ? 'bg-primary-600 border-primary-600 text-white' : 'bg-white border-gray-200 text-gray-600 hover:border-primary-300' }}">
                                            {{ $li['flag'] }} {{ $li['native'] }}
                                        </a>
                                    @endif
                                @endforeach
                            </div>
                            @if($lang)
                                <a href="{{ request()->fullUrlWithQuery(['lang' => null]) }}" class="text-xs text-gray-400 hover:text-primary-600 transition ml-1">Reset</a>
                            @endif
                        </div>
                    @endif

                    <div class="flex flex-wrap items-center gap-4 text-sm">
                        @if($reviewsCount)
                            <div class="flex items-center gap-1">
                                @for($i = 1; $i <= 5; $i++)
                                    <svg class="w-4 h-4 {{ $i <= round($avgRating) ? 'text-accent-400' : 'text-primary-700' }}" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                                @endfor
                                <span class="font-semibold text-white">{{ number_format($avgRating, 1) }}</span>
                                <span class="text-primary-300">({{ number_format($reviewsCount) }} reviews)</span>
                            </div>
                        @else
                            <span class="text-primary-300">New course</span>
                        @endif
                        <span class="text-primary-400">|</span>
                        <span class="text-primary-200">{{ number_format($studentsCount) }} students</span>
                        <span class="text-primary-400">|</span>
                        <span class="text-primary-200">{{ $course->duration_hours }} hours total</span>
                    </div>

                    <div class="flex items-center gap-3 mt-4">
                        <div class="w-10 h-10 bg-primary-700 rounded-full flex items-center justify-center text-white text-sm font-bold">{{ strtoupper(collect(explode(' ', $course->instructor->name ?? 'LP'))->map(fn ($p) => mb_substr($p, 0, 1))->take(2)->implode('')) }}</div>
                        <div>
                            <p class="text-white font-medium text-sm">{{ $course->instructor->name ?? 'LMS Portal' }}</p>
                            <p class="text-primary-300 text-xs">Course Instructor</p>
                        </div>
                    </div>
                </div>

                {{-- Course Thumbnail --}}
                <div class="aspect-video bg-gradient-to-br from-primary-100 to-primary-200 rounded-2xl mb-10 flex items-center justify-center relative overflow-hidden">
                    <div class="absolute inset-0 bg-gradient-to-br from-primary-900/80 to-primary-800/80"></div>
                    <button class="relative z-10 w-20 h-20 bg-white/20 backdrop-blur-sm rounded-full flex items-center justify-center hover:bg-white/30 transition group">
                        <svg class="w-8 h-8 text-white ml-1 group-hover:scale-110 transition-transform" fill="currentColor" viewBox="0 0 24 24"><path d="M8 5v14l11-7z"/></svg>
                    </button>
                </div>

                {{-- Tabs --}}
                <div class="border-b border-gray-200 mb-8">
                    <div class="flex gap-6">
                        <button @click="activeTab = 'overview'" :class="activeTab === 'overview' ? 'border-primary-600 text-primary-600' : 'border-transparent text-gray-500 hover:text-gray-700'" class="pb-3 text-sm font-semibold border-b-2 transition">Overview</button>
                        <button @click="activeTab = 'curriculum'" :class="activeTab === 'curriculum' ? 'border-primary-600 text-primary-600' : 'border-transparent text-gray-500 hover:text-gray-700'" class="pb-3 text-sm font-semibold border-b-2 transition">Curriculum</button>
                        <button @click="activeTab = 'tests'" :class="activeTab === 'tests' ? 'border-primary-600 text-primary-600' : 'border-transparent text-gray-500 hover:text-gray-700'" class="pb-3 text-sm font-semibold border-b-2 transition">Tests &amp; Exams</button>
                        <button @click="activeTab = 'instructor'" :class="activeTab === 'instructor' ? 'border-primary-600 text-primary-600' : 'border-transparent text-gray-500 hover:text-gray-700'" class="pb-3 text-sm font-semibold border-b-2 transition">Instructor</button>
                        <button @click="activeTab = 'reviews'" :class="activeTab === 'reviews' ? 'border-primary-600 text-primary-600' : 'border-transparent text-gray-500 hover:text-gray-700'" class="pb-3 text-sm font-semibold border-b-2 transition">Reviews</button>
                    </div>
                </div>

                {{-- Tab Content: Overview --}}
                <div x-show="activeTab === 'overview'" x-transition>
                    <h2 class="text-2xl font-bold text-gray-900 mb-4">About This Course</h2>
                    <div class="prose prose-gray max-w-none text-gray-600 leading-relaxed space-y-4">
                        @if($course->localize('description', $lang))
                            {!! nl2br(e($course->localize('description', $lang))) !!}
                        @else
                            <p>Enroll in this course to master the skills covered across its {{ $course->modules->count() }} modules, taught step by step by {{ $course->instructor->name ?? 'our expert instructor' }}.</p>
                        @endif

                        @if($course->modules->isNotEmpty())
                            <h3 class="text-lg font-bold text-gray-900 mt-6 mb-3">What You'll Learn</h3>
                            <div class="grid sm:grid-cols-2 gap-3">
                                @foreach($course->modules as $module)
                                    <div class="flex items-start gap-2">
                                        <svg class="w-5 h-5 text-secondary-500 mt-0.5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                                        <span class="text-sm text-gray-600">{{ \Illuminate\Support\Str::ucfirst(\Illuminate\Support\Str::lower($module->localize('title', $lang))) }}</span>
                                    </div>
                                @endforeach
                            </div>
                        @endif
                    </div>
                </div>

                {{-- Tab Content: Tests & Exams --}}
                <div x-show="activeTab === 'tests'" x-transition style="display:none;">
                    <h2 class="text-2xl font-bold text-gray-900 mb-2">Tests, Assignments &amp; Exams</h2>
                    @php $canTake = (bool) ($enrollment || auth()->user()?->hasRole('admin')); @endphp
                    <p class="text-sm text-gray-500 mb-6">
                        {{ $quizzes->count() }} assessment{{ $quizzes->count() === 1 ? '' : 's' }} in this course.
                        @unless($canTake) Enroll to unlock them. @endunless
                    </p>

                    @if($quizzes->isNotEmpty())
                        <div class="space-y-3">
                            @foreach($quizzes as $quiz)
                                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 border border-gray-100 rounded-xl p-5 hover:border-primary-200 transition bg-white shadow-sm">
                                    <div class="flex items-start gap-4 min-w-0">
                                        <span class="w-10 h-10 rounded-lg flex items-center justify-center shrink-0 {{ $quiz->type === 'final_exam' ? 'bg-red-100 text-red-600' : ($quiz->type === 'assignment' ? 'bg-accent-100 text-accent-700' : 'bg-primary-100 text-primary-700') }}">
                                            @if($quiz->type === 'final_exam')
                                                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M4.26 10.147a60.438 60.438 0 00-.491 6.347A48.62 48.62 0 0112 20.904a48.62 48.62 0 018.232-4.41 60.46 60.46 0 00-.491-6.347m-15.482 0a50.636 50.636 0 00-2.658-.813A59.906 59.906 0 0112 3.493a59.903 59.903 0 0110.399 5.84c-.896.248-1.783.52-2.658.814m-15.482 0A50.717 50.717 0 0112 13.489a50.702 50.702 0 017.74-3.342M6.75 15a.75.75 0 100-1.5.75.75 0 000 1.5zm0 0v-3.675A55.378 55.378 0 0112 8.443m-7.007 11.55A5.981 5.981 0 006.75 15.75v-1.5"/></svg>
                                            @elseif($quiz->type === 'assignment')
                                                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z"/></svg>
                                            @else
                                                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12c0 1.268-.63 2.39-1.593 3.068a3.745 3.745 0 01-1.043 3.296 3.745 3.745 0 01-3.296 1.043A3.745 3.745 0 0112 21c-1.268 0-2.39-.63-3.068-1.593a3.746 3.746 0 01-3.296-1.043 3.745 3.745 0 01-1.043-3.296A3.745 3.745 0 013 12c0-1.268.63-2.39 1.593-3.068a3.746 3.746 0 011.043-3.296 3.746 3.746 0 013.296-1.043A3.746 3.746 0 0112 3c1.268 0 2.39.63 3.068 1.593a3.746 3.746 0 013.296 1.043 3.746 3.746 0 011.043 3.296A3.745 3.745 0 0121 12z"/></svg>
                                            @endif
                                        </span>
                                        <div class="min-w-0">
                                            <p class="text-sm font-semibold text-gray-900">{{ $quiz->title }}
                                                <span class="ml-1 px-2 py-0.5 rounded-full text-[10px] font-medium align-middle {{ $quiz->type === 'final_exam' ? 'bg-red-100 text-red-700' : ($quiz->type === 'assignment' ? 'bg-accent-100 text-accent-800' : 'bg-primary-100 text-primary-800') }}">{{ str_replace('_', ' ', ucfirst($quiz->type)) }}</span>
                                            </p>
                                            <p class="text-xs text-gray-400 mt-1">
                                                {{ $quiz->questions_count }} questions · Pass ≥ {{ $quiz->passing_score }}% · {{ $quiz->duration_minutes ? $quiz->duration_minutes . ' min' : 'No time limit' }}
                                                @if(isset($bestScores[$quiz->id]))
                                                    · {{ $attemptCounts[$quiz->id] ?? 1 }} attempt{{ ($attemptCounts[$quiz->id] ?? 1) > 1 ? 's' : '' }}
                                                @endif
                                            </p>
                                            @if(isset($bestScores[$quiz->id]))
                                                @php
                                                    $best = $bestScores[$quiz->id];
                                                    $passed = $best >= $quiz->passing_score;
                                                @endphp
                                                <div class="mt-2.5 flex items-center gap-3 max-w-sm">
                                                    <span class="text-[11px] font-medium text-gray-500 shrink-0">Your best:</span>
                                                    <div class="flex-1 h-1.5 bg-gray-100 rounded-full overflow-hidden">
                                                        <div class="h-full rounded-full transition-all duration-500 {{ $passed ? 'bg-secondary-500' : 'bg-red-400' }}" style="width: {{ min(100, round($best)) }}%"></div>
                                                    </div>
                                                    <strong class="text-xs {{ $passed ? 'text-secondary-600' : 'text-red-500' }}">{{ number_format($best, 0) }}%</strong>
                                                    <span class="shrink-0 px-2 py-0.5 rounded-full text-[10px] font-bold {{ $passed ? 'bg-secondary-100 text-secondary-700' : 'bg-red-100 text-red-600' }}">{{ $passed ? 'PASSED' : 'FAILED' }}</span>
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                    @if($canTake)
                                        <a href="{{ route('courses.tests.show', [$course, $quiz]) }}"
                                            class="shrink-0 self-end sm:self-auto inline-flex items-center gap-2 px-5 py-2.5 text-sm font-semibold rounded-lg transition {{ isset($bestScores[$quiz->id]) ? 'bg-white border border-gray-200 text-gray-700 hover:bg-gray-50' : 'bg-primary-600 text-white hover:bg-primary-700 shadow-sm' }}">
                                            {{ isset($bestScores[$quiz->id]) ? 'Retake' : 'Start Test' }}
                                        </a>
                                    @else
                                        <span class="shrink-0 self-end sm:self-auto inline-flex items-center gap-2 px-5 py-2.5 text-sm font-medium rounded-lg bg-gray-100 text-gray-400 cursor-not-allowed">
                                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M16.5 10.5V6.75a4.5 4.5 0 10-9 0v3.75m-.75 11.25h10.5a2.25 2.25 0 002.25-2.25v-6.75a2.25 2.25 0 00-2.25-2.25H6.75a2.25 2.25 0 00-2.25 2.25v6.75a2.25 2.25 0 002.25 2.25z"/></svg>
                                            Locked
                                        </span>
                                    @endif
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="p-8 bg-gray-50 rounded-xl text-center text-gray-500 text-sm">No tests have been added to this course yet.</div>
                    @endif
                </div>

                {{-- Tab Content: Curriculum --}}
                <div x-show="activeTab === 'curriculum'" x-transition style="display:none;">
                    <h2 class="text-2xl font-bold text-gray-900 mb-4">Course Curriculum</h2>
                    @php
                        $fmtHours = intdiv($totalMinutes, 60);
                        $fmtMins = $totalMinutes % 60;
                    @endphp
                    <p class="text-sm text-gray-500 mb-6">{{ $course->modules->count() }} Modules &middot; {{ $totalLectures }} Lectures &middot; @if($fmtHours){{ $fmtHours }}h @endif @if($fmtMins){{ $fmtMins }}m @endif Total</p>

                    @if($course->modules->isNotEmpty())
                        <div class="space-y-3">
                            @foreach($course->modules as $i => $module)
                                @php
                                    $moduleMinutes = $module->lessons->sum('duration_minutes');
                                    $mHours = intdiv($moduleMinutes, 60);
                                    $mMins = $moduleMinutes % 60;
                                    $moduleLocked = $module->unlocks_at && $module->unlocks_at->isFuture();
                                @endphp
                                <div class="border border-gray-200 rounded-xl overflow-hidden" x-data="{ open: {{ $i === 0 ? 'true' : 'false' }} }">
                                    <button @click="open = !open" class="w-full flex items-center justify-between px-6 py-4 bg-gray-50 hover:bg-gray-100 transition text-left">
                                        <div>
                                            <div class="flex items-center gap-2">
                                                <h3 class="font-semibold {{ $moduleLocked ? 'text-gray-500' : 'text-gray-900' }}">Module {{ $i + 1 }}: {{ $module->localize('title', $lang) }}</h3>
                                                @if($moduleLocked)
                                                    <span class="inline-flex items-center gap-1 px-2 py-0.5 bg-violet-50 text-violet-700 text-[11px] font-semibold rounded-md whitespace-nowrap">
                                                        <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 1a4.5 4.5 0 00-4.5 4.5V9H5a2 2 0 00-2 2v6a2 2 0 002 2h10a2 2 0 002-2v-6a2 2 0 00-2-2h-.5V5.5A4.5 4.5 0 0010 1zm3 8V5.5a3 3 0 10-6 0V9h6z" clip-rule="evenodd"/></svg>
                                                        Unlocks {{ $module->unlocks_at->format('M j') }}
                                                    </span>
                                                @endif
                                            </div>
                                            <p class="text-xs text-gray-500 mt-1">{{ $module->lessons->count() }} lectures &middot; @if($mHours){{ $mHours }}h @endif @if($mMins){{ $mMins }}m @endif</p>
                                        </div>
                                        <svg class="w-5 h-5 text-gray-400 transition" :class="open && 'rotate-180'" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/></svg>
                                    </button>
                                    <div x-show="open" x-transition class="px-6 py-4 space-y-3" style="display:none;">
                                        @forelse($module->lessons as $li => $lesson)
                                            @php $lessonLocked = ($module->unlocks_at && $module->unlocks_at->isFuture()) || ($lesson->unlocks_at && $lesson->unlocks_at->isFuture()); @endphp
                                            <div class="flex items-center justify-between">
                                                <div class="flex items-center gap-3">
                                                    <span class="w-6 h-6 {{ $lessonLocked ? 'bg-gray-100 text-gray-400' : 'bg-primary-50 text-primary-600' }} rounded-full flex items-center justify-center text-xs font-semibold shrink-0">{{ $li + 1 }}</span>
                                                    <span class="text-sm {{ $lessonLocked ? 'text-gray-400' : 'text-gray-700' }}">{{ $lesson->localize('title', $lang) }}</span>
                                                    @if($lessonLocked)
                                                        <svg class="w-3.5 h-3.5 text-gray-400" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 1a4.5 4.5 0 00-4.5 4.5V9H5a2 2 0 00-2 2v6a2 2 0 002 2h10a2 2 0 002-2v-6a2 2 0 00-2-2h-.5V5.5A4.5 4.5 0 0010 1zm3 8V5.5a3 3 0 10-6 0V9h6z" clip-rule="evenodd"/></svg>
                                                    @endif
                                                </div>
                                                <span class="text-xs text-gray-400 shrink-0">{{ $lesson->duration_minutes }} min</span>
                                            </div>
                                        @empty
                                            <p class="text-sm text-gray-400">Lessons coming soon.</p>
                                        @endforelse
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="p-8 bg-gray-50 rounded-xl text-center text-gray-500 text-sm">Curriculum for this course is being prepared.</div>
                    @endif
                </div>

                {{-- Tab Content: Instructor --}}
                <div x-show="activeTab === 'instructor'" x-transition style="display:none;">
                    <div class="flex items-start gap-6 p-6 bg-gray-50 rounded-2xl">
                        <div class="w-20 h-20 bg-primary-600 rounded-2xl flex items-center justify-center text-white text-2xl font-bold shrink-0">{{ strtoupper(collect(explode(' ', $course->instructor->name ?? 'LP'))->map(fn ($p) => mb_substr($p, 0, 1))->take(2)->implode('')) }}</div>
                        <div>
                            <h3 class="text-xl font-bold text-gray-900">{{ $course->instructor->name ?? 'LMS Portal Team' }}</h3>
                            <p class="text-sm text-gray-500 mb-3">Course Instructor at LMS Portal</p>
                            <p class="text-sm text-gray-600 leading-relaxed mb-4">{{ $course->instructor->bio ?? 'This instructor has not added a bio yet, but their courses speak for themselves. Enroll to start learning today.' }}</p>
                            <div class="flex items-center gap-6 text-sm text-gray-500">
                                <span class="flex items-center gap-1">
                                    <svg class="w-4 h-4 text-accent-400" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                                    {{ number_format($avgRating, 1) }} Rating
                                </span>
                                <span>{{ number_format($instructorStudents) }} Students</span>
                                <span>{{ $instructorCourses }} Course{{ $instructorCourses === 1 ? '' : 's' }}</span>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Tab Content: Reviews --}}
                <div x-show="activeTab === 'reviews'" x-transition style="display:none;">
                    @if($reviewsCount)
                        <div class="flex flex-col md:flex-row gap-8 mb-8">
                            <div class="text-center md:text-left">
                                <div class="text-6xl font-bold text-gray-900 mb-2">{{ number_format($avgRating, 1) }}</div>
                                <div class="flex items-center justify-center md:justify-start gap-0.5 mb-2">
                                    @for($i = 1; $i <= 5; $i++)
                                        <svg class="w-5 h-5 {{ $i <= round($avgRating) ? 'text-accent-400' : 'text-gray-200' }}" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                                    @endfor
                                </div>
                                <p class="text-sm text-gray-500">{{ number_format($reviewsCount) }} reviews</p>
                            </div>
                            <div class="flex-1 space-y-2">
                                @foreach($ratingDistribution as $stars => $bar)
                                    <div class="flex items-center gap-3">
                                        <span class="text-sm text-gray-500 w-10">{{ $stars }} star</span>
                                        <div class="flex-1 h-2 bg-gray-200 rounded-full overflow-hidden">
                                            <div class="h-full bg-accent-400 rounded-full" style="width: {{ $bar['pct'] }}%"></div>
                                        </div>
                                        <span class="text-sm text-gray-500 w-10">{{ $bar['pct'] }}%</span>
                                    </div>
                                @endforeach
                            </div>
                        </div>

                        <div class="space-y-6">
                            @foreach($course->reviews as $review)
                                <div class="p-6 bg-gray-50 rounded-xl">
                                    <div class="flex items-center justify-between mb-3">
                                        <div class="flex items-center gap-3">
                                            <div class="w-10 h-10 bg-primary-600 rounded-full flex items-center justify-center text-white text-sm font-bold">{{ strtoupper(collect(explode(' ', $review->user->name))->map(fn ($p) => mb_substr($p, 0, 1))->take(2)->implode('')) }}</div>
                                            <div>
                                                <p class="font-semibold text-gray-900 text-sm">{{ $review->user->name }}</p>
                                                <div class="flex items-center gap-1">
                                                    @for($i = 1; $i <= 5; $i++)
                                                        <svg class="w-3 h-3 {{ $i <= $review->rating ? 'text-accent-400' : 'text-gray-200' }}" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                                                    @endfor
                                                </div>
                                            </div>
                                        </div>
                                        <span class="text-xs text-gray-400">{{ $review->created_at->diffForHumans() }}</span>
                                    </div>
                                    <p class="text-sm text-gray-600 leading-relaxed">{{ $review->comment }}</p>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="p-12 bg-gray-50 rounded-xl text-center">
                            <svg class="w-14 h-14 text-gray-300 mx-auto mb-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1"><path stroke-linecap="round" stroke-linejoin="round" d="M11.48 3.499a.562.562 0 011.04 0l2.125 5.111a.563.563 0 00.475.345l5.518.442c.499.04.701.663.321.988l-4.204 3.602a.563.563 0 00-.182.557l1.285 5.385a.562.562 0 01-.84.61l-4.725-2.885a.563.563 0 00-.586 0L6.982 20.54a.562.562 0 01-.84-.61l1.285-5.386a.562.562 0 00-.182-.557l-4.204-3.602a.563.563 0 01.321-.988l5.518-.442a.563.563 0 00.475-.345L11.48 3.5z"/></svg>
                            <h3 class="text-lg font-medium text-gray-900 mb-2">No reviews yet</h3>
                            <p class="text-gray-500">Be the first to review this course after enrolling.</p>
                        </div>
                    @endif
                </div>
            </div>

            {{-- Sidebar --}}
            <div class="lg:w-96 shrink-0">
                <div class="sticky top-24 bg-white border border-gray-200 rounded-2xl shadow-lg overflow-hidden">
                    @if($enrollment)
                        {{-- Enrollment Status --}}
                        <div class="p-6 border-b border-gray-100">
                            @if($enrollment->isCompleted())
                                <div class="flex items-center gap-3 mb-4">
                                    <div class="w-11 h-11 bg-secondary-100 rounded-xl flex items-center justify-center shrink-0">
                                        <svg class="w-6 h-6 text-secondary-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                    </div>
                                    <div>
                                        <p class="font-bold text-gray-900">Course Completed</p>
                                        <p class="text-xs text-gray-500">Finished {{ ($enrollment->completed_at ?? $enrollment->updated_at)->diffForHumans() }}</p>
                                    </div>
                                </div>

                                {{-- Progress --}}
                                <div class="flex items-center gap-3 mb-5">
                                    <div class="flex-1 bg-gray-100 rounded-full h-2.5">
                                        <div class="bg-secondary-500 h-2.5 rounded-full" style="width: {{ $enrollment->progress }}%"></div>
                                    </div>
                                    <span class="text-sm font-semibold text-secondary-600">{{ $enrollment->progress }}%</span>
                                </div>

                                @if($certificate)
                                    <a href="{{ route('certificates.show', $certificate) }}" target="_blank" class="w-full inline-flex items-center justify-center gap-2 py-3.5 bg-accent-500 hover:bg-accent-600 text-white font-bold rounded-xl transition shadow-lg shadow-accent-500/25 mb-3">
                                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M4.26 10.147a60.436 60.436 0 00-.491 6.347A48.627 48.627 0 0112 20.904a48.627 48.627 0 018.232-4.41 60.46 60.46 0 00-.491-6.347m-15.482 0a50.57 50.57 0 00-2.658-.813A59.905 59.905 0 0112 3.493a59.902 59.902 0 0110.399 5.84c-.896.248-1.783.52-2.658.814m-15.482 0A50.697 50.697 0 0112 13.489a50.702 50.702 0 017.74-3.336M6.75 15a.75.75 0 100-1.5.75.75 0 000 1.5zm0 0v-3.675A55.378 55.378 0 0112 8.443m-7.007 11.55A5.981 5.981 0 006.75 15.75v-1.5"/></svg>
                                        View Your Certificate
                                    </a>
                                    <div class="flex items-center justify-center gap-4 mb-4 text-sm">
                                        <a href="{{ route('certificates.download', $certificate) }}" class="inline-flex items-center gap-1.5 font-medium text-primary-600 hover:text-primary-700 transition">
                                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5M16.5 12L12 16.5m0 0L7.5 12m4.5 4.5V3"/></svg>
                                            Download PDF
                                        </a>
                                        <a href="{{ route('certificates.verify', $certificate->code) }}" target="_blank" class="inline-flex items-center gap-1.5 font-medium text-primary-600 hover:text-primary-700 underline decoration-dotted transition">
                                            Verify certificate
                                        </a>
                                    </div>
                                @endif

                                <button class="w-full py-3.5 border-2 border-primary-200 text-primary-700 font-semibold rounded-xl hover:bg-primary-50 transition inline-flex items-center justify-center gap-2">
                                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M16.023 9.348h4.992v-.001M2.985 19.644v-4.992m0 0h4.992m-4.993 0l3.181 3.183a8.25 8.25 0 0013.803-3.7M4.031 9.865a8.25 8.25 0 0113.803-3.7l3.181 3.182m0-4.991v4.99"/></svg>
                                    Review Course Content
                                </button>
                                <p class="text-center text-xs text-gray-400 mt-3">You can revisit any lesson anytime.</p>
                            @else
                                <p class="text-sm font-semibold text-gray-900 mb-1">Your Progress</p>
                                <p class="text-xs text-gray-500 mb-4">Last accessed {{ $enrollment->updated_at->diffForHumans() }}</p>

                                <div class="flex items-center gap-3 mb-5">
                                    <div class="flex-1 bg-gray-100 rounded-full h-2.5">
                                        <div class="bg-primary-500 h-2.5 rounded-full transition-all duration-500" style="width: {{ $enrollment->progress }}%"></div>
                                    </div>
                                    <span class="text-sm font-semibold text-primary-600">{{ $enrollment->progress }}%</span>
                                </div>

                                <button class="w-full py-3.5 bg-primary-600 hover:bg-primary-700 text-white font-bold rounded-xl transition shadow-lg shadow-primary-600/25 mb-3 inline-flex items-center justify-center gap-2">
                                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M8 5v14l11-7z"/></svg>
                                    Continue Learning
                                </button>
                                <p class="text-center text-xs text-gray-400 mt-3">Complete all lessons to earn your certificate.</p>
                            @endif
                        </div>
                    @elseif($comingSoon)
                        {{-- Coming Soon --}}
                        <div class="p-6 border-b border-gray-100">
                            <div class="flex items-baseline gap-3 mb-4">
                                <span class="text-3xl font-bold text-gray-900">${{ number_format($course->price, 2) }}</span>
                            </div>
                            <div class="rounded-xl bg-violet-50 border border-violet-100 p-5 mb-4">
                                <div class="flex items-center justify-center gap-2 mb-2">
                                    <svg class="w-6 h-6 text-violet-600" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 001.415-1.415L11 9.586V6z" clip-rule="evenodd"/></svg>
                                    <span class="font-bold text-violet-700">Coming Soon</span>
                                </div>
                                <p class="text-sm text-violet-700 font-semibold text-center">Enrollment opens {{ $unlocksAt->format('M j, Y') }}</p>
                                <p class="text-xs text-violet-500 mt-1 text-center">All modules and lessons unlock automatically on this date.</p>
                            </div>
                            @auth
                                <form method="POST" action="{{ route('courses.wishlist', $course) }}" class="mt-3">
                                    @csrf
                                    <button type="submit" class="w-full py-3.5 border-2 border-gray-200 text-gray-700 font-semibold rounded-xl hover:bg-gray-50 transition">
                                        @if($wishlisted) Remove from Wishlist @else Add to Wishlist @endif
                                    </button>
                                </form>
                            @endauth
                            <p class="text-center text-xs text-gray-400 mt-3">30-Day Money-Back Guarantee</p>
                        </div>
                    @else
                        {{-- Price --}}
                        <div class="p-6 border-b border-gray-100">
                            <div class="flex items-baseline gap-3 mb-1">
                                <span class="text-3xl font-bold text-gray-900">${{ number_format($course->price, 2) }}</span>
                                @if($course->price > 0)
                                    <span class="text-lg text-gray-400 line-through">${{ number_format($course->price * 4, 2) }}</span>
                                    <span class="text-sm font-semibold text-secondary-600 bg-secondary-50 px-2 py-0.5 rounded">75% Off</span>
                                @endif
                            </div>
                            <p class="text-sm text-gray-500 mb-4">3 days left at this price!</p>

                            @auth
                                @if($course->price > 0)
                                    <a href="{{ route('checkout.course', $course) }}"
                                        class="w-full block text-center py-3.5 bg-primary-600 hover:bg-primary-700 text-white font-bold rounded-xl transition shadow-lg shadow-primary-600/25 mb-3">
                                        Enroll Now
                                    </a>
                                    <form method="POST" action="{{ route('cart.add', $course) }}">
                                        @csrf
                                        <button type="submit" class="w-full py-3.5 border-2 border-gray-200 text-gray-700 font-semibold rounded-xl hover:bg-gray-50 transition inline-flex items-center justify-center gap-2">
                                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 3h1.386c.51 0 .955.343 1.087.835l.383 1.437M7.5 14.25a3 3 0 00-3 3h15.75m-12.75-3h11.218c1.121-2.3 2.1-4.684 2.924-7.138a60.114 60.114 0 00-16.536-1.84M7.5 14.25L5.106 5.272M6 20.25a.75.75 0 11-1.5 0 .75.75 0 011.5 0zm12.75 0a.75.75 0 11-1.5 0 .75.75 0 011.5 0z"/></svg>
                                            Add to Cart
                                        </button>
                                    </form>
                                @else
                                    <form method="POST" action="{{ route('courses.enroll.free', $course) }}" class="mb-3">
                                        @csrf
                                        <button type="submit" class="w-full py-3.5 bg-primary-600 hover:bg-primary-700 text-white font-bold rounded-xl transition shadow-lg shadow-primary-600/25">
                                            Enroll Now — It's Free!
                                        </button>
                                    </form>
                                @endif
                            @endauth

                            @guest
                                <a href="{{ route('checkout.course', $course) }}"
                                    class="w-full block text-center py-3.5 bg-primary-600 hover:bg-primary-700 text-white font-bold rounded-xl transition shadow-lg shadow-primary-600/25 mb-3">
                                    Enroll Now
                                </a>
                                <a href="{{ route('checkout.course', $course) }}"
                                    class="w-full block text-center py-3.5 border-2 border-gray-200 text-gray-700 font-semibold rounded-xl hover:bg-gray-50 transition">
                                    Add to Cart
                                </a>
                            @endguest

                            <p class="text-center text-xs text-gray-400 mt-3">30-Day Money-Back Guarantee</p>
                        </div>
                    @endif

                    {{-- Course Info --}}
                    <div class="p-6 space-y-4">
                        <h3 class="font-semibold text-gray-900">This course includes:</h3>
                        @foreach([
                            ['icon' => 'M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z', 'text' => $course->duration_hours . ' hours on-demand video'],
                            ['icon' => 'M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z', 'text' => '15 downloadable resources'],
                            ['icon' => 'M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z', 'text' => '12 hands-on projects'],
                            ['icon' => 'M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z', 'text' => '12 live sessions'],
                            ['icon' => 'M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z', 'text' => 'Access on mobile & TV'],
                            ['icon' => 'M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z', 'text' => 'Certificate of completion'],
                        ] as $item)
                            <div class="flex items-center gap-3">
                                <svg class="w-5 h-5 text-gray-400 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="{{ $item['icon'] }}"/></svg>
                                <span class="text-sm text-gray-600">{{ $item['text'] }}</span>
                            </div>
                        @endforeach
                    </div>

                    {{-- Share --}}
                    <div class="px-6 pb-6">
                        <button class="w-full py-2.5 text-sm font-medium text-gray-500 hover:text-primary-600 transition flex items-center justify-center gap-2">
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M8.684 13.342C8.886 12.938 9 12.482 9 12c0-.482-.114-.938-.316-1.342m0 2.684a3 3 0 110-2.684m0 2.684l6.632 3.316m-6.632-6l6.632-3.316m0 0a3 3 0 105.367-2.684 3 3 0 00-5.367 2.684zm0 9.316a3 3 0 105.368 2.684 3 3 0 00-5.368-2.684z"/></svg>
                            Share This Course
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
