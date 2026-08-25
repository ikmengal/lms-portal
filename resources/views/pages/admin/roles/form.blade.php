@extends('layouts.dashboard')
@section('title', $role->exists ? 'Edit Role' : 'New Role')
@section('content')
    <div class="mx-auto space-y-6">
        {{-- Header --}}
        <div class="flex items-center gap-3">
            <a href="{{ route('admin.roles.index') }}" class="p-2 text-gray-400 hover:text-gray-700 hover:bg-gray-100 rounded-lg transition">
                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18"/></svg>
            </a>
            <div>
                <h1 class="text-2xl font-bold text-gray-900">{{ $role->exists ? 'Edit Role' : 'Create New Role' }}</h1>
                <p class="text-gray-500 mt-0.5">Set the role name and choose which permissions it grants.</p>
            </div>
        </div>

        <form method="POST" action="{{ $role->exists ? route('admin.roles.update', $role) : route('admin.roles.store') }}" class="space-y-6">
            @csrf
            @if($role->exists)
                @method('PUT')
            @endif

            {{-- Role name --}}
            <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-6">
                <label for="name" class="block text-sm font-medium text-gray-700 mb-1">Role Name <span class="text-red-500">*</span></label>
                <input type="text" id="name" name="name" value="{{ old('name', $role->name) }}" required maxlength="100" placeholder="e.g. Content Moderator"
                    @if($role->exists && in_array($role->name, \App\Http\Controllers\Admin\RoleController::SYSTEM_ROLES))
                        disabled
                        title="System role names cannot be changed"
                    @endif
                    class="w-full px-4 py-2.5 border border-gray-200 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500 text-sm transition @error('name') border-red-300 @enderror {{ $role->exists && in_array($role->name, \App\Http\Controllers\Admin\RoleController::SYSTEM_ROLES) ? 'bg-gray-50 text-gray-400 cursor-not-allowed' : '' }}" />
                @if($role->exists && in_array($role->name, \App\Http\Controllers\Admin\RoleController::SYSTEM_ROLES))
                    <input type="hidden" name="name" value="{{ $role->name }}">
                    <p class="mt-1.5 text-xs text-amber-600 flex items-center gap-1">
                        <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z"/></svg>
                        System role — the name is locked because the application logic depends on it.
                    </p>
                @endif
                @error('name')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
            </div>

            {{-- Permissions --}}
            <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-6">
                <div class="flex items-center justify-between mb-4">
                    <h2 class="text-lg font-semibold text-gray-900">Permissions</h2>
                    <span class="text-xs text-gray-400">{{ count(old('permissions', $assignedIds)) }} selected</span>
                </div>

                <div class="grid sm:grid-cols-2 gap-4">
                    @foreach($groups as $group => $perms)
                        <div class="border border-gray-100 rounded-xl overflow-hidden perm-group">
                            <div class="px-4 py-2.5 bg-gray-50/70 border-b border-gray-100 flex items-center justify-between gap-2">
                                <p class="text-sm font-semibold text-gray-700">{{ ucfirst($group) }}</p>
                                <button type="button"
                                    onclick="const box = this.closest('.perm-group'); const boxes = box.querySelectorAll('.perm-check'); const target = !boxes[0].checked; boxes.forEach(b => b.checked = target); this.textContent = target ? 'Deselect all' : 'Select all';"
                                    class="text-xs font-medium text-primary-600 hover:text-primary-700">Select all</button>
                            </div>
                            <div class="p-4 space-y-2.5">
                                @foreach($perms as $perm)
                                    <label class="flex items-center gap-2.5 cursor-pointer group/perm">
                                        <input type="checkbox" name="permissions[]" value="{{ $perm->id }}"
                                            class="perm-check w-4 h-4 rounded border-gray-300 text-primary-600 focus:ring-primary-500 transition"
                                            {{ in_array($perm->id, (array) old('permissions', $assignedIds)) ? 'checked' : '' }}>
                                        <span class="text-sm text-gray-600 group-hover/perm:text-gray-900 transition">{{ $perm->name }}</span>
                                    </label>
                                @endforeach
                            </div>
                        </div>
                    @endforeach
                </div>

                @if(collect($groups)->isEmpty())
                    <div class="border border-dashed border-gray-200 rounded-xl p-8 text-center">
                        <p class="text-sm text-gray-400 mb-2">No permissions exist yet.</p>
                        <a href="{{ route('admin.permissions.index') }}" class="text-sm font-medium text-primary-600 hover:text-primary-700">Create permissions first →</a>
                    </div>
                @endif
            </div>

            {{-- Actions --}}
            <div class="flex items-center justify-end gap-3">
                <a href="{{ route('admin.roles.index') }}" class="px-5 py-2.5 text-sm font-medium text-gray-600 hover:text-gray-900 transition">Cancel</a>
                <button type="submit" class="px-6 py-2.5 bg-primary-600 hover:bg-primary-700 text-white font-medium rounded-lg text-sm transition shadow-sm">
                    {{ $role->exists ? 'Save Changes' : 'Create Role' }}
                </button>
            </div>
        </form>
    </div>
@endsection
