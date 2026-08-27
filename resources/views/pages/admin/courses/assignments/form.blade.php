@extends('layouts.dashboard')
@section('title', $quiz->exists ? 'Edit Assignment' : 'New Assignment')
@section('content')
    <div class="max-w-3xl mx-auto space-y-6">
        {{-- Header --}}
        <div class="flex items-center gap-3">
            <a href="{{ route('admin.courses.assignments.index', $course) }}" class="p-2 text-gray-400 hover:text-gray-700 hover:bg-gray-100 rounded-lg transition">
                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18"/></svg>
            </a>
            <div>
                <h1 class="text-2xl font-bold text-gray-900">{{ $quiz->exists ? 'Edit Assignment' : 'Create New Assignment' }}</h1>
                <p class="text-gray-500 mt-0.5 truncate">{{ $course->title }}</p>
            </div>
        </div>

        <form method="POST"
            action="{{ $quiz->exists ? route('admin.courses.assignments.update', [$course, $quiz]) : route('admin.courses.assignments.store', $course) }}"
            class="space-y-6">
            @csrf
            @if($quiz->exists)
                @method('PUT')
            @endif

            <input type="hidden" name="type" value="assignment">

            {{-- Settings --}}
            <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-6 space-y-5">
                <h2 class="text-lg font-semibold text-gray-900">Assignment Settings</h2>

                <div class="grid sm:grid-cols-2 gap-5">
                    <div class="sm:col-span-2">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Title <span class="text-red-500">*</span></label>
                        <input type="text" name="title" value="{{ old('title', $quiz->title) }}" required maxlength="255" placeholder="e.g. Final Project / Research Paper"
                            class="w-full px-4 py-2.5 border border-gray-200 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500 text-sm transition @error('title') border-red-300 @enderror" />
                        @error('title')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
                    </div>

                    <div class="sm:col-span-2">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Description / Instructions</label>
                        <textarea name="description" rows="3" maxlength="2000" placeholder="Describe what students need to submit..."
                            class="w-full px-4 py-2.5 border border-gray-200 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500 text-sm transition">{{ old('description', $quiz->description) }}</textarea>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Due Date</label>
                        <input type="datetime-local" name="due_date" value="{{ old('due_date', $quiz->due_date?->format('Y-m-d\TH:i')) }}"
                            class="w-full px-4 py-2.5 border border-gray-200 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500 text-sm transition @error('due_date') border-red-300 @enderror" />
                        <p class="mt-1 text-xs text-gray-400">Leave empty for no deadline. Students cannot submit after this time.</p>
                        @error('due_date')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Passing Percentage (%) <span class="text-red-500">*</span></label>
                        <input type="number" name="passing_score" value="{{ old('passing_score', $quiz->passing_score ?? 60) }}" required min="1" max="100"
                            class="w-full px-4 py-2.5 border border-gray-200 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500 text-sm transition @error('passing_score') border-red-300 @enderror" />
                        <p class="mt-1 text-xs text-gray-400">For instructor reference — students are graded manually.</p>
                        @error('passing_score')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Max File Size (MB) <span class="text-red-500">*</span></label>
                        <input type="number" name="max_file_size_mb" value="{{ old('max_file_size_mb', $quiz->max_file_size_mb ?? 10) }}" required min="1" max="50"
                            class="w-full px-4 py-2.5 border border-gray-200 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500 text-sm transition @error('max_file_size_mb') border-red-300 @enderror" />
                        @error('max_file_size_mb')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Allowed File Types</label>
                        <input type="text" name="allowed_extensions" value="{{ old('allowed_extensions', $quiz->allowed_extensions ?? 'pdf,doc,docx,png,jpg,jpeg') }}" maxlength="255"
                            placeholder="pdf,doc,docx,png,jpg,jpeg"
                            class="w-full px-4 py-2.5 border border-gray-200 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500 text-sm transition @error('allowed_extensions') border-red-300 @enderror" />
                        <p class="mt-1 text-xs text-gray-400">Comma-separated list of allowed extensions (without dots).</p>
                        @error('allowed_extensions')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
                    </div>

                    <div class="sm:col-span-2 flex items-center">
                        <label class="inline-flex items-center gap-3 cursor-pointer">
                            <input type="checkbox" name="is_active" value="1" {{ old('is_active', $quiz->is_active) ? 'checked' : '' }}
                                class="w-5 h-5 rounded border-gray-300 text-primary-600 focus:ring-primary-500 transition" />
                            <span class="text-sm font-medium text-gray-700">Active (visible to students)</span>
                        </label>
                    </div>
                </div>
            </div>

            {{-- Actions --}}
            <div class="flex items-center justify-end gap-3">
                <a href="{{ route('admin.courses.assignments.index', $course) }}" class="px-5 py-2.5 text-sm font-medium text-gray-600 hover:text-gray-900 transition">Cancel</a>
                <button type="submit" class="px-6 py-2.5 bg-primary-600 hover:bg-primary-700 text-white font-medium rounded-lg text-sm transition shadow-sm">
                    {{ $quiz->exists ? 'Save Changes' : 'Create Assignment' }}
                </button>
            </div>
        </form>
    </div>
@endsection
