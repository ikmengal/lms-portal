@extends('layouts.dashboard')
@section('title', 'Live Classes')

@section('content')
<div class="max-w-5xl mx-auto">
    <div class="flex items-center justify-between mb-8">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Live Classes</h1>
            <p class="text-sm text-gray-500 mt-1">Join scheduled live sessions with your instructors</p>
        </div>
    </div>

    {{-- Tabs --}}
    <div class="flex gap-1 bg-gray-100 rounded-lg p-1 mb-6 w-fit" x-data>
        <a href="{{ route('live-classes.index', ['tab' => 'upcoming']) }}"
           class="px-4 py-2 text-sm font-medium rounded-md transition {{ $tab === 'upcoming' ? 'bg-white text-primary-700 shadow-sm' : 'text-gray-600 hover:text-gray-900' }}">
            Upcoming ({{ $upcoming->count() }})
        </a>
        <a href="{{ route('live-classes.index', ['tab' => 'past']) }}"
           class="px-4 py-2 text-sm font-medium rounded-md transition {{ $tab === 'past' ? 'bg-white text-primary-700 shadow-sm' : 'text-gray-600 hover:text-gray-900' }}">
            Past ({{ $past->count() }})
        </a>
    </div>

    {{-- Upcoming Classes --}}
    @if($tab === 'upcoming')
        @forelse($upcoming as $class)
            @php $startsIn = $class->scheduled_at->diffForHumans(); @endphp
            <div class="bg-white border border-primary-100 rounded-xl p-5 mb-4 shadow-sm hover:shadow-md transition">
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                    <div class="flex-1 min-w-0">
                        <div class="flex items-center gap-2 mb-1">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-green-100 text-green-700">UPCOMING</span>
                            <span class="text-xs text-gray-500">Starts {{ $startsIn }}</span>
                        </div>
                        <h3 class="text-lg font-semibold text-gray-900 mt-1">{{ $class->title }}</h3>
                        <p class="text-sm text-gray-500 mt-0.5">
                            {{ $class->course->title }} &middot; {{ $class->scheduled_at->format('D, M j, Y \a\t g:i A') }}
                        </p>
                        @if($class->duration_minutes)
                            <p class="text-xs text-gray-400 mt-1">Duration: {{ $class->duration_minutes }} minutes</p>
                        @endif
                        @if($class->description)
                            <p class="text-sm text-gray-600 mt-2">{{ $class->description }}</p>
                        @endif
                    </div>
                    <div class="flex items-center gap-2 shrink-0">
                        <form method="POST" action="{{ route('live-classes.join', $class) }}">
                            @csrf
                            <button type="submit" class="inline-flex items-center gap-2 px-5 py-2.5 bg-primary-600 text-white text-sm font-semibold rounded-lg hover:bg-primary-700 transition shadow-sm">
                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M5.25 5.653c0-.856.917-1.398 1.667-.986l11.54 6.347a1.125 1.125 0 010 1.972l-11.54 6.347a1.125 1.125 0 01-1.667-.985V5.653z"/></svg>
                                Join Now
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        @empty
            <div class="text-center py-16 bg-white rounded-xl border border-gray-100">
                <svg class="w-16 h-16 text-gray-200 mx-auto mb-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1"><path stroke-linecap="round" stroke-linejoin="round" d="M5.25 5.653c0-.856.917-1.398 1.667-.986l11.54 6.347a1.125 1.125 0 010 1.972l-11.54 6.347a1.125 1.125 0 01-1.667-.985V5.653z"/></svg>
                <h3 class="text-lg font-semibold text-gray-900 mb-1">No upcoming classes</h3>
                <p class="text-sm text-gray-500">Check back later for scheduled live sessions.</p>
            </div>
        @endforelse
    @endif

    {{-- Past Classes --}}
    @if($tab === 'past')
        @forelse($past as $class)
            <div class="bg-white border border-gray-100 rounded-xl p-5 mb-4 opacity-75">
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                    <div class="flex-1 min-w-0">
                        <div class="flex items-center gap-2 mb-1">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-gray-100 text-gray-500">PAST</span>
                            <span class="text-xs text-gray-400">{{ $class->scheduled_at->format('D, M j, Y \a\t g:i A') }}</span>
                        </div>
                        <h3 class="text-lg font-semibold text-gray-700 mt-1">{{ $class->title }}</h3>
                        <p class="text-sm text-gray-500 mt-0.5">{{ $class->course->title }}</p>
                        @if($class->description)
                            <p class="text-sm text-gray-500 mt-2">{{ $class->description }}</p>
                        @endif
                    </div>
                    <div class="shrink-0">
                        @if($class->join_url)
                            <a href="{{ $class->join_url }}" target="_blank" rel="noopener"
                               class="inline-flex items-center gap-2 px-4 py-2 text-sm font-medium text-gray-600 bg-gray-100 rounded-lg hover:bg-gray-200 transition">
                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M13.5 6H5.25A2.25 2.25 0 003 8.25v10.5A2.25 2.25 0 005.25 21h10.5A2.25 2.25 0 0018 18.75V10.5m-10.5 6L21 3m0 0h-5.25M21 3v5.25"/></svg>
                                Open Link
                            </a>
                        @endif
                    </div>
                </div>
            </div>
        @empty
            <div class="text-center py-16 bg-white rounded-xl border border-gray-100">
                <svg class="w-16 h-16 text-gray-200 mx-auto mb-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                <h3 class="text-lg font-semibold text-gray-900 mb-1">No past classes</h3>
                <p class="text-sm text-gray-500">Completed live sessions will appear here.</p>
            </div>
        @endforelse
    @endif
</div>
@endsection
