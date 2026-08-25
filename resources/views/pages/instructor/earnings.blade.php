@extends('layouts.dashboard')
@section('title', 'My Earnings')
@section('content')
    <div class="space-y-6">
        {{-- Header --}}
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Earnings</h1>
                <p class="text-gray-500 mt-1">Your revenue record across all courses.</p>
            </div>
        </div>

        {{-- Stats --}}
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
            <div class="bg-white rounded-xl p-5 border border-gray-100 shadow-sm">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 bg-secondary-100 rounded-xl flex items-center justify-center shrink-0">
                        <svg class="w-5 h-5 text-secondary-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6v12m-3-2.818l.879.659c1.171.879 3.07.879 4.242 0 1.172-.879 1.172-2.303 0-3.182C13.536 12.219 12.768 12 12 12c-.725 0-1.45-.22-2.003-.659-1.106-.879-1.106-2.303 0-3.182s2.9-.879 4.006 0l.415.33M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    </div>
                    <div>
                        <p class="text-xl font-bold text-gray-900">${{ number_format($stats['total'], 2) }}</p>
                        <p class="text-xs text-gray-500">Total Earnings</p>
                    </div>
                </div>
            </div>
            <div class="bg-white rounded-xl p-5 border border-gray-100 shadow-sm">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 bg-primary-100 rounded-xl flex items-center justify-center shrink-0">
                        <svg class="w-5 h-5 text-primary-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 012.25-2.25h13.5A2.25 2.25 0 0121 7.5v11.25m-18 0A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75m-18 0v-7.5A2.25 2.25 0 015.25 9h13.5A2.25 2.25 0 0121 11.25v7.5"/></svg>
                    </div>
                    <div>
                        <p class="text-xl font-bold text-gray-900">${{ number_format($stats['thisMonth'], 2) }}</p>
                        <p class="text-xs text-gray-500">This Month (recent sales)</p>
                    </div>
                </div>
            </div>
            <div class="bg-white rounded-xl p-5 border border-gray-100 shadow-sm">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 bg-accent-100 rounded-xl flex items-center justify-center shrink-0">
                        <svg class="w-5 h-5 text-accent-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 18L9 11.25l4.306 4.307a11.95 11.95 0 015.814-5.519l2.74-1.22m0 0l-5.94-2.28m5.94 2.28l-2.28 5.941"/></svg>
                    </div>
                    <div>
                        <p class="text-xl font-bold text-gray-900">${{ number_format($stats['avgPerSale'], 2) }}</p>
                        <p class="text-xs text-gray-500">Avg per Enrollment</p>
                    </div>
                </div>
            </div>
            <div class="bg-white rounded-xl p-5 border border-gray-100 shadow-sm">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 bg-blue-100 rounded-xl flex items-center justify-center shrink-0">
                        <svg class="w-5 h-5 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19.128a9.38 9.38 0 002.625.372 9.337 9.337 0 004.121-.952 4.125 4.125 0 00-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 018.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0111.964-3.07"/></svg>
                    </div>
                    <div>
                        <p class="text-xl font-bold text-gray-900">{{ $perCourse->sum('students') }}</p>
                        <p class="text-xs text-gray-500">Paid Enrollments</p>
                    </div>
                </div>
            </div>
        </div>

        {{-- Monthly chart --}}
        <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-6">
            <h2 class="text-lg font-semibold text-gray-900 mb-6">Last 6 Months Revenue</h2>
            <div class="flex items-end justify-between gap-3 sm:gap-5 h-44">
                @foreach($monthly as $month)
                    <div class="flex-1 flex flex-col items-center gap-2 h-full justify-end">
                        <span class="text-[10px] sm:text-xs font-semibold text-gray-500">${{ number_format($month->revenue, 0) }}</span>
                        <div class="w-full max-w-[56px] rounded-t-lg {{ $month->revenue > 0 ? 'bg-gradient-to-t from-primary-600 to-primary-400' : 'bg-gray-100' }}"
                            style="height: {{ max(round($month->revenue / $stats['maxMonthly'] * 100), 3) }}%"></div>
                        <span class="text-xs text-gray-400 font-medium">{{ $month->label }}</span>
                    </div>
                @endforeach
            </div>
        </div>

        {{-- Per course --}}
        <div class="bg-white rounded-xl border border-gray-100 shadow-sm overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-100">
                <h2 class="text-lg font-semibold text-gray-900">Revenue by Course</h2>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead>
                        <tr class="border-b border-gray-100 bg-gray-50">
                            <th class="text-left text-xs font-medium text-gray-500 uppercase tracking-wider px-6 py-3">Course</th>
                            <th class="text-left text-xs font-medium text-gray-500 uppercase tracking-wider px-6 py-3">Price</th>
                            <th class="text-left text-xs font-medium text-gray-500 uppercase tracking-wider px-6 py-3">Students</th>
                            <th class="text-left text-xs font-medium text-gray-500 uppercase tracking-wider px-6 py-3">Rating</th>
                            <th class="text-right text-xs font-medium text-gray-500 uppercase tracking-wider px-6 py-3">Revenue</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50">
                        @forelse($perCourse as $course)
                            <tr class="hover:bg-gray-50 transition">
                                <td class="px-6 py-4 max-w-xs">
                                    <a href="{{ url('/courses/' . $course->slug) }}" class="text-sm font-medium text-gray-900 hover:text-primary-600 truncate block">{{ $course->title }}</a>
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-500">${{ number_format($course->price, 2) }}</td>
                                <td class="px-6 py-4 text-sm font-medium text-gray-700">{{ $course->students }}</td>
                                <td class="px-6 py-4 text-sm">
                                    @if($course->rating)
                                        <span class="inline-flex items-center gap-1 text-amber-600 font-medium">
                                            <svg class="w-3.5 h-3.5 fill-current" viewBox="0 0 24 24"><path d="M11.48 3.499a.562.562 0 011.04 0l2.125 5.111a.563.563 0 00.475.345l5.518.442c.499.04.701.663.321.988l-4.204 3.602a.563.563 0 00-.182.557l1.285 5.385a.562.562 0 01-.84.61l-4.725-2.885a.563.563 0 00-.586 0L6.982 20.54a.562.562 0 01-.84-.61l1.285-5.386a.562.562 0 00-.182-.557l-4.204-3.602a.563.563 0 01.321-.988l5.518-.442a.563.563 0 00.475-.345L11.48 3.5z"/></svg>
                                            {{ number_format($course->rating, 1) }}
                                        </span>
                                    @else
                                        <span class="text-gray-300">—</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 text-right text-sm font-bold text-secondary-600">${{ number_format($course->revenue, 2) }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="5" class="px-6 py-12 text-center text-sm text-gray-400">Create a course to start earning.</td></tr>
                        @endforelse
                    </tbody>
                    @if($perCourse->isNotEmpty())
                        <tfoot>
                            <tr class="bg-gray-50 border-t border-gray-100">
                                <td class="px-6 py-3.5 text-sm font-bold text-gray-900" colspan="4">Total</td>
                                <td class="px-6 py-3.5 text-right text-sm font-bold text-secondary-700">${{ number_format($stats['total'], 2) }}</td>
                            </tr>
                        </tfoot>
                    @endif
                </table>
            </div>
        </div>

        {{-- Earning records --}}
        <div class="bg-white rounded-xl border border-gray-100 shadow-sm overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-100">
                <h2 class="text-lg font-semibold text-gray-900">Earning Records</h2>
                <p class="text-xs text-gray-400 mt-0.5">Every enrollment and its value, newest first.</p>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead>
                        <tr class="border-b border-gray-100 bg-gray-50">
                            <th class="text-left text-xs font-medium text-gray-500 uppercase tracking-wider px-6 py-3">Student</th>
                            <th class="text-left text-xs font-medium text-gray-500 uppercase tracking-wider px-6 py-3">Course</th>
                            <th class="text-left text-xs font-medium text-gray-500 uppercase tracking-wider px-6 py-3">Date</th>
                            <th class="text-left text-xs font-medium text-gray-500 uppercase tracking-wider px-6 py-3">Progress</th>
                            <th class="text-right text-xs font-medium text-gray-500 uppercase tracking-wider px-6 py-3">Amount</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50">
                        @forelse($records as $record)
                            <tr class="hover:bg-gray-50 transition">
                                <td class="px-6 py-3.5 text-sm font-medium text-gray-900">{{ $record->student }}</td>
                                <td class="px-6 py-3.5 max-w-[240px]"><p class="text-sm text-gray-600 truncate">{{ $record->course }}</p></td>
                                <td class="px-6 py-3.5 text-sm text-gray-400 whitespace-nowrap">{{ $record->date->format('M j, Y') }}</td>
                                <td class="px-6 py-3.5 w-40">
                                    <div class="flex items-center gap-2">
                                        <div class="flex-1 h-1.5 bg-gray-100 rounded-full overflow-hidden">
                                            <div class="h-full rounded-full {{ $record->progress >= 100 ? 'bg-secondary-500' : 'bg-primary-500' }}" style="width: {{ $record->progress }}%"></div>
                                        </div>
                                        <span class="text-xs text-gray-400 w-8">{{ $record->progress }}%</span>
                                    </div>
                                </td>
                                <td class="px-6 py-3.5 text-right text-sm font-semibold text-secondary-600">${{ number_format($record->amount, 2) }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="5" class="px-6 py-12 text-center text-sm text-gray-400">No earning records yet.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if($records->hasPages())
                <div class="px-6 py-4 border-t border-gray-100">{{ $records->links() }}</div>
            @endif
        </div>
    </div>
@endsection
