@extends('layouts.dashboard')
@section('title', 'Submissions: ' . $quiz->title)
@section('content')
    <div class="max-w-5xl mx-auto space-y-6">
        {{-- Header --}}
        <div class="flex items-center gap-3">
            <a href="{{ route('admin.courses.assignments.index', $course) }}" class="p-2 text-gray-400 hover:text-gray-700 hover:bg-gray-100 rounded-lg transition">
                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18"/></svg>
            </a>
            <div class="flex-1 min-w-0">
                <h1 class="text-2xl font-bold text-gray-900 truncate">Submissions</h1>
                <p class="text-gray-500 mt-0.5 truncate">{{ $quiz->title }} · {{ $submissions->count() }} submission{{ $submissions->count() === 1 ? '' : 's' }}</p>
            </div>
        </div>

        {{-- Submissions Table --}}
        <div class="bg-white rounded-xl border border-gray-100 shadow-sm overflow-hidden">
            @if($submissions->isNotEmpty())
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead class="bg-gray-50 border-b border-gray-100">
                            <tr>
                                <th class="text-left px-6 py-3 font-semibold text-gray-600">Student</th>
                                <th class="text-left px-6 py-3 font-semibold text-gray-600">File</th>
                                <th class="text-left px-6 py-3 font-semibold text-gray-600">Submitted</th>
                                <th class="text-left px-6 py-3 font-semibold text-gray-600">Status</th>
                                <th class="text-left px-6 py-3 font-semibold text-gray-600">Marks</th>
                                <th class="text-left px-6 py-3 font-semibold text-gray-600">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-50">
                            @foreach($submissions as $sub)
                                <tr class="hover:bg-gray-50/50 transition">
                                    <td class="px-6 py-4">
                                        <div class="flex items-center gap-3">
                                            <x-avatar :user="$sub->user" size="w-8 h-8 text-xs" />
                                            <div>
                                                <p class="text-sm font-medium text-gray-900">{{ $sub->user->name }}</p>
                                                <p class="text-xs text-gray-400">{{ $sub->user->email }}</p>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4">
                                        <a href="{{ route('assignments.download', $sub) }}" class="inline-flex items-center gap-1.5 text-sm text-primary-600 hover:text-primary-700 font-medium">
                                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5M16.5 12L12 16.5m0 0L7.5 12m4.5 4.5V3"/></svg>
                                            {{ Str::limit($sub->file_original_name, 25) }}
                                        </a>
                                    </td>
                                    <td class="px-6 py-4 text-sm text-gray-500">
                                        {{ $sub->submitted_at->format('M j, Y g:i A') }}
                                        @if($sub->submitted_at->diffInMinutes($quiz->due_date ?? now()) > 0 && $quiz->due_date && $sub->submitted_at->gt($quiz->due_date))
                                            <span class="text-xs text-red-600 block">Late</span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4">
                                        @if($sub->isGraded())
                                            <span class="px-2 py-0.5 rounded-full text-xs font-semibold bg-secondary-100 text-secondary-700">Graded</span>
                                        @elseif($sub->isLate())
                                            <span class="px-2 py-0.5 rounded-full text-xs font-semibold bg-amber-100 text-amber-700">Late</span>
                                        @else
                                            <span class="px-2 py-0.5 rounded-full text-xs font-semibold bg-blue-100 text-blue-700">Submitted</span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 text-sm font-medium text-gray-900">
                                        {{ $sub->marks !== null ? number_format($sub->marks, 1) . '%' : '—' }}
                                    </td>
                                    <td class="px-6 py-4">
                                        <a href="{{ route('admin.courses.assignments.submissions.grade', [$course, $quiz, $sub]) }}"
                                            class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-medium {{ $sub->isGraded() ? 'text-gray-600 bg-gray-100 hover:bg-gray-200' : 'text-white bg-primary-600 hover:bg-primary-700' }} rounded-lg transition">
                                            <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931zm0 0L19.5 7.125"/></svg>
                                            {{ $sub->isGraded() ? 'Review' : 'Grade' }}
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="px-6 py-16 text-center">
                    <svg class="w-12 h-12 text-gray-300 mx-auto mb-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z"/></svg>
                    <p class="text-sm font-medium text-gray-500">No submissions yet</p>
                    <p class="text-sm text-gray-400 mt-1">Students will submit files here once the assignment is active.</p>
                </div>
            @endif
        </div>
    </div>
@endsection
