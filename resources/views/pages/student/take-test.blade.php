@extends('layouts.app')
@section('title', $quiz->title)
@section('content')
    <div class="max-w-3xl mx-auto space-y-6">
        {{-- Header --}}
        <div class="flex items-center gap-3">
            <a href="{{ url()->previous() }}" class="p-2 text-gray-400 hover:text-gray-700 hover:bg-gray-100 rounded-lg transition">
                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18"/></svg>
            </a>
            <div class="min-w-0">
                <p class="text-xs text-primary-700 font-semibold uppercase tracking-wide">{{ str_replace('_', ' ', ucfirst($quiz->type)) }} · {{ $course->title }}</p>
                <h1 class="text-2xl font-bold text-gray-900 truncate">{{ $quiz->title }}</h1>
            </div>
        </div>

        @php
            $results = session('attempt_results');
        @endphp

        {{-- Result banner --}}
        @if($results)
            <div class="rounded-xl border shadow-sm overflow-hidden {{ $results['passed'] ? 'border-secondary-200 bg-secondary-50/60' : 'border-red-200 bg-red-50/60' }}">
                <div class="p-6 flex items-center gap-4 flex-wrap sm:flex-nowrap">
                    @php
                        $ring = $results['score'] >= 80 ? '#16a34a' : ($results['score'] >= 40 ? '#f59e0b' : '#dc2626');
                        $deg = (int) round($results['score'] * 3.6);
                    @endphp
                    <div class="w-20 h-20 rounded-full grid place-items-center shrink-0"
                        style="background: conic-gradient({{ $ring }} {{ $deg }}deg, #e5e7eb {{ $deg }}deg);">
                        <div class="w-14 h-14 bg-white rounded-full grid place-items-center">
                            <span class="text-sm font-bold text-gray-900">{{ number_format($results['score'], 0) }}%</span>
                        </div>
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-lg font-bold {{ $results['passed'] ? 'text-secondary-700' : 'text-red-700' }}">
                            {{ $results['passed'] ? 'Passed! Well done.' : 'Not passed this time.' }}
                        </p>
                        <p class="text-sm text-gray-500 mt-0.5">You earned {{ $results['earned'] }} of {{ $results['total'] }} points · passing score is {{ $quiz->passing_score }}%.</p>
                    </div>
                </div>

                {{-- Per-question breakdown --}}
                <details class="border-t {{ $results['passed'] ? 'border-secondary-200' : 'border-red-200' }}">
                    <summary class="px-6 py-3 text-sm font-medium text-gray-600 cursor-pointer hover:bg-black/[0.03] transition">Review your answers</summary>
                    <ul class="px-6 pb-4 space-y-2">
                        @foreach($quiz->questions as $q)
                            @php
                                $detail = $results['details'][$q->id] ?? null;
                                if (!$detail) { continue; }
                            @endphp
                            <li class="text-sm flex items-start gap-2">
                                @if($detail['is_correct'])
                                    <svg class="w-4 h-4 text-secondary-600 mt-0.5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                @else
                                    <svg class="w-4 h-4 text-red-500 mt-0.5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                                @endif
                                <span>
                                    <span class="font-medium text-gray-800">{{ $q->question }}</span>
                                    @unless($detail['is_correct'])
                                        — correct answer:
                                        <em class="not-italic font-medium text-secondary-700">{{ optional($q->options->firstWhere('id', $detail['correct_ids'][0] ?? null))->option_text ?? '—' }}</em>
                                    @endunless
                                </span>
                            </li>
                        @endforeach
                    </ul>
                </details>
            </div>
        @endif

        {{-- Best attempt summary --}}
        @if($bestAttempt && !$results)
            <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-5 flex items-center justify-between gap-4">
                <div>
                    <p class="text-sm font-semibold text-gray-900">Your best attempt</p>
                    <p class="text-xs text-gray-400 mt-0.5">
                        {{ number_format($bestAttempt->score, 1) }}%
                        · {{ $bestAttempt->passed ? '<span class="text-secondary-600 font-medium">Passed</span>' : '<span class="text-red-500 font-medium">Failed</span>' }}
                        · {{ $bestAttempt->completed_at?->diffForHumans() }}
                    </p>
                </div>
                <span class="px-3 py-1 rounded-full text-xs font-semibold {{ $bestAttempt->passed ? 'bg-secondary-100 text-secondary-700' : 'bg-red-100 text-red-600' }}">
                    {{ $bestAttempt->passed ? 'PASSED' : 'FAILED' }}
                </span>
            </div>
        @endif

        {{-- Quiz info --}}
        <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-6">
            @if($quiz->description)
                <p class="text-sm text-gray-600 mb-4 leading-relaxed">{{ $quiz->description }}</p>
            @endif
            <div class="flex flex-wrap gap-x-6 gap-y-2 text-sm">
                <span class="inline-flex items-center gap-1.5 text-gray-500">
                    <svg class="w-4 h-4 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9.879 7.519c1.171-1.025 3.071-1.025 4.242 0 1.172 1.025 1.172 2.687 0 3.712-.203.179-.43.326-.67.442-.745.361-1.45.999-1.45 1.827v.75M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-9 5.25h.008v.008H12v-.008z"/></svg>
                    {{ $quiz->questions->count() }} questions
                </span>
                <span class="inline-flex items-center gap-1.5 text-gray-500">
                    <svg class="w-4 h-4 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    {{ $quiz->duration_minutes ? $quiz->duration_minutes . ' minutes suggested' : 'No time limit' }}
                </span>
                <span class="inline-flex items-center gap-1.5 text-gray-500">
                    <svg class="w-4 h-4 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12c0 1.268-.63 2.39-1.593 3.068a3.745 3.745 0 01-1.043 3.296 3.745 3.745 0 01-3.296 1.043A3.745 3.745 0 0112 21c-1.268 0-2.39-.63-3.068-1.593a3.746 3.746 0 01-3.296-1.043 3.745 3.745 0 01-1.043-3.296A3.745 3.745 0 013 12c0-1.268.63-2.39 1.593-3.068a3.746 3.746 0 011.043-3.296 3.746 3.746 0 013.296-1.043A3.746 3.746 0 0112 3c1.268 0 2.39.63 3.068 1.593a3.746 3.746 0 013.296 1.043 3.746 3.746 0 011.043 3.296A3.745 3.745 0 0121 12z"/></svg>
                    Pass ≥ {{ $quiz->passing_score }}%
                </span>
                @if($lastAttempt && !$results)
                    <span class="inline-flex items-center gap-1.5 text-accent-700 font-medium">Retake available</span>
                @endif
            </div>
        </div>

        {{-- The test --}}
        <form method="POST" action="{{ route('courses.tests.submit', [$course, $quiz]) }}"
            data-confirm="Submit your answers? You'll get your score right away."
            data-confirm-title="Submit test?"
            data-confirm-button="Yes, submit"
            data-confirm-icon="question">
            @csrf
            <div class="space-y-4">
                @foreach($quiz->questions as $qi => $question)
                    <fieldset class="bg-white rounded-xl border border-gray-100 shadow-sm p-6">
                        <legend class="sr-only">Question {{ $qi + 1 }}</legend>
                        <div class="flex items-start gap-3 mb-4">
                            <span class="w-7 h-7 bg-primary-600 text-white rounded-lg flex items-center justify-center text-xs font-bold shrink-0">{{ $qi + 1 }}</span>
                            <p class="text-sm font-semibold text-gray-900 pt-1">{{ $question->question }} <span class="text-xs font-normal text-gray-400">({{ $question->points }} pt{{ $question->points === 1 ? '' : 's' }})</span></p>
                        </div>
                        <div class="space-y-2 pl-10">
                            @foreach($question->options as $option)
                                <label class="flex items-center gap-3 p-3 border border-gray-200 rounded-lg cursor-pointer hover:border-primary-300 hover:bg-primary-50/40 has-[:checked]:border-primary-500 has-[:checked]:bg-primary-50 transition">
                                    <input type="radio" name="answers[{{ $question->id }}]" value="{{ $option->id }}" required
                                        class="w-4 h-4 border-gray-300 text-primary-600 focus:ring-primary-500" />
                                    <span class="text-sm text-gray-700">{{ $option->option_text }}</span>
                                </label>
                            @endforeach
                        </div>
                    </fieldset>
                @endforeach
            </div>

            <div class="pt-2 flex justify-end">
                <button type="submit" class="inline-flex items-center gap-2 px-8 py-3 bg-primary-600 hover:bg-primary-700 text-white font-semibold rounded-xl transition shadow-sm">
                    Submit Test
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6 12L3.269 3.126A59.768 59.768 0 0121.485 12 59.77 59.77 0 013.27 20.876L5.999 12zm0 0h7.5"/></svg>
                </button>
            </div>
        </form>
    </div>
@endsection
