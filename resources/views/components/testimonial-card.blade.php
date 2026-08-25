@props([
    'name' => 'Student Name',
    'role' => 'Software Engineer',
    'company' => 'Google',
    'review' => 'Great course!',
    'rating' => 5,
    'avatar' => null,
    'course' => null,
])

@php
    $initials = strtoupper(substr($name, 0, 1));
    $colors = ['bg-primary-500', 'bg-accent-500', 'bg-secondary-500', 'bg-purple-500', 'bg-red-500', 'bg-indigo-500'];
    $colorIndex = crc32($name) % count($colors);
@endphp

<div class="bg-white p-6 rounded-xl border border-gray-100 shadow-sm hover:shadow-md transition-shadow duration-300">
    {{-- Rating --}}
    <div class="flex items-center gap-0.5 mb-4">
        @for($i = 1; $i <= 5; $i++)
            @if($i <= $rating)
                <svg class="w-4 h-4 text-accent-400" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
            @endif
        @endfor
    </div>

    {{-- Review --}}
    <p class="text-gray-600 text-sm leading-relaxed mb-6">"{{ $review }}"</p>

    @if($course)
        <p class="text-xs text-primary-600 font-medium mb-4">Course: {{ $course }}</p>
    @endif

    {{-- Author --}}
    <div class="flex items-center gap-3 pt-4 border-t border-gray-100">
        @if($avatar)
            <img src="{{ $avatar }}" alt="{{ $name }}" class="w-10 h-10 rounded-full object-cover">
        @else
            <div class="w-10 h-10 {{ $colors[$colorIndex] }} rounded-full flex items-center justify-center text-white text-sm font-bold">{{ $initials }}</div>
        @endif
        <div>
            <p class="text-sm font-semibold text-gray-900">{{ $name }}</p>
            <p class="text-xs text-gray-500">{{ $role }}, {{ $company }}</p>
        </div>
    </div>
</div>
