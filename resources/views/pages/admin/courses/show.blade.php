@extends('layouts.dashboard')
@section('title', 'Course Details')
@section('content')
    <div class="max-w-5xl mx-auto space-y-6">
        {{-- Header --}}
        <div class="flex items-center gap-3">
            <a href="{{ route('admin.courses.index') }}" class="p-2 text-gray-400 hover:text-gray-700 hover:bg-gray-100 rounded-lg transition">
                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18"/></svg>
            </a>
            <div class="flex-1 min-w-0">
                <h1 class="text-2xl font-bold text-gray-900 truncate">{{ $course->title }}</h1>
                <p class="text-gray-500 mt-0.5">Full course overview and management.</p>
            </div>
        </div>

        {{-- Quick Actions --}}
        <div class="flex flex-wrap gap-2">
            <a href="{{ route('courses.show', $course) }}" target="_blank" class="inline-flex items-center gap-2 px-4 py-2 text-sm font-medium text-gray-600 bg-white border border-gray-200 rounded-lg hover:bg-gray-50 transition">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178z"/><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                View Public Page
            </a>
            <a href="{{ route('admin.courses.edit', $course) }}" class="inline-flex items-center gap-2 px-4 py-2 text-sm font-medium text-primary-700 bg-primary-50 hover:bg-primary-100 rounded-lg transition">Edit Details</a>
            <a href="{{ route('admin.courses.curriculum', $course) }}" class="inline-flex items-center gap-2 px-4 py-2 text-sm font-medium text-primary-700 bg-primary-50 hover:bg-primary-100 rounded-lg transition">Curriculum</a>
            <a href="{{ route('admin.courses.tests.index', $course) }}" class="inline-flex items-center gap-2 px-4 py-2 text-sm font-medium text-primary-700 bg-primary-50 hover:bg-primary-100 rounded-lg transition">Tests & Exams</a>
        </div>

        {{-- Hero Card --}}
        <div class="bg-white rounded-xl border border-gray-100 shadow-sm overflow-hidden">
            <div class="p-6 flex flex-col sm:flex-row gap-6">
                <div class="w-full sm:w-56 h-32 bg-gradient-to-br from-primary-600 to-primary-800 rounded-xl shrink-0 overflow-hidden flex items-center justify-center">
                    @if($course->thumbnail)
                        <img src="{{ asset('assets/upload/' . $course->thumbnail) }}" class="w-full h-full object-cover" alt="{{ $course->title }}">
                    @else
                        <svg class="w-10 h-10 text-white/60" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6.042A8.967 8.967 0 006 3.75c-1.052 0-2.062.18-3 .512v14.25A8.987 8.987 0 016 18c2.305 0 4.408.867 6 2.292m0-14.25a8.966 8.966 0 016-2.292c1.052 0 2.062.18 3 .512v14.25A8.987 8.987 0 0018 18a8.967 8.967 0 00-6 2.292m0-14.25v14.25"/></svg>
                    @endif
                </div>
                <div class="flex-1 min-w-0">
                    <div class="flex flex-wrap items-center gap-2 mb-2">
                        <span class="px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-50 text-blue-700">{{ $course->category ?? 'Uncategorized' }}</span>
                        <span class="px-2.5 py-0.5 rounded-full text-xs font-medium bg-accent-100 text-accent-800">{{ $course->level }}</span>
                        @if($course->trashed())
                            <span class="px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-700">In Trash</span>
                        @endif
                    </div>
                    <h2 class="text-lg font-bold text-gray-900">{{ $course->title }}</h2>
                    <p class="text-sm text-gray-500 mt-1 line-clamp-2">{{ Str::limit($course->description, 180) }}</p>
                    <dl class="grid grid-cols-2 sm:grid-cols-4 gap-4 mt-4 text-sm">
                        <div><dt class="text-xs text-gray-400 uppercase tracking-wide">Price</dt><dd class="font-semibold text-gray-900">${{ number_format($course->price, 2) }}</dd></div>
                        <div><dt class="text-xs text-gray-400 uppercase tracking-wide">Duration</dt><dd class="font-semibold text-gray-900">{{ $course->duration_hours }}h</dd></div>
                        <div><dt class="text-xs text-gray-400 uppercase tracking-wide">Instructor</dt><dd class="font-semibold text-gray-900 truncate">{{ $course->instructor->name ?? '—' }}</dd></div>
                        <div><dt class="text-xs text-gray-400 uppercase tracking-wide">Slug</dt><dd class="font-mono text-xs text-gray-600 truncate">{{ $course->slug }}</dd></div>
                    </dl>
                </div>
            </div>
        </div>

        {{-- Stats --}}
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
            @php $fmt = sprintf('%dh %02dm', intdiv($totalMinutes, 60), $totalMinutes % 60); @endphp
            <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-5">
                <p class="text-xs font-medium text-gray-400 uppercase tracking-wider">Enrolled Students</p>
                <p class="text-2xl font-bold text-primary-700 mt-1">{{ $studentsCount }}</p>
            </div>
            <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-5">
                <p class="text-xs font-medium text-gray-400 uppercase tracking-wider">Chapters</p>
                <p class="text-2xl font-bold text-primary-700 mt-1">{{ $course->modules->count() }}</p>
            </div>
            <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-5">
                <p class="text-xs font-medium text-gray-400 uppercase tracking-wider">Lessons</p>
                <p class="text-2xl font-bold text-primary-700 mt-1">{{ $lecturesCount }} <span class="text-sm font-normal text-gray-400">({{ $fmt }})</span></p>
            </div>
            <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-5">
                <p class="text-xs font-medium text-gray-400 uppercase tracking-wider">Tests & Exams</p>
                <p class="text-2xl font-bold text-primary-700 mt-1">{{ $course->quizzes->count() }}</p>
            </div>
        </div>

        <div class="grid lg:grid-cols-2 gap-6">
            {{-- Curriculum Preview --}}
            <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-6">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-semibold text-gray-900">Curriculum</h3>
                    <a href="{{ route('admin.courses.curriculum', $course) }}" class="text-sm font-medium text-primary-600 hover:text-primary-700">Manage →</a>
                </div>
                <div class="space-y-3 max-h-80 overflow-y-auto pr-1">
                    @forelse($course->modules as $i => $module)
                        <div class="border border-gray-100 rounded-lg p-3">
                            <p class="text-sm font-medium text-gray-900">{{ $i + 1 }}. {{ $module->title }} <span class="text-xs text-gray-400 font-normal">({{ $module->lessons->count() }} lessons)</span></p>
                        </div>
                    @empty
                        <p class="text-sm text-gray-400 text-center py-6">No chapters yet. Add them from the curriculum manager.</p>
                    @endforelse
                </div>
            </div>

            {{-- Tests Preview --}}
            <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-6">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-semibold text-gray-900">Tests & Exams</h3>
                    <a href="{{ route('admin.courses.tests.index', $course) }}" class="text-sm font-medium text-primary-600 hover:text-primary-700">Manage →</a>
                </div>
                <div class="space-y-3 max-h-80 overflow-y-auto pr-1">
                    @forelse($course->quizzes as $quiz)
                        <div class="border border-gray-100 rounded-lg p-3 flex items-center justify-between gap-3">
                            <div class="min-w-0">
                                <p class="text-sm font-medium text-gray-900 truncate">{{ $quiz->title }}</p>
                                <p class="text-xs text-gray-400">{{ $quiz->questions->count() }} questions · Pass ≥ {{ $quiz->passing_score }}%</p>
                            </div>
                            <span class="shrink-0 px-2 py-0.5 rounded-full text-xs font-medium {{ $quiz->type === 'final_exam' ? 'bg-red-100 text-red-700' : ($quiz->type === 'assignment' ? 'bg-accent-100 text-accent-800' : 'bg-primary-100 text-primary-800') }}">{{ str_replace('_', ' ', ucfirst($quiz->type)) }}</span>
                        </div>
                    @empty
                        <p class="text-sm text-gray-400 text-center py-6">No tests yet. Create quizzes or a final exam.</p>
                    @endforelse
                </div>
            </div>
        </div>

        {{-- Enrolled Students --}}
        <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Recent Students</h3>
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead>
                        <tr class="border-b border-gray-100 bg-gray-50">
                            <th class="text-left text-xs font-medium text-gray-500 uppercase tracking-wider px-4 py-2.5">Student</th>
                            <th class="text-left text-xs font-medium text-gray-500 uppercase tracking-wider px-4 py-2.5">Progress</th>
                            <th class="text-left text-xs font-medium text-gray-500 uppercase tracking-wider px-4 py-2.5">Enrolled</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50">
                        @forelse($recentStudents as $enrollment)
                            <tr class="hover:bg-gray-50 transition">
                                <td class="px-4 py-3">
                                    <div class="flex items-center gap-3">
                                        <div class="w-8 h-8 bg-primary-100 rounded-full flex items-center justify-center text-primary-700 text-xs font-bold">{{ strtoupper(substr($enrollment->user->name, 0, 2)) }}</div>
                                        <div>
                                            <p class="text-sm font-medium text-gray-900">{{ $enrollment->user->name }}</p>
                                            <p class="text-xs text-gray-400">{{ $enrollment->user->email }}</p>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-4 py-3 w-48">
                                    <div class="flex items-center gap-2">
                                        <div class="flex-1 h-1.5 bg-gray-100 rounded-full overflow-hidden">
                                            <div class="h-full bg-secondary-500 rounded-full" style="width: {{ $enrollment->progress }}%"></div>
                                        </div>
                                        <span class="text-xs font-medium text-gray-500">{{ $enrollment->progress }}%</span>
                                    </div>
                                </td>
                                <td class="px-4 py-3 text-sm text-gray-500">{{ $enrollment->created_at->format('M d, Y') }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="3" class="px-4 py-6 text-center text-sm text-gray-400">No students enrolled yet.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection
