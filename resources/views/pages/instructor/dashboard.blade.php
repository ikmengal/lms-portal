@extends('layouts.dashboard')
@section('title', 'Instructor Dashboard')
@section('content')
    <div class="space-y-8">
        {{-- Header --}}
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div class="flex items-center gap-4">
                <x-avatar :user="auth()->user()" size="w-12 h-12 text-lg" />
                <div>
                    <h1 class="text-2xl font-bold text-gray-900">Instructor Dashboard</h1>
                    <p class="text-gray-500 mt-1">Welcome back, {{ auth()->user()->name }}. Here's how your courses are doing.</p>
                </div>
            </div>
            <div class="flex items-center gap-2">
                <a href="{{ route('instructor.students') }}" class="inline-flex items-center gap-2 px-4 py-2.5 text-sm font-medium text-gray-600 bg-white border border-gray-200 rounded-lg hover:bg-gray-50 transition">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19.128a9.38 9.38 0 002.625.372 9.337 9.337 0 004.121-.952 4.125 4.125 0 00-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 018.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0111.964-3.07M12 6.375a3.375 3.375 0 11-6.75 0 3.375 3.375 0 016.75 0zm8.25 2.25a2.625 2.625 0 11-5.25 0 2.625 2.625 0 015.25 0z"/></svg>
                    Students
                </a>
                <a href="{{ route('admin.courses.create') }}" class="inline-flex items-center gap-2 px-5 py-2.5 bg-primary-600 hover:bg-primary-700 text-white font-medium rounded-lg transition shadow-sm">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/></svg>
                    New Course
                </a>
            </div>
        </div>

        {{-- Stats --}}
        <div class="grid sm:grid-cols-2 lg:grid-cols-4 gap-6">
            <div class="bg-white rounded-xl p-6 border border-gray-100 shadow-sm">
                <div class="flex items-center gap-4">
                    <div class="w-12 h-12 bg-primary-100 rounded-xl flex items-center justify-center">
                        <svg class="w-6 h-6 text-primary-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6.042A8.967 8.967 0 006 3.75c-1.052 0-2.062.18-3 .512v14.25A8.987 8.987 0 016 18c2.305 0 4.408.867 6 2.292m0-14.25a8.966 8.966 0 016-2.292c1.052 0 2.062.18 3 .512v14.25A8.987 8.987 0 0018 18a8.967 8.967 0 00-6 2.292m0-14.25v14.25"/></svg>
                    </div>
                    <div>
                        <p class="text-2xl font-bold text-gray-900">{{ $stats['totalCourses'] }}</p>
                        <p class="text-sm text-gray-500">My Courses</p>
                    </div>
                </div>
                <p class="mt-3 text-xs text-gray-400">{{ $stats['publishedLessons'] }} lessons published</p>
            </div>

            <div class="bg-white rounded-xl p-6 border border-gray-100 shadow-sm">
                <div class="flex items-center gap-4">
                    <div class="w-12 h-12 bg-accent-100 rounded-xl flex items-center justify-center">
                        <svg class="w-6 h-6 text-accent-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19.128a9.38 9.38 0 002.625.372 9.337 9.337 0 004.121-.952 4.125 4.125 0 00-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 018.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0111.964-3.07M12 6.375a3.375 3.375 0 11-6.75 0 3.375 3.375 0 016.75 0zm8.25 2.25a2.625 2.625 0 11-5.25 0 2.625 2.625 0 015.25 0z"/></svg>
                    </div>
                    <div>
                        <p class="text-2xl font-bold text-gray-900">{{ $stats['totalStudents'] }}</p>
                        <p class="text-sm text-gray-500">Total Students</p>
                    </div>
                </div>
                <p class="mt-3 text-xs text-gray-400">{{ $stats['completedCount'] }} completed · {{ $stats['avgProgress'] }}% avg progress</p>
            </div>

            <div class="bg-white rounded-xl p-6 border border-gray-100 shadow-sm">
                <div class="flex items-center gap-4">
                    <div class="w-12 h-12 bg-secondary-100 rounded-xl flex items-center justify-center">
                        <svg class="w-6 h-6 text-secondary-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6v12m-3-2.818l.879.659c1.171.879 3.07.879 4.242 0 1.172-.879 1.172-2.303 0-3.182C13.536 12.219 12.768 12 12 12c-.725 0-1.45-.22-2.003-.659-1.106-.879-1.106-2.303 0-3.182s2.9-.879 4.006 0l.415.33M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    </div>
                    <div>
                        <p class="text-2xl font-bold text-gray-900">${{ number_format($stats['revenueTotal'], 0) }}</p>
                        <p class="text-sm text-gray-500">Total Earnings</p>
                    </div>
                </div>
                @if($stats['revenueGrowth'] !== null && $stats['revenueLastMonth'] > 0)
                    <p class="mt-3 text-xs {{ $stats['revenueGrowth'] >= 0 ? 'text-secondary-600' : 'text-red-500' }}">
                        {{ $stats['revenueGrowth'] >= 0 ? '▲' : '▼' }} {{ abs($stats['revenueGrowth']) }}% vs last month (${{ number_format($stats['revenueThisMonth'], 0) }} this month)
                    </p>
                @else
                    <p class="mt-3 text-xs text-gray-400">${{ number_format($stats['revenueThisMonth'], 0) }} this month</p>
                @endif
            </div>

            <div class="bg-white rounded-xl p-6 border border-gray-100 shadow-sm">
                <div class="flex items-center gap-4">
                    <div class="w-12 h-12 bg-amber-100 rounded-xl flex items-center justify-center">
                        <svg class="w-6 h-6 text-amber-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M11.48 3.499a.562.562 0 011.04 0l2.125 5.111a.563.563 0 00.475.345l5.518.442c.499.04.701.663.321.988l-4.204 3.602a.563.563 0 00-.182.557l1.285 5.385a.562.562 0 01-.84.61l-4.725-2.885a.563.563 0 00-.586 0L6.982 20.54a.562.562 0 01-.84-.61l1.285-5.386a.562.562 0 00-.182-.557l-4.204-3.602a.563.563 0 01.321-.988l5.518-.442a.563.563 0 00.475-.345L11.48 3.5z"/></svg>
                    </div>
                    <div>
                        <p class="text-2xl font-bold text-gray-900">{{ $stats['avgRating'] ?? '—' }}</p>
                        <p class="text-sm text-gray-500">Avg Rating</p>
                    </div>
                </div>
                <p class="mt-3 text-xs text-gray-400">from {{ $stats['totalReviews'] }} review{{ $stats['totalReviews'] === 1 ? '' : 's' }}</p>
            </div>
        </div>

        {{-- Empty state --}}
        @if($courses->isEmpty())
            <div class="bg-white rounded-2xl border border-dashed border-gray-300 p-12 text-center">
                <div class="w-14 h-14 mx-auto bg-primary-50 rounded-2xl flex items-center justify-center mb-4">
                    <svg class="w-7 h-7 text-primary-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6.042A8.967 8.967 0 006 3.75c-1.052 0-2.062.18-3 .512v14.25A8.987 8.987 0 016 18c2.305 0 4.408.867 6 2.292m0-14.25a8.966 8.966 0 016-2.292c1.052 0 2.062.18 3 .512v14.25A8.987 8.987 0 0018 18a8.967 8.967 0 00-6 2.292m0-14.25v14.25"/></svg>
                </div>
                <h2 class="text-lg font-semibold text-gray-900">Create your first course</h2>
                <p class="text-sm text-gray-500 mt-1 max-w-sm mx-auto">Publish a course, add lessons and tests, and start teaching students around the world.</p>
                <a href="{{ route('admin.courses.create') }}" class="inline-flex items-center gap-2 mt-5 px-6 py-2.5 bg-primary-600 hover:bg-primary-700 text-white font-medium rounded-lg transition shadow-sm">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/></svg>
                    Create Course
                </a>
            </div>
        @else

        {{-- My Courses --}}
        <div class="bg-white rounded-xl border border-gray-100 shadow-sm overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between">
                <h2 class="text-lg font-semibold text-gray-900">My Courses</h2>
                <a href="{{ route('admin.courses.index') }}" class="text-sm font-medium text-primary-600 hover:text-primary-700">View all →</a>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead>
                        <tr class="border-b border-gray-100 bg-gray-50">
                            <th class="text-left text-xs font-medium text-gray-500 uppercase tracking-wider px-6 py-3">Course</th>
                            <th class="text-left text-xs font-medium text-gray-500 uppercase tracking-wider px-6 py-3">Students</th>
                            <th class="text-left text-xs font-medium text-gray-500 uppercase tracking-wider px-6 py-3">Content</th>
                            <th class="text-left text-xs font-medium text-gray-500 uppercase tracking-wider px-6 py-3">Rating</th>
                            <th class="text-left text-xs font-medium text-gray-500 uppercase tracking-wider px-6 py-3">Revenue</th>
                            <th class="text-right text-xs font-medium text-gray-500 uppercase tracking-wider px-6 py-3">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50">
                        @foreach($courses->take(5) as $course)
                            <tr class="hover:bg-gray-50 transition">
                                <td class="px-6 py-4 max-w-xs">
                                    <p class="text-sm font-medium text-gray-900 truncate">{{ $course->title }}</p>
                                    <p class="text-xs text-gray-400 mt-0.5">{{ $course->category }} · ${{ number_format($course->price, 2) }}</p>
                                </td>
                                <td class="px-6 py-4 text-sm font-semibold text-gray-700">{{ $course->enrollments_count }}</td>
                                <td class="px-6 py-4 text-sm text-gray-500">
                                    {{ $course->modules_count }} modules
                                    <span class="text-gray-300 mx-1">·</span>
                                    {{ $course->quizzes()->count() }} tests
                                </td>
                                <td class="px-6 py-4">
                                    @if($course->reviews_avg_rating !== null)
                                        <span class="inline-flex items-center gap-1 text-sm font-medium text-amber-600">
                                            <svg class="w-4 h-4 fill-current" viewBox="0 0 24 24"><path d="M11.48 3.499a.562.562 0 011.04 0l2.125 5.111a.563.563 0 00.475.345l5.518.442c.499.04.701.663.321.988l-4.204 3.602a.563.563 0 00-.182.557l1.285 5.385a.562.562 0 01-.84.61l-4.725-2.885a.563.563 0 00-.586 0L6.982 20.54a.562.562 0 01-.84-.61l1.285-5.386a.562.562 0 00-.182-.557l-4.204-3.602a.563.563 0 01.321-.988l5.518-.442a.563.563 0 00.475-.345L11.48 3.5z"/></svg>
                                            {{ number_format($course->reviews_avg_rating, 1) }}
                                        </span>
                                        <span class="text-xs text-gray-400 ml-1">({{ $course->reviews_count }})</span>
                                    @else
                                        <span class="text-sm text-gray-300">No reviews</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 text-sm font-semibold text-secondary-600">${{ number_format(($course->price ?? 0) * $course->enrollments_count, 0) }}</td>
                                <td class="px-6 py-4 text-right">
                                    <div class="flex items-center justify-end gap-1.5">
                                        <a href="{{ route('admin.courses.show', $course) }}" class="p-1.5 text-gray-400 hover:text-gray-700 hover:bg-gray-100 rounded-lg transition" title="View Course">
                                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178z"/><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                                        </a>
                                        <a href="{{ route('admin.courses.edit', $course) }}" class="p-1.5 text-gray-400 hover:text-primary-600 hover:bg-primary-50 rounded-lg transition" title="Edit Course">
                                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0115.75 21H5.25A2.25 2.25 0 013 18.75V8.25A2.25 2.25 0 015.25 6H10"/></svg>
                                        </a>
                                        <a href="{{ route('admin.courses.curriculum', $course) }}" class="p-1.5 text-gray-400 hover:text-secondary-600 hover:bg-secondary-50 rounded-lg transition" title="Curriculum">
                                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25H12"/></svg>
                                        </a>
                                        <a href="{{ route('admin.courses.tests.index', $course) }}" class="p-1.5 text-gray-400 hover:text-accent-600 hover:bg-accent-50 rounded-lg transition" title="Tests & Exams">
                                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12c0 1.268-.63 2.39-1.593 3.068a3.745 3.745 0 01-1.043 3.296 3.745 3.745 0 01-3.296 1.043A3.745 3.745 0 0112 21c-1.268 0-2.39-.63-3.068-1.593a3.746 3.746 0 01-3.296-1.043 3.745 3.745 0 01-1.043-3.296A3.745 3.745 0 013 12c0-1.268.63-2.39 1.593-3.068a3.746 3.746 0 011.043-3.296 3.746 3.746 0 013.296-1.043A3.746 3.746 0 0112 3c1.268 0 2.39.63 3.068 1.593a3.746 3.746 0 013.296 1.043 3.746 3.746 0 011.043 3.296A3.745 3.745 0 0121 12z"/></svg>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <div class="grid lg:grid-cols-2 gap-6">
            {{-- Recent Enrollments --}}
            <div class="bg-white rounded-xl border border-gray-100 shadow-sm overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between">
                    <h2 class="text-lg font-semibold text-gray-900">Recent Enrollments</h2>
                    <a href="{{ route('instructor.students') }}" class="text-sm font-medium text-primary-600 hover:text-primary-700">All students →</a>
                </div>
                @if($recentEnrollments->isEmpty())
                    <p class="px-6 py-8 text-sm text-gray-400 text-center">No enrollments yet.</p>
                @else
                    <ul class="divide-y divide-gray-50">
                        @foreach($recentEnrollments as $enrollment)
                            <li class="px-6 py-3.5 flex items-center gap-3 hover:bg-gray-50 transition">
                                <div class="w-9 h-9 rounded-full bg-primary-600 text-white grid place-items-center text-xs font-bold shrink-0">
                                    {{ strtoupper(substr($enrollment->user?->name ?? 'U', 0, 1)) }}
                                </div>
                                <div class="min-w-0 flex-1">
                                    <p class="text-sm font-medium text-gray-900 truncate">{{ $enrollment->user?->name ?? 'Deleted user' }}</p>
                                    <p class="text-xs text-gray-400 truncate">enrolled in <span class="font-medium text-gray-600">{{ $enrollment->course?->title }}</span></p>
                                </div>
                                <div class="text-right shrink-0">
                                    <p class="text-xs font-medium text-gray-500">{{ $enrollment->created_at->diffForHumans() }}</p>
                                    <div class="flex items-center gap-1.5 justify-end mt-1">
                                        <div class="w-16 h-1.5 bg-gray-100 rounded-full overflow-hidden">
                                            <div class="h-full bg-primary-500 rounded-full" style="width: {{ $enrollment->progress }}%"></div>
                                        </div>
                                        <span class="text-[10px] text-gray-400 w-7">{{ $enrollment->progress }}%</span>
                                    </div>
                                </div>
                            </li>
                        @endforeach
                    </ul>
                @endif
            </div>

            {{-- Top Courses --}}
            <div class="bg-white rounded-xl border border-gray-100 shadow-sm overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-100">
                    <h2 class="text-lg font-semibold text-gray-900">Top Performing Courses</h2>
                </div>
                @php $maxStudents = max($topCourses->max('enrollments_count'), 1); @endphp
                <div class="p-6 space-y-5">
                    @forelse($topCourses as $course)
                        <div>
                            <div class="flex items-center justify-between mb-1.5">
                                <p class="text-sm font-medium text-gray-800 truncate pr-3">{{ $course->title }}</p>
                                <span class="text-xs text-gray-500 shrink-0">{{ $course->enrollments_count }} student{{ $course->enrollments_count === 1 ? '' : 's' }}</span>
                            </div>
                            <div class="h-2.5 bg-gray-100 rounded-full overflow-hidden">
                                <div class="h-full rounded-full {{ $loop->index === 0 ? 'bg-gradient-to-r from-primary-500 to-primary-600' : 'bg-primary-400/80' }}" style="width: {{ round($course->enrollments_count / $maxStudents * 100) }}%"></div>
                            </div>
                        </div>
                    @empty
                        <p class="text-sm text-gray-400 text-center py-4">No data yet.</p>
                    @endforelse
                </div>
            </div>
        </div>
        @endif
    </div>
@endsection
