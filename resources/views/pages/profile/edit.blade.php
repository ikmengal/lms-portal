@extends('layouts.dashboard')
@section('title', 'My Profile')
@section('content')
    <div class="mx-auto space-y-8">
        {{-- Header --}}
        <div>
            <h1 class="text-2xl font-bold text-gray-900">My Profile</h1>
            <p class="text-gray-500 mt-1">Manage your account settings and preferences.</p>
        </div>

        {{-- Profile Picture & Basic Info --}}
        <div class="bg-white rounded-xl border border-gray-100 shadow-sm overflow-hidden">
            {{-- Banner --}}
            <div class="relative h-44 sm:h-56 bg-gradient-to-r from-primary-600 via-primary-700 to-secondary-600">
                @if(auth()->user()->banner_url)
                    <img src="{{ auth()->user()->banner_url }}?v={{ strtotime(auth()->user()->updated_at) }}" alt="Profile banner" class="absolute inset-0 w-full h-full object-cover">
                @else
                    <div class="absolute inset-0 flex flex-col items-center justify-center text-white/70 gap-2">
                        <svg class="w-10 h-10 opacity-60" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 15.75l5.159-5.159a2.25 2.25 0 013.182 0l5.159 5.159m-1.5-1.5l1.409-1.409a2.25 2.25 0 013.182 0l2.909 2.909M3.75 21h16.5A1.5 1.5 0 0021.75 19.5V4.5A1.5 1.5 0 0020.25 3H3.75A1.5 1.5 0 002.25 4.5v15A1.5 1.5 0 003.75 21z"/></svg>
                        <p class="text-sm font-medium">No banner image yet</p>
                    </div>
                @endif

                {{-- Always-visible controls (top-right) --}}
                <div class="absolute top-4 right-4 flex items-center gap-2">
                    <button type="button" onclick="document.getElementById('banner-input').click()"
                        class="inline-flex items-center gap-1.5 px-3.5 py-2 bg-black/45 hover:bg-black/65 backdrop-blur text-white text-xs font-semibold rounded-lg shadow-lg transition border border-white/20">
                        <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6.827 6.175A2.31 2.31 0 015.186 7.23c-.38.054-.757.112-1.134.175C2.999 7.58 2.25 8.507 2.25 9.574V18a2.25 2.25 0 002.25 2.25h15A2.25 2.25 0 0021.75 18V9.574c0-1.067-.75-1.994-1.802-2.169a47.865 47.865 0 00-1.134-.175 2.31 2.31 0 01-1.64-1.055l-.822-1.316a2.192 2.192 0 00-1.736-1.039 48.774 48.774 0 00-5.232 0 2.192 2.192 0 00-1.736 1.039l-.821 1.316z"/><path stroke-linecap="round" stroke-linejoin="round" d="M16.5 12.75a4.5 4.5 0 11-9 0 4.5 4.5 0 019 0z"/></svg>
                        {{ auth()->user()->banner_url ? 'Change Banner' : 'Upload Banner' }}
                    </button>
                    @if(auth()->user()->banner_url)
                        <form method="POST" action="{{ route('profile.banner.remove') }}" data-confirm="Remove your banner image?" data-confirm-title="Remove Banner" data-confirm-icon="warning" data-confirm-button="Remove">
                            @csrf
                            @method('DELETE')
                            <button type="submit" title="Remove banner"
                                class="inline-flex items-center justify-center w-8 h-8 bg-black/45 hover:bg-red-500/90 backdrop-blur text-white rounded-lg shadow-lg transition border border-white/20">
                                <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0"/></svg>
                            </button>
                        </form>
                    @endif
                </div>

                {{-- Identity overlay --}}
                <div class="absolute inset-x-0 bottom-0 bg-gradient-to-t from-black/50 to-transparent"></div>
                <p class="absolute bottom-3 right-4 text-[11px] font-medium text-white/75">{{ auth()->user()->banner_url ? 'Your banner' : 'Recommended size: 1500 × 500px' }}</p>
            </div>

            <form method="POST" action="{{ route('profile.banner.update') }}" enctype="multipart/form-data" class="hidden" id="banner-form">
                @csrf
                <input type="file" name="banner" accept=".jpg,.jpeg,.png,.webp" id="banner-input"
                    onchange="if(this.files.length) this.closest('form').submit()">
            </form>

            {{-- Avatar & Identity --}}
            <div class="px-6 sm:px-8 pb-6 relative">
                <div class="-mt-12 sm:-mt-14 flex flex-col sm:flex-row sm:items-end sm:justify-between gap-4">
                    <div class="flex items-end gap-5">
                        <div class="relative group/avatar shrink-0">
                            <x-avatar :user="auth()->user()" size="w-28 h-28 sm:w-32 sm:h-32 text-4xl" class="ring-4 ring-white shadow-lg" />
                            <button type="button" onclick="document.getElementById('avatar-input').click()"
                                title="{{ auth()->user()->avatar_url ? 'Change picture' : 'Upload picture' }}"
                                class="absolute inset-0 rounded-full bg-primary-900/60 opacity-0 group-hover/avatar:opacity-100 transition-opacity flex items-center justify-center cursor-pointer">
                                <svg class="w-7 h-7 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6.827 6.175A2.31 2.31 0 015.186 7.23c-.38.054-.757.112-1.134.175C2.999 7.58 2.25 8.507 2.25 9.574V18a2.25 2.25 0 002.25 2.25h15A2.25 2.25 0 0021.75 18V9.574c0-1.067-.75-1.994-1.802-2.169a47.865 47.865 0 00-1.134-.175 2.31 2.31 0 01-1.64-1.055l-.822-1.316a2.192 2.192 0 00-1.736-1.039 48.774 48.774 0 00-5.232 0 2.192 2.192 0 00-1.736 1.039l-.821 1.316z"/><path stroke-linecap="round" stroke-linejoin="round" d="M16.5 12.75a4.5 4.5 0 11-9 0 4.5 4.5 0 019 0z"/></svg>
                            </button>
                            @if(auth()->user()->avatar_url)
