@props([
    'title' => 'Category',
    'icon' => 'DS',
    'count' => '50+',
    'color' => 'primary',
    'slug' => '#',
])

@php
    $colorMap = [
        'primary' => ['bg' => 'bg-primary-100', 'text' => 'text-primary-600', 'hover' => 'group-hover:bg-primary-600', 'hoverText' => 'group-hover:text-white'],
        'accent' => ['bg' => 'bg-accent-100', 'text' => 'text-accent-600', 'hover' => 'group-hover:bg-accent-600', 'hoverText' => 'group-hover:text-white'],
        'secondary' => ['bg' => 'bg-secondary-100', 'text' => 'text-secondary-600', 'hover' => 'group-hover:bg-secondary-600', 'hoverText' => 'group-hover:text-white'],
        'purple' => ['bg' => 'bg-purple-100', 'text' => 'text-purple-600', 'hover' => 'group-hover:bg-purple-600', 'hoverText' => 'group-hover:text-white'],
        'red' => ['bg' => 'bg-red-100', 'text' => 'text-red-600', 'hover' => 'group-hover:bg-red-600', 'hoverText' => 'group-hover:text-white'],
        'indigo' => ['bg' => 'bg-indigo-100', 'text' => 'text-indigo-600', 'hover' => 'group-hover:bg-indigo-600', 'hoverText' => 'group-hover:text-white'],
        'yellow' => ['bg' => 'bg-yellow-100', 'text' => 'text-yellow-600', 'hover' => 'group-hover:bg-yellow-600', 'hoverText' => 'group-hover:text-white'],
    ];
    $c = $colorMap[$color] ?? $colorMap['primary'];
@endphp

<a href="{{ $slug }}" class="group block p-6 bg-white rounded-xl border border-gray-100 shadow-sm hover:shadow-lg hover:border-primary-200 transition-all duration-300 text-center">
    <div class="w-16 h-16 {{ $c['bg'] }} {{ $c['hover'] }} rounded-2xl flex items-center justify-center mx-auto mb-4 transition-all duration-300">
        <span class="text-xl font-bold {{ $c['text'] }} {{ $c['hoverText'] }} transition-colors duration-300">{{ $icon }}</span>
    </div>
    <h3 class="font-semibold text-gray-900 group-hover:text-primary-700 transition mb-1">{{ $title }}</h3>
    <p class="text-sm text-gray-500">{{ $count }} Courses</p>
</a>
