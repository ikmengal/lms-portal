@extends('layouts.dashboard')
@section('title', 'Website Settings')
@php
    $val = fn (string $key, string $default = '') => old($key, $site[$key] ?? $default);
    $img = fn (string $key) => old($key) ? null : (\App\Models\Setting::get($key) ? asset('storage/' . \App\Models\Setting::get($key)) : null);
@endphp
@section('content')
    <div class="max-w-5xl mx-auto space-y-6" x-data="{ tab: 'general' }">

        {{-- Header --}}
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Website Settings</h1>
                <p class="text-gray-500 mt-1">Manage your site name, branding, contact info and more.</p>
            </div>
            <a href="{{ route('home') }}" target="_blank" class="inline-flex items-center gap-2 px-4 py-2 text-sm font-medium text-gray-600 bg-white border border-gray-200 rounded-lg hover:bg-gray-50 transition">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M13.5 6H5.25A2.25 2.25 0 003 8.25v10.5A2.25 2.25 0 005.25 21h10.5A2.25 2.25 0 0018 18.75V10.5m-10.5 6L21 3m0 0h-5.25M21 3v5.25"/></svg>
                Preview Site
            </a>
        </div>

        {{-- Tabs --}}
        <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-1.5 flex flex-wrap gap-1">
            @php
                $tabs = [
                    'general'   => ['General', ['site_name', 'site_tagline'], 'M12 6.042A8.967 8.967 0 006 3.75c-1.052 0-2.062.18-3 .512v14.25A8.987 8.987 0 016 18c2.305 0 4.408.867 6 2.292m0-14.25a8.966 8.966 0 016-2.292c1.052 0 2.062.18 3 .512v14.25A8.987 8.987 0 0018 18a8.967 8.967 0 00-6 2.292m0-14.25v14.25'],
                    'branding'  => ['Branding', ['logo', 'dark_logo', 'favicon', 'banner'], 'M2.25 15.75l5.159-5.159a2.25 2.25 0 013.182 0l5.159 5.159m-1.5-1.5l1.409-1.409a2.25 2.25 0 013.182 0l2.909 2.909M3.75 21h16.5A1.5 1.5 0 0021.75 19.5V4.5A1.5 1.5 0 0020.25 3H3.75A1.5 1.5 0 002.25 4.5v15A1.5 1.5 0 003.75 21z'],
                    'about'     => ['About & Description', ['about_title', 'about_description', 'footer_description'], 'M7.5 8.25h9m-9 3H12m-9.75 1.51c0 1.6 1.123 2.994 2.707 3.227 1.087.16 2.185.283 3.293.369V21l4.076-4.076a1.526 1.526 0 011.037-.443 48.282 48.282 0 005.68-.494c1.584-.233 2.707-1.626 2.707-3.228V6.741c0-1.602-1.123-2.995-2.707-3.228A48.394 48.394 0 0012 3c-2.392 0-4.744.175-7.043.513C3.373 3.746 2.25 5.14 2.25 6.741v6.018'],
                    'contact'   => ['Contact Info', ['contact_email', 'support_email', 'contact_phone', 'contact_address', 'office_hours'], 'M21.75 6.75v10.5a2.25 2.25 0 01-2.25 2.25h-15a2.25 2.25 0 01-2.25-2.25V6.75m19.5 0A2.25 2.25 0 0019.5 4.5h-15a2.25 2.25 0 00-2.25 2.25m19.5 0v.243a2.25 2.25 0 01-1.07 1.916l-7.5 4.615a2.25 2.25 0 01-2.36 0L3.32 8.91a2.25 2.25 0 01-1.07-1.916V6.75'],
                    'social'    => ['Social Links', ['facebook_url', 'twitter_url', 'instagram_url', 'linkedin_url', 'youtube_url'], 'M13.19 8.688a4.5 4.5 0 011.242 7.244l-4.5 4.5a4.5 4.5 0 01-6.364-6.364l1.757-1.757m13.35-.622l1.757-1.757a4.5 4.5 0 00-6.364-6.364l-4.5 4.5a4.5 4.5 0 001.242 7.244'],
                    'seo'       => ['SEO & Meta', ['meta_title', 'meta_description', 'meta_keywords'], 'M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607z'],
                    'advanced'  => ['Advanced', ['copyright_text', 'maintenance_mode', 'maintenance_message'], 'M9.594 3.94c.09-.542.56-.94 1.11-.94h2.593c.55 0 1.02.398 1.11.94l.213 1.281c.063.374.313.686.645.87.074.04.147.083.22.127.324.196.72.257 1.075.124l1.217-.456a1.125 1.125 0 011.37.49l1.296 2.247a1.125 1.125 0 01-.26 1.431l-1.003.827c-.293.24-.438.613-.431.992a6.759 6.759 0 010 .255c-.007.378.138.75.43.99l1.005.828c.424.35.534.954.26 1.43l-1.298 2.247a1.125 1.125 0 01-1.369.491l-1.217-.456c-.355-.133-.75-.072-1.076.124a6.57 6.57 0 01-.22.128c-.331.183-.581.495-.644.869l-.213 1.28c-.09.543-.56.941-1.11.941h-2.594c-.55 0-1.02-.398-1.11-.94l-.213-1.281c-.062-.374-.312-.686-.644-.87a6.52 6.52 0 01-.22-.127c-.325-.196-.72-.257-1.076-.124l-1.217.456a1.125 1.125 0 01-1.369-.49l-1.297-2.247a1.125 1.125 0 01.26-1.431l1.004-.827c.292-.24.437-.613.43-.992a6.932 6.932 0 010-.255c.007-.378-.138-.75-.43-.99l-1.004-.828a1.125 1.125 0 01-.26-1.43l1.297-2.247a1.125 1.125 0 011.37-.491l1.216.456c.356.133.751.072 1.076-.124.072-.044.146-.087.22-.128.332-.183.582-.495.644-.869l.214-1.281z'],
                ];
            @endphp

            @foreach($tabs as $key => [$label, $fields, $icon])
                <button type="button" @click="tab = '{{ $key }}'"
                    class="flex items-center gap-2 px-4 py-2.5 text-sm font-medium rounded-lg transition"
                    :class="tab === '{{ $key }}' ? 'bg-primary-600 text-white shadow-sm' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900'">
                    <svg class="w-4 h-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="{{ $icon }}"/></svg>
                    {{ $label }}
                    @if($errors->hasAny($fields))
                        <span class="w-1.5 h-1.5 bg-red-500 rounded-full"></span>
                    @endif
                </button>
            @endforeach
        </div>

        {{-- Form --}}
        <form method="POST" action="{{ route('admin.settings.update') }}" enctype="multipart/form-data" class="space-y-6">
            @csrf
            @method('PUT')

            {{-- ================= GENERAL ================= --}}
            <div x-show="tab === 'general'" x-cloak class="bg-white rounded-xl border border-gray-100 shadow-sm p-6 space-y-6">
                <div>
                    <h2 class="text-lg font-semibold text-gray-900">General</h2>
                    <p class="text-sm text-gray-500 mt-0.5">Basic identity of your website shown across headers, titles and emails.</p>
                </div>

                <div class="grid sm:grid-cols-2 gap-6">
                    <div>
                        <label for="site_name" class="block text-sm font-medium text-gray-700 mb-1">Site Name <span class="text-red-500">*</span></label>
                        <input type="text" id="site_name" name="site_name" value="{{ $val('site_name', 'LMS Portal') }}" required
                            class="w-full px-4 py-2.5 border border-gray-200 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500 text-sm transition @error('site_name') border-red-300 @enderror" />
                        @error('site_name')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label for="site_tagline" class="block text-sm font-medium text-gray-700 mb-1">Site Tagline</label>
                        <input type="text" id="site_tagline" name="site_tagline" value="{{ $val('site_tagline', 'Learn. Grow. Succeed.') }}"
                            class="w-full px-4 py-2.5 border border-gray-200 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500 text-sm transition @error('site_tagline') border-red-300 @enderror" />
                        <p class="mt-1 text-xs text-gray-400">Shown next to the site title in browser tabs.</p>
                        @error('site_tagline')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
                    </div>
                </div>

                <div class="bg-gray-50 border border-gray-100 rounded-lg p-4">
                    <p class="text-xs font-medium text-gray-500 uppercase tracking-wider mb-2">Live Preview</p>
                    <p class="text-lg font-bold text-primary-900">{{ $val('site_name', 'LMS Portal') }} <span class="font-normal text-gray-400 text-sm">— {{ $val('site_tagline') }}</span></p>
                </div>
            </div>

            {{-- ================= BRANDING ================= --}}
            <div x-show="tab === 'branding'" x-cloak class="space-y-6">
                <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-6">
                    <h2 class="text-lg font-semibold text-gray-900">Branding</h2>
                    <p class="text-sm text-gray-500 mt-0.5 mb-6">Upload your logos, favicon and banner. PNG, JPG, SVG or WebP.</p>

                    <div class="grid md:grid-cols-2 gap-6">

                        {{-- Logo (light) --}}
                        <div x-data="{ preview: null }" class="border border-gray-200 rounded-xl p-5">
                            <p class="text-sm font-semibold text-gray-900 mb-1">Logo <span class="font-normal text-gray-400">(light background)</span></p>
                            <p class="text-xs text-gray-400 mb-4">Recommended: 250×80px, transparent PNG/SVG.</p>
                            <div class="flex items-center gap-4 mb-4">
                                <div class="w-32 h-20 bg-white border border-dashed border-gray-200 rounded-lg flex items-center justify-center overflow-hidden shrink-0">
                                    <template x-if="preview"><img :src="preview" class="max-w-full max-h-full object-contain"></template>
                                    <template x-if="!preview && {{ $img('logo') ? 'true' : 'false' }}"><img src="{{ $img('logo') }}" class="max-w-full max-h-full object-contain"></template>
                                    <template x-if="!preview && {{ $img('logo') ? 'false' : 'true' }}">
                                        <svg class="w-8 h-8 text-gray-300" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 15.75l5.159-5.159a2.25 2.25 0 013.182 0l5.159 5.159m-1.5-1.5l1.409-1.409a2.25 2.25 0 013.182 0l2.909 2.909M3.75 21h16.5A1.5 1.5 0 0021.75 19.5V4.5A1.5 1.5 0 0020.25 3H3.75A1.5 1.5 0 002.25 4.5v15A1.5 1.5 0 003.75 21z"/></svg>
                                    </template>
                                </div>
                                <div class="space-y-2">
                                    <label class="cursor-pointer inline-flex items-center gap-2 px-3 py-2 text-xs font-medium text-primary-700 bg-primary-50 hover:bg-primary-100 rounded-lg transition">
                                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5m-13.5-9L12 3m0 0l4.5 4.5M12 3v13.5"/></svg>
                                        Choose File
                                        <input type="file" name="logo" accept=".jpg,.jpeg,.png,.svg,.webp" class="hidden" @change="preview = $event.target.files[0] ? URL.createObjectURL($event.target.files[0]) : null">
                                    </label>
                                    @if($img('logo'))
                                        <button type="submit" form="remove-logo-form" class="block w-full inline-flex justify-center items-center gap-2 px-3 py-2 text-xs font-medium text-red-600 bg-red-50 hover:bg-red-100 rounded-lg transition">Remove Current</button>
                                    @endif
                                </div>
                            </div>
                            @error('logo')<p class="text-xs text-red-600">{{ $message }}</p>@enderror
                        </div>

                        {{-- Dark Logo --}}
                        <div x-data="{ preview: null }" class="border border-gray-200 rounded-xl p-5">
                            <p class="text-sm font-semibold text-gray-900 mb-1">Dark Logo <span class="font-normal text-gray-400">(footer / dark areas)</span></p>
                            <p class="text-xs text-gray-400 mb-4">Used on dark backgrounds like the footer.</p>
                            <div class="flex items-center gap-4 mb-4">
                                <div class="w-32 h-20 bg-gray-900 border border-dashed border-gray-700 rounded-lg flex items-center justify-center overflow-hidden shrink-0">
                                    <template x-if="preview"><img :src="preview" class="max-w-full max-h-full object-contain"></template>
                                    <template x-if="!preview && {{ $img('dark_logo') ? 'true' : 'false' }}"><img src="{{ $img('dark_logo') }}" class="max-w-full max-h-full object-contain"></template>
                                    <template x-if="!preview && {{ $img('dark_logo') ? 'false' : 'true' }}">
                                        <svg class="w-8 h-8 text-gray-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 15.75l5.159-5.159a2.25 2.25 0 013.182 0l5.159 5.159m-1.5-1.5l1.409-1.409a2.25 2.25 0 013.182 0l2.909 2.909M3.75 21h16.5A1.5 1.5 0 0021.75 19.5V4.5A1.5 1.5 0 0020.25 3H3.75A1.5 1.5 0 002.25 4.5v15A1.5 1.5 0 003.75 21z"/></svg>
                                    </template>
                                </div>
                                <div class="space-y-2">
                                    <label class="cursor-pointer inline-flex items-center gap-2 px-3 py-2 text-xs font-medium text-primary-700 bg-primary-50 hover:bg-primary-100 rounded-lg transition">
                                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5m-13.5-9L12 3m0 0l4.5 4.5M12 3v13.5"/></svg>
                                        Choose File
                                        <input type="file" name="dark_logo" accept=".jpg,.jpeg,.png,.svg,.webp" class="hidden" @change="preview = $event.target.files[0] ? URL.createObjectURL($event.target.files[0]) : null">
                                    </label>
                                    @if($img('dark_logo'))
                                        <button type="submit" form="remove-dark-logo-form" class="block w-full inline-flex justify-center items-center gap-2 px-3 py-2 text-xs font-medium text-red-600 bg-red-50 hover:bg-red-100 rounded-lg transition">Remove Current</button>
                                    @endif
                                </div>
                            </div>
                            @error('dark_logo')<p class="text-xs text-red-600">{{ $message }}</p>@enderror
                        </div>

                        {{-- Favicon --}}
                        <div x-data="{ preview: null }" class="border border-gray-200 rounded-xl p-5">
                            <p class="text-sm font-semibold text-gray-900 mb-1">Favicon</p>
                            <p class="text-xs text-gray-400 mb-4">Browser tab icon. Recommended: 64×64px ICO or PNG.</p>
                            <div class="flex items-center gap-4 mb-4">
                                <div class="w-32 h-20 bg-white border border-dashed border-gray-200 rounded-lg flex items-center justify-center overflow-hidden shrink-0">
                                    <template x-if="preview"><img :src="preview" class="max-w-10 max-h-10 object-contain"></template>
                                    <template x-if="!preview && {{ $img('favicon') ? 'true' : 'false' }}"><img src="{{ $img('favicon') }}" class="w-10 h-10 object-contain"></template>
                                    <template x-if="!preview && {{ $img('favicon') ? 'false' : 'true' }}">
                                        <svg class="w-8 h-8 text-gray-300" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M7.864 4.243A7.5 7.5 0 0119.5 10.5c0 2.92-.556 5.709-1.568 8.268M5.742 6.364A7.465 7.465 0 004.5 10.5a7.464 7.464 0 01-1.15 3.993m1.989 3.559A11.209 11.209 0 008.25 10.5a3.75 3.75 0 117.5 0c0 .527-.021 1.049-.064 1.565M12 10.5a14.94 14.94 0 00-3.6 9.75m6.633-4.596a18.666 18.666 0 01-2.485 5.33"/></svg>
                                    </template>
                                </div>
                                <div class="space-y-2">
                                    <label class="cursor-pointer inline-flex items-center gap-2 px-3 py-2 text-xs font-medium text-primary-700 bg-primary-50 hover:bg-primary-100 rounded-lg transition">
                                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5m-13.5-9L12 3m0 0l4.5 4.5M12 3v13.5"/></svg>
                                        Choose File
                                        <input type="file" name="favicon" accept=".ico,.png,.jpg,.jpeg,.svg" class="hidden" @change="preview = $event.target.files[0] ? URL.createObjectURL($event.target.files[0]) : null">
                                    </label>
                                    @if($img('favicon'))
                                        <button type="submit" form="remove-favicon-form" class="block w-full inline-flex justify-center items-center gap-2 px-3 py-2 text-xs font-medium text-red-600 bg-red-50 hover:bg-red-100 rounded-lg transition">Remove Current</button>
                                    @endif
                                </div>
                            </div>
                            @error('favicon')<p class="text-xs text-red-600">{{ $message }}</p>@enderror
                        </div>

                        {{-- Banner --}}
                        <div x-data="{ preview: null }" class="border border-gray-200 rounded-xl p-5">
                            <p class="text-sm font-semibold text-gray-900 mb-1">Banner Image</p>
                            <p class="text-xs text-gray-400 mb-4">Homepage hero banner. Recommended: 1920×600px.</p>
                            <div class="mb-4">
                                <div class="w-full h-28 bg-gradient-to-br from-primary-600 to-primary-800 rounded-lg flex items-center justify-center overflow-hidden">
                                    <template x-if="preview"><img :src="preview" class="w-full h-full object-cover"></template>
                                    <template x-if="!preview && {{ $img('banner') ? 'true' : 'false' }}"><img src="{{ $img('banner') }}" class="w-full h-full object-cover"></template>
                                    <template x-if="!preview && {{ $img('banner') ? 'false' : 'true' }}">
                                        <span class="text-white/60 text-xs font-medium">Default gradient in use</span>
                                    </template>
                                </div>
                            </div>
                            <div class="flex items-center gap-2">
                                <label class="cursor-pointer inline-flex items-center gap-2 px-3 py-2 text-xs font-medium text-primary-700 bg-primary-50 hover:bg-primary-100 rounded-lg transition">
                                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5m-13.5-9L12 3m0 0l4.5 4.5M12 3v13.5"/></svg>
                                    Choose File
                                    <input type="file" name="banner" accept=".jpg,.jpeg,.png,.webp" class="hidden" @change="preview = $event.target.files[0] ? URL.createObjectURL($event.target.files[0]) : null">
                                </label>
                                @if($img('banner'))
                                    <button type="submit" form="remove-banner-form" class="inline-flex items-center gap-2 px-3 py-2 text-xs font-medium text-red-600 bg-red-50 hover:bg-red-100 rounded-lg transition">Remove Current</button>
                                @endif
                            </div>
                            @error('banner')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
                        </div>
                    </div>
                </div>
            </div>

            {{-- ================= ABOUT ================= --}}
            <div x-show="tab === 'about'" x-cloak class="bg-white rounded-xl border border-gray-100 shadow-sm p-6 space-y-6">
                <div>
                    <h2 class="text-lg font-semibold text-gray-900">About & Description</h2>
                    <p class="text-sm text-gray-500 mt-0.5">Tell visitors what your platform is about.</p>
                </div>

                <div>
                    <label for="about_title" class="block text-sm font-medium text-gray-700 mb-1">About Title</label>
                    <input type="text" id="about_title" name="about_title" value="{{ $val('about_title', 'About Us') }}"
                        class="w-full px-4 py-2.5 border border-gray-200 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500 text-sm transition @error('about_title') border-red-300 @enderror" />
                    @error('about_title')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label for="about_description" class="block text-sm font-medium text-gray-700 mb-1">About Description</label>
                    <textarea id="about_description" name="about_description" rows="6"
                        class="w-full px-4 py-2.5 border border-gray-200 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500 text-sm transition @error('about_description') border-red-300 @enderror"
                        placeholder="Describe your learning platform, mission and vision...">{{ $val('about_description') }}</textarea>
                    @error('about_description')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label for="footer_description" class="block text-sm font-medium text-gray-700 mb-1">Footer Short Description</label>
                    <textarea id="footer_description" name="footer_description" rows="3"
                        class="w-full px-4 py-2.5 border border-gray-200 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500 text-sm transition @error('footer_description') border-red-300 @enderror"
                        placeholder="Short brand blurb shown in the footer...">{{ $val('footer_description') }}</textarea>
                    @error('footer_description')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
                </div>
            </div>

            {{-- ================= CONTACT ================= --}}
            <div x-show="tab === 'contact'" x-cloak class="bg-white rounded-xl border border-gray-100 shadow-sm p-6 space-y-6">
                <div>
                    <h2 class="text-lg font-semibold text-gray-900">Contact Information</h2>
                    <p class="text-sm text-gray-500 mt-0.5">Shown on the top bar, contact page and footer.</p>
                </div>

                <div class="grid sm:grid-cols-2 gap-6">
                    <div>
                        <label for="contact_email" class="block text-sm font-medium text-gray-700 mb-1">Contact Email</label>
                        <input type="email" id="contact_email" name="contact_email" value="{{ $val('contact_email', 'info@example.com') }}"
                            class="w-full px-4 py-2.5 border border-gray-200 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500 text-sm transition @error('contact_email') border-red-300 @enderror" />
                        @error('contact_email')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label for="support_email" class="block text-sm font-medium text-gray-700 mb-1">Support Email</label>
                        <input type="email" id="support_email" name="support_email" value="{{ $val('support_email', 'support@example.com') }}"
                            class="w-full px-4 py-2.5 border border-gray-200 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500 text-sm transition @error('support_email') border-red-300 @enderror" />
                        @error('support_email')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label for="contact_phone" class="block text-sm font-medium text-gray-700 mb-1">Phone Number</label>
                        <input type="text" id="contact_phone" name="contact_phone" value="{{ $val('contact_phone', '+1 (800) 123-4567') }}"
                            class="w-full px-4 py-2.5 border border-gray-200 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500 text-sm transition @error('contact_phone') border-red-300 @enderror" />
                        @error('contact_phone')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label for="office_hours" class="block text-sm font-medium text-gray-700 mb-1">Office Hours</label>
                        <input type="text" id="office_hours" name="office_hours" value="{{ $val('office_hours', 'Mon – Fri, 9:00 AM – 6:00 PM') }}"
                            class="w-full px-4 py-2.5 border border-gray-200 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500 text-sm transition @error('office_hours') border-red-300 @enderror" />
                        @error('office_hours')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
                    </div>
                    <div class="sm:col-span-2">
                        <label for="contact_address" class="block text-sm font-medium text-gray-700 mb-1">Office Address</label>
                        <textarea id="contact_address" name="contact_address" rows="2"
                            class="w-full px-4 py-2.5 border border-gray-200 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500 text-sm transition @error('contact_address') border-red-300 @enderror">{{ $val('contact_address') }}</textarea>
                        @error('contact_address')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
                    </div>
                </div>
            </div>

            {{-- ================= SOCIAL ================= --}}
            <div x-show="tab === 'social'" x-cloak class="bg-white rounded-xl border border-gray-100 shadow-sm p-6 space-y-6">
                <div>
                    <h2 class="text-lg font-semibold text-gray-900">Social Media Links</h2>
                    <p class="text-sm text-gray-500 mt-0.5">Full URLs including https://. Leave blank to hide an icon.</p>
                </div>

                <div class="space-y-4">
                    @foreach([
                        'facebook_url'  => ['Facebook', 'https://facebook.com/yourpage', 'M24 4.557c-.883.392-1.832.656-2.828.775 1.017-.609 1.798-1.574 2.165-2.724-.951.564-2.005.974-3.127 1.195-.897-.957-2.178-1.555-3.594-1.555-3.179 0-5.515 2.966-4.797 6.045-4.091-.205-7.719-2.165-10.148-5.144-1.29 2.213-.669 5.108 1.523 6.574-.806-.026-1.566-.247-2.229-.616-.054 2.281 1.581 4.415 3.949 4.89-.693.188-1.452.232-2.224.084.626 1.956 2.444 3.379 4.6 3.419-2.07 1.623-4.678 2.348-7.29 2.04 2.179 1.397 4.768 2.212 7.548 2.212 9.142 0 14.307-7.721 13.995-14.646.962-.695 1.797-1.562 2.457-2.549z'],
                        'twitter_url'   => ['Twitter / X', 'https://x.com/yourpage', 'M18.244 2.25h3.308l-7.227 8.26 8.502 11.24H16.17l-5.214-6.817L4.99 21.75H1.68l7.73-8.835L1.254 2.25H8.08l4.713 6.231zm-1.161 17.52h1.833L7.084 4.126H5.117z'],
                        'instagram_url' => ['Instagram', 'https://instagram.com/yourpage', 'M12 0C5.374 0 0 5.373 0 12c0 5.302 3.438 9.8 8.207 11.387.599.111.793-.261.793-.577v-2.234c-3.338.726-4.033-1.416-4.033-1.416-.546-1.387-1.333-1.756-1.333-1.756-1.089-.745.083-.729.083-.729 1.205.084 1.839 1.237 1.839 1.237 1.07 1.834 2.807 1.304 3.492.997.107-.775.418-1.305.762-1.604-2.665-.305-5.467-1.334-5.467-5.931 0-1.311.469-2.381 1.236-3.221-.124-.303-.535-1.524.117-3.176 0 0 1.008-.322 3.301 1.23A11.509 11.509 0 0112 5.803c1.02.005 2.047.138 3.006.404 2.291-1.552 3.297-1.23 3.297-1.23.653 1.653.242 2.874.118 3.176.77.84 1.235 1.911 1.235 3.221 0 4.609-2.807 5.624-5.479 5.921.43.372.823 1.102.823 2.222v3.293c0 .319.192.694.801.576C20.566 21.797 24 17.3 24 12c0-6.627-5.373-12-12-12z'],
                        'linkedin_url'  => ['LinkedIn', 'https://linkedin.com/company/yourpage', 'M20.447 20.452h-3.554v-5.569c0-1.328-.027-3.037-1.852-3.037-1.853 0-2.136 1.445-2.136 2.939v5.667H9.351V9h3.414v1.561h.046c.477-.9 1.637-1.85 3.37-1.85 3.601 0 4.267 2.37 4.267 5.455v6.286zM5.337 7.433c-1.144 0-2.063-.926-2.063-2.065 0-1.138.92-2.063 2.063-2.063 1.14 0 2.064.925 2.064 2.063 0 1.139-.925 2.065-2.064 2.065zm1.782 13.019H3.555V9h3.564v11.452zM22.225 0H1.771C.792 0 0 .774 0 1.729v20.542C0 23.227.792 24 1.771 24h20.451C23.2 24 24 23.227 24 22.271V1.729C24 .774 23.2 0 22.222 0h.003z'],
                        'youtube_url'   => ['YouTube', 'https://youtube.com/@yourpage', 'M19.615 3.184c-3.604-.246-11.631-.245-15.23 0-3.897.266-4.356 2.62-4.385 8.816.029 6.185.484 8.549 4.385 8.816 3.6.245 11.626.246 15.23 0 3.897-.266 4.356-2.62 4.385-8.816-.029-6.185-.484-8.549-4.385-8.816zM9 16V8l8 3.993L9 16z'],
                    ] as $field => [$label, $placeholder, $path])
                        <div class="flex items-center gap-4">
                            <div class="w-10 h-10 bg-gray-100 rounded-lg flex items-center justify-center shrink-0">
                                <svg class="w-4 h-4 text-gray-600" fill="currentColor" viewBox="0 0 24 24"><path d="{{ $path }}"/></svg>
                            </div>
                            <div class="flex-1">
                                <label for="{{ $field }}" class="block text-sm font-medium text-gray-700 mb-1">{{ $label }}</label>
                                <input type="url" id="{{ $field }}" name="{{ $field }}" value="{{ $val($field) }}" placeholder="{{ $placeholder }}"
                                    class="w-full px-4 py-2.5 border border-gray-200 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500 text-sm transition @error($field) border-red-300 @enderror" />
                                @error($field)<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            {{-- ================= SEO ================= --}}
            <div x-show="tab === 'seo'" x-cloak class="bg-white rounded-xl border border-gray-100 shadow-sm p-6 space-y-6">
                <div>
                    <h2 class="text-lg font-semibold text-gray-900">SEO & Meta Tags</h2>
                    <p class="text-sm text-gray-500 mt-0.5">How your site appears in search engines and social shares.</p>
                </div>

                <div>
                    <label for="meta_title" class="block text-sm font-medium text-gray-700 mb-1">Meta Title</label>
                    <input type="text" id="meta_title" name="meta_title" value="{{ $val('meta_title') }}" placeholder="{{ $val('site_name', 'LMS Portal') }} - Online Learning Platform"
                        class="w-full px-4 py-2.5 border border-gray-200 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500 text-sm transition @error('meta_title') border-red-300 @enderror" />
                    <p class="mt-1 text-xs text-gray-400">{{ strlen($val('meta_title')) }}/60 characters recommended.</p>
                    @error('meta_title')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label for="meta_description" class="block text-sm font-medium text-gray-700 mb-1">Meta Description</label>
                    <textarea id="meta_description" name="meta_description" rows="3" maxlength="160"
                        class="w-full px-4 py-2.5 border border-gray-200 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500 text-sm transition @error('meta_description') border-red-300 @enderror"
                        placeholder="A short summary of your site for search results...">{{ $val('meta_description') }}</textarea>
                    <p class="mt-1 text-xs text-gray-400">{{ strlen($val('meta_description')) }}/160 characters recommended.</p>
                    @error('meta_description')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label for="meta_keywords" class="block text-sm font-medium text-gray-700 mb-1">Meta Keywords</label>
                    <input type="text" id="meta_keywords" name="meta_keywords" value="{{ $val('meta_keywords') }}" placeholder="online courses, e-learning, certifications"
                        class="w-full px-4 py-2.5 border border-gray-200 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500 text-sm transition @error('meta_keywords') border-red-300 @enderror" />
                    <p class="mt-1 text-xs text-gray-400">Comma separated keywords.</p>
                    @error('meta_keywords')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
                </div>
            </div>

            {{-- ================= ADVANCED ================= --}}
            <div x-show="tab === 'advanced'" x-cloak class="space-y-6">
                <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-6 space-y-6">
                    <div>
                        <h2 class="text-lg font-semibold text-gray-900">Advanced</h2>
                        <p class="text-sm text-gray-500 mt-0.5">Footer copyright and maintenance options.</p>
                    </div>

                    <div>
                        <label for="copyright_text" class="block text-sm font-medium text-gray-700 mb-1">Copyright Text</label>
                        <input type="text" id="copyright_text" name="copyright_text" value="{{ $val('copyright_text', 'All rights reserved.') }}"
                            class="w-full px-4 py-2.5 border border-gray-200 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500 text-sm transition @error('copyright_text') border-red-300 @enderror" />
                        @error('copyright_text')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
                    </div>

                    <div class="border-t border-gray-100 pt-6">
                        <label class="flex items-start gap-3 cursor-pointer">
                            <input type="checkbox" name="maintenance_mode" value="1" class="mt-0.5 w-4 h-4 rounded border-gray-300 text-primary-600 focus:ring-primary-500"
                                @checked(($site['maintenance_mode'] ?? '0') === '1')>
                            <span>
                                <span class="block text-sm font-medium text-gray-900">Enable maintenance mode notice</span>
                                <span class="block text-xs text-gray-500 mt-0.5">Shows a banner to visitors while you work on the site.</span>
                            </span>
                        </label>
                    </div>

                    <div>
                        <label for="maintenance_message" class="block text-sm font-medium text-gray-700 mb-1">Maintenance Message</label>
                        <textarea id="maintenance_message" name="maintenance_message" rows="2"
                            class="w-full px-4 py-2.5 border border-gray-200 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500 text-sm transition @error('maintenance_message') border-red-300 @enderror"
                            placeholder="We're performing scheduled maintenance. Some features may be unavailable.">{{ $val('maintenance_message') }}</textarea>
                        @error('maintenance_message')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
                    </div>
                </div>
            </div>

            {{-- Sticky Save Bar --}}
            <div class="sticky bottom-4 z-30">
                <div class="bg-white/95 backdrop-blur border border-gray-200 shadow-lg rounded-xl px-6 py-4 flex items-center justify-between">
                    <p class="text-sm text-gray-500 hidden sm:block">
                        <svg class="w-4 h-4 inline-block mr-1 text-secondary-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        Changes apply instantly across the whole site.
                    </p>
                    <div class="flex items-center gap-3 ml-auto">
                        <a href="{{ route('admin.settings') }}" class="px-5 py-2.5 text-sm font-medium text-gray-600 hover:text-gray-900 transition">Reset</a>
                        <button type="submit" class="px-6 py-2.5 bg-primary-600 hover:bg-primary-700 text-white font-medium rounded-lg text-sm transition shadow-sm">
                            Save All Settings
                        </button>
                    </div>
                </div>
            </div>
        </form>

        {{-- Hidden remove-image forms (kept outside the main form since nested forms are invalid HTML) --}}
        @foreach(['logo' => 'remove-logo-form', 'dark_logo' => 'remove-dark-logo-form', 'favicon' => 'remove-favicon-form', 'banner' => 'remove-banner-form'] as $key => $formId)
            @if(\App\Models\Setting::get($key))
                <form id="{{ $formId }}" method="POST" action="{{ route('admin.settings.images.remove', ['key' => $key]) }}" class="hidden"
                    data-confirm="This will remove the current {{ str_replace('_', ' ', $key) }} from the site." data-confirm-title="Remove image?" data-confirm-button="Yes, remove it">
                    @csrf
                    @method('DELETE')
                </form>
            @endif
        @endforeach
    </div>

    <style>[x-cloak]{display:none!important}</style>
@endsection
