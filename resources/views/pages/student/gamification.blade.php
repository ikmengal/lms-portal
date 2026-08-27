@extends('layouts.dashboard')
@section('title', 'Gamification')

@section('content')
<div class="max-w-5xl mx-auto space-y-8">
    {{-- Header --}}
    <div>
        <h1 class="text-2xl font-bold text-gray-900">Gamification</h1>
        <p class="text-sm text-gray-500 mt-1">Track your progress, earn badges, and climb the leaderboard</p>
    </div>

    {{-- XP + Level + Streak Cards --}}
    <div class="grid sm:grid-cols-3 gap-5">
        {{-- XP Card --}}
        <div class="bg-gradient-to-br from-primary-600 to-primary-700 rounded-2xl p-6 text-white shadow-lg">
            <div class="flex items-center gap-3 mb-4">
                <div class="w-12 h-12 bg-white/20 rounded-xl grid place-items-center">
                    <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M11.48 3.499a.562.562 0 011.04 0l2.125 5.111a.563.563 0 00.475.345l5.518.442c.499.04.701.663.321.988l-4.204 3.602a.563.563 0 00-.182.557l1.285 5.385a.562.562 0 01-.84.61l-4.725-2.885a.563.563 0 00-.586 0L6.982 20.54a.562.562 0 01-.84-.61l1.285-5.386a.562.562 0 00-.182-.557l-4.204-3.602a.563.563 0 01.321-.988l5.518-.442a.563.563 0 00.475-.345L11.48 3.5z"/></svg>
                </div>
                <div>
                    <p class="text-sm text-white/70">Total XP</p>
                    <p class="text-3xl font-bold">{{ number_format($totalXp) }}</p>
                </div>
            </div>
            <div class="mt-3">
                <div class="flex justify-between text-xs text-white/60 mb-1">
                    <span>Level {{ $level }}</span>
                    <span>{{ $xpProgress }}%</span>
                </div>
                <div class="h-2 bg-white/20 rounded-full overflow-hidden">
                    <div class="h-full bg-white rounded-full transition-all duration-500" style="width: {{ $xpProgress }}%"></div>
                </div>
                <p class="text-xs text-white/50 mt-1">{{ number_format($xpForNext) }} XP to Level {{ $level + 1 }}</p>
            </div>
        </div>

        {{-- Streak Card --}}
        <div class="bg-gradient-to-br from-orange-500 to-red-500 rounded-2xl p-6 text-white shadow-lg">
            <div class="flex items-center gap-3 mb-4">
                <div class="w-12 h-12 bg-white/20 rounded-xl grid place-items-center">
                    <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M15.362 5.214A8.252 8.252 0 0112 21 8.25 8.25 0 016.038 7.048 8.287 8.287 0 009 9.6a8.983 8.983 0 013.361-6.867 8.21 8.21 0 003 2.48z"/><path stroke-linecap="round" stroke-linejoin="round" d="M12 18a3.75 3.75 0 00.495-7.467 5.99 5.99 0 00-1.925 3.546 5.974 5.974 0 01-2.133-1A3.75 3.75 0 0012 18z"/></svg>
                </div>
                <div>
                    <p class="text-sm text-white/70">Current Streak</p>
                    <p class="text-3xl font-bold">{{ $streak?->current_streak ?? 0 }} days</p>
                </div>
            </div>
            <div class="mt-3 flex items-center gap-2">
                <div class="flex-1">
                    <p class="text-xs text-white/60">Longest Streak</p>
                    <p class="text-lg font-semibold">{{ $streak?->longest_streak ?? 0 }} days</p>
                </div>
            </div>
        </div>

        {{-- Rank Card --}}
        <div class="bg-gradient-to-br from-secondary-500 to-emerald-600 rounded-2xl p-6 text-white shadow-lg">
            <div class="flex items-center gap-3 mb-4">
                <div class="w-12 h-12 bg-white/20 rounded-xl grid place-items-center">
                    <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M16.5 18.75h-9m9 0a3 3 0 013 3h-15a3 3 0 013-3m9 0v-3.375c0-.621-.503-1.125-1.125-1.125h-.871M7.5 18.75v-3.375c0-.621.504-1.125 1.125-1.125h.872m5.007 0H9.497m5.007 0a7.454 7.454 0 01-.982-3.172M9.497 14.25a7.454 7.454 0 00.981-3.172M5.25 4.236c-.982.143-1.954.317-2.916.52A6.003 6.003 0 007.73 9.728M5.25 4.236V4.5c0 2.108.966 3.99 2.48 5.228M5.25 4.236V2.721C7.456 2.41 9.71 2.25 12 2.25c2.291 0 4.545.16 6.75.47v1.516M18.75 4.236c.982.143 1.954.317 2.916.52A6.003 6.003 0 0116.27 9.728M18.75 4.236V4.5c0 2.108-.966 3.99-2.48 5.228m0 0a6.003 6.003 0 01-2.48.772m0 0c-.536 0-1.063.075-1.566.215M15.27 10.5a6.003 6.003 0 00-2.48.772m0 0c-.536 0-1.063.075-1.566.215"/></svg>
                </div>
                <div>
                    <p class="text-sm text-white/70">Your Rank</p>
                    <p class="text-3xl font-bold">#{{ number_format($rank) }}</p>
                </div>
            </div>
            <a href="{{ route('gamification.leaderboard') }}" class="mt-3 inline-flex items-center gap-1 text-sm text-white/80 hover:text-white transition">
                View Leaderboard
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5L21 12m0 0l-7.5 7.5M21 12H3"/></svg>
            </a>
        </div>
    </div>

    <div class="grid lg:grid-cols-[1fr_380px] gap-8">
        {{-- Recent XP Activity --}}
        <div>
            <div class="flex items-center justify-between mb-4">
                <h2 class="text-lg font-bold text-gray-900">Recent XP Activity</h2>
                <a href="{{ route('gamification.badges') }}" class="text-sm text-primary-600 hover:text-primary-700 font-medium">All Badges →</a>
            </div>
            @forelse($recentXp as $tx)
                <div class="flex items-center gap-4 py-3 border-b border-gray-50 last:border-0">
                    <div class="w-10 h-10 rounded-xl bg-primary-50 grid place-items-center shrink-0">
                        @if($tx->type === 'lesson_complete')
                            <svg class="w-5 h-5 text-primary-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M4.26 10.147a60.438 60.438 0 00-.491 6.347A48.62 48.62 0 0112 20.904a48.62 48.62 0 018.232-4.41 60.46 60.46 0 00-.491-6.347m-15.482 0a50.636 50.636 0 00-2.658-.813A59.906 59.906 0 0112 3.493a59.903 59.903 0 0110.399 5.84c-.896.248-1.783.52-2.658.814m-15.482 0A50.717 50.717 0 0112 13.489a50.702 50.702 0 017.74-3.342"/></svg>
                        @elseif($tx->type === 'quiz_passed')
                            <svg class="w-5 h-5 text-primary-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M16.5 18.75h-9m9 0a3 3 0 013 3h-15a3 3 0 013-3m9 0v-3.375c0-.621-.503-1.125-1.125-1.125h-.871M7.5 18.75v-3.375c0-.621.504-1.125 1.125-1.125h.872m5.007 0H9.497m5.007 0a7.454 7.454 0 01-.982-3.172M9.497 14.25a7.454 7.454 0 00.981-3.172M5.25 4.236c-.982.143-1.954.317-2.916.52A6.003 6.003 0 007.73 9.728M5.25 4.236V4.5c0 2.108.966 3.99 2.48 5.228M5.25 4.236V2.721C7.456 2.41 9.71 2.25 12 2.25c2.291 0 4.545.16 6.75.47v1.516M18.75 4.236c.982.143 1.954.317 2.916.52A6.003 6.003 0 0116.27 9.728M18.75 4.236V4.5c0 2.108-.966 3.99-2.48 5.228m0 0a6.003 6.003 0 01-2.48.772m0 0c-.536 0-1.063.075-1.566.215"/></svg>
                        @elseif($tx->type === 'course_completed')
                            <svg class="w-5 h-5 text-primary-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M4.26 10.147a60.438 60.438 0 00-.491 6.347A48.62 48.62 0 0112 20.904a48.62 48.62 0 018.232-4.41 60.46 60.46 0 00-.491-6.347m-15.482 0a50.636 50.636 0 00-2.658-.813A59.906 59.906 0 0112 3.493a59.903 59.903 0 0110.399 5.84c-.896.248-1.783.52-2.658.814m-15.482 0A50.717 50.717 0 0112 13.489a50.702 50.702 0 017.74-3.342"/></svg>
                        @elseif($tx->type === 'streak_bonus')
                            <svg class="w-5 h-5 text-primary-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M15.362 5.214A8.252 8.252 0 0112 21 8.25 8.25 0 016.038 7.048 8.287 8.287 0 009 9.6a8.983 8.983 0 013.361-6.867 8.21 8.21 0 003 2.48z"/></svg>
                        @elseif($tx->type === 'badge_reward')
                            <svg class="w-5 h-5 text-primary-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M16.5 18.75h-9m9 0a3 3 0 013 3h-15a3 3 0 013-3m9 0v-3.375c0-.621-.503-1.125-1.125-1.125h-.871M7.5 18.75v-3.375c0-.621.504-1.125 1.125-1.125h.872m5.007 0H9.497m5.007 0a7.454 7.454 0 01-.982-3.172M9.497 14.25a7.454 7.454 0 00.981-3.172M5.25 4.236c-.982.143-1.954.317-2.916.52A6.003 6.003 0 007.73 9.728M5.25 4.236V4.5c0 2.108.966 3.99 2.48 5.228M5.25 4.236V2.721C7.456 2.41 9.71 2.25 12 2.25c2.291 0 4.545.16 6.75.47v1.516M18.75 4.236c.982.143 1.954.317 2.916.52A6.003 6.003 0 0116.27 9.728M18.75 4.236V4.5c0 2.108-.966 3.99-2.48 5.228m0 0a6.003 6.003 0 01-2.48.772m0 0c-.536 0-1.063.075-1.566.215"/></svg>
                        @else
                            <svg class="w-5 h-5 text-primary-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M11.48 3.499a.562.562 0 011.04 0l2.125 5.111a.563.563 0 00.475.345l5.518.442c.499.04.701.663.321.988l-4.204 3.602a.563.563 0 00-.182.557l1.285 5.385a.562.562 0 01-.84.61l-4.725-2.885a.563.563 0 00-.586 0L6.982 20.54a.562.562 0 01-.84-.61l1.285-5.386a.562.562 0 00-.182-.557l-4.204-3.602a.563.563 0 01.321-.988l5.518-.442a.563.563 0 00.475-.345L11.48 3.5z"/></svg>
                        @endif
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-medium text-gray-900 truncate">{{ $tx->description }}</p>
                        <p class="text-xs text-gray-400">{{ $tx->created_at->diffForHumans() }}</p>
                    </div>
                    <span class="text-sm font-bold text-secondary-600 shrink-0">+{{ $tx->amount }} XP</span>
                </div>
            @empty
                <div class="text-center py-12 text-gray-400">
                    <svg class="w-12 h-12 mx-auto mb-3 text-gray-300" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M11.48 3.499a.562.562 0 011.04 0l2.125 5.111a.563.563 0 00.475.345l5.518.442c.499.04.701.663.321.988l-4.204 3.602a.563.563 0 00-.182.557l1.285 5.385a.562.562 0 01-.84.61l-4.725-2.885a.563.563 0 00-.586 0L6.982 20.54a.562.562 0 01-.84-.61l1.285-5.386a.562.562 0 00-.182-.557l-4.204-3.602a.563.563 0 01.321-.988l5.518-.442a.563.563 0 00.475-.345L11.48 3.5z"/></svg>
                    <p class="text-sm font-medium">No XP earned yet</p>
                    <p class="text-xs mt-1">Complete lessons, pass quizzes, and maintain your streak to earn XP!</p>
                </div>
            @endforelse
        </div>

        {{-- Mini Leaderboard --}}
        <div>
            <div class="flex items-center justify-between mb-4">
                <h2 class="text-lg font-bold text-gray-900">Top Learners</h2>
                <a href="{{ route('gamification.leaderboard') }}" class="text-sm text-primary-600 hover:text-primary-700 font-medium">Full Board →</a>
            </div>
            <div class="bg-white border border-gray-100 rounded-xl overflow-hidden shadow-sm">
                @forelse($leaderboard->take(5) as $i => $u)
                    <div class="flex items-center gap-3 px-4 py-3 {{ $i > 0 ? 'border-t border-gray-50' : '' }} {{ $u->id === auth()->id() ? 'bg-primary-50' : '' }}">
                        <span class="w-7 h-7 rounded-full {{ $i < 3 ? 'bg-primary-100 text-primary-700' : 'bg-gray-100 text-gray-500' }} grid place-items-center text-xs font-bold shrink-0">
                            {{ $i + 1 }}
                        </span>
                        @if($u->avatar_url)
                            <img src="{{ $u->avatar_url }}" class="w-8 h-8 rounded-full object-cover" alt="">
                        @else
                            <div class="w-8 h-8 rounded-full bg-primary-100 text-primary-700 grid place-items-center text-xs font-bold">{{ $u->initials }}</div>
                        @endif
                        <div class="flex-1 min-w-0">
                            <p class="text-sm font-medium text-gray-900 truncate">{{ $u->name }}</p>
                        </div>
                        <span class="text-sm font-semibold text-gray-600 shrink-0">{{ number_format($u->xp) }} XP</span>
                    </div>
                @empty
                    <div class="px-4 py-6 text-center text-gray-400 text-sm">No learners yet</div>
                @endforelse
            </div>

            {{-- Earned Badges Preview --}}
            @if($badges->isNotEmpty())
                <h2 class="text-lg font-bold text-gray-900 mt-8 mb-4">Your Badges ({{ $badges->count() }})</h2>
                <div class="flex flex-wrap gap-3">
                    @foreach($badges->take(8) as $badge)
                        <div class="group relative w-12 h-12 rounded-xl grid place-items-center shadow-sm border border-gray-100 hover:scale-110 transition cursor-pointer" style="background: {{ $badge->color }}20; border-color: {{ $badge->color }}40">
                            @if($badge->icon === 'star')
                                <svg class="w-6 h-6" style="color: {{ $badge->color }}" fill="currentColor" viewBox="0 0 24 24"><path d="M11.48 3.499a.562.562 0 011.04 0l2.125 5.111a.563.563 0 00.475.345l5.518.442c.499.04.701.663.321.988l-4.204 3.602a.563.563 0 00-.182.557l1.285 5.385a.562.562 0 01-.84.61l-4.725-2.885a.563.563 0 00-.586 0L6.982 20.54a.562.562 0 01-.84-.61l1.285-5.386a.562.562 0 00-.182-.557l-4.204-3.602a.563.563 0 01.321-.988l5.518-.442a.563.563 0 00.475-.345L11.48 3.5z"/></svg>
                            @elseif($badge->icon === 'trophy')
                                <svg class="w-6 h-6" style="color: {{ $badge->color }}" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M16.5 18.75h-9m9 0a3 3 0 013 3h-15a3 3 0 013-3m9 0v-3.375c0-.621-.503-1.125-1.125-1.125h-.871M7.5 18.75v-3.375c0-.621.504-1.125 1.125-1.125h.872m5.007 0H9.497m5.007 0a7.454 7.454 0 01-.982-3.172M9.497 14.25a7.454 7.454 0 00.981-3.172M5.25 4.236c-.982.143-1.954.317-2.916.52A6.003 6.003 0 007.73 9.728M5.25 4.236V4.5c0 2.108.966 3.99 2.48 5.228M5.25 4.236V2.721C7.456 2.41 9.71 2.25 12 2.25c2.291 0 4.545.16 6.75.47v1.516M18.75 4.236c.982.143 1.954.317 2.916.52A6.003 6.003 0 0116.27 9.728M18.75 4.236V4.5c0 2.108-.966 3.99-2.48 5.228m0 0a6.003 6.003 0 01-2.48.772m0 0c-.536 0-1.063.075-1.566.215"/></svg>
                            @elseif($badge->icon === 'fire' || $badge->icon === 'bolt' || $badge->icon === 'flame')
                                <svg class="w-6 h-6" style="color: {{ $badge->color }}" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M15.362 5.214A8.252 8.252 0 0112 21 8.25 8.25 0 016.038 7.048 8.287 8.287 0 009 9.6a8.983 8.983 0 013.361-6.867 8.21 8.21 0 003 2.48z"/><path stroke-linecap="round" stroke-linejoin="round" d="M12 18a3.75 3.75 0 00.495-7.467 5.99 5.99 0 00-1.925 3.546 5.974 5.974 0 01-2.133-1A3.75 3.75 0 0012 18z"/></svg>
                            @elseif($badge->icon === 'graduation-cap')
                                <svg class="w-6 h-6" style="color: {{ $badge->color }}" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M4.26 10.147a60.438 60.438 0 00-.491 6.347A48.62 48.62 0 0112 20.904a48.62 48.62 0 018.232-4.41 60.46 60.46 0 00-.491-6.347m-15.482 0a50.636 50.636 0 00-2.658-.813A59.906 59.906 0 0112 3.493a59.903 59.903 0 0110.399 5.84c-.896.248-1.783.52-2.658.814m-15.482 0A50.717 50.717 0 0112 13.489a50.702 50.702 0 017.74-3.342"/></svg>
                            @elseif($badge->icon === 'crown')
                                <svg class="w-6 h-6" style="color: {{ $badge->color }}" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M16.5 18.75h-9m9 0a3 3 0 013 3h-15a3 3 0 013-3m9 0v-3.375c0-.621-.503-1.125-1.125-1.125h-.871M7.5 18.75v-3.375c0-.621.504-1.125 1.125-1.125h.872m5.007 0H9.497m5.007 0a7.454 7.454 0 01-.982-3.172M9.497 14.25a7.454 7.454 0 00.981-3.172"/></svg>
                            @else
                                <svg class="w-6 h-6" style="color: {{ $badge->color }}" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M16.5 18.75h-9m9 0a3 3 0 013 3h-15a3 3 0 013-3m9 0v-3.375c0-.621-.503-1.125-1.125-1.125h-.871M7.5 18.75v-3.375c0-.621.504-1.125 1.125-1.125h.872m5.007 0H9.497m5.007 0a7.454 7.454 0 01-.982-3.172M9.497 14.25a7.454 7.454 0 00.981-3.172M5.25 4.236c-.982.143-1.954.317-2.916.52A6.003 6.003 0 007.73 9.728M5.25 4.236V4.5c0 2.108.966 3.99 2.48 5.228M5.25 4.236V2.721C7.456 2.41 9.71 2.25 12 2.25c2.291 0 4.545.16 6.75.47v1.516M18.75 4.236c.982.143 1.954.317 2.916.52A6.003 6.003 0 0116.27 9.728M18.75 4.236V4.5c0 2.108-.966 3.99-2.48 5.228m0 0a6.003 6.003 0 01-2.48.772m0 0c-.536 0-1.063.075-1.566.215"/></svg>
                            @endif
                            <div class="absolute bottom-full left-1/2 -translate-x-1/2 mb-2 px-3 py-1.5 bg-gray-900 text-white text-xs font-medium rounded-lg whitespace-nowrap opacity-0 group-hover:opacity-100 transition pointer-events-none shadow-lg">
                                {{ $badge->name }}
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </div>
</div>
@endsection