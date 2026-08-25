@extends('layouts.dashboard')
@section('title', 'Edit Course')
@section('content')
    <div class="max mx-auto space-y-6">
        <div class="flex items-center gap-3">
            <a href="{{ route('admin.courses.index') }}" class="p-2 text-gray-400 hover:text-gray-700 hover:bg-gray-100 rounded-lg transition">
                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18"/></svg>
            </a>
            <div class="flex-1">
                <h1 class="text-2xl font-bold text-gray-900">Edit Course</h1>
                <p class="text-gray-500 mt-0.5 truncate">{{ $course->title }} · /courses/{{ $course->slug }}</p>
            </div>
            <a href="{{ route('courses.show', $course) }}" target="_blank" class="inline-flex items-center gap-2 px-4 py-2 text-sm font-medium text-gray-600 bg-white border border-gray-200 rounded-lg hover:bg-gray-50 transition">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M13.5 6H5.25A2.25 2.25 0 003 8.25v10.5A2.25 2.25 0 005.25 21h10.5A2.25 2.25 0 0018 18.75V10.5m-10.5 6L21 3m0 0h-5.25M21 3v5.25"/></svg>
                View
            </a>
        </div>

        <form method="POST" action="{{ route('admin.courses.update', $course) }}" enctype="multipart/form-data">
            @csrf
            @method('PUT')
            @include('pages.admin.courses._form')
        </form>

        {{-- Danger Zone --}}
        <div class="bg-white rounded-xl border border-red-200 shadow-sm p-6">
            <h3 class="text-lg font-semibold text-red-700 mb-1">Danger Zone</h3>
            <p class="text-sm text-gray-500 mb-4">Permanently delete this course along with its enrollments and certificates. This cannot be undone.</p>
            <form method="POST" action="{{ route('admin.courses.destroy', $course) }}" data-confirm="This will move {{ $course->title }} to trash. You can restore it later from the trash." data-confirm-title="Delete {{ $course->title }}?" data-confirm-button="Yes, move to trash">
                @csrf
                @method('DELETE')
                <button type="submit" class="px-5 py-2.5 bg-red-50 hover:bg-red-100 text-red-700 font-medium rounded-lg text-sm border border-red-200 transition">Delete Course</button>
            </form>
        </div>
    </div>
@endsection
