@extends('layouts.app')
@section('title', $quiz->title)
@section('content')
    @php
        $hasSubmitted = $submission !== null;
        $isOverdue = $quiz->isOverdue();
        $canSubmit = !$hasSubmitted && !$isOverdue && $quiz->is_active;
        $isGraded = $hasSubmitted && $submission->isGraded();
    @endphp

    <div class="max-w-3xl mx-auto space-y-6 py-8 px-4 sm:px-6">
        {{-- Header --}}
        <div class="flex items-center gap-3">
            <a href="{{ url()->previous() }}" class="p-2 text-gray-400 hover:text-gray-700 hover:bg-gray-100 rounded-lg transition">
                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18"/></svg>
            </a>
            <div class="min-w-0 flex-1">
                <p class="text-xs text-primary-700 font-semibold uppercase tracking-wide">Assignment · {{ $course->title }}</p>
                <h1 class="text-2xl font-bold text-gray-900 truncate">{{ $quiz->title }}</h1>
            </div>
            <a href="{{ route('assignment.history') }}" class="hidden sm:inline-flex items-center gap-1.5 px-3 py-2 text-xs font-semibold text-primary-700 bg-primary-50 hover:bg-primary-100 rounded-lg transition shrink-0">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                History
            </a>
        </div>

        {{-- Deadline warning --}}
        @if($quiz->due_date)
            @php
                $now = now();
                $timeLeft = $now->diff($quiz->due_date);
                $isPastDue = $quiz->due_date->isPast();
            @endphp
            <div class="rounded-xl border shadow-sm overflow-hidden {{ $isPastDue ? 'border-red-200 bg-red-50/60' : ($quiz->due_date->diffInHours(now()) < 24 ? 'border-amber-200 bg-amber-50/60' : 'border-primary-200 bg-primary-50/60') }}">
                <div class="p-4 flex items-center gap-3">
                    <svg class="w-5 h-5 shrink-0 {{ $isPastDue ? 'text-red-600' : ($quiz->due_date->diffInHours(now()) < 24 ? 'text-amber-600' : 'text-primary-600') }}" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    <p class="text-sm font-medium {{ $isPastDue ? 'text-red-800' : ($quiz->due_date->diffInHours(now()) < 24 ? 'text-amber-800' : 'text-primary-800') }}">
                        @if($isPastDue)
                            Deadline passed · {{ $quiz->due_date->format('M j, Y g:i A') }}
                        @else
                            Due {{ $quiz->due_date->diffForHumans() }} · {{ $quiz->due_date->format('M j, Y g:i A') }}
                        @endif
                    </p>
                </div>
            </div>
        @endif

        {{-- Assignment Info --}}
        <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-6">
            @if($quiz->description)
                <div class="prose prose-sm max-w-none text-gray-600 mb-4">
                    {!! nl2br(e($quiz->description)) !!}
                </div>
            @endif

            <div class="flex flex-wrap gap-x-6 gap-y-2 text-sm">
                <span class="inline-flex items-center gap-1.5 text-gray-500">
                    <svg class="w-4 h-4 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5m-13.5-9L12 3m0 0l4.5 4.5M12 3v13.5"/></svg>
                    Max {{ $quiz->max_file_size_mb ?? 10 }}MB
                </span>
                <span class="inline-flex items-center gap-1.5 text-gray-500">
                    <svg class="w-4 h-4 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z"/></svg>
                    Accepted: {{ strtoupper($quiz->allowed_extensions ?? 'pdf, doc, docx, images') }}
                </span>
                @if($quiz->due_date)
                    <span class="inline-flex items-center gap-1.5 text-gray-500">
                        <svg class="w-4 h-4 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        {{ $quiz->due_date->format('M j, Y g:i A') }}
                    </span>
                @endif
            </div>
        </div>

        {{-- Previous Submission Status --}}
        @if($hasSubmitted)
            <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-6">
                <h2 class="text-lg font-semibold text-gray-900 mb-4">Your Submission</h2>

                <div class="space-y-4">
                    <div class="flex items-center gap-4 p-4 bg-gray-50 rounded-lg">
                        <div class="w-10 h-10 bg-primary-100 text-primary-600 rounded-lg grid place-items-center shrink-0">
                            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m2.25 0H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z"/></svg>
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="text-sm font-medium text-gray-900 truncate">{{ $submission->file_original_name }}</p>
                            <p class="text-xs text-gray-400">Submitted {{ $submission->submitted_at->diffForHumans() }}</p>
                        </div>
                        <span class="px-2.5 py-1 rounded-full text-xs font-semibold
                            {{ $submission->isGraded() ? 'bg-secondary-100 text-secondary-700' : ($submission->isLate() ? 'bg-amber-100 text-amber-700' : 'bg-blue-100 text-blue-700') }}">
                            {{ ucfirst($submission->status) }}
                        </span>
                    </div>

                    @if($isGraded)
                        <div class="p-4 {{ $submission->marks >= ($quiz->passing_score ?? 60) ? 'bg-secondary-50 border border-secondary-200' : 'bg-red-50 border border-red-200' }} rounded-lg">
                            <div class="flex items-center justify-between mb-2">
                                <p class="text-sm font-semibold {{ $submission->marks >= ($quiz->passing_score ?? 60) ? 'text-secondary-700' : 'text-red-700' }}">
                                    Score: {{ number_format($submission->marks, 1) }}%
                                </p>
                                <span class="px-2.5 py-1 rounded-full text-xs font-semibold {{ $submission->marks >= ($quiz->passing_score ?? 60) ? 'bg-secondary-100 text-secondary-700' : 'bg-red-100 text-red-600' }}">
                                    {{ $submission->marks >= ($quiz->passing_score ?? 60) ? 'PASSED' : 'FAILED' }}
                                </span>
                            </div>
                            @if($submission->feedback)
                                <div class="mt-3 pt-3 border-t {{ $submission->marks >= ($quiz->passing_score ?? 60) ? 'border-secondary-200' : 'border-red-200' }}">
                                    <p class="text-xs font-semibold text-gray-500 uppercase tracking-wide mb-1">Instructor Feedback</p>
                                    <p class="text-sm text-gray-700 leading-relaxed">{{ $submission->feedback }}</p>
                                </div>
                            @endif
                            <p class="text-xs text-gray-400 mt-2">Graded {{ $submission->graded_at?->diffForHumans() }}</p>
                        </div>
                    @endif
                </div>
            </div>
        @endif

        {{-- Upload Form --}}
        @if($canSubmit)
            <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-6" x-data="{ uploading: false }">
                <h2 class="text-lg font-semibold text-gray-900 mb-4">Submit Your Assignment</h2>

                <form method="POST" action="{{ route('courses.assignments.submit', [$course, $quiz]) }}" enctype="multipart/form-data" @submit="uploading = true">
                    @csrf

                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Upload File <span class="text-red-500">*</span></label>
                            <div class="flex items-center justify-center w-full">
                                <label for="file-upload" class="flex flex-col items-center justify-center w-full h-32 border-2 border-gray-300 border-dashed rounded-lg cursor-pointer bg-gray-50 hover:bg-gray-100 transition">
                                    <div class="flex flex-col items-center justify-center pt-5 pb-6">
                                        <svg class="w-8 h-8 mb-2 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5m-13.5-9L12 3m0 0l4.5 4.5M12 3v13.5"/></svg>
                                        <p class="text-sm text-gray-500"><span class="font-semibold">Click to upload</span> or drag and drop</p>
                                        <p class="text-xs text-gray-400 mt-1">Allowed: {{ strtoupper($quiz->allowed_extensions ?? 'PDF, DOC, DOCX, images') }} · Max {{ $quiz->max_file_size_mb ?? 10 }}MB</p>
                                    </div>
                                    <input id="file-upload" type="file" name="file" required
                                        accept=".{{ str_replace(',', ',.', $quiz->allowed_extensions ?? 'pdf,doc,docx,png,jpg,jpeg') }}"
                                        class="hidden" />
                                </label>
                            </div>
                            @error('file')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
                        </div>

                        <div class="flex items-center justify-end">
                            <button type="submit" :disabled="uploading"
                                class="inline-flex items-center gap-2 px-8 py-3 bg-primary-600 hover:bg-primary-700 text-white font-semibold rounded-xl transition shadow-sm disabled:opacity-50 disabled:cursor-not-allowed">
                                <svg x-show="!uploading" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5m-13.5-9L12 3m0 0l4.5 4.5M12 3v13.5"/></svg>
                                <svg x-show="uploading" class="w-5 h-5 animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path></svg>
                                <span x-text="uploading ? 'Uploading...' : 'Submit Assignment'"></span>
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        @elseif($isOverdue && !$hasSubmitted)
            <div class="bg-white rounded-xl border border-red-200 bg-red-50/60 shadow-sm p-8 text-center">
                <div class="w-14 h-14 mx-auto bg-red-100 rounded-2xl grid place-items-center mb-4">
                    <svg class="w-7 h-7 text-red-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </div>
                <h2 class="text-lg font-bold text-gray-900">Deadline Passed</h2>
                <p class="text-sm text-gray-500 mt-1 max-w-md mx-auto">The submission deadline for this assignment has passed. You can no longer submit your work.</p>
            </div>
        @endif
    </div>
@endsection
