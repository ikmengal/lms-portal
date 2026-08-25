@extends('layouts.dashboard')
@section('title', 'Manage Courses')
@section('content')
    <div class="space-y-6">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Manage Courses</h1>
                <p class="text-gray-500 mt-1">Create, edit and manage all courses.</p>
            </div>
            <div class="flex items-center gap-2">
                @role('admin')
                    <a href="{{ route('admin.courses.trash') }}" class="inline-flex items-center gap-2 px-4 py-2.5 text-sm font-medium text-gray-600 bg-white border border-gray-200 rounded-lg hover:bg-gray-50 transition">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0"/></svg>
                        Trash
                    </a>
                @endrole
                <a href="{{ route('admin.courses.create') }}" class="inline-flex items-center gap-2 px-4 py-2.5 text-sm font-medium text-white bg-primary-600 hover:bg-primary-700 rounded-lg transition shadow-sm">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/></svg>
                    Add Course
                </a>
            </div>
        </div>

        {{-- Filters --}}
        <form method="GET" action="{{ route('admin.courses.index') }}" class="flex flex-col sm:flex-row gap-3 sm:items-center">
            <div class="relative sm:w-64 shrink-0">
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Search by title..." class="pl-10 pr-4 h-[42px] border border-gray-200 rounded-lg text-sm focus:ring-2 focus:ring-primary-500 focus:border-primary-500 w-full" />
                <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607z"/></svg>
            </div>
            <select name="category" class="px-3 h-[42px] border border-gray-200 rounded-lg text-sm text-gray-700 bg-white focus:ring-2 focus:ring-primary-500 focus:border-primary-500 w-full sm:w-44 transition">
                <option value="">All Categories</option>
                @foreach($categories as $cat)
                    <option value="{{ $cat->id }}" @selected(request('category') == $cat->id)>{{ $cat->name }}</option>
                @endforeach
            </select>
            <select name="level" class="px-3 h-[42px] border border-gray-200 rounded-lg text-sm text-gray-700 bg-white focus:ring-2 focus:ring-primary-500 focus:border-primary-500 w-full sm:w-40 transition">
                <option value="">All Levels</option>
                @foreach($levels as $level)
                    <option value="{{ $level->id }}" @selected(request('level') == $level->id)>{{ $level->name }}</option>
                @endforeach
            </select>
            <div class="flex items-center gap-2 sm:ml-auto">
                <button type="submit" class="px-5 h-[42px] text-sm font-medium text-primary-700 bg-primary-50 hover:bg-primary-100 rounded-lg transition">Filter</button>
                @if(request()->filled('search') || request()->filled('category') || request()->filled('level'))
                    <a href="{{ route('admin.courses.index') }}" class="inline-flex items-center gap-1.5 px-4 h-[42px] text-sm font-medium text-gray-500 hover:text-gray-700 transition">
                        Clear
                    </a>
                @endif
            </div>
        </form>

        {{-- Courses Table --}}
        <div class="bg-white rounded-xl border border-gray-100 shadow-sm overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead>
                        <tr class="border-b border-gray-100 bg-gray-50">
                            <th class="text-left text-xs font-medium text-gray-500 uppercase tracking-wider px-6 py-3">Course</th>
                            <th class="text-left text-xs font-medium text-gray-500 uppercase tracking-wider px-6 py-3">Category</th>
                            <th class="text-left text-xs font-medium text-gray-500 uppercase tracking-wider px-6 py-3">Level</th>
                            <th class="text-left text-xs font-medium text-gray-500 uppercase tracking-wider px-6 py-3">Price</th>
                            <th class="text-left text-xs font-medium text-gray-500 uppercase tracking-wider px-6 py-3">Students</th>
                            <th class="text-right text-xs font-medium text-gray-500 uppercase tracking-wider px-6 py-3">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50">
                        @forelse($courses as $course)
                            <tr class="hover:bg-gray-50 transition">
                                <td class="px-6 py-4 max-w-sm">
                                    <p class="text-sm font-medium text-gray-900 truncate">{{ $course->title }}</p>
                                    <p class="text-xs text-gray-400 mt-0.5 truncate">{{ $course->duration_hours }}h · {{ $course->instructor->name ?? 'No instructor' }}</p>
                                </td>
                                <td class="px-6 py-4">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-50 text-blue-700">{{ $course->category ?? '—' }}</span>
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-500">{{ $course->level }}</td>
                                <td class="px-6 py-4 text-sm font-semibold text-gray-900">${{ number_format($course->price, 2) }}</td>
                                <td class="px-6 py-4 text-sm text-gray-500">{{ $course->enrollments->count() }}</td>
                                <td class="px-6 py-4 text-right">
                                    <div class="flex items-center justify-end gap-1.5">
                                        <a href="{{ route('admin.courses.show', $course) }}" class="p-1.5 text-gray-400 hover:text-gray-700 hover:bg-gray-100 rounded-lg transition" title="Details">
                                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m2.25 12h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z"/></svg>
                                        </a>
                                        <a href="{{ route('admin.courses.curriculum', $course) }}" class="p-1.5 text-gray-400 hover:text-secondary-600 hover:bg-secondary-50 rounded-lg transition" title="Curriculum">
                                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25H12"/></svg>
                                        </a>
                                        <a href="{{ route('admin.courses.tests.index', $course) }}" class="p-1.5 text-gray-400 hover:text-accent-600 hover:bg-accent-50 rounded-lg transition" title="Tests & Exams">
                                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12c0 1.268-.63 2.39-1.593 3.068a3.745 3.745 0 01-1.043 3.296 3.745 3.745 0 01-3.296 1.043A3.745 3.745 0 0112 21c-1.268 0-2.39-.63-3.068-1.593a3.746 3.746 0 01-3.296-1.043 3.745 3.745 0 01-1.043-3.296A3.745 3.745 0 013 12c0-1.268.63-2.39 1.593-3.068a3.746 3.746 0 011.043-3.296 3.746 3.746 0 013.296-1.043A3.746 3.746 0 0112 3c1.268 0 2.39.63 3.068 1.593a3.746 3.746 0 013.296 1.043 3.746 3.746 0 011.043 3.296A3.745 3.745 0 0121 12z"/></svg>
                                        </a>
                                        <a href="{{ route('courses.show', $course) }}" target="_blank" class="p-1.5 text-gray-400 hover:text-primary-600 hover:bg-primary-50 rounded-lg transition" title="View">
                                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178z"/><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                                        </a>
                                        <a href="{{ route('admin.courses.edit', $course) }}" class="p-1.5 text-gray-400 hover:text-primary-600 hover:bg-primary-50 rounded-lg transition" title="Edit">
                                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0115.75 21H5.25A2.25 2.25 0 013 18.75V8.25A2.25 2.25 0 015.25 6H10"/></svg>
                                        </a>
                                        <button type="submit" form="delete-course-{{ $course->id }}"
                                            data-confirm="This will move {{ $course->title }} to trash. You can restore it later."
                                            data-confirm-title="Delete course?"
                                            data-confirm-button="Yes, move to trash"
                                            class="p-1.5 text-gray-400 hover:text-red-600 hover:bg-red-50 rounded-lg transition" title="Delete">
                                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0"/></svg>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="6" class="px-6 py-8 text-center text-sm text-gray-400">No courses found.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if($courses->hasPages())
                <div class="px-6 py-4 border-t border-gray-100">
                    {{ $courses->links() }}
                </div>
            @endif
        </div>
    </div>

    {{-- Hidden delete forms --}}
    @foreach($courses as $course)
        <form id="delete-course-{{ $course->id }}" method="POST" action="{{ route('admin.courses.destroy', $course) }}" class="hidden">
            @csrf
            @method('DELETE')
        </form>
    @endforeach
@endsection
