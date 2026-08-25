@extends('layouts.dashboard')
@section('title', 'Roles & Permissions')
@section('content')
    <div class="mx-auto space-y-6">
        {{-- Header --}}
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Roles</h1>
                <p class="text-gray-500 mt-1">Manage roles and the permissions assigned to them.</p>
            </div>
            <a href="{{ route('admin.roles.create') }}" class="inline-flex items-center gap-2 px-4 py-2.5 text-sm font-medium text-white bg-primary-600 hover:bg-primary-700 rounded-lg transition shadow-sm">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/></svg>
                Add Role
            </a>
        </div>

        {{-- Quick link --}}
        <a href="{{ route('admin.permissions.index') }}" class="flex items-center justify-between bg-white border border-gray-200 rounded-xl px-5 py-4 hover:border-primary-300 hover:bg-primary-50/40 transition group">
            <div class="flex items-center gap-3">
                <span class="w-9 h-9 bg-accent-100 text-accent-700 rounded-lg flex items-center justify-center">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M13.5 10.5V6.75a4.5 4.5 0 119 0v3.75M3.75 21.75h10.5a2.25 2.25 0 002.25-2.25v-6.75a2.25 2.25 0 00-2.25-2.25H3.75a2.25 2.25 0 00-2.25 2.25v6.75a2.25 2.25 0 002.25 2.25z"/></svg>
                </span>
                <div>
                    <p class="text-sm font-semibold text-gray-900">Permissions</p>
                    <p class="text-xs text-gray-400">Create, rename or remove individual permissions</p>
                </div>
            </div>
            <svg class="w-4 h-4 text-gray-400 group-hover:text-primary-600 transition" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M8.25 4.5l7.5 7.5-7.5 7.5"/></svg>
        </a>

        {{-- Roles grid --}}
        <div class="grid md:grid-cols-2 gap-4">
            @forelse($roles as $role)
                <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-5">
                    <div class="flex items-start justify-between gap-3">
                        <div class="flex items-start gap-3 min-w-0">
                            <span class="w-10 h-10 rounded-lg flex items-center justify-center shrink-0 {{ $role->name === 'admin' ? 'bg-red-100 text-red-600' : ($role->name === 'instructor' ? 'bg-accent-100 text-accent-700' : ($role->name === 'student' ? 'bg-secondary-100 text-secondary-700' : 'bg-primary-100 text-primary-700')) }}">
                                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.501 20.118a7.5 7.5 0 0114.998 0A17.933 17.933 0 0112 21.75c-2.676 0-5.216-.584-7.499-1.632z"/></svg>
                            </span>
                            <div class="min-w-0">
                                <p class="text-sm font-bold text-gray-900 capitalize truncate">{{ $role->name }}
                                    @if(in_array($role->name, \App\Http\Controllers\Admin\RoleController::SYSTEM_ROLES))
                                        <span class="ml-1 px-2 py-0.5 rounded-full text-[10px] font-medium bg-gray-100 text-gray-500 align-middle">System</span>
                                    @endif
                                </p>
                                <p class="text-xs text-gray-400 mt-0.5">{{ $role->users_count }} user{{ $role->users_count === 1 ? '' : 's' }} · {{ $role->permissions_count }} permission{{ $role->permissions_count === 1 ? '' : 's' }}</p>
                            </div>
                        </div>
                        <div class="flex items-center gap-1 shrink-0">
                            <a href="{{ route('admin.roles.edit', $role) }}" class="p-1.5 text-gray-400 hover:text-primary-600 hover:bg-primary-50 rounded-lg transition" title="Edit role & permissions">
                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0115.75 21H5.25A2.25 2.25 0 013 18.75V8.25A2.25 2.25 0 015.25 6H10"/></svg>
                            </a>
                            @if(!in_array($role->name, \App\Http\Controllers\Admin\RoleController::SYSTEM_ROLES))
                                <button type="submit" form="delete-role-{{ $role->id }}"
                                    data-confirm="Delete the role '{{ $role->name }}'? Its permissions will be detached."
                                    data-confirm-title="Delete role?"
                                    class="p-1.5 text-gray-400 hover:text-red-600 hover:bg-red-50 rounded-lg transition" title="Delete role">
                                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0"/></svg>
                                </button>
                            @endif
                        </div>
                    </div>

                    {{-- Permission chips preview --}}
                    <div class="mt-4 flex flex-wrap gap-1.5">
                        @foreach($role->permissions->take(4) as $perm)
                            <span class="px-2 py-0.5 bg-gray-100 text-gray-600 text-xs rounded-md">{{ $perm->name }}</span>
                        @endforeach
                        @if($role->permissions_count > 4)
                            <span class="px-2 py-0.5 bg-primary-50 text-primary-700 text-xs font-medium rounded-md">+{{ $role->permissions_count - 4 }} more</span>
                        @endif
                        @if($role->permissions_count === 0)
                            <span class="text-xs text-gray-400 italic">No permissions assigned</span>
                        @endif
                    </div>
                </div>
            @empty
                <div class="md:col-span-2 bg-white rounded-xl border border-dashed border-gray-200 p-10 text-center text-sm text-gray-400">No roles yet.</div>
            @endforelse
        </div>
    </div>

    @foreach($roles as $role)
        @unless(in_array($role->name, \App\Http\Controllers\Admin\RoleController::SYSTEM_ROLES))
            <form id="delete-role-{{ $role->id }}" method="POST" action="{{ route('admin.roles.destroy', $role) }}" class="hidden">@csrf @method('DELETE')</form>
        @endunless
    @endforeach
@endsection
