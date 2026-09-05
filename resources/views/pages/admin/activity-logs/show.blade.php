@extends('layouts.dashboard')
@section('title', 'Activity Log Detail')
@section('content')
    <div class="mx-auto space-y-6">
        <div class="flex items-center gap-4">
            <a href="{{ route('admin.activity-logs.index') }}" class="inline-flex items-center gap-2 px-4 py-2 text-sm font-medium text-primary-600 hover:text-primary-900 transition">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18"/></svg>
                Back to Logs
            </a>
            <h1 class="text-2xl font-bold text-gray-900">Activity Log #{{ $activity->id }}</h1>
        </div>

        <div class="bg-white border border-gray-200 rounded-xl overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-200">
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        @if($activity->causer)
                            <div class="w-10 h-10 bg-primary-100 text-primary-700 rounded-full flex items-center justify-center text-sm font-bold">
                                {{ substr($activity->causer->name ?? '?', 0, 1) }}
                            </div>
                            <div>
                                <p class="font-medium text-gray-900">{{ $activity->causer->name }}</p>
                                <p class="text-sm text-gray-500">{{ $activity->causer->email }}</p>
                            </div>
                        @else
                            <div class="w-10 h-10 bg-gray-100 text-gray-600 rounded-full flex items-center justify-center text-sm font-bold">
                                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/></svg>
                            </div>
                            <div>
                                <p class="font-medium text-gray-900">System</p>
                                <p class="text-sm text-gray-500">No user</p>
                            </div>
                        @endif
                    </div>
                    <div class="flex items-center gap-2">
                        <span class="px-3 py-1 text-sm font-medium rounded-full
                            @if(in_array($activity->event, ['created', 'updated', 'deleted']))
                                @if($activity->event == 'created') bg-green-100 text-green-800
                                @elseif($activity->event == 'updated') bg-blue-100 text-blue-800
                                @elseif($activity->event == 'deleted') bg-red-100 text-red-800
                                @endif
                            @else bg-gray-100 text-gray-800 @endif">
                            {{ ucfirst($activity->event) }}
                        </span>
                        <form method="POST" action="{{ route('admin.activity-logs.destroy', $activity->id) }}" data-confirm="Are you sure you want to delete this log record?" data-confirm-title="Delete Log" data-confirm-icon="warning" data-confirm-button="Delete">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="px-3 py-1 text-sm font-medium text-red-600 hover:text-red-700 hover:bg-red-50 rounded-lg transition border border-red-200">
                                Delete
                            </button>
                        </form>
                    </div>
                </div>
            </div>

            <div class="px-6 py-4 space-y-4">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <p class="text-sm font-medium text-gray-500">Description</p>
                        <p class="mt-1 text-gray-900">{{ $activity->description }}</p>
                    </div>
                    <div>
                        <p class="text-sm font-medium text-gray-500">Log Name</p>
                        <p class="mt-1 text-gray-900">{{ $activity->log_name ?? 'default' }}</p>
                    </div>
                    <div>
                        <p class="text-sm font-medium text-gray-500">Date</p>
                        <p class="mt-1 text-gray-900">{{ $activity->created_at->format('F j, Y, g:i A') }}</p>
                    </div>
                    <div>
                        <p class="text-sm font-medium text-gray-500">Subject</p>
                        <p class="mt-1 text-gray-900">{{ $activity->subject_type ? str_replace('App\\Models\\', '', $activity->subject_type) . ' #' . ($activity->subject_id ?? 'N/A') : 'N/A' }}</p>
                    </div>
                </div>

                @if($activity->properties && $activity->properties->count() > 0)
                    <div>
                        <p class="text-sm font-medium text-gray-500 mb-2">Changes</p>
                        <div class="bg-gray-50 rounded-lg p-4 overflow-x-auto">
                            <table class="w-full text-sm">
                                <thead>
                                    <tr class="border-b border-gray-200">
                                        <th class="px-3 py-2 text-left text-gray-500">Field</th>
                                        <th class="px-3 py-2 text-left text-gray-500">Old Value</th>
                                        <th class="px-3 py-2 text-left text-gray-500">New Value</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @php
                                        $properties = $activity->properties;
                                        $attributes = $properties->get('attributes', []);
                                        $old = $properties->get('old', []);
                                    @endphp
                                    @if(!empty($attributes))
                                        @foreach($attributes as $key => $value)
                                            <tr class="border-b border-gray-100">
                                                <td class="px-3 py-2 font-medium">{{ $key }}</td>
                                                <td class="px-3 py-2 text-red-600">{{ isset($old[$key]) ? (is_array($old[$key]) || is_object($old[$key]) ? json_encode($old[$key]) : $old[$key]) : '-' }}</td>
                                                <td class="px-3 py-2 text-green-600">{{ is_array($value) || is_object($value) ? json_encode($value) : $value }}</td>
                                            </tr>
                                        @endforeach
                                    @else
                                        <tr>
                                            <td colspan="3" class="px-3 py-4 text-center text-gray-500">No detailed changes recorded.</td>
                                        </tr>
                                    @endif
                                </tbody>
                            </table>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection
