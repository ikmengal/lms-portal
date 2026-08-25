@extends('layouts.app')
@section('title', 'Dashboard')
@section('content')
    <div class="bg-gray-50 min-h-[80vh]">
        {{-- Dashboard Header --}}
        <div class="bg-white border-b border-gray-200">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
                <div class="flex items-center gap-4">
                    <div class="w-14 h-14 bg-primary-600 rounded-full flex items-center justify-center text-white text-xl font-bold">JD</div>
                    <div>
                        <h1 class="text-2xl font-bold text-gray-900">Welcome back, John!</h1>
                        <p class="text-gray-500 text-sm">Continue where you left off</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8" x-data="{ activeTab: 'enrolled' }">

            {{-- Stats Cards --}}
            <div class="grid sm:grid-cols-2 lg:grid-cols-4 gap-6 mb-10">
                <div class="bg-white rounded-xl p-6 border border-gray-100 shadow-sm">
                    <div class="flex items-center gap-4">
                        <div class="w-12 h-12 bg-primary-100 rounded-xl flex items-center justify-center">
                            <svg class="w-6 h-6 text-primary-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>
                        </div>
                        <div>
                            <p class="text-2xl font-bold text-gray-900">4</p>
                            <p class="text-sm text-gray-500">Enrolled Courses</p>
                        </div>
                    </div>
                </div>
                <div class="bg-white rounded-xl p-6 border border-gray-100 shadow-sm">
                    <div class="flex items-center gap-4">
                        <div class="w-12 h-12 bg-secondary-100 rounded-xl flex items-center justify-center">
                            <svg class="w-6 h-6 text-secondary-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        </div>
                        <div>
                            <p class="text-2xl font-bold text-gray-900">2</p>
                            <p class="text-sm text-gray-500">Completed</p>
                        </div>
                    </div>
                </div>
                <div class="bg-white rounded-xl p-6 border border-gray-100 shadow-sm">
                    <div class="flex items-center gap-4">
                        <div class="w-12 h-12 bg-accent-100 rounded-xl flex items-center justify-center">
                            <svg class="w-6 h-6 text-accent-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M11.48 3.499a.562.562 0 011.04 0l2.125 5.111a.563.563 0 00.475.345l5.518.442c.499.04.701.663.321.988l-4.204 3.602a.563.563 0 00-.182.557l1.285 5.385a.562.562 0 01-.84.61l-4.725-2.885a.563.563 0 00-.586 0L6.982 20.54a.562.562 0 01-.84-.61l1.285-5.386a.562.562 0 00-.182-.557l-4.204-3.602a.563.563 0 01.321-.988l5.518-.442a.563.563 0 00.475-.345L11.48 3.5z"/></svg>
                        </div>
                        <div>
                            <p class="text-2xl font-bold text-gray-900">4.7</p>
                            <p class="text-sm text-gray-500">Avg. Rating</p>
                        </div>
                    </div>
                </div>
                <div class="bg-white rounded-xl p-6 border border-gray-100 shadow-sm">
                    <div class="flex items-center gap-4">
                        <div class="w-12 h-12 bg-purple-100 rounded-xl flex items-center justify-center">
                            <svg class="w-6 h-6 text-purple-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M4.26 10.147a60.438 60.438 0 0 0-.491 6.347A48.62 48.62 0 0 1 12 20.904a48.62 48.62 0 0 1 8.232-4.41 60.46 60.46 0 0 0-.491-6.347m-15.482 0a50.636 50.636 0 0 0-2.658-.813A59.906 59.906 0 0 1 12 3.493a59.903 59.903 0 0 1 10.399 5.84c-.896.248-1.783.52-2.658.814m-15.482 0A50.717 50.717 0 0 1 12 13.489a50.702 50.702 0 0 1 7.74-3.342"/></svg>
                        </div>
                        <div>
                            <p class="text-2xl font-bold text-gray-900">86h</p>
                            <p class="text-sm text-gray-500">Learning Time</p>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Tabs --}}
            <div class="border-b border-gray-200 mb-8">
                <div class="flex gap-6">
                    <button @click="activeTab = 'enrolled'" :class="activeTab === 'enrolled' ? 'border-primary-600 text-primary-600' : 'border-transparent text-gray-500 hover:text-gray-700'" class="pb-3 text-sm font-semibold border-b-2 transition">My Courses</button>
                    <button @click="activeTab = 'wishlist'" :class="activeTab === 'wishlist' ? 'border-primary-600 text-primary-600' : 'border-transparent text-gray-500 hover:text-gray-700'" class="pb-3 text-sm font-semibold border-b-2 transition">Wishlist</button>
                    <button @click="activeTab = 'certificates'" :class="activeTab === 'certificates' ? 'border-primary-600 text-primary-600' : 'border-transparent text-gray-500 hover:text-gray-700'" class="pb-3 text-sm font-semibold border-b-2 transition">Certificates</button>
                </div>
            </div>

            {{-- Enrolled Courses --}}
            <div x-show="activeTab === 'enrolled'" x-transition>
                <div class="space-y-4">
                    @foreach([
                        ['title' => 'Python for Data Science & ML Bootcamp', 'instructor' => 'Dr. Sarah Johnson', 'progress' => 72, 'lastAccess' => '2 hours ago', 'nextLesson' => 'Decision Trees & Random Forests'],
                        ['title' => 'AWS Certified Solutions Architect', 'instructor' => 'Mike Chen', 'progress' => 45, 'lastAccess' => '1 day ago', 'nextLesson' => 'EC2 & Elastic Beanstalk'],
                        ['title' => 'Complete Cyber Security Course', 'instructor' => 'James Wilson', 'progress' => 100, 'lastAccess' => '3 days ago', 'nextLesson' => null],
                        ['title' => 'Full Stack Web Development', 'instructor' => 'Alex Martinez', 'progress' => 23, 'lastAccess' => '5 days ago', 'nextLesson' => 'React Components & State'],
                    ] as $course)
                        <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-6 hover:shadow-md transition">
                            <div class="flex flex-col sm:flex-row gap-5">
                                <div class="w-full sm:w-48 h-32 bg-gradient-to-br from-primary-100 to-primary-200 rounded-xl flex items-center justify-center shrink-0">
                                    <svg class="w-10 h-10 text-primary-300" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M4.26 10.147a60.438 60.438 0 0 0-.491 6.347A48.62 48.62 0 0 1 12 20.904a48.62 48.62 0 0 1 8.232-4.41 60.46 60.46 0 0 0-.491-6.347m-15.482 0a50.636 50.636 0 0 0-2.658-.813A59.906 59.906 0 0 1 12 3.493a59.903 59.903 0 0 1 10.399 5.84c-.896.248-1.783.52-2.658.814m-15.482 0A50.717 50.717 0 0 1 12 13.489a50.702 50.702 0 0 1 7.74-3.342"/></svg>
                                </div>
                                <div class="flex-1">
                                    <h3 class="font-bold text-gray-900 text-lg mb-1">{{ $course['title'] }}</h3>
                                    <p class="text-sm text-gray-500 mb-3">By {{ $course['instructor'] }}</p>

                                    {{-- Progress Bar --}}
                                    <div class="mb-3">
                                        <div class="flex items-center justify-between text-sm mb-1">
                                            <span class="text-gray-500">Progress</span>
                                            <span class="font-semibold {{ $course['progress'] === 100 ? 'text-secondary-600' : 'text-primary-600' }}">{{ $course['progress'] }}%</span>
                                        </div>
                                        <div class="w-full h-2 bg-gray-100 rounded-full overflow-hidden">
                                            <div class="h-full {{ $course['progress'] === 100 ? 'bg-secondary-500' : 'bg-primary-500' }} rounded-full transition-all duration-500" style="width: {{ $course['progress'] }}%"></div>
                                        </div>
                                    </div>

                                    <div class="flex flex-wrap items-center gap-4 text-xs text-gray-400">
                                        <span>Last accessed: {{ $course['lastAccess'] }}</span>
                                        @if($course['nextLesson'])
                                            <span>Next: {{ $course['nextLesson'] }}</span>
                                        @endif
                                    </div>
                                </div>
                                <div class="sm:self-center shrink-0">
                                    @if($course['progress'] === 100)
                                        <button class="px-5 py-2.5 bg-secondary-50 text-secondary-700 font-semibold text-sm rounded-lg hover:bg-secondary-100 transition">Review Course</button>
                                    @else
                                        <button class="px-5 py-2.5 bg-primary-600 hover:bg-primary-700 text-white font-semibold text-sm rounded-lg transition shadow-sm">Continue Learning</button>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            {{-- Wishlist --}}
            <div x-show="activeTab === 'wishlist'" x-transition style="display:none;">
                <div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-6">
                    <x-course-card title="Docker & Kubernetes: The Practical Guide" category="DevOps" instructor="Linda Park" :rating="4.8" :reviews="1680" students="9K" duration="32 hours" price="$47.99" originalPrice="$189.99" level="Intermediate" slug="#" />
                    <x-course-card title="Azure DevOps & Cloud Infrastructure" category="Cloud Computing" instructor="David Kim" :rating="4.6" :reviews="1450" students="8K" duration="30 hours" price="$52.99" originalPrice="$219.99" level="Intermediate" slug="#" />
                    <x-course-card title="Digital Marketing Masterclass" category="Digital Marketing" instructor="Chris Anderson" :rating="4.4" :reviews="2100" students="14K" duration="38 hours" price="$34.99" originalPrice="$159.99" level="Beginner" slug="#" />
                </div>
            </div>

            {{-- Certificates --}}
            <div x-show="activeTab === 'certificates'" x-transition style="display:none;">
                <div class="grid sm:grid-cols-2 gap-6">
                    @foreach(['Complete Cyber Security Course' => 'James Wilson', 'Full Stack Web Development' => 'Alex Martinez'] as $cert => $inst)
                        <div class="bg-white rounded-xl border border-gray-100 shadow-sm overflow-hidden">
                            <div class="bg-gradient-to-r from-primary-600 to-primary-800 p-8 text-center">
                                <div class="w-16 h-16 bg-white/20 rounded-full flex items-center justify-center mx-auto mb-4">
                                    <svg class="w-8 h-8 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12c0 1.268-.63 2.39-1.593 3.068a3.745 3.745 0 01-1.043 3.296 3.745 3.745 0 01-3.296 1.043A3.745 3.745 0 0112 21c-1.268 0-2.39-.63-3.068-1.593a3.746 3.746 0 01-3.296-1.043 3.745 3.745 0 01-1.043-3.296A3.745 3.745 0 013 12c0-1.268.63-2.39 1.593-3.068a3.745 3.745 0 011.043-3.296 3.746 3.746 0 013.296-1.043A3.746 3.746 0 0112 3c1.268 0 2.39.63 3.068 1.593a3.746 3.746 0 013.296 1.043 3.746 3.746 0 011.043 3.296A3.745 3.745 0 0121 12z"/></svg>
                                </div>
                                <p class="text-white/80 text-sm uppercase tracking-wider mb-1">Certificate of Completion</p>
                                <h3 class="text-white font-bold text-lg">{{ $cert }}</h3>
                                <p class="text-white/60 text-sm mt-1">Instructor: {{ $inst }}</p>
                            </div>
                            <div class="p-4 flex justify-between items-center">
                                <span class="text-xs text-gray-400">Issued: January 2026</span>
                                <button class="text-sm font-semibold text-primary-600 hover:text-primary-700 transition flex items-center gap-1">
                                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5M16.5 12L12 16.5m0 0L7.5 12m4.5 4.5V3"/></svg>
                                    Download
                                </button>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
@endsection
