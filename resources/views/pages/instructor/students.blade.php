@extends('layouts.dashboard')
@section('title', 'My Students')
@section('content')
    <div class="space-y-6">
        {{-- Header --}}
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">My Students</h1>
                <p class="text-gray-500 mt-1">Everyone enrolled across your courses.</p>
            </div>
        </div>

        {{-- Stats --}}
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
            <div class="bg-white rounded-xl p-5 border border-gray-100 shadow-sm">
                <p class="text-2xl font-bold text-gray-900">{{ $studentStats['total'] }}</p>
                <p class="text-sm text-gray-500 mt-0.5">Total Enrollments</p>
            </div>
            <div class="bg-white rounded-xl p-5 border border-gray-100 shadow-sm">
                <p class="text-2xl font-bold text-primary-600">{{ $studentStats['unique'] }}</p>
                <p class="text-sm text-gray-500 mt-0.5">Unique Students</p>
            </div>
            <div class="bg-white rounded-xl p-5 border border-gray-100 shadow-sm">
                <p class="text-2xl font-bold text-secondary-600">{{ $studentStats['completed'] }}</p>
                <p class="text-sm text-gray-500 mt-0.5">Completed</p>
            </div>
            <div class="bg-white rounded-xl p-5 border border-gray-100 shadow-sm">
                <p class="text-2xl font-bold text-accent-600">{{ $studentStats['justStarted'] }}</p>
                <p class="text-sm text-gray-500 mt-0.5">Not Started</p>
            </div>
        </div>

        {{-- Filters --}}
        <form method="GET" action="{{ route('instructor.students') }}" class="flex flex-col sm:flex-row gap-3 sm:items-center">
            <div class="relative w-full sm:w-64 shrink-0">
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Search by name or email..." class="pl-10 pr-4 h-[42px] border border-gray-200 rounded-lg text-sm focus:ring-2 focus:ring-primary-500 focus:border-primary-500 w-full" />
                <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607z"/></svg>
            </div>
            <select name="course" onchange="this.form.submit()"
                class="px-3 h-[42px] border border-gray-200 rounded-lg text-sm text-gray-700 bg-white focus:ring-2 focus:ring-primary-500 focus:border-primary-500 w-full sm:w-52 transition">
                <option value="">All My Courses</option>
                @foreach($myCourses as $course)
                    <option value="{{ $course->id }}" @selected(request('course') == $course->id)>{{ Str::limit($course->title, 30) }}</option>
                @endforeach
            </select>
            <select name="status" onchange="this.form.submit()"
                class="px-3 h-[42px] border border-gray-200 rounded-lg text-sm text-gray-700 bg-white focus:ring-2 focus:ring-primary-500 focus:border-primary-500 w-full sm:w-40 transition">
                <option value="">Any Status</option>
                <option value="active" @selected(request('status') === 'active')>In Progress</option>
                <option value="completed" @selected(request('status') === 'completed')>Completed</option>
                <option value="new" @selected(request('status') === 'new')>Not Started</option>
            </select>
            <div class="flex items-center gap-2 sm:ml-auto">
                <button type="submit" class="px-5 h-[42px] text-sm font-medium text-primary-700 bg-primary-50 hover:bg-primary-100 rounded-lg transition">Filter</button>
                @if(request()->filled('search') || request()->filled('course') || request()->filled('status'))
                    <a href="{{ route('instructor.students') }}" class="inline-flex items-center px-4 h-[42px] text-sm font-medium text-gray-500 hover:text-gray-700 transition">Clear</a>
                @endif
            </div>
        </form>

        {{-- Students Table --}}
        <div class="bg-white rounded-xl border border-gray-100 shadow-sm overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead>
                        <tr class="border-b border-gray-100 bg-gray-50">
                            <th class="text-left text-xs font-medium text-gray-500 uppercase tracking-wider px-6 py-3">Student</th>
                            <th class="text-left text-xs font-medium text-gray-500 uppercase tracking-wider px-6 py-3">Course</th>
                            <th class="text-left text-xs font-medium text-gray-500 uppercase tracking-wider px-6 py-3">Progress</th>
                            <th class="text-left text-xs font-medium text-gray-500 uppercase tracking-wider px-6 py-3">Status</th>
                            <th class="text-left text-xs font-medium text-gray-500 uppercase tracking-wider px-6 py-3">Enrolled</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50">
                        @forelse($enrollments as $enrollment)
                            <tr class="hover:bg-gray-50 transition">
                                <td class="px-6 py-4">
                                    <div class="flex items-center gap-3">
                                        <div class="w-9 h-9 rounded-full {{ $enrollment->isCompleted() ? 'bg-secondary-600' : 'bg-primary-600' }} text-white grid place-items-center text-xs font-bold shrink-0">
                                            {{ strtoupper(substr($enrollment->user?->name ?? 'U', 0, 1)) }}
                                        </div>
                                        <div class="min-w-0">
                                            <p class="text-sm font-medium text-gray-900 truncate">{{ $enrollment->user?->name ?? 'Deleted user' }}</p>
                                            <p class="text-xs text-gray-400 truncate">{{ $enrollment->user?->email }}</p>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 max-w-[220px]">
                                    <p class="text-sm text-gray-700 truncate">{{ $enrollment->course?->title }}</p>
                                </td>
                                <td class="px-6 py-4 w-44">
                                    <div class="flex items-center gap-2">
                                        <div class="flex-1 h-2 bg-gray-100 rounded-full overflow-hidden">
                                            <div class="h-full rounded-full {{ $enrollment->isCompleted() ? 'bg-secondary-500' : 'bg-primary-500' }}" style="width: {{ $enrollment->progress }}%"></div>
                                        </div>
                                        <span class="text-xs font-medium text-gray-500 w-9">{{ $enrollment->progress }}%</span>
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    @if($enrollment->isCompleted())
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-secondary-50 text-secondary-700">Completed</span>
                                    @elseif($enrollment->progress > 0)
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-50 text-blue-700">In Progress</span>
                                    @else
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-500">Not Started</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-400 whitespace-nowrap">{{ $enrollment->created_at->format('M j, Y') }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-6 py-12 text-center">
                                    <p class="text-sm text-gray-400">No students found{{ request()->filled() ? ' matching your filters.' : ' yet.' }}</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if($enrollments->hasPages())
                <div class="px-6 py-4 border-t border-gray-100">{{ $enrollments->links() }}</div>
            @endif
        </div>
    </div>
@endsection
