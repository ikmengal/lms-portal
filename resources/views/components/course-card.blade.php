@props([
    'title' => 'Course Title',
    'category' => 'Category',
    'instructor' => 'Instructor Name',
    'rating' => 4.5,
    'reviews' => 1200,
    'students' => '15K',
    'duration' => '20 hours',
    'price' => '$49.99',
    'originalPrice' => null,
    'image' => null,
    'level' => 'Beginner',
    'bestseller' => false,
    'slug' => '#',
])

@php
    $fullStars = floor($rating);
    $hasHalf = $rating - $fullStars >= 0.5;
@endphp

<a href="{{ $slug }}" class="group block bg-white rounded-xl border border-gray-100 shadow-sm hover:shadow-lg hover:border-primary-200 transition-all duration-300 overflow-hidden">
    {{-- Thumbnail --}}
    <div class="relative aspect-video bg-gradient-to-br from-primary-100 to-primary-200 overflow-hidden">
        @if($image)
            <img src="{{ $image }}" alt="{{ $title }}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300">
        @else
            <div class="w-full h-full flex items-center justify-center">
                <svg class="w-12 h-12 text-primary-300" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M4.26 10.147a60.438 60.438 0 0 0-.491 6.347A48.62 48.62 0 0 1 12 20.904a48.62 48.62 0 0 1 8.232-4.41 60.46 60.46 0 0 0-.491-6.347m-15.482 0a50.636 50.636 0 0 0-2.658-.813A59.906 59.906 0 0 1 12 3.493a59.903 59.903 0 0 1 10.399 5.84c-.896.248-1.783.52-2.658.814m-15.482 0A50.717 50.717 0 0 1 12 13.489a50.702 50.702 0 0 1 7.74-3.342M6.75 15a.75.75 0 1 0 0-1.5.75.75 0 0 0 0 1.5Zm0 0v-3.675A55.378 55.378 0 0 1 12 8.443m-7.007 11.55A5.981 5.981 0 0 0 6.75 15.75v-1.5" />
                </svg>
            </div>
        @endif
        @if($bestseller)
            <span class="absolute top-3 left-3 px-2.5 py-1 bg-accent-500 text-white text-xs font-bold rounded-md shadow">Bestseller</span>
        @endif
        <span class="absolute top-3 right-3 px-2.5 py-1 bg-white/90 backdrop-blur text-gray-700 text-xs font-medium rounded-md">{{ $level }}</span>
    </div>

    {{-- Content --}}
    <div class="p-5">
        <p class="text-xs font-medium text-primary-600 uppercase tracking-wide mb-1.5">{{ $category }}</p>
        <h3 class="font-semibold text-gray-900 group-hover:text-primary-700 transition line-clamp-2 leading-snug mb-3">{{ $title }}</h3>
        <p class="text-sm text-gray-500 mb-3">By {{ $instructor }}</p>

        {{-- Rating --}}
        <div class="flex items-center gap-2 mb-3">
            <div class="flex items-center gap-0.5">
                @for($i = 1; $i <= 5; $i++)
                    @if($i <= $fullStars)
                        <svg class="w-4 h-4 text-accent-400" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                    @elseif($hasHalf && $i === $fullStars + 1)
                        <svg class="w-4 h-4 text-accent-400" fill="currentColor" viewBox="0 0 20 20"><defs><linearGradient id="half"><stop offset="50%" stop-color="currentColor"/><stop offset="50%" stop-color="#D1D5DB"/></linearGradient></defs><path fill="url(#half)" d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                    @else
                        <svg class="w-4 h-4 text-gray-200" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                    @endif
                @endfor
            </div>
            <span class="text-sm font-medium text-gray-700">{{ $rating }}</span>
            <span class="text-sm text-gray-400">({{ number_format($reviews) }})</span>
        </div>

        {{-- Meta --}}
        <div class="flex items-center gap-3 text-xs text-gray-500 mb-4">
            <span class="flex items-center gap-1">
                <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                {{ $duration }}
            </span>
            <span class="flex items-center gap-1">
                <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19.128a9.38 9.38 0 002.625.372 9.337 9.337 0 004.121-.952 4.125 4.125 0 00-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 018.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0111.964-3.07M12 6.375a3.375 3.375 0 11-6.75 0 3.375 3.375 0 016.75 0zm8.25 2.25a2.625 2.625 0 11-5.25 0 2.625 2.625 0 015.25 0z"/></svg>
                {{ $students }} students
            </span>
        </div>

        {{-- Price --}}
        <div class="flex items-center justify-between pt-3 border-t border-gray-100">
            <div class="flex items-center gap-2">
                <span class="text-lg font-bold text-gray-900">{{ $price }}</span>
                @if($originalPrice)
                    <span class="text-sm text-gray-400 line-through">{{ $originalPrice }}</span>
                @endif
            </div>
            <span class="text-xs font-medium text-secondary-600 bg-secondary-50 px-2 py-1 rounded">Enroll Now</span>
        </div>
    </div>
</a>
