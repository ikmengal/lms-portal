@extends('layouts.dashboard')
@section('title', 'Live Classes')
@section('content')
    <div class="max-w-4xl mx-auto space-y-6">
        {{-- Header --}}
        <div class="flex items-center gap-3">
            <a href="{{ route('admin.courses.curriculum', $course) }}" class="p-2 text-gray-400 hover:text-gray-700 hover:bg-gray-100 rounded-lg transition">
                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18"/></svg>
            </a>
            <div class="flex-1 min-w-0">
                <h1 class="text-2xl font-bold text-gray-900 truncate">Live Classes</h1>
                <p class="text-gray-500 mt-0.5 truncate">{{ $course->title }} · enrolled students get scheduled &amp; reminder notifications</p>
            </div>
        </div>

        @if(session('success'))
            <div class="bg-secondary-50 border border-secondary-200 rounded-xl p-4 text-sm text-secondary-800">{{ session('success') }}</div>
        @endif

        {{-- Schedule form --}}
        <form method="POST" action="{{ route('admin.courses.live-classes.store', $course) }}" class="bg-white rounded-xl border border-gray-100 shadow-sm p-6 space-y-4">
            @csrf
            <h2 class="text-lg font-semibold text-gray-900">Schedule a Live Class</h2>
            <div class="grid sm:grid-cols-2 gap-4">
                <div class="sm:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Title <span class="text-red-500">*</span></label>
                    <input type="text" name="title" required maxlength="255" placeholder="e.g. Week 1 Kickoff Q&A Session"
                        class="w-full px-4 py-2.5 border border-gray-200 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500 text-sm transition" />
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Date &amp; Time <span class="text-red-500">*</span></label>
                    <input type="datetime-local" name="scheduled_at" required
                        class="w-full px-4 py-2.5 border border-gray-200 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500 text-sm transition" />
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Duration (minutes)</label>
                    <input type="number" name="duration_minutes" value="60" min="5" max="600"
                        class="w-full px-4 py-2.5 border border-gray-200 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500 text-sm transition" />
                </div>
                <div class="sm:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Join URL (Zoom / Meet / Teams) <span class="text-red-500">*</span></label>
                    <input type="url" name="join_url" required placeholder="https://zoom.us/j/..."
                        class="w-full px-4 py-2.5 border border-gray-200 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500 text-sm transition" />
                </div>
                <div class="sm:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Agenda / Notes</label>
                    <textarea name="description" rows="2" maxlength="2000" placeholder="What will be covered in this session..."
                        class="w-full px-4 py-2.5 border border-gray-200 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500 text-sm transition"></textarea>
                </div>
            </div>
            <div class="flex justify-end">
                <button type="submit" class="px-6 py-2.5 bg-primary-600 hover:bg-primary-700 text-white font-medium rounded-lg text-sm transition shadow-sm">
                    Schedule &amp; Notify Students
                </button>
            </div>
        </form>

        {{-- List --}}
        <div class="space-y-3">
            @forelse($classes as $class)
                @php $upcoming = $class->isUpcoming(); @endphp
                <div class="bg-white rounded-xl border {{ $upcoming ? 'border-primary-200' : 'border-gray-100 opacity-75' }} shadow-sm p-5 flex flex-col sm:flex-row sm:items-center gap-4"
                    x-data="{ editing: false }">
                    <div class="w-11 h-11 rounded-xl grid place-items-center shrink-0 {{ $upcoming ? 'bg-red-100 text-red-600' : 'bg-gray-100 text-gray-400' }}">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M5.25 5.653c0-.856.917-1.398 1.667-.986l11.54 6.347a1.125 1.125 0 010 1.972l-11.54 6.347a1.125 1.125 0 01-1.667-.985V5.653z"/></svg>
                    </div>

                    <div class="flex-1 min-w-0">
                        <div x-show="!editing">
                            <p class="text-sm font-semibold text-gray-900 truncate">{{ $class->title }}
                                @if($upcoming)
                                    <span class="ml-1 px-1.5 py-0.5 rounded text-[10px] font-bold bg-primary-100 text-primary-700 align-middle">UPCOMING</span>
                                @endif
                            </p>
                            <p class="text-xs text-gray-400 mt-0.5">
                                {{ $class->scheduled_at->format('D, M j, Y g:i A') }} · {{ $class->duration_minutes }} min
                                @if($upcoming) · in {{ Carbon\Carbon::parse($class->scheduled_at)->diffForHumans(['parts' => 2]) }} @endif
                            </p>
                            <a href="{{ $class->join_url }}" target="_blank" rel="noopener" class="inline-block mt-1 text-xs font-mono text-primary-600 hover:underline truncate max-w-full">{{ $class->join_url }}</a>
                        </div>

                        {{-- Inline edit --}}
                        <form method="POST" action="{{ route('admin.courses.live-classes.update', [$course, $class]) }}" x-show="editing" x-cloak class="grid sm:grid-cols-2 gap-3">
                            @csrf
                            @method('PUT')
                            <div class="sm:col-span-2">
                                <input type="text" name="title" value="{{ $class->title }}" required maxlength="255" class="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm" />
                            </div>
                            <div><input type="datetime-local" name="scheduled_at" value="{{ $class->scheduled_at->format('Y-m-d\TH:i') }}" required class="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm" /></div>
                            <div><input type="number" name="duration_minutes" value="{{ $class->duration_minutes }}" min="5" max="600" class="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm" /></div>
                            <div class="sm:col-span-2"><input type="url" name="join_url" value="{{ $class->join_url }}" required class="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm" /></div>
                            <div class="sm:col-span-2 flex gap-2">
                                <button type="submit" class="px-4 py-2 bg-primary-600 text-white rounded-lg text-sm font-medium">Save</button>
                                <button type="button" @click="editing = false" class="px-4 py-2 text-gray-600 text-sm">Cancel</button>
                            </div>
                        </form>
                    </div>

                    <div class="flex items-center gap-1.5 shrink-0 self-end sm:self-auto" x-show="!editing">
                        <button type="button" @click="editing = true" class="p-1.5 text-gray-400 hover:text-primary-600 hover:bg-primary-50 rounded-lg transition" title="Edit / Reschedule">
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931zm0 0L19.5 7.125"/></svg>
                        </button>
                        <button type="submit" form="delete-class-{{ $class->id }}" data-confirm="Remove this live class?" data-confirm-title="Delete?"
                            class="p-1.5 text-gray-400 hover:text-red-600 hover:bg-red-50 rounded-lg transition" title="Delete">
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0"/></svg>
                        </button>
                    </div>
                </div>
                <form id="delete-class-{{ $class->id }}" method="POST" action="{{ route('admin.courses.live-classes.destroy', [$course, $class]) }}" class="hidden">@csrf @method('DELETE')</form>
            @empty
                <div class="bg-white rounded-xl border border-dashed border-gray-200 p-12 text-center">
                    <svg class="w-10 h-10 text-gray-300 mx-auto mb-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M15 10.5a3 3 0 11-6 0 3 3 0 016 0zm3 0h.008v.008H18V10.5zm-12 0h.008v.008H6V10.5zm21 12c0 4.97-4.03 9-9 9s-9-4.03-9-9 4.03-9 9-9 9 4.03 9 9z"/></svg>
                    <p class="text-sm font-medium text-gray-500">No live classes scheduled</p>
                    <p class="text-sm text-gray-400 mt-1">Schedule one above — students are notified instantly plus reminders at 24 hours and 15 minutes before start.</p>
                </div>
            @endforelse
        </div>
    </div>
@endsection
