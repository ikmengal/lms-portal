@extends('layouts.dashboard')
@section('title', 'Attendance - ' . $class->title)

@section('content')
<div class="max mx-auto">
    <div class="flex items-center justify-between mb-8">
        <div>
            <div class="flex items-center gap-2 text-sm text-gray-500 mb-1">
                <a href="{{ route('admin.courses.live-classes.index', $course) }}" class="hover:text-primary-600 transition">{{ $course->title }}</a>
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                <span>Attendance</span>
            </div>
            <h1 class="text-2xl font-bold text-gray-900">{{ $class->title }}</h1>
            <p class="text-sm text-gray-500 mt-1">
                {{ $class->scheduled_at->format('D, M j, Y \a\t g:i A') }}
                &middot; {{ $class->duration_minutes }} min
                &middot; {{ $attended->count() }} attended
            </p>
        </div>
    </div>

    {{-- Stats --}}
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-8">
        <div class="bg-white rounded-xl border border-gray-100 p-5">
            <p class="text-sm text-gray-500">Total Enrolled</p>
            <p class="text-2xl font-bold text-gray-900 mt-1">{{ $attended->count() + $absent->count() }}</p>
        </div>
        <div class="bg-white rounded-xl border border-green-100 p-5">
            <p class="text-sm text-green-600">Attended</p>
            <p class="text-2xl font-bold text-green-700 mt-1">{{ $attended->count() }}</p>
        </div>
        <div class="bg-white rounded-xl border border-red-100 p-5">
            <p class="text-sm text-red-600">Absent</p>
            <p class="text-2xl font-bold text-red-700 mt-1">{{ $absent->count() }}</p>
        </div>
    </div>

    {{-- Attended --}}
    <div class="bg-white rounded-xl border border-gray-100 overflow-hidden mb-6">
        <div class="px-6 py-4 border-b border-gray-100">
            <h2 class="text-lg font-semibold text-gray-900">Attended</h2>
        </div>
        @forelse($attended as $record)
            <div class="flex items-center justify-between px-6 py-3 border-b border-gray-50 last:border-0">
                <div class="flex items-center gap-3">
                    <x-avatar :user="$record->user" size="w-8 h-8 text-xs" />
                    <div>
                        <p class="text-sm font-medium text-gray-900">{{ $record->user->name }}</p>
                        <p class="text-xs text-gray-500">{{ $record->user->email }}</p>
                    </div>
                </div>
                <div class="text-right text-sm">
                    <p class="text-gray-900">Joined {{ $record->joined_at?->format('g:i A') ?? '-' }}</p>
                    @if($record->duration_seconds > 0)
                        <p class="text-xs text-gray-500">{{ floor($record->duration_seconds / 60) }}m {{ $record->duration_seconds % 60 }}s</p>
                    @endif
                </div>
            </div>
        @empty
            <div class="px-6 py-8 text-center text-sm text-gray-500">No students attended this class.</div>
        @endforelse
    </div>

    {{-- Absent --}}
    <div class="bg-white rounded-xl border border-gray-100 overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-100">
            <h2 class="text-lg font-semibold text-gray-900">Absent</h2>
        </div>
        @forelse($absent as $student)
            <div class="flex items-center gap-3 px-6 py-3 border-b border-gray-50 last:border-0">
                <x-avatar :user="$student" size="w-8 h-8 text-xs" />
                <div>
                    <p class="text-sm font-medium text-gray-900">{{ $student->name }}</p>
                    <p class="text-xs text-gray-500">{{ $student->email }}</p>
                </div>
            </div>
        @empty
            <div class="px-6 py-8 text-center text-sm text-gray-500">All students attended!</div>
        @endforelse
    </div>
</div>
@endsection
