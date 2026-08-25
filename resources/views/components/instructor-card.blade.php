@props([
    'instructor',
    'compact' => false,
])

@php
    $initials = $instructor->initials;
    $avgRating = (float) ($instructor->avg_rating ?? 0);
    $fullStars = floor($avgRating);
@endphp

<div class="group bg-white rounded-2xl border border-gray-100 shadow-sm hover:shadow-lg hover:border-primary-200 transition-all duration-300 overflow-hidden flex flex-col">
    {{-- Header banner --}}
    <div class="relative h-24 bg-gradient-to-r from-primary-600 to-primary-800">
        <div class="absolute inset-0 opacity-20" style="background-image: radial-gradient(circle at 20% 50%, white 1px, transparent 1px); background-size: 18px 18px;"></div>
        <span class="absolute top-3 right-3 px-2.5 py-1 bg-white/90 backdrop-blur text-xs font-semibold rounded-md text-gray-700">
            {{ number_format($instructor->students_count ?? 0) }} Students
        </span>
    </div>

    <div class="flex-1 flex flex-col px-6 pb-6 -mt-10">
        {{-- Avatar --}}
        @if($instructor->avatar_url)
            <img src="{{ $instructor->avatar_url }}" alt="{{ $instructor->name }}" class="w-20 h-20 rounded-2xl object-cover ring-4 ring-white shadow-md mb-4">
        @else
            <div class="w-20 h-20 rounded-2xl bg-gradient-to-br from-accent-400 to-accent-600 ring-4 ring-white shadow-md mb-4 grid place-items-center text-white text-xl font-bold">
                {{ $initials }}
            </div>
        @endif

        <h3 class="font-bold text-lg text-gray-900 group-hover:text-primary-700 transition leading-snug">
            {{ $instructor->name }}
        </h3>

        {{-- Rating --}}
        @if($avgRating > 0)
            <div class="flex items-center gap-1.5 mt-1.5">
                <svg class="w-4 h-4 text-accent-400" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                <span class="text-sm font-semibold text-gray-900">{{ number_format($avgRating, 1) }}</span>
                <span class="text-xs text-gray-400">({{ number_format($instructor->reviews_count ?? 0) }} reviews)</span>
            </div>
        @else
            <p class="text-xs text-gray-400 mt-1.5">New instructor</p>
        @endif

        @unless($compact)
            <p class="text-sm text-gray-500 mt-3 line-clamp-2 leading-relaxed">{{ Str::limit($instructor->bio ?? 'Experienced professional passionate about teaching.', 110) }}</p>
        @endunless

        {{-- Stats --}}
        <div class="grid grid-cols-2 gap-3 mt-5">
            <div class="bg-gray-50 rounded-xl py-2.5 text-center">
                <p class="text-base font-bold text-gray-900">{{ number_format($instructor->courses_count ?? 0) }}</p>
                <p class="text-[11px] text-gray-500 uppercase tracking-wide">Courses</p>
            </div>
            <div class="bg-gray-50 rounded-xl py-2.5 text-center">
                <p class="text-base font-bold text-gray-900">{{ number_format($instructor->students_count ?? 0) }}</p>
                <p class="text-[11px] text-gray-500 uppercase tracking-wide">Students</p>
            </div>
        </div>

        <a href="{{ route('instructors.show', $instructor) }}"
           class="mt-5 w-full inline-flex items-center justify-center gap-2 px-4 py-2.5 bg-primary-600 hover:bg-primary-700 text-white text-sm font-semibold rounded-xl transition group-hover:shadow-lg group-hover:shadow-primary-600/25">
            View Profile
            <svg class="w-4 h-4 transition-transform group-hover:translate-x-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5L21 12m0 0l-7.5 7.5M21 12H3"/></svg>
        </a>
    </div>
</div>
