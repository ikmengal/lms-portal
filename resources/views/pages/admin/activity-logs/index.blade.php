@extends('layouts.dashboard')
@section('title', 'Activity Logs')
@section('content')
    <div class="mx-auto space-y-6">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Activity Logs</h1>
                <p class="text-gray-500 mt-1">Track all user actions including logins, logouts, and CRUD operations.</p>
            </div>
            <div class="flex items-center gap-2">
                <span class="text-sm text-gray-500">{{ $totalLogs ?? \DB::table('activity_log')->count() }} records</span>
                <form method="POST" action="{{ route('admin.activity-logs.destroyAll') }}" data-confirm="Are you sure? All logs will be deleted." data-confirm-title="Clear All Logs" data-confirm-icon="warning" data-confirm-button="Yes, Clear All">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="inline-flex items-center gap-1.5 px-3 py-2 text-sm font-medium text-red-600 hover:text-red-700 hover:bg-red-50 rounded-lg transition border border-red-200">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.121-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0"/></svg>
                        Clear All
                    </button>
                </form>
            </div>
        </div>

        {{-- Filters --}}
        <div class="bg-white border border-gray-200 rounded-xl p-4 flex flex-wrap gap-4">
            <form method="GET" action="{{ route('admin.activity-logs.index') }}" class="flex items-center gap-4">
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Search logs..." class="px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-primary-500 focus:border-primary-500 outline-none">
                <select name="log_name" class="px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-primary-500 focus:border-primary-500 outline-none">
                    <option value="">All Logs</option>
                    <option value="default" {{ request('log_name') == 'default' ? 'selected' : '' }}>Default</option>
                    <option value="errors" {{ request('log_name') == 'errors' ? 'selected' : '' }}>Errors</option>
                </select>
                <button type="submit" class="px-4 py-2 text-sm font-medium text-white bg-primary-600 hover:bg-primary-700 rounded-lg transition">Filter</button>
                @if(request('search') || request('log_name'))
                    <a href="{{ route('admin.activity-logs.index') }}" class="px-3 py-2 text-sm font-medium text-gray-600 hover:text-gray-900 transition">Reset</a>
                @endif
            </form>
        </div>

        {{-- Activity Table --}}
        <div class="bg-white border border-gray-200 rounded-xl overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full min-w-[800px]">
                    <thead class="bg-gray-50 border-b border-gray-200">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ID</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">User</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Action</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Description</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                            <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Details</th>
                            <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Delete</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @forelse($activities as $activity)
                            <tr class="hover:bg-gray-50 transition">
                                <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-500 font-mono">#{{ $activity->id }}</td>
                                <td class="px-4 py-3 whitespace-nowrap text-sm">
                                    @if($activity->causer)
                                        <div class="flex items-center gap-2">
                                            <div class="w-7 h-7 bg-primary-100 text-primary-700 rounded-full flex items-center justify-center text-xs font-bold shrink-0">
                                                {{ substr($activity->causer->name ?? '?', 0, 1) }}
                                            </div>
                                            <div>
                                                <p class="font-medium text-gray-900">{{ $activity->causer->name ?? 'Unknown' }}</p>
                                                <p class="text-xs text-gray-400">{{ $activity->causer->email ?? '' }}</p>
                                            </div>
                                        </div>
                                    @else
                                        <div class="flex items-center gap-2">
                                            <div class="w-7 h-7 bg-gray-100 text-gray-600 rounded-full flex items-center justify-center">
                                                <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/></svg>
                                            </div>
                                            <span class="text-gray-500 text-sm">System</span>
                                        </div>
                                    @endif
                                </td>
                                <td class="px-4 py-3 whitespace-nowrap">
                                    @php
                                        $event = $activity->event;
                                        $badgeClass = match($event) {
                                            'created', 'login', 'assigned', 'imported', 'exported' => 'bg-green-100 text-green-800',
                                            'updated', 'unassigned' => 'bg-blue-100 text-blue-800',
                                            'deleted', 'logout' => 'bg-red-100 text-red-800',
                                            default => 'bg-gray-100 text-gray-800',
                                        };
                                    @endphp
                                    <span class="px-2.5 py-1 text-xs font-semibold rounded-full capitalize {{ $badgeClass }}">
                                        {{ ucfirst($event) }}
                                    </span>
                                </td>
                                <td class="px-4 py-3 text-sm text-gray-900 max-w-xs truncate">{{ $activity->description }}</td>
                                <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-500">{{ $activity->created_at->diffForHumans() }}</td>
                                <td class="px-4 py-3 text-center">
                                    <a href="{{ route('admin.activity-logs.show', $activity->id) }}" class="text-primary-600 hover:text-primary-900 text-sm font-medium">View</a>
                                </td>
                                <td class="px-4 py-3 text-center">
                                    <form method="POST" action="{{ route('admin.activity-logs.destroy', $activity->id) }}" data-confirm="Delete log #{{ $activity->id }}? This cannot be undone." data-confirm-title="Delete Log" data-confirm-icon="warning" data-confirm-button="Delete">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-red-500 hover:text-red-700 hover:bg-red-50 text-sm font-medium transition px-2 py-1 rounded">
                                            <svg class="w-4 h-4 inline" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.121-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0"/></svg>
                                            Delete
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="px-4 py-12 text-center">
                                    <svg class="w-12 h-12 text-gray-200 mx-auto mb-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1"><path stroke-linecap="round" stroke-linejoin="round" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"/></svg>
                                    <p class="text-sm text-gray-500">No activity logs found.</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- Pagination --}}
            @if($activities->hasPages())
                <div class="px-4 py-3 border-t border-gray-200 flex items-center justify-between">
                    <div class="text-sm text-gray-500">
                        Showing {{ $activities->firstItem() ?? 0 }}–{{ $activities->lastItem() ?? 0 }} of {{ $activities->total() }} results
                    </div>
                    <div class="flex items-center gap-1">
                        {{ $activities->appends(request()->query())->links() }}
                    </div>
                </div>
            @endif
        </div>
    </div>
@endsection
