@extends('layouts.dashboard')
@section('title', 'Course Curriculum')
@section('content')
    <div class="max-w-4xl mx-auto space-y-6">
        {{-- Header --}}
        <div class="flex items-center gap-3">
            <a href="{{ route('admin.courses.index') }}" class="p-2 text-gray-400 hover:text-gray-700 hover:bg-gray-100 rounded-lg transition">
                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18"/></svg>
            </a>
            <div class="flex-1 min-w-0">
                <h1 class="text-2xl font-bold text-gray-900 truncate">Curriculum</h1>
                <p class="text-gray-500 mt-0.5 truncate">{{ $course->title }} · {{ $course->modules->count() }} chapters · {{ $totalLessons }} lessons</p>
            </div>
            <a href="{{ route('admin.courses.tests.index', $course) }}" class="inline-flex items-center gap-2 px-4 py-2 text-sm font-medium text-primary-700 bg-primary-50 hover:bg-primary-100 rounded-lg transition">Tests & Exams</a>
        </div>

        {{-- Add Chapter --}}
        <form method="POST" action="{{ route('admin.modules.store', $course) }}" class="bg-white rounded-xl border border-gray-100 shadow-sm p-5 flex gap-3 items-end">
            @csrf
            <div class="flex-1">
                <label for="module_title" class="block text-sm font-medium text-gray-700 mb-1">Add Chapter / Module</label>
                <input type="text" id="module_title" name="title" required placeholder="e.g. Getting Started with HTML"
                    class="w-full px-4 py-2.5 border border-gray-200 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500 text-sm transition @error('title') border-red-300 @enderror" />
                @error('title')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
            </div>
            <button type="submit" class="px-5 py-2.5 bg-primary-600 hover:bg-primary-700 text-white font-medium rounded-lg text-sm transition shadow-sm whitespace-nowrap">+ Add Chapter</button>
        </form>

        {{-- Modules List --}}
        <div class="space-y-4">
            @forelse($course->modules->sortBy('sort_order') as $mi => $module)
                <div class="bg-white rounded-xl border border-gray-100 shadow-sm overflow-hidden" x-data="{ renaming: false }">
                    {{-- Module header --}}
                    <div class="p-4 flex items-center justify-between gap-3 bg-gray-50/60 border-b border-gray-100">
                        <div class="flex items-center gap-3 min-w-0">
                            <span class="w-8 h-8 bg-primary-600 text-white rounded-lg flex items-center justify-center text-xs font-bold shrink-0">{{ $mi + 1 }}</span>
                            <div class="min-w-0" x-show="!renaming">
                                <p class="text-sm font-semibold text-gray-900 truncate">{{ $module->title }}</p>
                                <p class="text-xs text-gray-400">{{ $module->lessons->count() }} lessons · {{ $module->lessons->sum('duration_minutes') }} min total</p>
                            </div>
                            <form method="POST" action="{{ route('admin.modules.update', $module) }}" x-show="renaming" style="display:none;" class="flex gap-2 w-full">
                                @csrf
                                @method('PUT')
                                <input type="text" name="title" value="{{ $module->title }}" required class="flex-1 px-3 py-1.5 border border-gray-200 rounded-lg text-sm focus:ring-2 focus:ring-primary-500 focus:border-primary-500" />
                                <button type="submit" class="px-3 py-1.5 bg-primary-600 text-white text-xs font-medium rounded-lg hover:bg-primary-700 transition">Save</button>
                            </form>
                        </div>
                        <div class="flex items-center gap-1 shrink-0">
                            <button type="button" @click="renaming = !renaming" class="p-1.5 text-gray-400 hover:text-primary-600 hover:bg-primary-50 rounded-lg transition" title="Rename">
                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0115.75 21H5.25A2.25 2.25 0 013 18.75V8.25A2.25 2.25 0 015.25 6H10"/></svg>
                            </button>
                            <form method="POST" action="{{ route('admin.modules.move', $module) }}">
                                @csrf
                                <input type="hidden" name="direction" value="up">
                                <button type="submit" class="p-1.5 text-gray-400 hover:text-gray-700 hover:bg-gray-100 rounded-lg transition" title="Move up">
                                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M4.5 15.75l7.5-7.5 7.5 7.5"/></svg>
                                </button>
                            </form>
                            <form method="POST" action="{{ route('admin.modules.move', $module) }}">
                                @csrf
                                <input type="hidden" name="direction" value="down">
                                <button type="submit" class="p-1.5 text-gray-400 hover:text-gray-700 hover:bg-gray-100 rounded-lg transition" title="Move down">
                                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 8.25l-7.5 7.5-7.5-7.5"/></svg>
                                </button>
                            </form>
                            <button type="submit" form="delete-module-{{ $module->id }}"
                                data-confirm="This will delete the chapter '{{ $module->title }}' and all its lessons."
                                data-confirm-title="Delete chapter?"
                                class="p-1.5 text-gray-400 hover:text-red-600 hover:bg-red-50 rounded-lg transition" title="Delete">
                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0"/></svg>
                            </button>
                        </div>
                    </div>

                    {{-- Lessons --}}
                    <div class="divide-y divide-gray-50">
                        @forelse($module->lessons as $lesson)
                            <div class="px-4 py-3 flex items-center justify-between gap-3 hover:bg-gray-50 transition" x-data="{ editing: false }">
                                <div class="flex items-center gap-3 min-w-0">
                                    <svg class="w-4 h-4 text-gray-300 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M5.25 5.653c0-.856.917-1.398 1.667-.986l11.54 6.347a1.125 1.125 0 010 1.972l-11.54 6.347a1.125 1.125 0 01-1.667-.986V5.653z"/></svg>
                                    <div class="min-w-0" x-show="!editing">
                                        <p class="text-sm text-gray-800 truncate">{{ $lesson->title }}</p>
                                        <p class="text-xs text-gray-400">{{ $lesson->duration_minutes }} min</p>
                                    </div>
                                    <form method="POST" action="{{ route('admin.lessons.update', $lesson) }}" x-show="editing" style="display:none;" class="w-full space-y-2 py-1">
                                        @csrf
                                        @method('PUT')
                                        <div class="flex flex-col sm:flex-row gap-2">
                                            <input type="text" name="title" value="{{ $lesson->title }}" required class="flex-1 px-3 py-1.5 border border-gray-200 rounded-lg text-sm focus:ring-2 focus:ring-primary-500 focus:border-primary-500" />
                                            <input type="number" name="duration_minutes" value="{{ $lesson->duration_minutes }}" min="0" max="600" required class="w-24 px-3 py-1.5 border border-gray-200 rounded-lg text-sm focus:ring-2 focus:ring-primary-500 focus:border-primary-500" placeholder="min" />
                                            <button type="submit" class="px-3 py-1.5 bg-primary-600 text-white text-xs font-medium rounded-lg hover:bg-primary-700 transition">Save</button>
                                        </div>
                                        <input type="url" name="video_url" value="{{ $lesson->video_url }}" placeholder="Video URL (YouTube, Vimeo or MP4)..."
                                            class="w-full px-3 py-1.5 border border-gray-200 rounded-lg text-sm focus:ring-2 focus:ring-primary-500 focus:border-primary-500" />

                                        {{-- Resources --}}
                                        <div class="border border-gray-100 rounded-lg p-3 bg-gray-50/60 space-y-2">
                                            <p class="text-xs font-semibold text-gray-500 uppercase tracking-wide">Resources & Downloads</p>
                                            @forelse($lesson->resources as $resource)
                                                <div class="flex items-center justify-between gap-2 text-sm">
                                                    <span class="truncate text-gray-700">📄 {{ $resource->title }}{{ $resource->external_url ? ' (link)' : '' }}</span>
                                                    <button type="submit" form="delete-resource-{{ $resource->id }}" data-confirm="Remove resource '{{ $resource->title }}'?" class="text-xs text-red-500 hover:text-red-700 shrink-0">remove</button>
                                                </div>
                                            @empty
                                                <p class="text-xs text-gray-400">No resources yet.</p>
                                            @endforelse
                                            <form method="POST" action="{{ route('admin.lessons.resources.store', $lesson) }}" enctype="multipart/form-data" class="flex flex-col sm:flex-row gap-2 pt-1">
                                                @csrf
                                                <input type="text" name="title" required placeholder="Resource title..." class="flex-1 px-2.5 py-1.5 border border-gray-200 rounded-lg text-xs" />
                                                <input type="file" name="file" class="text-xs text-gray-500 file:mr-2 file:px-2 file:py-1 file:border-0 file:bg-primary-50 file:text-primary-700 file:rounded file:cursor-pointer" />
                                                <input type="url" name="external_url" placeholder="...or link" class="sm:w-36 px-2.5 py-1.5 border border-gray-200 rounded-lg text-xs" />
                                                <button type="submit" class="px-2.5 py-1.5 bg-secondary-500 hover:bg-secondary-600 text-white text-xs font-medium rounded-lg transition whitespace-nowrap">+ Add</button>
                                            </form>
                                        </div>
                                    </form>
                                </div>
                                <div class="flex items-center gap-1 shrink-0">
                                    <button type="button" @click="editing = !editing" class="p-1.5 text-gray-400 hover:text-primary-600 hover:bg-primary-50 rounded-lg transition" title="Edit lesson">
                                        <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0115.75 21H5.25A2.25 2.25 0 013 18.75V8.25A2.25 2.25 0 015.25 6H10"/></svg>
                                    </button>
                                    <button type="submit" form="delete-lesson-{{ $lesson->id }}"
                                        data-confirm="Delete lesson '{{ $lesson->title }}'?"
                                        data-confirm-title="Delete lesson?"
                                        class="p-1.5 text-gray-400 hover:text-red-600 hover:bg-red-50 rounded-lg transition" title="Delete lesson">
                                        <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                                    </button>
                                </div>
                            </div>
                        @empty
                            <div class="px-4 py-4 text-center text-sm text-gray-400">No lessons in this chapter yet.</div>
                        @endforelse

                        {{-- Add lesson --}}
                        <form method="POST" action="{{ route('admin.lessons.store', $module) }}" class="px-4 py-3 space-y-2 bg-gray-50/40">
                            @csrf
                            <div class="flex gap-2 items-end">
                                <input type="text" name="title" required placeholder="New lesson title..."
                                    class="flex-1 px-3 py-2 border border-gray-200 rounded-lg text-sm focus:ring-2 focus:ring-primary-500 focus:border-primary-500" />
                                <input type="number" name="duration_minutes" value="10" min="0" max="600"
                                    class="w-20 px-3 py-2 border border-gray-200 rounded-lg text-sm focus:ring-2 focus:ring-primary-500 focus:border-primary-500" title="Duration (minutes)" />
                                <button type="submit" class="px-3 py-2 bg-secondary-500 hover:bg-secondary-600 text-white text-xs font-medium rounded-lg transition whitespace-nowrap">+ Lesson</button>
                            </div>
                            <input type="url" name="video_url" placeholder="Video URL — optional, can be added later by editing the lesson (YouTube, Vimeo or MP4)..."
                                class="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm focus:ring-2 focus:ring-primary-500 focus:border-primary-500" />
                        </form>
                    </div>
                </div>

                @php
                    $deleteForms[] = ['id' => 'delete-module-' . $module->id, 'action' => route('admin.modules.destroy', $module)];
                @endphp

                @foreach($module->lessons as $lesson)
                    @php
                        $deleteForms[] = ['id' => 'delete-lesson-' . $lesson->id, 'action' => route('admin.lessons.destroy', $lesson)];
                    @endphp
                    @foreach($lesson->resources as $resource)
                        @php
                            $deleteForms[] = ['id' => 'delete-resource-' . $resource->id, 'action' => route('admin.lessons.resources.destroy', $resource)];
                        @endphp
                    @endforeach
                @endforeach
            @empty
                <div class="bg-white rounded-xl border border-dashed border-gray-200 p-10 text-center text-sm text-gray-400">
                    No chapters yet. Add your first chapter above.
                </div>
            @endforelse
        </div>
    </div>

    {{-- Hidden delete forms --}}
    @foreach(($deleteForms ?? []) as $f)
        <form id="{{ $f['id'] }}" method="POST" action="{{ $f['action'] }}" class="hidden">@csrf @method('DELETE')</form>
    @endforeach
@endsection
