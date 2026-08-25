@extends('layouts.dashboard')
@section('title', 'Manage Users')
@section('content')
    <div class="space-y-6">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Manage Users</h1>
                <p class="text-gray-500 mt-1">View, create and manage all registered users.</p>
            </div>
            <a href="{{ route('admin.users.create') }}" class="inline-flex items-center gap-2 px-4 py-2.5 text-sm font-medium text-white bg-primary-600 hover:bg-primary-700 rounded-lg transition shadow-sm">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/></svg>
                Add User
            </a>
        </div>

        {{-- Filters --}}
        <form method="GET" action="{{ route('admin.users') }}" class="flex flex-col sm:flex-row gap-3 sm:items-center">
            <div class="relative sm:w-64 shrink-0">
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Search by name or email..." class="pl-10 pr-4 h-[42px] border border-gray-200 rounded-lg text-sm focus:ring-2 focus:ring-primary-500 focus:border-primary-500 w-full" />
                <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607z"/></svg>
            </div>
            <select name="role" onchange="this.form.submit()"
                class="px-3 h-[42px] border border-gray-200 rounded-lg text-sm text-gray-700 bg-white focus:ring-2 focus:ring-primary-500 focus:border-primary-500 w-full sm:w-44 transition">
                <option value="">All Roles</option>
                @foreach($roles as $r)
                    <option value="{{ $r->name }}" @selected(request('role') === $r)>{{ ucfirst($r->name) }}</option>
                @endforeach
            </select>
            <div class="flex items-center gap-2 sm:ml-auto">
                <button type="submit" class="px-5 h-[42px] text-sm font-medium text-primary-700 bg-primary-50 hover:bg-primary-100 rounded-lg transition">Filter</button>
                @if(request()->filled('search') || request()->filled('role'))
                    <a href="{{ route('admin.users') }}" class="inline-flex items-center gap-1.5 px-4 h-[42px] text-sm font-medium text-gray-500 hover:text-gray-700 transition">Clear</a>
                @endif
            </div>
        </form>

        {{-- Users Table --}}
        <div class="bg-white rounded-xl border border-gray-100 shadow-sm overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead>
                        <tr class="border-b border-gray-100 bg-gray-50">
                            <th class="text-left text-xs font-medium text-gray-500 uppercase tracking-wider px-6 py-3">User</th>
                            <th class="text-left text-xs font-medium text-gray-500 uppercase tracking-wider px-6 py-3">Role</th>
                            <th class="text-left text-xs font-medium text-gray-500 uppercase tracking-wider px-6 py-3">Joined</th>
                            <th class="text-left text-xs font-medium text-gray-500 uppercase tracking-wider px-6 py-3">Status</th>
                            <th class="text-right text-xs font-medium text-gray-500 uppercase tracking-wider px-6 py-3">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50">
                        @forelse($users as $user)
                            <tr class="hover:bg-gray-50 transition">
                                <td class="px-6 py-4">
                                    <div class="flex items-center gap-3">
                                        <div class="w-10 h-10 bg-primary-100 rounded-full flex items-center justify-center text-primary-700 text-sm font-bold">{{ strtoupper(substr($user->name, 0, 2)) }}</div>
                                        <div>
                                            <p class="text-sm font-medium text-gray-900">{{ $user->name }}</p>
                                            <p class="text-xs text-gray-500">{{ $user->email }}</p>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    @foreach($user->getRoleNames() as $role)
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                            {{ $role === 'admin' ? 'bg-purple-100 text-purple-800' : '' }}
                                            {{ $role === 'instructor' ? 'bg-accent-100 text-accent-800' : '' }}
                                            {{ $role === 'student' ? 'bg-primary-100 text-primary-800' : '' }}">
                                            {{ ucfirst($role) }}
                                        </span>
                                    @endforeach
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-500">{{ $user->created_at->format('M d, Y') }}</td>
                                <td class="px-6 py-4">
                                    <span class="inline-flex items-center gap-1.5 text-xs font-medium text-secondary-700">
                                        <span class="w-1.5 h-1.5 bg-secondary-500 rounded-full"></span> Active
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-right">
                                    <div class="flex items-center justify-end gap-2">
                                        <a href="{{ route('admin.users.edit', $user) }}" class="p-1.5 text-gray-400 hover:text-primary-600 hover:bg-primary-50 rounded-lg transition" title="Edit">
                                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0115.75 21H5.25A2.25 2.25 0 013 18.75V8.25A2.25 2.25 0 015.25 6H10"/></svg>
                                        </a>
                                        <button type="submit" form="delete-user-{{ $user->id }}"
                                            @if($user->id === auth()->id()) disabled title="You cannot delete your own account"
                                            @else data-confirm="This will permanently delete {{ $user->name }} and revoke access." data-confirm-title="Delete user?" @endif
                                            class="p-1.5 text-gray-400 {{ $user->id === auth()->id() ? 'opacity-30 cursor-not-allowed' : 'hover:text-red-600 hover:bg-red-50' }} rounded-lg transition" title="Delete">
                                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0"/></svg>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="5" class="px-6 py-8 text-center text-sm text-gray-400">No users found.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if($users->hasPages())
                <div class="px-6 py-4 border-t border-gray-100">
                    {{ $users->links() }}
                </div>
            @endif
        </div>
    </div>

    {{-- Hidden delete forms --}}
    @foreach($users as $user)
        <form id="delete-user-{{ $user->id }}" method="POST" action="{{ route('admin.users.destroy', $user) }}" class="hidden">
            @csrf
            @method('DELETE')
        </form>
    @endforeach
@endsection