<form method="POST" action="{{ route('profile.avatar.remove') }}" class="absolute top-0 right-0"
                                     data-confirm="Remove your profile picture?" data-confirm-title="Remove Avatar" data-confirm-icon="warning" data-confirm-button="Remove">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" title="Remove picture"
                                        class="w-7 h-7 bg-red-500 hover:bg-red-600 text-white rounded-full shadow-md ring-2 ring-white flex items-center justify-center transition">
                                        <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                                    </button>
                                </form>
                            @endif
                        </div>
                        <div class="pb-1">
                            <h2 class="text-xl sm:text-2xl font-bold text-gray-900 leading-tight">{{ auth()->user()->name }}</h2>
                            <p class="text-sm text-gray-500 mt-0.5">{{ auth()->user()->email }}</p>
                        </div>
                    </div>
                    <span class="self-start sm:self-auto inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full text-xs font-semibold bg-primary-50 text-primary-700 border border-primary-100 capitalize mb-1">
                        <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                        {{ auth()->user()->getRoleNames()->first() ?? 'User' }}
                    </span>
                </div>
            </div>

            <form method="POST" action="{{ route('profile.avatar.update') }}" enctype="multipart/form-data" class="hidden" id="avatar-form">
                @csrf
                <input type="file" name="avatar" accept=".jpg,.jpeg,.png,.webp" id="avatar-input"
                    onchange="if(this.files.length) this.closest('form').submit()">
            </form>

            {{-- Profile Images --}}
            <div class="border-t border-gray-100 pt-6 mb-6 px-6 pb-6">
                <h3 class="text-sm font-semibold text-gray-900 mb-4">Profile Images</h3>

                <div class="grid sm:grid-cols-2 gap-6">
                    {{-- Avatar row --}}
                    <div class="rounded-xl border border-gray-200 p-4" x-data="{ fileName: null }">
                        <div class="flex items-center gap-4 mb-4">
                            <x-avatar :user="auth()->user()" size="w-16 h-16 text-lg" />
                            <div>
                                <p class="text-sm font-medium text-gray-900">Profile Picture</p>
                                <p class="text-xs text-gray-400">JPG, PNG or WEBP · max 2 MB · square recommended</p>
                            </div>
                        </div>
                        <form method="POST" action="{{ route('profile.avatar.update') }}" enctype="multipart/form-data" class="space-y-3">
                            @csrf
                            <input type="file" name="avatar" accept=".jpg,.jpeg,.png,.webp" required
                                class="block w-full text-sm text-gray-500 border border-gray-200 rounded-lg cursor-pointer
                                       focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-primary-500
                                       file:mr-3 file:py-2 file:px-4 file:rounded-l-lg file:border-0
                                       file:text-xs file:font-semibold file:bg-primary-50 file:text-primary-700
                                       hover:file:bg-primary-100 transition"
                                x-on:change="fileName = $event.target.files.length ? $event.target.files[0].name : null">
                            <p x-show="fileName" x-text="'Selected: ' + fileName" x-cloak class="text-xs text-gray-500 -mt-1"></p>
                            @error('avatar')
                                <p class="text-xs text-red-600">{{ $message }}</p>
                            @enderror
                            <button type="submit"
                                class="w-full py-2 bg-primary-600 hover:bg-primary-700 disabled:opacity-40 disabled:cursor-not-allowed text-white text-xs font-bold rounded-lg transition"
                                :disabled="!fileName">
                                {{ auth()->user()->avatar_url ? 'Update Profile Picture' : 'Upload Profile Picture' }}
                            </button>
                        </form>
                    </div>

                    {{-- Banner row --}}
                    <div class="rounded-xl border border-gray-200 p-4" x-data="{ fileName: null }">
                        <div class="flex items-center gap-4 mb-4">
                            <div class="w-28 h-14 rounded-lg overflow-hidden shrink-0 bg-gradient-to-r from-primary-600 via-primary-700 to-secondary-600 flex items-center justify-center">
                                @if(auth()->user()->banner_url)
                                    <img src="{{ auth()->user()->banner_url }}" alt="Banner preview" class="w-full h-full object-cover">
                                @else
                                    <svg class="w-5 h-5 text-white/80" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 15.75l5.159-5.159a2.25 2.25 0 013.182 0l5.159 5.159m-1.5-1.5l1.409-1.409a2.25 2.25 0 013.182 0l2.909 2.909M3.75 21h16.5A1.5 1.5 0 0021.75 19.5V4.5A1.5 1.5 0 0020.25 3H3.75A1.5 1.5 0 002.25 4.5v15A1.5 1.5 0 003.75 21z"/></svg>
                                @endif
                            </div>
                            <div>
                                <p class="text-sm font-medium text-gray-900">Profile Banner</p>
                                <p class="text-xs text-gray-400">JPG, PNG or WEBP · max 4 MB · wide image recommended</p>
                            </div>
                        </div>
                        <form method="POST" action="{{ route('profile.banner.update') }}" enctype="multipart/form-data" class="space-y-3">
                            @csrf
                            <input type="file" name="banner" accept=".jpg,.jpeg,.png,.webp" required
                                class="block w-full text-sm text-gray-500 border border-gray-200 rounded-lg cursor-pointer
                                       focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-primary-500
                                       file:mr-3 file:py-2 file:px-4 file:rounded-l-lg file:border-0
                                       file:text-xs file:font-semibold file:bg-primary-50 file:text-primary-700
                                       hover:file:bg-primary-100 transition"
                                x-on:change="fileName = $event.target.files.length ? $event.target.files[0].name : null">
                            <p x-show="fileName" x-text="'Selected: ' + fileName" x-cloak class="text-xs text-gray-500 -mt-1"></p>
                            @error('banner')
                                <p class="text-xs text-red-600">{{ $message }}</p>
                            @enderror
                            <div class="flex items-center gap-2">
                                <button type="submit"
                                    class="flex-1 py-2 bg-primary-600 hover:bg-primary-700 disabled:opacity-40 disabled:cursor-not-allowed text-white text-xs font-bold rounded-lg transition"
                                    :disabled="!fileName">
                                    {{ auth()->user()->banner_url ? 'Update Banner' : 'Upload Banner' }}
                                </button>
                            </div>
                        </form>
                        @if(auth()->user()->banner_url)
                            <form method="POST" action="{{ route('profile.banner.remove') }}" data-confirm="Remove your banner image?" data-confirm-title="Remove Banner" data-confirm-icon="warning" data-confirm-button="Remove" class="mt-2">
                                @csrf
                                @method('DELETE')
                                <button type="submit"
                                    class="w-full py-2 bg-red-50 hover:bg-red-100 text-red-600 text-xs font-bold rounded-lg border border-red-200 transition">
                                    Remove Banner
                                </button>
                            </form>
                        @endif
                    </div>
                </div>
            </div>

            {{-- Profile Form --}}
            <form method="POST" action="{{ route('profile.update') }}" class="px-6 pb-6">
                @csrf
                @method('PUT')

                <div class="grid sm:grid-cols-2 gap-6 mb-6">
                    <div>
                        <label for="name" class="block text-sm font-medium text-gray-700 mb-1">Full Name</label>
                        <input type="text" id="name" name="name" value="{{ old('name', auth()->user()->name) }}" class="w-full px-4 py-2.5 border border-gray-200 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500 text-sm transition @error('name') border-red-300 @enderror" />
                        @error('name')
                            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label for="email" class="block text-sm font-medium text-gray-700 mb-1">Email Address</label>
                        <input type="email" id="email" name="email" value="{{ old('email', auth()->user()->email) }}" class="w-full px-4 py-2.5 border border-gray-200 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500 text-sm transition @error('email') border-red-300 @enderror" />
                        @error('email')
                            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                    <div class="sm:col-span-2">
                        <label for="bio" class="block text-sm font-medium text-gray-700 mb-1">Bio</label>
                        <textarea id="bio" name="bio" rows="3" placeholder="Tell us a little about yourself..." class="w-full px-4 py-2.5 border border-gray-200 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500 text-sm transition resize-none @error('bio') border-red-300 @enderror">{{ old('bio', auth()->user()->bio) }}</textarea>
                        @error('bio')
                            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div class="border-t border-gray-100 pt-6 mb-6">
                    <h3 class="text-sm font-semibold text-gray-900 mb-4">Change Password</h3>
                    <p class="text-xs text-gray-500 mb-4">Leave blank to keep current password.</p>
                    <div class="grid sm:grid-cols-2 gap-6">
                        <div>
                            <label for="password" class="block text-sm font-medium text-gray-700 mb-1">New Password</label>
                            <input type="password" id="password" name="password" class="w-full px-4 py-2.5 border border-gray-200 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500 text-sm transition @error('password') border-red-300 @enderror" />
                            @error('password')
                                <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                        <div>
                            <label for="password_confirmation" class="block text-sm font-medium text-gray-700 mb-1">Confirm Password</label>
                            <input type="password" id="password_confirmation" name="password_confirmation" class="w-full px-4 py-2.5 border border-gray-200 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500 text-sm transition" />
                        </div>
                    </div>
                </div>

                <div class="flex items-center gap-3">
                    <button type="submit" class="px-6 py-2.5 bg-primary-600 hover:bg-primary-700 text-white font-medium rounded-lg text-sm transition">Save Changes</button>
                    <span class="text-xs text-gray-400">Changes saved at {{ now()->format('h:i A') }}</span>
                </div>
            </form>
        </div>

        {{-- Account Info --}}
        <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Account Information</h3>
            <div class="grid sm:grid-cols-2 gap-6">
                <div>
                    <label class="block text-xs font-medium text-gray-500 uppercase tracking-wider mb-1">Role</label>
                    <p class="text-sm font-medium text-gray-900 capitalize">{{ auth()->user()->getRoleNames()->first() ?? 'User' }}</p>
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-500 uppercase tracking-wider mb-1">Member Since</label>
                    <p class="text-sm font-medium text-gray-900">{{ auth()->user()->created_at->format('F d, Y') }}</p>
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-500 uppercase tracking-wider mb-1">Email Verified</label>
                    <p class="text-sm font-medium {{ auth()->user()->email_verified_at ? 'text-secondary-600' : 'text-red-500' }}">
                        {{ auth()->user()->email_verified_at ? 'Verified on ' . auth()->user()->email_verified_at->format('M d, Y') : 'Not verified' }}
                    </p>
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-500 uppercase tracking-wider mb-1">Last Login</label>
                    <p class="text-sm font-medium text-gray-900">{{ auth()->user()->updated_at->diffForHumans() }}</p>
                </div>
            </div>
        </div>

        {{-- Danger Zone --}}
        <div class="bg-white rounded-xl border border-red-200 shadow-sm p-6">
            <h3 class="text-lg font-semibold text-red-700 mb-2">Danger Zone</h3>
            <p class="text-sm text-gray-500 mb-4">Permanently delete your account and all associated data. This action cannot be undone.</p>
            <button class="px-5 py-2.5 bg-red-50 hover:bg-red-100 text-red-700 font-medium rounded-lg text-sm border border-red-200 transition">Delete Account</button>
        </div>
    </div>
@endsection
