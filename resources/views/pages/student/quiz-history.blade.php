@extends('layouts.app')
@section('title', 'Quiz History')

@section('content')
    {{-- Page Header --}}
    <div class="bg-gradient-to-r from-primary-900 to-primary-800 py-14">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
            <span class="inline-flex items-center gap-2 px-4 py-1.5 bg-white/10 border border-white/20 text-accent-300 text-xs font-bold uppercase tracking-widest rounded-full mb-5">Performance Tracker</span>
            <h1 class="text-3xl md:text-4xl font-bold text-white mb-3">My Quiz History</h1>
            <p class="text-lg text-primary-200 max-w-2xl mx-auto">Every test, quiz and exam you've taken — scores, pass/fail results and time spent.</p>
        </div>
    </div>

    <section class="py-12 bg-gray-50 min-h-[50vh]">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

            {{-- Stats --}}
            <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-8">
                @foreach([
                    ['Tests Taken', number_format($stats['total']), 'bg-primary-100 text-primary-600', 'M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z'],
                    ['Passed', number_format($stats['passed']), 'bg-secondary-100 text-secondary-600', 'M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z'],
                    ['Average Score', $stats['avg_score'] . '%', 'bg-accent-100 text-accent-600', 'M7.5 14.25v2.25m3-4.5v4.5m3-6.75v6.75m3-9v9M6 20.25h12A2.25 2.25 0 0020.25 18V6A2.25 2.25 0 0018 3.75H6A2.25 2.25 0 003.75 6v12A2.25 2.25 0 006 20.25z'],
                    ['Best Score', $stats['best_score'] . '%', 'bg-purple-100 text-purple-600', 'M16.5 18.75h-9m9 0a3 3 0 013 3h-15a3 3 0 013-3m9 0v-3.375c0-.621-.503-1.125-1.125-1.125h-.871M7.5 18.75v-3.375c0-.621.504-1.125 1.125-1.125h.872m5.007 0H9.497m5.007 0a7.454 7.454 0 01-.982-3.172M9.497 14.25a7.454 7.454 0 00.981-3.172M5.25 4.236c-.982.143-1.954.317-2.916.52A6.003 6.003 0 007.73 9.728'],
                ] as [$label, $value, $style, $iconPath])
                    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6 flex items-center gap-4">
                        <span class="w-12 h-12 rounded-xl {{ $style }} grid place-items-center shrink-0">
                            <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="{{ $iconPath }}"/></svg>
                        </span>
                        <div>
                            <p class="text-xs font-semibold text-gray-400 uppercase tracking-wide">{{ $label }}</p>
                            <p class="text-xl font-bold text-gray-900">{{ $value }}</p>
                        </div>
                    </div>
                @endforeach
            </div>

            {{-- Attempts list --}}
            <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between">
                    <h2 class="font-bold text-gray-900">All Attempts</h2>
                    <span class="text-xs text-gray-400">Newest first</span>
                </div>

                @forelse($attempts as $attempt)
                    <div class="px-6 py-4 flex flex-wrap sm:flex-nowrap items-center gap-4 hover:bg-gray-50/70 transition border-b border-gray-50 last:border-0">
                        <div class="w-12 h-12 rounded-full grid place-items-center shrink-0 {{ $attempt->passed ? 'bg-secondary-100' : 'bg-red-100' }}">
                            <span class="text-xs font-bold {{ $attempt->passed ? 'text-secondary-700' : 'text-red-600' }}">{{ number_format($attempt->score, 0) }}%</span>
                        </div>
                        <div class="flex-1 min-w-0">
                            <div class="flex items-center gap-2 flex-wrap">
                                <p class="text-sm font-semibold text-gray-900 truncate">{{ $attempt->quiz?->title ?? 'Deleted Test' }}</p>
                                <span class="px-2 py-0.5 rounded-full text-[10px] font-bold uppercase tracking-wide {{ $attempt->passed ? 'bg-secondary-100 text-secondary-700' : 'bg-red-100 text-red-600' }}">{{ $attempt->passed ? 'Passed' : 'Failed' }}</span>
                            </div>
                            <p class="text-xs text-gray-400 mt-0.5 truncate">
                                {{ $attempt->quiz?->course?->title ?? '—' }}
                                · {{ ucfirst(str_replace('_', ' ', $attempt->quiz?->type ?? 'test')) }}
                                · pass mark {{ $attempt->quiz?->passing_score ?? '—' }}%
                            </p>
                        </div>
                        <div class="hidden md:block text-right shrink-0">
                            <p class="text-xs font-medium text-gray-500">{{ $attempt->completed_at?->format('M j, Y') }}</p>
                            <p class="text-[11px] text-gray-400">{{ $attempt->completed_at?->format('g:i A') }} · took {{ $attempt->formattedTimeSpent() }}</p>
                        </div>
                        <a href="{{ $attempt->quiz ? route('courses.tests.show', [$attempt->quiz->course_id, $attempt->quiz]) : '#' }}"
                           class="inline-flex items-center gap-1 px-3 py-1.5 text-xs font-medium text-gray-500 bg-white border border-gray-200 hover:border-primary-300 hover:text-primary-700 rounded-lg transition shrink-0 {{ $attempt->quiz ? '' : 'pointer-events-none opacity-40' }}">
                            View
                            <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5L21 12m0 0l-7.5 7.5M21 12H3"/></svg>
                        </a>
                    </div>
                @empty
                    <div class="p-16 text-center">
                        <svg class="w-14 h-14 text-gray-200 mx-auto mb-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12c0 1.268-.63 2.39-1.593 3.068a3.745 3.745 0 01-1.043 3.296 3.745 3.745 0 01-3.296 1.043A3.745 3.745 0 0112 21c-1.268 0-2.39-.63-3.068-1.593a3.746 3.746 0 01-3.296-1.043 3.745 3.745 0 01-1.043-3.296A3.745 3.745 0 013 12c0-1.268.63-2.39 1.593-3.068a3.746 3.746 0 011.043-3.296 3.746 3.746 0 013.296-1.043A3.746 3.746 0 0112 3c1.268 0 2.39.63 3.068 1.593a3.746 3.746 0 013.296 1.043 3.746 3.746 0 011.043 3.296A3.745 3.745 0 0121 12z"/></svg>
                        <h3 class="font-semibold text-gray-900 mb-1">No attempts yet</h3>
                        <p class="text-sm text-gray-500 mb-6">Enroll in a course and take your first quiz to see your history here.</p>
                        <a href="{{ route('dashboard') }}" class="inline-flex items-center px-6 py-3 bg-primary-600 hover:bg-primary-700 text-white text-sm font-bold rounded-xl transition">Go to My Learning</a>
                    </div>
                @endforelse
            </div>

            @if($attempts->hasPages())
                <div class="mt-6">
                    {{ $attempts->withQueryString()->links() }}
                </div>
            @endif
        </div>
    </section>
@endsection
