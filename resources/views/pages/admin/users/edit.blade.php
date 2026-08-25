@extends('layouts.dashboard')
@section('title', 'Edit User')
@section('content')
    <div class="max mx-auto space-y-6">
        <div class="flex items-center gap-3">
            <a href="{{ route('admin.users') }}" class="p-2 text-gray-400 hover:text-gray-700 hover:bg-gray-100 rounded-lg transition">
                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18"/></svg>
            </a>
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Edit User</h1>
                <p class="text-gray-500 mt-0.5">Update account details, password and role.</p>
            </div>
        </div>

        <form method="POST" action="{{ route('admin.users.update', $user) }}" class="bg-white rounded-xl border border-gray-100 shadow-sm p-6 space-y-6">
            @csrf
            @method('PUT')

            <div class="flex items-center gap-4 pb-6 border-b border-gray-100">
                <div class="w-14 h-14 bg-primary-600 rounded-full flex items-center justify-center text-white text-lg font-bold">{{ $user->initials }}</div>
                <div>
                    <p class="text-base font-semibold text-gray-900">{{ $user->name }}</p>
                    <span class="inline-flex items-center mt-1 px-2.5 py-0.5 rounded-full text-xs font-medium bg-primary-100 text-primary-800 capitalize">{{ $userRole ?? 'User' }}</span>
                    <span class="ml-2 text-xs text-gray-400">Member since {{ $user->created_at->format('M d, Y') }}</span>
                </div>
            </div>

            <div class="grid sm:grid-cols-2 gap-6">
                <div class="sm:col-span-2">
                    <label for="name" class="block text-sm font-medium text-gray-700 mb-1">Full Name <span class="text-red-500">*</span></label>
                    <input type="text" id="name" name="name" value="{{ old('name', $user->name) }}" required
                        class="w-full px-4 py-2.5 border border-gray-200 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500 text-sm transition @error('name') border-red-300 @enderror" />
                    @error('name')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
                </div>
                <div class="sm:col-span-2">
                    <label for="email" class="block text-sm font-medium text-gray-700 mb-1">Email Address <span class="text-red-500">*</span></label>
                    <input type="email" id="email" name="email" value="{{ old('email', $user->email) }}" required
                        class="w-full px-4 py-2.5 border border-gray-200 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500 text-sm transition @error('email') border-red-300 @enderror" />
                    @error('email')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label for="password" class="block text-sm font-medium text-gray-700 mb-1">New Password</label>
                    <input type="password" id="password" name="password"
                        class="w-full px-4 py-2.5 border border-gray-200 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500 text-sm transition @error('password') border-red-300 @enderror" />
                    <p class="mt-1 text-xs text-gray-400">Leave blank to keep current password.</p>
                    @error('password')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label for="role" class="block text-sm font-medium text-gray-700 mb-1">Role <span class="text-red-500">*</span></label>
                    <select id="role" name="role" required
                        class="w-full px-4 py-2.5 border border-gray-200 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500 text-sm transition bg-white @error('role') border-red-300 @enderror">
                        @foreach($roles as $role)
                            <option value="{{ $role }}" @selected(old('role', $userRole) === $role)>{{ ucfirst($role) }}</option>
                        @endforeach
                    </select>
                    @error('role')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
                </div>
            </div>

            <div class="flex items-center justify-end gap-3 border-t border-gray-100 pt-6">
                <a href="{{ route('admin.users') }}" class="px-5 py-2.5 text-sm font-medium text-gray-600 hover:text-gray-900 transition">Cancel</a>
                <button type="submit" class="px-6 py-2.5 bg-primary-600 hover:bg-primary-700 text-white font-medium rounded-lg text-sm transition shadow-sm">Save Changes</button>
            </div>
        </form>

        {{-- Danger Zone --}}
        @unless($user->id === auth()->id())
            <div class="bg-white rounded-xl border border-red-200 shadow-sm p-6">
                <h3 class="text-lg font-semibold text-red-700 mb-1">Danger Zone</h3>
                <p class="text-sm text-gray-500 mb-4">Permanently delete this user and revoke access. This cannot be undone.</p>
                <form method="POST" action="{{ route('admin.users.destroy', $user) }}" data-confirm="This will permanently delete {{ $user->name }} and revoke access." data-confirm-title="Delete user?" data-confirm-button="Yes, delete user">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="px-5 py-2.5 bg-red-50 hover:bg-red-100 text-red-700 font-medium rounded-lg text-sm border border-red-200 transition">Delete User</button>
                </form>
            </div>
        @endunless
    </div>
@endsection
