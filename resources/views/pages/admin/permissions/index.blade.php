@extends('layouts.dashboard')
@section('title', 'Permissions')
@section('content')
    <div class="mx-auto space-y-6">
        {{-- Header --}}
        <div class="flex items-center gap-3">
            <a href="{{ route('admin.roles.index') }}" class="p-2 text-gray-400 hover:text-gray-700 hover:bg-gray-100 rounded-lg transition">
                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18"/></svg>
            </a>
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Permissions</h1>
                <p class="text-gray-500 mt-1">Permissions are grouped by their last word (module). Assign them to roles from the Roles page.</p>
            </div>
        </div>

        {{-- Roles overview chips --}}
        <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-4 flex flex-wrap items-center gap-x-6 gap-y-2">
            <span class="text-xs font-medium text-gray-400 uppercase tracking-wider">Assigned to:</span>
            @foreach($rolesWithCounts as $role)
                <span class="inline-flex items-center gap-1.5 text-sm">
                    <span class="w-1.5 h-1.5 rounded-full {{ $role->permissions_count > 0 ? 'bg-secondary-500' : 'bg-gray-300' }}"></span>
                    <span class="font-medium text-gray-700 capitalize">{{ $role->name }}</span>
                    <span class="text-xs text-gray-400">({{ $role->permissions_count }})</span>
                </span>
            @endforeach
        </div>

        {{-- Add form --}}
        <form method="POST" action="{{ route('admin.permissions.store') }}" class="bg-white rounded-xl border border-gray-100 shadow-sm p-5 flex flex-col sm:flex-row gap-3 sm:items-end">
            @csrf
            <div class="flex-1">
                <label class="block text-sm font-medium text-gray-700 mb-1">New Permission <span class="text-red-500">*</span></label>
                <input type="text" name="name" maxlength="150" pattern="[A-Za-z0-9 ]+" placeholder='e.g. "manage certificates"'
                    class="w-full px-4 py-2.5 border border-gray-200 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500 text-sm transition @error('name') border-red-300 @enderror" />
                @error('name')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
            </div>
            <button type="submit" class="px-5 py-2.5 bg-primary-600 hover:bg-primary-700 text-white font-medium rounded-lg text-sm transition shadow-sm whitespace-nowrap">+ Add Permission</button>
        </form>

        {{-- Grouped list --}}
        <div class="grid md:grid-cols-2 gap-4">
            @forelse($groups as $group => $perms)
                <div class="bg-white rounded-xl border border-gray-100 shadow-sm overflow-hidden">
                    <div class="px-5 py-3 bg-gray-50/70 border-b border-gray-100 flex items-center justify-between">
                        <p class="text-sm font-semibold text-gray-700">{{ ucfirst($group) }}</p>
                        <span class="px-2 py-0.5 bg-primary-50 text-primary-700 text-xs font-medium rounded-full">{{ $perms->count() }}</span>
                    </div>
                    <ul class="divide-y divide-gray-50">
                        @foreach($perms as $perm)
                            <li class="px-5 py-3 flex items-center justify-between gap-3 hover:bg-gray-50 transition" x-data="{ editing: false }">
                                <p class="text-sm text-gray-800" x-show="!editing">{{ $perm->name }}</p>
                                <form method="POST" action="{{ route('admin.permissions.update', $perm->id) }}" x-show="editing" x-cloak style="display:none;" class="flex gap-2 w-full sm:w-auto">
                                    @csrf
                                    @method('PUT')
                                    <input type="text" name="name" value="{{ $perm->name }}" required maxlength="150" pattern="[A-Za-z0-9 ]+"
                                        class="flex-1 min-w-0 px-3 py-1.5 border border-gray-200 rounded-lg text-sm focus:ring-2 focus:ring-primary-500 focus:border-primary-500" />
                                    <button type="submit" class="px-3 py-1.5 bg-primary-600 text-white text-xs font-medium rounded-lg hover:bg-primary-700 transition whitespace-nowrap">Save</button>
                                    <button type="button" @click="editing = false" class="px-2 text-gray-500 text-xs font-medium hover:text-gray-700">Cancel</button>
                                </form>
                                <div class="flex items-center gap-1 shrink-0 ml-auto" x-show="!editing">
                                    <button type="button" @click="editing = !editing" class="p-1.5 text-gray-400 hover:text-primary-600 hover:bg-primary-50 rounded-lg transition" title="Rename">
                                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0115.75 21H5.25A2.25 2.25 0 013 18.75V8.25A2.25 2.25 0 015.25 6H10"/></svg>
                                    </button>
                                    <button type="submit" form="delete-perm-{{ $perm->id }}"
                                        data-confirm="'{{ $perm->name }}' will be removed from ALL roles that use it."
                                        data-confirm-title="Delete permission?"
                                        class="p-1.5 text-gray-400 hover:text-red-600 hover:bg-red-50 rounded-lg transition" title="Delete">
                                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0"/></svg>
                                    </button>
                                </div>
                            </li>
                        @endforeach
                    </ul>
                </div>
            @empty
                <div class="md:col-span-2 bg-white rounded-xl border border-dashed border-gray-200 p-10 text-center text-sm text-gray-400">No permissions yet.</div>
            @endforelse
        </div>
    </div>

    @foreach(collect($groups)->flatten() as $perm)
        <form id="delete-perm-{{ $perm->id }}" method="POST" action="{{ route('admin.permissions.destroy', $perm->id) }}" class="hidden">@csrf @method('DELETE')</form>
    @endforeach
@endsection
