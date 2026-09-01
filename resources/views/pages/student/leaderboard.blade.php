@extends('layouts.dashboard')
@section('title', 'Leaderboard')

@section('content')
<div class="max mx-auto">
    <div class="mb-8">
        <h1 class="text-2xl font-bold text-gray-900">Leaderboard</h1>
        <p class="text-sm text-gray-500 mt-1">Top learners ranked by total XP earned</p>
    </div>

    {{-- My Rank Banner --}}
    <div class="bg-gradient-to-r from-primary-600 to-primary-700 rounded-2xl p-6 text-white mb-8 shadow-lg">
        <div class="flex items-center gap-4">
            <div class="w-14 h-14 bg-white/20 rounded-2xl grid place-items-center text-2xl font-bold">
                #{{ number_format($myRank) }}
            </div>
            <div>
                <p class="text-sm text-white/70">Your current rank</p>
                <p class="text-2xl font-bold">{{ auth()->user()->name }}</p>
            </div>
            <div class="ml-auto text-right">
                <p class="text-sm text-white/70">Total XP</p>
                <p class="text-2xl font-bold">{{ number_format(auth()->user()->xp ?? 0) }}</p>
            </div>
        </div>
    </div>

    {{-- Leaderboard Table --}}
    <div class="bg-white border border-gray-100 rounded-xl shadow-sm overflow-hidden">
        @forelse($leaderboard as $i => $user)
            @php $isMe = $user->id === auth()->id(); @endphp
            <div class="flex items-center gap-4 px-5 py-4 {{ $i > 0 ? 'border-t border-gray-50' : '' }} {{ $isMe ? 'bg-primary-50' : '' }}">
                {{-- Rank --}}
                <div class="w-10 h-10 rounded-xl {{ $i === 0 ? 'bg-yellow-100 text-yellow-700' : ($i === 1 ? 'bg-gray-100 text-gray-600' : ($i === 2 ? 'bg-orange-100 text-orange-700' : 'bg-gray-50 text-gray-500')) }} grid place-items-center font-bold text-sm shrink-0">
                    @if($i === 0)
                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M11.48 3.499a.562.562 0 011.04 0l2.125 5.111a.563.563 0 00.475.345l5.518.442c.499.04.701.663.321.988l-4.204 3.602a.563.563 0 00-.182.557l1.285 5.385a.562.562 0 01-.84.61l-4.725-2.885a.563.563 0 00-.586 0L6.982 20.54a.562.562 0 01-.84-.61l1.285-5.386a.562.562 0 00-.182-.557l-4.204-3.602a.563.563 0 01.321-.988l5.518-.442a.563.563 0 00.475-.345L11.48 3.5z"/></svg>
                    @elseif($i === 1)
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M16.5 18.75h-9m9 0a3 3 0 013 3h-15a3 3 0 013-3m9 0v-3.375c0-.621-.503-1.125-1.125-1.125h-.871M7.5 18.75v-3.375c0-.621.504-1.125 1.125-1.125h.872m5.007 0H9.497m5.007 0a7.454 7.454 0 01-.982-3.172M9.497 14.25a7.454 7.454 0 00.981-3.172M5.25 4.236c-.982.143-1.954.317-2.916.52A6.003 6.003 0 007.73 9.728M5.25 4.236V4.5c0 2.108.966 3.99 2.48 5.228M5.25 4.236V2.721C7.456 2.41 9.71 2.25 12 2.25c2.291 0 4.545.16 6.75.47v1.516M18.75 4.236c.982.143 1.954.317 2.916.52A6.003 6.003 0 0116.27 9.728M18.75 4.236V4.5c0 2.108-.966 3.99-2.48 5.228m0 0a6.003 6.003 0 01-2.48.772m0 0c-.536 0-1.063.075-1.566.215"/></svg>
                    @elseif($i === 2)
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M15.362 5.214A8.252 8.252 0 0112 21 8.25 8.25 0 016.038 7.048 8.287 8.287 0 009 9.6a8.983 8.983 0 013.361-6.867 8.21 8.21 0 003 2.48z"/></svg>
                    @else
                        {{ $i + 1 }}
                    @endif
                </div>

                {{-- Avatar --}}
                @if($user->avatar_url)
                    <img src="{{ $user->avatar_url }}" class="w-10 h-10 rounded-full object-cover" alt="">
                @else
                    <div class="w-10 h-10 rounded-full bg-primary-100 text-primary-700 grid place-items-center text-sm font-bold">{{ $user->initials }}</div>
                @endif

                {{-- Name --}}
                <div class="flex-1 min-w-0">
                    <p class="text-sm font-semibold text-gray-900 truncate">
                        {{ $user->name }}
                        @if($isMe)
                            <span class="text-xs text-primary-600 font-medium">(You)</span>
                        @endif
                    </p>
                    @php
                        $userLevel = \App\Services\GamificationService::currentLevel($user);
                    @endphp
                    <p class="text-xs text-gray-400">Level {{ $userLevel }}</p>
                </div>

                {{-- XP --}}
                <div class="text-right shrink-0">
                    <p class="text-lg font-bold text-gray-900">{{ number_format($user->xp) }}</p>
                    <p class="text-xs text-gray-400">XP</p>
                </div>
            </div>
        @empty
            <div class="px-5 py-12 text-center text-gray-400">
                <svg class="w-12 h-12 mx-auto mb-3 text-gray-300" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M16.5 18.75h-9m9 0a3 3 0 013 3h-15a3 3 0 013-3m9 0v-3.375c0-.621-.503-1.125-1.125-1.125h-.871M7.5 18.75v-3.375c0-.621.504-1.125 1.125-1.125h.872m5.007 0H9.497m5.007 0a7.454 7.454 0 01-.982-3.172M9.497 14.25a7.454 7.454 0 00.981-3.172"/></svg>
                <p class="text-sm font-medium">No learners on the leaderboard yet</p>
                <p class="text-xs mt-1">Complete lessons and quizzes to earn XP and climb the ranks!</p>
            </div>
        @endforelse
    </div>
</div>
@endsection
