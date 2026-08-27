@extends('layouts.dashboard')
@section('title', 'Reports & Analytics')
@section('content')
    @php
        $tabIcons = [
            'enrollments' => 'M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5',
            'payments' => 'M21 11.25v8.25a1.5 1.5 0 01-1.5 1.5H5.25a1.5 1.5 0 01-1.5-1.5v-8.25M12 4.875A2.625 2.625 0 109.375 7.5H12m0-2.625V7.5m0-2.625A2.625 2.625 0 1114.625 7.5H12m0 0V21m-8.625-9.75h18c.621 0 1.125-.504 1.125-1.125v-1.5c0-.621-.504-1.125-1.125-1.125h-18c-.621 0-1.125.504-1.125 1.125v1.5c0 .621.504 1.125 1.125 1.125z',
            'students' => 'M15 19.128a9.38 9.38 0 002.625.372 9.337 9.337 0 004.121-.952 4.125 4.125 0 00-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 018.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0111.964-3.07M12 6.375a3.375 3.375 0 11-6.75 0 3.375 3.375 0 016.75 0zm8.25 2.25a2.625 2.625 0 11-5.25 0 2.625 2.625 0 015.25 0z',
            'courses' => 'M12 6.042A8.967 8.967 0 006 3.75c-1.052 0-2.062.18-3 .512v14.25A8.987 8.987 0 016 18c2.305 0 4.408.867 6 2.292m0-14.25a8.966 8.966 0 016-2.292c1.052 0 2.062.18 3 .512v14.25A8.987 8.987 0 0018 18a8.967 8.967 0 00-6 2.292m0-14.25v14.25',
            'quizzes' => 'M9 12.75L11.25 15 15 9.75M21 12c0 1.268-.63 2.39-1.593 3.068a3.745 3.745 0 01-1.043 3.296 3.745 3.745 0 01-3.296 1.043A3.745 3.745 0 0112 21c-1.268 0-2.39-.63-3.068-1.593a3.746 3.746 0 01-3.296-1.043 3.745 3.745 0 01-1.043-3.296A3.745 3.745 0 013 12c0-1.268.63-2.39 1.593-3.068a3.746 3.746 0 011.043-3.296 3.746 3.746 0 013.296-1.043A3.746 3.746 0 0112 3c1.268 0 2.39.63 3.068 1.593a3.746 3.746 0 013.296 1.043 3.746 3.746 0 011.043 3.296A3.745 3.745 0 0121 12z',
            'assignments' => 'M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z',
            'certificates' => 'M9 12.75L11.25 15 15 9.75M21 12c0 1.268-.63 2.39-1.593 3.068a3.745 3.745 0 01-1.043 3.296 3.745 3.745 0 01-3.296 1.043A3.745 3.745 0 0112 21c-1.268 0-2.39-.63-3.068-1.593a3.746 3.746 0 01-3.296-1.043 3.745 3.745 0 01-1.043-3.296A3.745 3.745 0 013 12c0-1.268.63-2.39 1.593-3.068a3.746 3.746 0 011.043-3.296 3.746 3.746 0 013.296-1.043A3.746 3.746 0 0112 3c1.268 0 2.39.63 3.068 1.593a3.746 3.746 0 013.296 1.043 3.746 3.746 0 011.043 3.296A3.745 3.745 0 0121 12z',
            'live-classes' => 'M5.25 5.653c0-.856.917-1.398 1.667-.986l11.54 6.347a1.125 1.125 0 010 1.972l-11.54 6.347a1.125 1.125 0 01-1.667-.985V5.653z',
        ];
        $courseScopeTabs = ['enrollments', 'payments', 'quizzes', 'assignments', 'certificates', 'live-classes'];
        $statusScopeTabs = ['enrollments', 'payments', 'assignments', 'live-classes'];
        $showCourseFilter = in_array($tab, $courseScopeTabs);
        $showStatusFilter = in_array($tab, $statusScopeTabs);
        $showRoleFilter = $tab === 'students';
        $tabUrl = fn ($t) => route('admin.reports.index', array_merge($filters, ['tab' => $t]));
        $exportBaseUrl = route('admin.reports.export', array_merge($filters, ['tab' => $tab]));
    @endphp

    <div class="max-w-[90rem] mx-auto space-y-6 overflow-x-hidden">
        {{-- Header --}}
        <div class="flex flex-wrap items-center gap-3">
            <div class="flex-1 min-w-0">
                <h1 class="text-2xl font-bold text-gray-900">{{ $reportTitle }}</h1>
                <p class="text-gray-500 mt-0.5">{{ $reportDescription }}</p>
            </div>
            <div class="relative" x-data="{ open: false }">
                <button @click="open = !open" class="inline-flex items-center gap-2 px-4 py-2 text-sm font-semibold text-white bg-primary-600 hover:bg-primary-700 rounded-lg shadow-sm transition">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5M16.5 12L12 16.5m0 0L7.5 12m4.5 4.5V3"/></svg>
                    Export
                    <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/></svg>
                </button>
                <div x-show="open" @click.away="open = false" x-transition class="absolute right-0 top-full mt-1 w-44 bg-white rounded-xl shadow-xl border border-gray-100 py-1.5 z-20" style="display:none;">
                    @foreach([
                        ['csv', 'CSV', 'M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5'],
                        ['pdf', 'PDF', 'M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z'],
                        ['xlsx', 'Excel', 'M3.375 19.5h17.25m-17.25 0a1.125 1.125 0 01-1.125-1.125M20.625 19.5c.621 0 1.125-.504 1.125-1.125V4.875c0-.621-.504-1.125-1.125-1.125H3.375c-.621 0-1.125.504-1.125 1.125v13.5c0 .621.504 1.125 1.125 1.125z'],
                    ] as [$fmt, $label, $icon])
                        <a href="{{ $exportBaseUrl . '&format=' . $fmt }}" class="flex items-center gap-2.5 px-3.5 py-2 text-sm text-gray-700 hover:bg-primary-50 hover:text-primary-700 transition">
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="{{ $icon }}"/></svg>
                            {{ $label }}
                        </a>
                    @endforeach
                </div>
            </div>
        </div>

        {{-- Tabs --}}
        <div class="flex gap-2 overflow-x-auto pb-1 -mx-1 px-1">
            @foreach($tabs as $key => $label)
                <a href="{{ $tabUrl($key) }}" class="inline-flex items-center gap-2 px-3.5 py-2 text-sm font-medium rounded-lg whitespace-nowrap transition {{ $tab === $key ? 'bg-primary-600 text-white shadow-sm' : 'bg-white text-gray-600 hover:bg-gray-50 border border-gray-200' }}">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="{{ $tabIcons[$key] ?? $tabIcons['enrollments'] }}"/></svg>
                    {{ $label }}
                </a>
            @endforeach
        </div>

        {{-- Filters --}}
        <form method="GET" action="{{ route('admin.reports.index') }}" class="bg-white rounded-xl border border-gray-100 shadow-sm p-4">
            <input type="hidden" name="tab" value="{{ $tab }}">
            <div class="grid sm:grid-cols-2 lg:grid-cols-5 gap-3 items-end">
                <div>
                    <label class="block text-xs font-medium text-gray-500 mb-1">From</label>
                    <input type="date" name="from" value="{{ $filters['from'] ?? '' }}" class="w-full rounded-lg border-gray-200 text-sm focus:border-primary-500 focus:ring-primary-500">
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-500 mb-1">To</label>
                    <input type="date" name="to" value="{{ $filters['to'] ?? '' }}" class="w-full rounded-lg border-gray-200 text-sm focus:border-primary-500 focus:ring-primary-500">
                </div>
                @if($showCourseFilter)
                    <div>
                        <label class="block text-xs font-medium text-gray-500 mb-1">Course</label>
                        <select name="course" class="w-full rounded-lg border-gray-200 text-sm focus:border-primary-500 focus:ring-primary-500">
                            <option value="">All courses</option>
                            @foreach($courses as $course)
                                <option value="{{ $course->id }}" @selected((string) $filters['course'] === (string) $course->id)>{{ $course->title }}</option>
                            @endforeach
                        </select>
                    </div>
                @endif
                @if($showStatusFilter)
                    <div>
                        <label class="block text-xs font-medium text-gray-500 mb-1">Status</label>
                        <select name="status" class="w-full rounded-lg border-gray-200 text-sm focus:border-primary-500 focus:ring-primary-500">
                            @foreach(($statusOptions[$tab] ?? []) as $val => $label)
                                <option value="{{ $val }}" @selected($filters['status'] === $val)>{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>
                @endif
                @if($showRoleFilter)
                    <div>
                        <label class="block text-xs font-medium text-gray-500 mb-1">Role</label>
                        <select name="role" class="w-full rounded-lg border-gray-200 text-sm focus:border-primary-500 focus:ring-primary-500">
                            @foreach($roleOptions as $val => $label)
                                <option value="{{ $val }}" @selected($filters['role'] === $val)>{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>
                @endif
                <div class="flex items-center gap-2 {{ ($showCourseFilter || $showStatusFilter || $showRoleFilter) ? '' : 'sm:col-span-2' }}">
                    <button type="submit" class="px-4 py-2 text-sm font-semibold text-white bg-gray-900 hover:bg-gray-800 rounded-lg transition">Apply</button>
                    <a href="{{ route('admin.reports.index', ['tab' => $tab]) }}" class="px-4 py-2 text-sm font-medium text-gray-500 hover:text-gray-700 rounded-lg hover:bg-gray-100 transition">Reset</a>
                </div>
            </div>
        </form>

        {{-- Summary stats --}}
        <div class="grid sm:grid-cols-2 lg:grid-cols-4 gap-5">
            @foreach($stats as $stat)
                <div class="bg-white rounded-xl p-5 border border-gray-100 shadow-sm">
                    <p class="text-xl font-bold text-gray-900 leading-tight">{{ $stat['value'] }}</p>
                    <p class="text-xs font-semibold text-gray-500 mt-0.5">{{ $stat['label'] }}</p>
                    <p class="mt-2 text-[11px] text-gray-400">{{ $stat['sub'] ?? '' }}</p>
                </div>
            @endforeach
        </div>

        {{-- Data table --}}
        <div class="bg-white rounded-xl border border-gray-100 shadow-sm overflow-hidden">
            <div class="px-5 py-4 border-b border-gray-100 flex items-center justify-between gap-3">
                <h2 class="font-semibold text-gray-900">{{ $reportTitle }}</h2>
                <span class="text-xs text-gray-400">Showing {{ number_format($rows->count()) }} records</span>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-100 text-sm">
                    <thead class="bg-gray-50/70">
                        <tr>
                            @foreach($columns as $col)
                                <th class="px-4 py-2.5 text-left text-[11px] font-semibold uppercase tracking-wide text-gray-500 whitespace-nowrap">{{ $col['label'] }}</th>
                            @endforeach
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50">
                        @forelse($rows as $row)
                            <tr class="hover:bg-gray-50/60 transition">
                                @foreach($columns as $col)
                                    <td class="px-4 py-3 text-gray-600 whitespace-nowrap {{ $loop->first ? 'font-medium text-gray-900' : '' }}">
                                        @if($col['key'] === 'link' && in_array($tab, ['certificates']))
                                            <a href="{{ $row[$col['key']] }}" target="_blank" class="text-primary-600 hover:underline">{{ $row[$col['key']] }}</a>
                                        @elseif(in_array($col['key'], ['status', 'result', 'late']))
                                            @php $isRed = in_array($row[$col['key']], ['Fail', 'Late', 'Failed', 'Pending']); @endphp
                                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[11px] font-medium {{ $isRed ? 'bg-red-50 text-red-600' : ($row[$col['key']] === 'Completed' || $row[$col['key']] === 'Paid' || $row[$col['key']] === 'Pass' || $row[$col['key']] === 'Graded' || $row[$col['key']] === 'On time' ? 'bg-secondary-50 text-secondary-700' : 'bg-gray-100 text-gray-600') }}">
                                                {{ $row[$col['key']] }}
                                            </span>
                                        @else
                                            {{ $row[$col['key']] }}
                                        @endif
                                    </td>
                                @endforeach
                            </tr>
                        @empty
                            <tr>
                                <td colspan="{{ count($columns) }}" class="px-4 py-12 text-center text-gray-400">
                                    <p class="text-sm">No records match the selected filters.</p>
                                    <a href="{{ route('admin.reports.index', ['tab' => $tab]) }}" class="text-primary-600 hover:underline text-sm mt-1 inline-block">Reset filters</a>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection