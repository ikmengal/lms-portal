@extends('layouts.app')
@section('title', $quiz->title)
@section('content')
    @php
        $results = session('attempt_results');
        $isTimed = (bool) $quiz->duration_minutes;
        $typeLabels = [
            'multiple_choice' => 'Multiple Choice',
            'true_false' => 'True / False',
            'multiple_answers' => 'Multiple Answers',
        ];
    @endphp

    <div class="max-w-3xl mx-auto space-y-6">
        {{-- Header --}}
        <div class="flex items-center gap-3">
            <a href="{{ url()->previous() }}" class="p-2 text-gray-400 hover:text-gray-700 hover:bg-gray-100 rounded-lg transition">
                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18"/></svg>
            </a>
            <div class="min-w-0 flex-1">
                <p class="text-xs text-primary-700 font-semibold uppercase tracking-wide">{{ str_replace('_', ' ', ucfirst($quiz->type)) }} · {{ $course->title }}</p>
                <h1 class="text-2xl font-bold text-gray-900 truncate">{{ $quiz->title }}</h1>
            </div>
            <a href="{{ route('quiz.history') }}" class="hidden sm:inline-flex items-center gap-1.5 px-3 py-2 text-xs font-semibold text-primary-700 bg-primary-50 hover:bg-primary-100 rounded-lg transition shrink-0">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                Quiz History
            </a>
        </div>

        {{-- ===== Result banner (auto-graded) ===== --}}
        @if($results)
            <div class="rounded-xl border shadow-sm overflow-hidden {{ $results['passed'] ? 'border-secondary-200 bg-secondary-50/60' : 'border-red-200 bg-red-50/60' }}" x-data x-init="$nextTick(() => $el.scrollIntoView({ behavior: 'smooth', block: 'center' }))">
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
                            {{ $results['passed'] ? '🎉 Passed! Well done.' : 'Not passed this time.' }}
                        </p>
                        <p class="text-sm text-gray-500 mt-0.5">
                            You earned {{ $results['earned'] }} of {{ $results['total'] }} points · pass mark {{ $quiz->passing_score }}%
                            @if(!empty($results['time_spent']) && $results['time_spent'] !== '—')
                                · time used {{ $results['time_spent'] }}
                            @endif
                        </p>
                    </div>
                </div>

                {{-- Per-question breakdown --}}
                <details class="border-t {{ $results['passed'] ? 'border-secondary-200' : 'border-red-200' }}">
                    <summary class="px-6 py-3 text-sm font-medium text-gray-600 cursor-pointer hover:bg-black/[0.03] transition">Review your answers</summary>
                    <ul class="px-6 pb-4 space-y-3">
                        @foreach($quiz->questions as $q)
                            @php
                                $detail = $results['details'][$q->id] ?? null;
                                if (!$detail) { continue; }
                                $correctTexts = $q->options->whereIn('id', $detail['correct_ids'])->pluck('option_text')->implode(', ');
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
                                        — correct answer{{ str_contains($correctTexts, ', ') ? 's' : '' }}:
                                        <em class="not-italic font-medium text-secondary-700">{{ $correctTexts ?: '—' }}</em>
                                    @endunless
                                </span>
                            </li>
                        @endforeach
                    </ul>
                </details>
            </div>
        @endif

        {{-- ===== Attempts exhausted ===== --}}
        @if(!$results && $attemptsLeft === 0 && !$preview && !($attempt && !$attempt->isCompleted()))
            <div class="bg-white rounded-xl border border-accent-200 bg-accent-50/40 shadow-sm p-8 text-center">
                <div class="w-14 h-14 mx-auto bg-accent-100 rounded-2xl grid place-items-center mb-4">
                    <svg class="w-7 h-7 text-accent-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M16.418 4.157a8.386 8.386 0 010 11.686m.318 3.482A11.201 11.201 0 002.583 6.23a11.2 11.2 0 0011.666-.003M8.25 12a3.75 3.75 0 107.5 0 3.75 3.75 0 00-7.5 0z"/></svg>
                </div>
                <h2 class="text-lg font-bold text-gray-900">No attempts remaining</h2>
                <p class="text-sm text-gray-500 mt-1 max-w-md mx-auto">You've used all {{ $quiz->max_attempts }} allowed attempt{{ $quiz->max_attempts === 1 ? '' : 's' }} for this test.
                    Your best score was <strong>{{ number_format(optional($bestAttempt)->score ?? 0, 1) }}%</strong> ({{ optional($bestAttempt)?->passed ? 'passed' : 'not passed' }}).</p>
            </div>
        @endif

        {{-- ===== Intro / Start screen ===== --}}
        @if(!$attempt && $attemptsLeft !== 0 && !$preview && !$results)
            <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-6">
                @if($quiz->description)
                    <p class="text-sm text-gray-600 mb-4 leading-relaxed">{{ $quiz->description }}</p>
                @endif
                <div class="flex flex-wrap gap-x-6 gap-y-2 text-sm mb-6">
                    <span class="inline-flex items-center gap-1.5 text-gray-500">
                        <svg class="w-4 h-4 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12c0 1.268-.63 2.39-1.593 3.068a3.745 3.745 0 01-1.043 3.296 3.745 3.745 0 01-3.296 1.043A3.745 3.745 0 0112 21c-1.268 0-2.39-.63-3.068-1.593a3.746 3.746 0 01-3.296-1.043 3.745 3.745 0 01-1.043-3.296A3.745 3.745 0 013 12c0-1.268.63-2.39 1.593-3.068a3.746 3.746 0 011.043-3.296 3.746 3.746 0 013.296-1.043A3.746 3.746 0 0112 3c1.268 0 2.39.63 3.068 1.593a3.746 3.746 0 013.296 1.043 3.746 3.746 0 011.043 3.296A3.745 3.745 0 0121 12z"/></svg>
                        {{ $quiz->questions->count() }} questions · {{ $quiz->totalPoints() }} points
                    </span>
                    <span class="inline-flex items-center gap-1.5 text-gray-500">
                        <svg class="w-4 h-4 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        {{ $isTimed ? $quiz->duration_minutes . ' minute timer' : 'No time limit' }}
                    </span>
                    <span class="inline-flex items-center gap-1.5 text-gray-500">
                        <svg class="w-4 h-4 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12c0 1.268-.63 2.39-1.593 3.068a3.745 3.745 0 01-1.043 3.296 3.745 3.745 0 01-3.296 1.043A3.745 3.745 0 0112 21c-1.268 0-2.39-.63-3.068-1.593a3.746 3.746 0 01-3.296-1.043 3.745 3.745 0 01-1.043-3.296A3.745 3.745 0 013 12c0-1.268.63-2.39 1.593-3.068a3.746 3.746 0 011.043-3.296 3.746 3.746 0 013.296-1.043A3.746 3.746 0 0112 3c1.268 0 2.39.63 3.068 1.593a3.746 3.746 0 013.296 1.043 3.746 3.746 0 011.043 3.296A3.745 3.745 0 0121 12z"/></svg>
                        Pass ≥ {{ $quiz->passing_score }}%
                    </span>
                    <span class="inline-flex items-center gap-1.5 text-gray-500">
                        <svg class="w-4 h-4 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M16.023 9.348h4.992v-.001M2.985 19.644v-4.992m0 0h4.992m-4.993 0l3.181 3.183a8.25 8.25 0 0013.803-3.7M4.031 9.865a8.25 8.25 0 0113.803-3.7l3.181 3.182m0-4.991v4.99"/></svg>
                        {{ $attemptsLeft === null ? 'Unlimited attempts' : $attemptsLeft . ' attempt' . ($attemptsLeft === 1 ? '' : 's') . ' left' }}
                    </span>
                    @if($quiz->shuffle_questions || $quiz->shuffle_options)
                        <span class="inline-flex items-center gap-1.5 text-gray-500">
                            <svg class="w-4 h-4 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M3 7.5L7.5 3m0 0L12 7.5M7.5 3v13.5m13.5 0L16.5 21m0 0L12 16.5m4.5 4.5V7.5"/></svg>
                            Randomized order
                        </span>
                    @endif
                </div>

                @if($isTimed)
                    <div class="bg-primary-50 border border-primary-100 rounded-xl p-4 flex items-start gap-3 mb-6">
                        <svg class="w-5 h-5 text-primary-600 shrink-0 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M11.25 11.25l.041-.02a.75.75 0 011.063.852l-.708 2.836a.75.75 0 001.063.853l.041-.021M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-9-3.75h.008v.008H12V8.25z"/></svg>
                        <p class="text-sm text-primary-800">Once you start, the <strong>{{ $quiz->duration_minutes }}-minute countdown begins immediately</strong> and your answers auto-submit when time runs out.</p>
                    </div>
                @endif

                <a href="{{ route('courses.tests.show', [$course, $quiz]) }}?start=1"
                    class="w-full sm:w-auto inline-flex justify-center items-center gap-2 px-8 py-3.5 bg-primary-600 hover:bg-primary-700 text-white font-bold rounded-xl transition shadow-md">
                    Start Test
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M5.25 5.653c0-.856.917-1.398 1.667-.986l11.54 6.347a1.125 1.125 0 010 1.972l-11.54 6.347a1.125 1.125 0 01-1.667-.985V5.653z"/></svg>
                </a>
            </div>
        @endif

        {{-- Best attempt summary --}}
        @if($bestAttempt && !$results && (!$attempt || $attempt->isCompleted()))
            <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-5 flex items-center justify-between gap-4">
                <div>
                    <p class="text-sm font-semibold text-gray-900">Your best attempt</p>
                    <p class="text-xs text-gray-400 mt-0.5">{{ number_format($bestAttempt->score, 1) }}% · {{ $bestAttempt->completed_at?->diffForHumans() }} · {{ $bestAttempt->formattedTimeSpent() }}</p>
                </div>
                <span class="px-3 py-1 rounded-full text-xs font-semibold {{ $bestAttempt->passed ? 'bg-secondary-100 text-secondary-700' : 'bg-red-100 text-red-600' }}">
                    {{ $bestAttempt->passed ? 'PASSED' : 'FAILED' }}
                </span>
            </div>
        @endif

        {{-- ===== The running test ===== --}}
        @if(($preview || $attempt) && (!$attempt || !$attempt->isCompleted()))
            @php
                $displayQuestions = isset($attemptQuestions) && $attemptQuestions->isNotEmpty() ? $attemptQuestions : $quiz->questions;
            @endphp

            <form method="POST" action="{{ route('courses.tests.submit', [$course, $quiz]) }}"
                x-data="quizTimer({{ $remainingSeconds !== null && $remainingSeconds > 0 ? $remainingSeconds : 'null' }})"
                @submit="$el.dataset.submitting = '1'"
                data-confirm="Submit your answers? You'll get your auto-graded result right away."
                data-confirm-title="Submit test?"
                data-confirm-button="Yes, submit"
                data-confirm-icon="question">
                @csrf

                {{-- Sticky timer bar --}}
                @if($isTimed && $remainingSeconds !== null)
                    <div class="sticky top-[72px] z-20 -mx-1 px-1 pt-1 pb-3">
                        <div class="rounded-xl border shadow-md p-3.5 flex items-center justify-between gap-3 transition-colors duration-300"
                            :class="{
                                'bg-white border-gray-200': seconds > total * 0.2,
                                'bg-amber-50 border-amber-300': seconds <= total * 0.2 && seconds > total * 0.1,
                                'bg-red-50 border-red-300': seconds <= total * 0.1,
                            }">
                            <div class="flex items-center gap-2.5 min-w-0">
                                <span class="w-9 h-9 rounded-xl grid place-items-center shrink-0 transition-colors"
                                    :class="{ 'bg-primary-100 text-primary-600': seconds > total * 0.2, 'bg-amber-100 text-amber-600': seconds <= total * 0.2 && seconds > total * 0.1, 'bg-red-100 text-red-600 animate-pulse': seconds <= total * 0.1 }">
                                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                </span>
                                <div class="min-w-0">
                                    <p class="text-[11px] font-bold uppercase tracking-wider text-gray-400 leading-none">Time Remaining</p>
                                    <p class="font-mono font-bold text-lg tabular-nums leading-tight"
                                        :class="{ 'text-gray-900': seconds > total * 0.2, 'text-amber-600': seconds <= total * 0.2 && seconds > total * 0.1, 'text-red-600': seconds <= total * 0.1 }"
                                        x-text="display"></p>
                                </div>
                            </div>
                            <button type="submit" class="shrink-0 inline-flex items-center gap-1.5 px-4 py-2 text-xs font-bold text-white bg-primary-600 hover:bg-primary-700 rounded-lg transition">
                                Submit Now
                            </button>
                        </div>
                        <div class="mt-2 h-1.5 bg-gray-100 rounded-full overflow-hidden mx-1">
                            <div class="h-full rounded-full transition-all duration-1000 ease-linear"
                                :style="`width: ${total ? Math.max(0, seconds / total * 100) : 0}%`"
                                :class="{ 'bg-primary-500': seconds > total * 0.2, 'bg-amber-500': seconds <= total * 0.2 && seconds > total * 0.1, 'bg-red-500': seconds <= total * 0.1 }"></div>
                        </div>
                    </div>
                @endif

                <div class="space-y-4">
                    @foreach($displayQuestions as $qi => $question)
                        <fieldset class="bg-white rounded-xl border border-gray-100 shadow-sm p-6" x-data="{ answered: false }">
                            <legend class="sr-only">Question {{ $qi + 1 }}</legend>
                            <div class="flex items-start gap-3 mb-4">
                                <span class="w-7 h-7 bg-primary-600 text-white rounded-lg flex items-center justify-center text-xs font-bold shrink-0">{{ $qi + 1 }}</span>
                                <p class="text-sm font-semibold text-gray-900 pt-1 flex-1">
                                    {{ $question->question }}
                                    <span class="text-xs font-normal text-gray-400">({{ $question->points }} pt{{ $question->points === 1 ? '' : 's' }})</span>
                                </p>
                                <span class="hidden sm:inline-block px-2 py-0.5 rounded-full text-[10px] font-bold uppercase tracking-wide shrink-0 {{ $question->type === 'multiple_answers' ? 'bg-purple-100 text-purple-700' : ($question->type === 'true_false' ? 'bg-accent-100 text-accent-700' : 'bg-primary-100 text-primary-700') }}">
                                    {{ $typeLabels[$question->type] ?? 'Multiple Choice' }}
                                </span>
                            </div>
                            <div class="space-y-2 pl-10" @change="answered = true">
                                @foreach($question->options as $oi => $option)
                                    @php
                                        $inputName = $question->isMultiAnswer()
                                            ? 'answers[' . $question->id . '][]'
                                            : 'answers[' . $question->id . ']';
                                    @endphp
                                    <label class="group flex items-center gap-3 p-3 border border-gray-200 rounded-lg cursor-pointer hover:border-primary-300 hover:bg-primary-50/40 has-[:checked]:border-primary-500 has-[:checked]:bg-primary-50 has-[:checked]:shadow-sm transition">
                                        <input type="{{ $question->isMultiAnswer() ? 'checkbox' : 'radio' }}"
                                            name="{{ $inputName }}" value="{{ $option->id }}"
                                            {{ $question->isMultiAnswer() ? '' : 'required' }}
                                            class="w-4 h-4 border-gray-300 text-primary-600 focus:ring-primary-500 shrink-0" />
                                        <span class="text-sm text-gray-700 group-has-[:checked]:font-medium">{{ $option->option_text }}</span>
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
        @endif

        {{-- ===== Attempt history for this quiz ===== --}}
        @if($history->isNotEmpty())
            <div class="bg-white rounded-xl border border-gray-100 shadow-sm overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between">
                    <h2 class="font-bold text-gray-900">Attempt History</h2>
                    <span class="text-xs text-gray-400">{{ $attemptsUsed }} used · {{ $attemptsLeft === null ? 'Unlimited' : $attemptsLeft . ' left' }}</span>
                </div>
                <ul class="divide-y divide-gray-50">
                    @foreach($history as $h)
                        <li class="px-6 py-3.5 flex items-center gap-4">
                            <span class="w-11 h-11 rounded-xl grid place-items-center text-sm font-bold shrink-0 {{ $h->passed ? 'bg-secondary-100 text-secondary-700' : 'bg-red-100 text-red-600' }}">
                                {{ number_format($h->score, 0) }}%
                            </span>
                            <div class="flex-1 min-w-0">
                                <p class="text-sm font-medium text-gray-900">Attempt #{{ $loop->iteration }} <span class="ml-1.5 px-1.5 py-0.5 rounded text-[10px] font-bold align-middle {{ $h->passed ? 'bg-secondary-100 text-secondary-700' : 'bg-red-100 text-red-600' }}">{{ $h->passed ? 'PASSED' : 'FAILED' }}</span></p>
                                <p class="text-xs text-gray-400 mt-0.5">{{ $h->completed_at?->format('M j, Y g:i A') }} · took {{ $h->formattedTimeSpent() }}</p>
                            </div>
                            <div class="w-20 h-1.5 bg-gray-100 rounded-full overflow-hidden hidden sm:block">
                                <div class="h-full rounded-full {{ $h->passed ? 'bg-secondary-500' : 'bg-red-400' }}" style="width: {{ min(100, $h->score) }}%"></div>
                            </div>
                        </li>
                    @endforeach
                </ul>
            </div>
        @endif
    </div>

    <script>
        function quizTimer(initial) {
            return {
                seconds: initial,
                total: initial,
                display: '',
                interval: null,

                init() {
                    if (this.seconds === null || this.seconds <= 0) {
                        this.display = this.seconds !== null ? '00:00' : '';
                        if (this.seconds !== null) this.$nextTick(() => this.autoSubmit());
                        return;
                    }
                    this.render();
                    this.interval = setInterval(() => {
                        this.seconds--;
                        this.render();
                        if (this.seconds <= 0) {
                            clearInterval(this.interval);
                            this.autoSubmit();
                        }
                    }, 1000);
                    window.addEventListener('beforeunload', this.warn.bind(this));
                },

                render() {
                    const m = String(Math.floor(this.seconds / 60)).padStart(2, '0');
                    const s = String(this.seconds % 60).padStart(2, '0');
                    this.display = `${m}:${s}`;
                },

                confirmSubmit() {
                    // Manual early submit — let HTML5 validation run normally.
                    this.$root.requestSubmit();
                },

                autoSubmit() {
                    const form = this.$root;
                    if (form.dataset.submitting) return;
                    alert("Time's up! Your answers are being submitted automatically.");
                    form.dataset.submitting = '1';
                    // Bypass HTML5 validation so unanswered questions don't block submission.
                    form.noValidate = true;
                    form.submit();
                },

                warn(e) {
                    if (!this.$root.dataset.submitting && this.seconds > 0) {
                        e.preventDefault();
                        e.returnValue = '';
                    }
                },
            };
        }
    </script>
@endsection
