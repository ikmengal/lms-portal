@extends('layouts.dashboard')
@section('title', 'Analytics')
@section('content')
    <div class="max-w-6xl mx-auto space-y-8">
        {{-- Header --}}
        <div class="flex flex-wrap items-center gap-3">
            <div class="flex-1 min-w-0">
                <h1 class="text-2xl font-bold text-gray-900">Analytics Dashboard</h1>
                <p class="text-gray-500 mt-0.5">Platform-wide performance metrics · updated {{ now()->format('M j, Y g:i A') }}</p>
            </div>
            <span class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-semibold text-primary-700 bg-primary-50 rounded-lg">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M7.5 14.25v2.25m3-4.5v4.5m3-6.75v6.75m3-9v9M6 20.25h12A2.25 2.25 0 0020.25 18V6A2.25 2.25 0 0018 3.75H6A2.25 2.25 0 003.75 6v12A2.25 2.25 0 006 20.25z"/></svg>
                Last 30 days activity window
            </span>
        </div>

        {{-- ===== Row 1: Students & Enrollments & Completion ===== --}}
        <div class="grid sm:grid-cols-2 lg:grid-cols-4 gap-5">
            @foreach([
                ['Total Students', number_format($totalStudents), '+'.number_format($newStudents30d).' new (30d)', 'bg-primary-100 text-primary-600', 'M15 19.128a9.38 9.38 0 002.625.372 9.337 9.337 0 004.121-.952 4.125 4.125 0 00-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 018.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0111.964-3.07M12 6.375a3.375 3.375 0 11-6.75 0 3.375 3.375 0 016.75 0zm8.25 2.25a2.625 2.625 0 11-5.25 0 2.625 2.625 0 015.25 0z'],
                ['Active Students', number_format($activeStudents), $totalStudents > 0 ? round($activeStudents / max($totalStudents,1) * 100) . '% of all students' : '—', 'bg-secondary-100 text-secondary-600', 'M15 19.128a9.38 9.38 0 002.625.372 9.337 9.337 0 004.121-.952 4.125 4.125 0 00-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 018.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0111.964-3.07M12 6.375a3.375 3.375 0 11-6.75 0 3.375 3.375 0 016.75 0zm8.25 2.25a2.625 2.625 0 11-5.25 0 2.625 2.625 0 015.25 0z'],
                ['Course Enrollments', number_format($totalEnrollments), '+'.number_format($enrollmentsThisMonth).' this month', 'bg-accent-100 text-accent-600', 'M12 6.042A8.967 8.967 0 006 3.75c-1.052 0-2.062.18-3 .512v14.25A8.987 8.987 0 016 18c2.305 0 4.408.867 6 2.292m0-14.25a8.966 8.966 0 016-2.292c1.052 0 2.062.18 3 .512v14.25A8.987 8.987 0 0018 18a8.967 8.967 0 00-6 2.292m0-14.25v14.25'],
                ['Completion Rate', $completionRate . '%', 'avg progress ' . $avgProgress . '% · ' . number_format($completedEnrollments) . ' finished', 'bg-purple-100 text-purple-600', 'M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z'],
            ] as [$label, $value, $sub, $style, $icon])
                <div class="bg-white rounded-xl p-5 border border-gray-100 shadow-sm">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 {{ $style }} rounded-xl grid place-items-center shrink-0">
                            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="{{ $icon }}"/></svg>
                        </div>
                        <div class="min-w-0">
                            <p class="text-xl font-bold text-gray-900 leading-tight">{{ $value }}</p>
                            <p class="text-xs font-medium text-gray-500">{{ $label }}</p>
                        </div>
                    </div>
                    <p class="mt-3 text-xs text-gray-400">{{ $sub }}</p>
                </div>
            @endforeach
        </div>

        {{-- ===== Row 2: Revenue + Monthly trend ===== --}}
        <div class="grid lg:grid-cols-3 gap-6">
            {{-- Revenue cards --}}
            <div class="space-y-5">
                <div class="bg-white rounded-xl p-5 border border-gray-100 shadow-sm">
                    <p class="text-xs font-semibold uppercase tracking-wide text-gray-400">Total Revenue</p>
                    <p class="text-3xl font-bold text-gray-900 mt-1">${{ number_format($totalRevenue, 2) }}</p>
                    <p class="text-xs text-gray-400 mt-1">{{ number_format($paymentsCount) }} paid transactions</p>
                </div>
                <div class="bg-white rounded-xl p-5 border border-gray-100 shadow-sm">
                    <p class="text-xs font-semibold uppercase tracking-wide text-gray-400">Revenue This Month</p>
                    <p class="text-3xl font-bold text-secondary-600 mt-1">${{ number_format($revenueThisMonth, 2) }}</p>
                    @php
                        $prev = $monthlyRevenue->slice(5, 1)->first()['revenue'] ?? 0;
                        $growth = $prev > 0 ? round(($revenueThisMonth - $prev) / $prev * 100, 1) : null;
                    @endphp
                    <p class="text-xs mt-1 {{ $growth === null ? 'text-gray-400' : ($growth >= 0 ? 'text-secondary-600 font-medium' : 'text-red-500 font-medium') }}">
                        @if($growth !== null) {{ $growth >= 0 ? '▲ +' : '▼ ' }}{{ $growth }}% vs last month @else vs last month @endif
                    </p>
                </div>
                <div class="bg-white rounded-xl p-5 border border-gray-100 shadow-sm">
                    <p class="text-xs font-semibold uppercase tracking-wide text-gray-400">Certificates Issued</p>
                    <p class="text-3xl font-bold text-yellow-500 mt-1">{{ number_format(\App\Models\Certificate::count()) }}</p>
                    <p class="text-xs text-gray-400 mt-1">all time</p>
                </div>
            </div>

            {{-- Monthly revenue bar chart (pure CSS) --}}
            <div class="lg:col-span-2 bg-white rounded-xl border border-gray-100 shadow-sm p-6">
                <div class="flex items-center justify-between mb-6">
                    <h2 class="font-semibold text-gray-900">Revenue — Last 6 Months</h2>
                    <span class="text-xs text-gray-400">peak: ${{ number_format($monthlyRevenue->max('revenue'), 0) }}</span>
                </div>
                <div class="flex items-end gap-3 h-44">
                    @foreach($monthlyRevenue as $month)
                        <div class="flex-1 flex flex-col items-center gap-2 group">
                            <span class="text-[10px] font-bold text-gray-500 opacity-0 group-hover:opacity-100 transition">${{ number_format($month['revenue'], 0) }}</span>
                            <div class="w-full bg-gray-50 rounded-t-lg relative overflow-hidden" style="height: 120px;">
                                <div class="absolute bottom-0 left-0 right-0 bg-gradient-to-t from-primary-600 to-primary-400 rounded-t-lg transition-all" style="height: {{ round($month['revenue'] / $maxMonthlyRevenue * 100) }}%"></div>
                            </div>
                            <span class="text-xs font-medium text-gray-500">{{ $month['label'] }}</span>
                            <span class="text-[10px] text-gray-300 -mt-1">{{ $month['enrollments'] }} enr.</span>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>

        {{-- ===== Row 3: Popular courses + Instructor earnings ===== --}}
        <div class="grid lg:grid-cols-2 gap-6">
            {{-- Most popular courses --}}
            <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-6">
                <h2 class="font-semibold text-gray-900 mb-5 flex items-center gap-2">
                    <svg class="w-5 h-5 text-accent-500" fill="currentColor" viewBox="0 0 24 24"><path d="M11.48 3.499a.562.562 0 011.04 0l2.125 5.111a.563.563 0 00.475.345l5.518.442c.499.04.701.663.321.988l-4.204 3.602a.563.563 0 00-.182.557l1.285 5.385a.562.562 0 01-.84.61l-4.725-2.885a.563.563 0 00-.586 0L6.982 20.54a.562.562 0 01-.84-.61l1.285-5.386a.562.562 0 00-.182-.557l-4.204-3.602a.563.563 0 01.321-.988l5.518-.442a.563.563 0 00.475-.345L11.48 3.5z"/></svg>
                    Most Popular Courses
                </h2>
                <div class="space-y-4">
                    @forelse($popularCourses as $i => $course)
                        <div>
                            <div class="flex items-center gap-3 mb-1.5">
                                <span class="w-5 h-5 rounded grid place-items-center text-[10px] font-bold shrink-0 {{ $loop->first ? 'bg-accent-500 text-white' : 'bg-gray-100 text-gray-500' }}">{{ $i + 1 }}</span>
                                <span class="text-sm font-medium text-gray-900 truncate flex-1">{{ $course->title }}</span>
                                <span class="text-xs text-gray-400 shrink-0">{{ $course->instructor_name ?? '—' }}</span>
                            </div>
                            <div class="pl-8">
                                <div class="h-2 bg-gray-100 rounded-full overflow-hidden">
                                    <div class="h-full bg-gradient-to-r from-primary-500 to-accent-400 rounded-full" style="width: {{ round($course->enrollments_count / $maxCourseEnrollments * 100) }}%"></div>
                                </div>
                                <div class="flex justify-between mt-1 text-[11px] text-gray-400">
                                    <span>{{ number_format($course->enrollments_count) }} students · avg {{ \App\Models\Review::where('course_id', $course->id)->count() && $course->avg_rating ? number_format($course->avg_rating, 1) . '★' : 'no reviews' }}</span>
                                    <span class="font-semibold text-secondary-600">${{ number_format($course->revenue ?? 0, 0) }}</span>
                                </div>
                            </div>
                        </div>
                    @empty
                        <p class="text-sm text-gray-400 py-4 text-center">No enrollments yet.</p>
                    @endforelse
                </div>
            </div>

            {{-- Instructor earnings --}}
            <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-6">
                <h2 class="font-semibold text-gray-900 mb-5 flex items-center gap-2">
                    <svg class="w-5 h-5 text-secondary-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6v12m-3-2.818l.879.659c1.171.879 3.07.879 4.242 0 1.172-.879 1.172-2.303 0-3.182C13.536 12.219 12.768 12 12 12c-.725 0-1.45-.22-2.003-.659-1.106-.879-1.106-2.303 0-3.182s2.9-.879 4.006 0l.415.33M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    Top Instructor Earnings
                </h2>
                <div class="space-y-4">
                    @forelse($instructorEarnings as $i => $ins)
                        <div class="flex items-center gap-3">
                            <div class="w-9 h-9 bg-primary-100 text-primary-700 rounded-full grid place-items-center text-xs font-bold shrink-0">{{ strtoupper(substr($ins->name, 0, 2)) }}</div>
                            <div class="flex-1 min-w-0">
                                <div class="flex items-baseline justify-between gap-2">
                                    <p class="text-sm font-medium text-gray-900 truncate">#{{ $i + 1 }} {{ $ins->name }}</p>
                                    <p class="text-sm font-bold text-secondary-600 shrink-0">${{ number_format($ins->revenue, 2) }}</p>
                                </div>
                                <div class="h-1.5 bg-gray-100 rounded-full overflow-hidden mt-1">
                                    <div class="h-full bg-secondary-500 rounded-full" style="width: {{ round($ins->revenue / $maxInstructorRevenue * 100) }}%"></div>
                                </div>
                                <p class="text-[11px] text-gray-400 mt-0.5">{{ $ins->courses_count }} course{{ $ins->courses_count == 1 ? '' : 's' }} · {{ number_format($ins->sales_count) }} sales</p>
                            </div>
                        </div>
                    @empty
                        <p class="text-sm text-gray-400 py-4 text-center">No paid enrollments yet.</p>
                    @endforelse
                </div>
            </div>
        </div>

        {{-- ===== Row 4: Quiz performance + Student engagement ===== --}}
        <div class="grid lg:grid-cols-2 gap-6">
            {{-- Quiz performance --}}
            <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-6">
                <h2 class="font-semibold text-gray-900 mb-4 flex items-center gap-2">
                    <svg class="w-5 h-5 text-purple-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12c0 1.268-.63 2.39-1.593 3.068a3.745 3.745 0 01-1.043 3.296 3.745 3.745 0 01-3.296 1.043A3.745 3.745 0 0112 21c-1.268 0-2.39-.63-3.068-1.593a3.746 3.746 0 01-3.296-1.043 3.745 3.745 0 01-1.043-3.296A3.745 3.745 0 013 12c0-1.268.63-2.39 1.593-3.068a3.746 3.746 0 011.043-3.296 3.746 3.746 0 013.296-1.043A3.746 3.746 0 0112 3c1.268 0 2.39.63 3.068 1.593a3.746 3.746 0 013.296 1.043 3.746 3.746 0 011.043 3.296A3.745 3.745 0 0121 12z"/></svg>
                    Quiz Performance
                </h2>
                <div class="grid grid-cols-3 gap-3 mb-5">
                    <div class="bg-purple-50 rounded-xl p-3 text-center">
                        <p class="text-xl font-bold text-purple-700">{{ number_format($totalAttempts) }}</p>
                        <p class="text-[11px] text-purple-500 font-medium">Attempts</p>
                    </div>
                    <div class="bg-primary-50 rounded-xl p-3 text-center">
                        <p class="text-xl font-bold text-primary-700">{{ $avgQuizScore }}%</p>
                        <p class="text-[11px] text-primary-500 font-medium">Avg Score</p>
                    </div>
                    <div class="bg-secondary-50 rounded-xl p-3 text-center">
                        <p class="text-xl font-bold text-secondary-700">{{ $passRate }}%</p>
                        <p class="text-[11px] text-secondary-500 font-medium">Pass Rate</p>
                    </div>
                </div>
                @if($quizPerformanceByQuiz->isNotEmpty())
                    <p class="text-xs font-semibold text-gray-400 uppercase tracking-wide mb-2">Best performing quizzes</p>
                    <ul class="divide-y divide-gray-50">
                        @foreach($quizPerformanceByQuiz as $q)
                            <li class="py-2.5 flex items-center gap-3">
                                <div class="w-9 h-9 rounded-full grid place-items-center text-[11px] font-bold shrink-0 {{ $q->avg_score >= $q->passing_score ? 'bg-secondary-100 text-secondary-700' : 'bg-red-100 text-red-600' }}">{{ number_format($q->avg_score, 0) }}%</div>
                                <div class="min-w-0 flex-1">
                                    <p class="text-xs font-medium text-gray-900 truncate">{{ $q->title }}</p>
                                    <p class="text-[10px] text-gray-400">{{ str_replace('_', ' ', ucfirst($q->type)) }} · pass ≥ {{ $q->passing_score }}%</p>
                                </div>
                                <span class="text-[11px] text-gray-400 shrink-0">{{ number_format($q->attempts_count) }} attempt{{ $q->attempts_count == 1 ? '' : 's' }}</span>
                            </li>
                        @endforeach
                    </ul>
                @else
                    <p class="text-sm text-gray-400 py-4 text-center">No quiz attempts yet.</p>
                @endif
            </div>

            {{-- Student engagement --}}
            <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-6">
                <h2 class="font-semibold text-gray-900 mb-4 flex items-center gap-2">
                    <svg class="w-5 h-5 text-secondary-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M3 13.125C3 12.504 3.504 12 4.125 12h2.25c.621 0 1.125.504 1.125 1.125v6.75C7.5 20.496 6.996 21 6.375 21h-2.25A1.125 1.125 0 013 19.875v-6.75zM9.75 8.625c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125v11.25c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 01-1.125-1.125V8.625zM16.5 4.125c0-.621.504-1.125 1.125-1.125h2.25C20.496 3 21 3.504 21 4.125v15.75c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 01-1.125-1.125V4.125z"/></svg>
                    Student Engagement
                </h2>

                @php
                    $engagementRate = $totalStudents > 0 ? round($activeStudents / $totalStudents * 100, 1) : 0;
                    $bucketTotal = max(collect($progressBuckets)->sum('count'), 1);
                @endphp
                <div class="bg-secondary-50 border border-secondary-100 rounded-xl p-4 mb-5 flex items-center gap-4">
                    <div class="relative w-16 h-16 shrink-0">
                        <svg viewBox="0 0 36 36" class="w-16 h-16 -rotate-90">
                            <circle cx="18" cy="18" r="15.9" fill="none" stroke="#dcfce7" stroke-width="3"/>
                            <circle cx="18" cy="18" r="15.9" fill="none" stroke="#22c55e" stroke-width="3" stroke-linecap="round" stroke-dasharray="{{ $engagementRate * 1.0 }} 100"/>
                        </svg>
                        <span class="absolute inset-0 grid place-items-center text-[11px] font-bold text-secondary-700">{{ $engagementRate }}%</span>
                    </div>
                    <div>
                        <p class="text-sm font-bold text-gray-900">30-day engagement rate</p>
                        <p class="text-xs text-gray-500 mt-0.5">{{ number_format($activeStudents) }} of {{ number_format($totalStudents) }} students completed a lesson or enrolled recently.</p>
                    </div>
                </div>

                <p class="text-xs font-semibold text-gray-400 uppercase tracking-wide mb-3">Enrollment progress distribution</p>
                <div class="space-y-3">
                    @foreach($progressBuckets as $bucket)
                        <div>
                            <div class="flex justify-between text-xs mb-1">
                                <span class="text-gray-600 font-medium">{{ $bucket['label'] }}</span>
                                <span class="text-gray-400">{{ number_format($bucket['count']) }} ({{ round($bucket['count'] / $bucketTotal * 100) }}%)</span>
                            </div>
                            <div class="h-2.5 bg-gray-100 rounded-full overflow-hidden">
                                <div class="h-full rounded-full {{ $bucket['color'] }}" style="width: {{ round($bucket['count'] / $bucketTotal * 100) }}%"></div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
@endsection
