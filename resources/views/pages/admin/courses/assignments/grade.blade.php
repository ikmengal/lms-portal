@extends('layouts.dashboard')
@section('title', 'Grade Submission')
@section('content')
    <div class="max-w-3xl mx-auto space-y-6">
        {{-- Header --}}
        <div class="flex items-center gap-3">
            <a href="{{ route('admin.courses.assignments.submissions', [$course, $quiz]) }}" class="p-2 text-gray-400 hover:text-gray-700 hover:bg-gray-100 rounded-lg transition">
                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18"/></svg>
            </a>
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Grade Submission</h1>
                <p class="text-gray-500 mt-0.5">{{ $quiz->title }}</p>
            </div>
        </div>

        {{-- Student Info --}}
        <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-6">
            <div class="flex items-center gap-4">
                <x-avatar :user="$submission->user" size="w-12 h-12 text-sm" />
                <div>
                    <p class="text-base font-semibold text-gray-900">{{ $submission->user->name }}</p>
                    <p class="text-sm text-gray-400">{{ $submission->user->email }}</p>
                </div>
                <div class="ml-auto text-right">
                    <p class="text-sm text-gray-500">Submitted</p>
                    <p class="text-sm font-medium text-gray-900">{{ $submission->submitted_at->format('M j, Y g:i A') }}</p>
                </div>
            </div>
        </div>

        {{-- Submitted File --}}
        <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-6">
            <h2 class="text-lg font-semibold text-gray-900 mb-4">Submitted File</h2>
            <div class="flex items-center gap-4 p-4 bg-gray-50 rounded-lg">
                <div class="w-10 h-10 bg-primary-100 text-primary-600 rounded-lg grid place-items-center shrink-0">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m2.25 0H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z"/></svg>
                </div>
                <div class="flex-1 min-w-0">
                    <p class="text-sm font-medium text-gray-900 truncate">{{ $submission->file_original_name }}</p>
                    <p class="text-xs text-gray-400">{{ strtoupper(pathinfo($submission->file_original_name, PATHINFO_EXTENSION)) }} file</p>
                </div>
                <a href="{{ route('assignments.download', $submission) }}" class="inline-flex items-center gap-1.5 px-4 py-2 text-sm font-medium text-primary-700 bg-primary-50 hover:bg-primary-100 rounded-lg transition shrink-0">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5M16.5 12L12 16.5m0 0L7.5 12m4.5 4.5V3"/></svg>
                    Download
                </a>
            </div>
        </div>

        {{-- Grading Form --}}
        <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-6">
            <h2 class="text-lg font-semibold text-gray-900 mb-4">Grade & Feedback</h2>
            <form method="POST" action="{{ route('admin.courses.assignments.submissions.grade.store', [$course, $quiz, $submission]) }}" class="space-y-5">
                @csrf

                <div class="grid sm:grid-cols-2 gap-5">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Marks (%) <span class="text-red-500">*</span></label>
                        <input type="number" name="marks" value="{{ old('marks', $submission->marks) }}" required min="0" max="100" step="0.1"
                            placeholder="0-100"
                            class="w-full px-4 py-2.5 border border-gray-200 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500 text-sm transition @error('marks') border-red-300 @enderror" />
                        @error('marks')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
                    </div>
                    <div class="flex items-end">
                        <p class="text-sm text-gray-400 pb-2.5">Pass mark: {{ $quiz->passing_score }}%</p>
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Feedback</label>
                    <textarea name="feedback" rows="5" maxlength="5000" placeholder="Provide detailed feedback on the student's submission..."
                        class="w-full px-4 py-2.5 border border-gray-200 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500 text-sm transition">{{ old('feedback', $submission->feedback) }}</textarea>
                    @error('feedback')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
                </div>

                {{-- Previous grade info --}}
                @if($submission->isGraded())
                    <div class="bg-secondary-50 border border-secondary-200 rounded-lg p-4">
                        <div class="flex items-center gap-2 mb-1">
                            <svg class="w-4 h-4 text-secondary-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            <p class="text-sm font-medium text-secondary-800">Previously graded</p>
                        </div>
                        <p class="text-sm text-secondary-700">
                            Score: {{ number_format($submission->marks, 1) }}% · Graded {{ $submission->graded_at?->diffForHumans() }}
                            @if($submission->gradedBy) by {{ $submission->gradedBy->name }}@endif
                        </p>
                    </div>
                @endif

                <div class="flex items-center justify-end gap-3 pt-2">
                    <a href="{{ route('admin.courses.assignments.submissions', [$course, $quiz]) }}" class="px-5 py-2.5 text-sm font-medium text-gray-600 hover:text-gray-900 transition">Cancel</a>
                    <button type="submit" class="px-6 py-2.5 bg-primary-600 hover:bg-primary-700 text-white font-medium rounded-lg text-sm transition shadow-sm">
                        {{ $submission->isGraded() ? 'Update Grade' : 'Submit Grade' }}
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection
