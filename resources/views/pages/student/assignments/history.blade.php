@extends('layouts.dashboard')
@section('title', 'Assignment History')
@section('content')
    <div class="max-w-4xl mx-auto space-y-6">
        {{-- Header --}}
        <div class="flex items-center gap-3">
            <div class="flex-1 min-w-0">
                <h1 class="text-2xl font-bold text-gray-900">Assignment History</h1>
                <p class="text-gray-500 mt-0.5">Your past assignment submissions</p>
            </div>
        </div>

        {{-- Submissions --}}
        <div class="space-y-3">
            @forelse($submissions as $sub)
                @php
                    $isGraded = $sub->status === 'graded';
                    $isLate = $sub->status === 'late';
                @endphp
                <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-5 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                    <div class="flex items-start gap-4 min-w-0">
                        <div class="w-10 h-10 rounded-lg flex items-center justify-center shrink-0 {{ $isGraded ? 'bg-secondary-100 text-secondary-600' : ($isLate ? 'bg-amber-100 text-amber-600' : 'bg-blue-100 text-blue-600') }}">
                            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z"/></svg>
                        </div>
                        <div class="min-w-0">
                            <div class="flex items-center gap-2 flex-wrap">
                                <p class="text-sm font-semibold text-gray-900 truncate">{{ $sub->quiz->title ?? 'Deleted Assignment' }}</p>
                                <span class="px-2 py-0.5 rounded-full text-xs font-medium {{ $isGraded ? 'bg-secondary-100 text-secondary-700' : ($isLate ? 'bg-amber-100 text-amber-700' : 'bg-blue-100 text-blue-700') }}">
                                    {{ ucfirst($sub->status) }}
                                </span>
                            </div>
                            <p class="text-xs text-gray-400 mt-1">
                                {{ $sub->quiz->course->title ?? 'N/A' }} · Submitted {{ $sub->submitted_at->diffForHumans() }}
                            </p>
                            <p class="text-xs text-gray-400 mt-0.5">
                                File: {{ $sub->file_original_name }}
                            </p>
                        </div>
                    </div>
                    <div class="flex items-center gap-3 shrink-0 self-end sm:self-auto">
                        @if($isGraded)
                            <div class="text-right">
                                <p class="text-lg font-bold {{ $sub->marks >= ($sub->quiz->passing_score ?? 60) ? 'text-secondary-600' : 'text-red-600' }}">
                                    {{ number_format($sub->marks, 1) }}%
                                </p>
                                <p class="text-xs text-gray-400">{{ $sub->graded_at?->diffForHumans() }}</p>
                            </div>
                        @else
                            <p class="text-sm text-gray-400">Not graded yet</p>
                        @endif
                    </div>
                </div>

                {{-- Feedback (if graded and has feedback) --}}
                @if($isGraded && $sub->feedback)
                    <div class="bg-white rounded-xl border border-gray-100 shadow-sm -mt-2 pt-4 px-5 pb-5">
                        <p class="text-xs font-semibold text-gray-500 uppercase tracking-wide mb-1">Instructor Feedback</p>
                        <p class="text-sm text-gray-700 leading-relaxed">{{ $sub->feedback }}</p>
                    </div>
                @endif
            @empty
                <div class="bg-white rounded-xl border border-dashed border-gray-200 p-10 text-center">
                    <svg class="w-10 h-10 text-gray-300 mx-auto mb-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z"/></svg>
                    <p class="text-sm font-medium text-gray-500">No submissions yet</p>
                    <p class="text-sm text-gray-400 mt-1">Your assignment submissions will appear here.</p>
                </div>
            @endforelse
        </div>

        @if($submissions->hasPages())
            <div class="mt-6">
                {{ $submissions->links() }}
            </div>
        @endif
    </div>
@endsection
