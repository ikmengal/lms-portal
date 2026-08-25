@props([
    'user' => auth()->user(),
    'size' => 'w-10 h-10 text-sm',
])

@if($user->avatar_url)
    <img src="{{ $user->avatar_url }}?v={{ strtotime($user->updated_at) }}" alt="{{ $user->name }}"
        {{ $attributes->merge(['class' => "$size rounded-full object-cover shrink-0"]) }}>
@else
    <div {{ $attributes->merge(['class' => "$size bg-primary-600 rounded-full flex items-center justify-center text-white font-bold shrink-0"]) }}>
        {{ $user->initials }}
    </div>
@endif
