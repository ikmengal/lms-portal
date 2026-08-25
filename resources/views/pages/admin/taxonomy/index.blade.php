@extends('layouts.dashboard')
@section('title', $label)
@section('content')
    <div class="mx-auto space-y-6">
        {{-- Header --}}
        <div>
            <h1 class="text-2xl font-bold text-gray-900">{{ $label }}</h1>
            <p class="text-gray-500 mt-1">Manage course {{ strtolower($singular) }} used across the platform. Changes apply everywhere instantly.</p>
        </div>

        {{-- Quick nav --}}
        <div class="flex gap-2">
            <a href="{{ route('admin.categories.index') }}" class="px-4 py-2 text-sm font-medium rounded-lg transition {{ $baseRoute === 'admin.categories' ? 'bg-primary-600 text-white shadow-sm' : 'bg-white border border-gray-200 text-gray-600 hover:bg-gray-50' }}">Categories</a>
            <a href="{{ route('admin.levels.index') }}" class="px-4 py-2 text-sm font-medium rounded-lg transition {{ $baseRoute === 'admin.levels' ? 'bg-primary-600 text-white shadow-sm' : 'bg-white border border-gray-200 text-gray-600 hover:bg-gray-50' }}">Levels</a>
        </div>

        {{-- Add form --}}
        <form method="POST" action="{{ route($baseRoute . '.store') }}" class="bg-white rounded-xl border border-gray-100 shadow-sm p-5 flex flex-col sm:flex-row gap-3 sm:items-end">
            @csrf
            <div class="flex-1">
                <label class="block text-sm font-medium text-gray-700 mb-1">New {{ strtolower($singular) }} <span class="text-xs text-gray-400 font-normal">(order: {{ $items->count() + 1 }})</span></label>
                <input type="text" name="name" maxlength="100" placeholder="e.g. {{ $baseRoute === 'admin.categories' ? 'Music Production' : 'Expert' }}"
                    class="w-full px-4 py-2.5 border border-gray-200 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500 text-sm transition @error('name') border-red-300 @enderror" />
                @error('name')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
            </div>
            <button type="submit" class="px-5 py-2.5 bg-primary-600 hover:bg-primary-700 text-white font-medium rounded-lg text-sm transition shadow-sm whitespace-nowrap">+ Add</button>
        </form>

        {{-- List --}}
        <div class="bg-white rounded-xl border border-gray-100 shadow-sm overflow-hidden">
            <table class="w-full">
                <thead>
                    <tr class="border-b border-gray-100 bg-gray-50">
                        <th class="text-left text-xs font-medium text-gray-500 uppercase tracking-wider px-6 py-3">#</th>
                        <th class="text-left text-xs font-medium text-gray-500 uppercase tracking-wider px-6 py-3">Name</th>
                        <th class="text-left text-xs font-medium text-gray-500 uppercase tracking-wider px-6 py-3">Courses</th>
                        <th class="text-left text-xs font-medium text-gray-500 uppercase tracking-wider px-6 py-3">Status</th>
                        <th class="text-right text-xs font-medium text-gray-500 uppercase tracking-wider px-6 py-3">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @forelse($items as $i => $item)
                        <tr class="hover:bg-gray-50 transition" x-data="{ editing: false }">
                            <td class="px-6 py-3.5">
                                <div class="flex items-center gap-1">
                                    <form method="POST" action="{{ route($baseRoute . '.move', $item->id) }}">
                                        @csrf
                                        <input type="hidden" name="direction" value="up">
                                        <button type="submit" class="p-0.5 text-gray-300 hover:text-gray-700 transition {{ $i === 0 ? 'invisible' : '' }}" title="Move up">
                                            <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M4.5 15.75l7.5-7.5 7.5 7.5"/></svg>
                                        </button>
                                    </form>
                                    <span class="text-sm text-gray-400 font-medium tabular-nums">{{ $i + 1 }}</span>
                                    <form method="POST" action="{{ route($baseRoute . '.move', $item->id) }}">
                                        @csrf
                                        <input type="hidden" name="direction" value="down">
                                        <button type="submit" class="p-0.5 text-gray-300 hover:text-gray-700 transition {{ $i === $items->count() - 1 ? 'invisible' : '' }}" title="Move down">
                                            <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 8.25l-7.5 7.5-7.5-7.5"/></svg>
                                        </button>
                                    </form>
                                </div>
                            </td>
                            <td class="px-6 py-3.5">
                                <p class="text-sm font-medium text-gray-900" x-show="!editing">{{ $item->name }}</p>
                                <form method="POST" action="{{ route($baseRoute . '.update', $item->id) }}" x-show="editing" x-cloak style="display:none;" class="flex gap-2">
                                    @csrf
                                    @method('PUT')
                                    <input type="text" name="name" value="{{ $item->name }}" required maxlength="100"
                                        class="flex-1 px-3 py-1.5 border border-gray-200 rounded-lg text-sm focus:ring-2 focus:ring-primary-500 focus:border-primary-500" />
                                    <button type="submit" class="px-3 py-1.5 bg-primary-600 text-white text-xs font-medium rounded-lg hover:bg-primary-700 transition">Save</button>
                                    <button type="button" @click="editing = false" class="px-3 py-1.5 text-gray-500 text-xs font-medium hover:text-gray-700 transition">Cancel</button>
                                </form>
                            </td>
                            <td class="px-6 py-3.5"><span class="text-sm text-gray-500 tabular-nums">{{ $usageCounts[$item->id] ?? 0 }}</span></td>
                            <td class="px-6 py-3.5">
                                <form method="POST" action="{{ route($baseRoute . '.toggle', $item->id) }}">
                                    @csrf
                                    <button type="submit" class="inline-flex px-2.5 py-0.5 rounded-full text-xs font-medium transition {{ $item->is_active ? 'bg-secondary-100 text-secondary-700 hover:bg-secondary-200' : 'bg-gray-100 text-gray-500 hover:bg-gray-200' }}">
                                        {{ $item->is_active ? 'Active' : 'Hidden' }}
                                    </button>
                                </form>
                            </td>
                            <td class="px-6 py-3.5">
                                <div class="flex items-center justify-end gap-1.5">
                                    <button type="button" @click="editing = !editing" class="p-1.5 text-gray-400 hover:text-primary-600 hover:bg-primary-50 rounded-lg transition" title="Rename">
                                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0115.75 21H5.25A2.25 2.25 0 013 18.75V8.25A2.25 2.25 0 015.25 6H10"/></svg>
                                    </button>
                                    <button type="submit" form="delete-item-{{ $item->id }}"
                                        data-confirm="{{ ($usageCounts[$item->id] ?? 0) > 0
                                            ? '\'' . $item->name . '\' is used by ' . ($usageCounts[$item->id] ?? 0) . ' course(s). It will be moved to trash and hidden from new courses, existing ones keep working.'
                                            : 'Delete \'' . $item->name . '\'?' }}"
                                        data-confirm-title="Delete {{ strtolower($singular) }}?"
                                        class="p-1.5 text-gray-400 hover:text-red-600 hover:bg-red-50 rounded-lg transition" title="Delete">
                                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0"/></svg>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="5" class="px-6 py-10 text-center text-sm text-gray-400">No {{ strtolower($label) }} yet.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @foreach($items as $item)
            <form id="delete-item-{{ $item->id }}" method="POST" action="{{ route($baseRoute . '.destroy', $item->id) }}" class="hidden">@csrf @method('DELETE')</form>
        @endforeach

        {{-- Trash --}}
        @if($trashed->isNotEmpty())
            <div class="bg-white rounded-xl border border-dashed border-gray-300 overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-100">
                    <h3 class="text-sm font-semibold text-gray-900">Trash <span class="text-gray-400 font-normal">({{ $trashed->count() }})</span></h3>
                </div>
                <ul class="divide-y divide-gray-50">
                    @foreach($trashed as $item)
                        <li class="px-6 py-3 flex items-center justify-between gap-3">
                            <span class="text-sm text-gray-500 line-through">{{ $item->name }}</span>
                            <div class="flex items-center gap-2">
                                <button type="submit" form="restore-item-{{ $item->id }}" class="px-3 py-1.5 text-xs font-medium text-secondary-700 bg-secondary-50 hover:bg-secondary-100 rounded-lg transition">Restore</button>
                                <button type="submit" form="force-item-{{ $item->id }}"
                                    data-confirm="'{{ $item->name }}' will be gone forever."
                                    data-confirm-title="Permanently delete?"
                                    data-confirm-button="Yes, delete forever"
                                    class="px-3 py-1.5 text-xs font-medium text-red-600 bg-red-50 hover:bg-red-100 rounded-lg transition">Delete Forever</button>
                            </div>
                        </li>
                    @endforeach
                </ul>
            </div>
            @foreach($trashed as $item)
                <form id="restore-item-{{ $item->id }}" method="POST" action="{{ route($baseRoute . '.restore', $item->id) }}" class="hidden">@csrf</form>
                <form id="force-item-{{ $item->id }}" method="POST" action="{{ route($baseRoute . '.force-delete', $item->id) }}" class="hidden">@csrf @method('DELETE')</form>
            @endforeach
        @endif

        <p class="text-xs text-gray-400 text-center">Hidden {{ strtolower($label) }} stay assigned to existing courses but disappear from create/edit forms and filters.</p>
    </div>
@endsection
