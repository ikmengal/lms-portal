@extends('layouts.dashboard')
@section('title', 'Assignments')
@section('content')
    <div class="max-w-4xl mx-auto space-y-6">
        {{-- Header --}}
        <div class="flex items-center gap-3">
            <a href="{{ route('admin.courses.index') }}" class="p-2 text-gray-400 hover:text-gray-700 hover:bg-gray-100 rounded-lg transition">
                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18"/></svg>
            </a>
            <div class="flex-1 min-w-0">
                <h1 class="text-2xl font-bold text-gray-900 truncate">Assignments</h1>
                <p class="text-gray-500 mt-0.5 truncate">{{ $course->title }} · {{ $assignments->count() }} assignments</p>
            </div>
            <div class="flex items-center gap-2">
                <a href="{{ route('admin.courses.curriculum', $course) }}" class="px-4 py-2 text-sm font-medium text-primary-700 bg-primary-50 hover:bg-primary-100 rounded-lg transition">Curriculum</a>
                <a href="{{ route('admin.courses.assignments.create', $course) }}" class="inline-flex items-center gap-2 px-4 py-2 text-sm font-medium text-white bg-primary-600 hover:bg-primary-700 rounded-lg transition shadow-sm whitespace-nowrap">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/></svg>
                    New Assignment
                </a>
            </div>
        </div>

        {{-- List --}}
        <div class="space-y-3">
            @forelse($assignments as $assignment)
                @php
                    $submittedCount = $assignment->submissions->count();
                    $gradedCount = $assignment->submissions->where('status', 'graded')->count();
                    $isOverdue = $assignment->due_date && $assignment->due_date->isPast();
                @endphp
                <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-5 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                    <div class="flex items-start gap-4 min-w-0">
                        <div class="w-10 h-10 rounded-lg flex items-center justify-center shrink-0 bg-accent-100 text-accent-700">
                            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z"/></svg>
                        </div>
                        <div class="min-w-0">
                            <div class="flex items-center gap-2 flex-wrap">
                                <p class="text-sm font-semibold text-gray-900 truncate">{{ $assignment->title }}</p>
                                @if(!$assignment->is_active)
                                    <span class="px-2 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-500">Inactive</span>
                                @endif
                                @if($isOverdue)
                                    <span class="px-2 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-700">Overdue</span>
                                @endif
                            </div>
                            <p class="text-xs text-gray-400 mt-1">
                                @if($assignment->due_date)
                                    Due: {{ $assignment->due_date->format('M j, Y g:i A') }} ·
                                @endif
                                Max {{ $assignment->max_file_size_mb ?? 10 }}MB · {{ strtoupper($assignment->allowed_extensions ?? 'pdf,doc,docx') }}
                                · {{ $submittedCount }} submission{{ $submittedCount === 1 ? '' : 's' }}
                                @if($gradedCount > 0) · {{ $gradedCount }} graded @endif
                            </p>
                        </div>
                    </div>
                    <div class="flex items-center gap-1.5 shrink-0 self-end sm:self-auto">
                        <a href="{{ route('admin.courses.assignments.submissions', [$course, $assignment]) }}" class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-medium text-secondary-700 bg-secondary-50 border border-secondary-200 hover:bg-secondary-100 rounded-lg transition">
                            <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z"/></svg>
                            Submissions
                        </a>
                        <a href="{{ route('admin.courses.assignments.edit', [$course, $assignment]) }}" class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-medium text-gray-600 bg-white border border-gray-200 hover:bg-gray-50 rounded-lg transition">
                            <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0115.75 21H5.25A2.25 2.25 0 013 18.75V8.25A2.25 2.25 0 015.25 6H10"/></svg>
                            Edit
                        </a>
                        <button type="submit" form="delete-assignment-{{ $assignment->id }}"
                            data-confirm="This will permanently delete '{{ $assignment->title }}' and all submissions."
                            data-confirm-title="Delete assignment?"
                            class="p-1.5 text-gray-400 hover:text-red-600 hover:bg-red-50 rounded-lg transition" title="Delete">
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0"/></svg>
                        </button>
                    </div>
                </div>
            @empty
                <div class="bg-white rounded-xl border border-dashed border-gray-200 p-10 text-center">
                    <svg class="w-10 h-10 text-gray-300 mx-auto mb-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z"/></svg>
                    <p class="text-sm font-medium text-gray-500">No assignments yet</p>
                    <p class="text-sm text-gray-400 mt-1">Create file-based assignments for your students.</p>
                    <a href="{{ route('admin.courses.assignments.create', $course) }}" class="mt-4 inline-flex items-center px-4 py-2 text-sm font-medium text-white bg-primary-600 hover:bg-primary-700 rounded-lg transition">+ Create First Assignment</a>
                </div>
            @endforelse
        </div>
    </div>

    @foreach($assignments as $assignment)
        <form id="delete-assignment-{{ $assignment->id }}" method="POST" action="{{ route('admin.courses.assignments.destroy', [$course, $assignment]) }}" class="hidden">@csrf @method('DELETE')</form>
    @endforeach
@endsection
