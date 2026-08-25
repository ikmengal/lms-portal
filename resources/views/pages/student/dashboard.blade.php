@extends('layouts.dashboard')
@section('title', 'Student Dashboard')
    @php
        $activeTab = request('tab', 'learning');
        if (!in_array($activeTab, ['learning', 'completed', 'progress', 'certificates', 'wishlist', 'activity'])) {
            $activeTab = 'learning';
        }
        $certificateCourseIds = $certificates->pluck('course_id')->all();
        $inProgress = $enrolledCourses->filter(fn ($e) => !$e->isCompleted());
        $completed = $enrolledCourses->filter(fn ($e) => $e->isCompleted());
    @endphp
@section('content')
    <div class="space-y-8" x-data="{ activeTab: '{{ $activeTab }}' }">
        {{-- Header --}}
        <div class="rounded-2xl overflow-hidden border border-gray-100 shadow-sm bg-white">
            <div class="relative h-36 sm:h-44 bg-gradient-to-r from-primary-600 via-primary-700 to-secondary-600">
                @if(auth()->user()->banner)
                    <img src="{{ auth()->user()->banner_url }}" alt="Profile banner" class="absolute inset-0 w-full h-full object-cover">
                @endif
            </div>
            <div class="px-6 pb-5 flex flex-col sm:flex-row sm:items-end sm:justify-between gap-4 relative">
                <div class="flex items-end gap-4 -mt-8 sm:-mt-10">
                    <x-avatar :user="auth()->user()" size="w-20 h-20 text-2xl" class="ring-4 ring-white shadow-md" />
                    <div class="pb-1">
                        <h1 class="text-xl sm:text-2xl font-bold text-gray-900">Hi, {{ auth()->user()->name }} 👋</h1>
                        <p class="text-gray-500 mt-0.5 text-sm">Welcome back to your learning journey.</p>
                    </div>
                </div>
                <div class="flex items-center gap-2">
                    <a href="{{ route('profile.edit') }}" class="inline-flex items-center gap-2 px-4 py-2.5 text-sm font-medium text-gray-600 bg-white border border-gray-200 rounded-lg hover:bg-gray-50 transition">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                        My Profile
                    </a>
                    <a href="{{ route('courses.index') }}" class="inline-flex items-center gap-2 px-5 py-2.5 bg-primary-600 hover:bg-primary-700 text-white text-sm font-medium rounded-lg transition shadow-sm">Browse Courses</a>
                </div>
            </div>
        </div>

        {{-- Continue Learning Hero --}}
        @if($continueLearning)
            @php
                $heroCourse = $continueLearning->course;
                $heroLessons = $heroCourse->modules()->with('lessons')->get()->flatMap->lessons->values();
                $heroDone = \App\Models\LessonProgress::where('user_id', auth()->id())->whereIn('lesson_id', $heroLessons->pluck('id'))->count();
                $nextIdx = $heroLessons->search(fn ($l) => !\App\Models\LessonProgress::where('user_id', auth()->id())->where('lesson_id', $l->id)->exists());
                $nextLesson = $nextIdx !== false ? $heroLessons[$nextIdx] : null;
            @endphp
            <div class="rounded-2xl overflow-hidden shadow-sm border border-primary-100 bg-gradient-to-r from-primary-600 via-primary-600 to-secondary-600 relative">
                <div class="absolute -right-10 -top-10 w-48 h-48 bg-white/10 rounded-full"></div>
                <div class="absolute right-24 bottom-0 w-24 h-24 bg-white/10 rounded-full"></div>
                <div class="p-6 sm:p-8 flex flex-col lg:flex-row lg:items-center gap-6 relative">
                    <div class="flex-1 min-w-0 text-white">
                        <p class="text-xs font-bold uppercase tracking-widest text-white/70 mb-2">Continue Learning</p>
                        <h2 class="text-xl sm:text-2xl font-bold mb-1 truncate">{{ $heroCourse->title }}</h2>
                        <p class="text-sm text-white/70 mb-4">{{ $heroCourse->instructor->name ?? 'Instructor' }} · next up: {{ $nextLesson?->title ?? 'Review lessons' }}</p>
                        <div class="flex items-center gap-3 max-w-md">
                            <div class="flex-1 h-2.5 bg-white/25 rounded-full overflow-hidden">
                                <div class="h-full bg-white rounded-full transition-all duration-500" style="width: {{ $continueLearning->progress }}%"></div>
                            </div>
                            <span class="text-sm font-bold">{{ $continueLearning->progress }}%</span>
                        </div>
                        <p class="text-xs text-white/60 mt-2">{{ $heroDone }} of {{ $heroLessons->count() }} lessons complete</p>
                    </div>
                    <div class="shrink-0">
                        @if($heroCourse && $heroLessons->isNotEmpty())
                            <a href="{{ route('learn.start', $heroCourse) }}" class="inline-flex items-center gap-2 px-6 py-3 bg-white text-primary-700 hover:bg-primary-50 font-semibold rounded-xl transition shadow-lg whitespace-nowrap">
                                <svg class="w-5 h-5 fill-current" viewBox="0 0 24 24"><path d="M8 5v14l11-7z"/></svg>
                                {{ $continueLearning->progress > 0 ? 'Resume' : 'Start' }} Lesson {{ ($nextIdx !== false ? $nextIdx : 0) + 1 }}
                            </a>
                        @endif
                    </div>
                </div>
            </div>
        @endif

        {{-- Stats --}}
        <div class="grid sm:grid-cols-2 lg:grid-cols-4 gap-6">
            <div class="bg-white rounded-xl p-6 border border-gray-100 shadow-sm">
                <div class="flex items-center gap-4">
                    <div class="w-12 h-12 bg-primary-100 rounded-xl flex items-center justify-center">
                        <svg class="w-6 h-6 text-primary-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6.042A8.967 8.967 0 006 3.75c-1.052 0-2.062.18-3 .512v14.25A8.987 8.987 0 016 18c2.305 0 4.408.867 6 2.292m0-14.25a8.966 8.966 0 016-2.292c1.052 0 2.062.18 3 .512v14.25A8.987 8.987 0 0018 18a8.967 8.967 0 00-6 2.292m0-14.25v14.25"/></svg>
                    </div>
                    <div>
                        <p class="text-2xl font-bold text-gray-900">{{ $stats['enrolledCount'] }}</p>
                        <p class="text-sm text-gray-500">Enrolled Courses</p>
                    </div>
                </div>
                <p class="mt-3 text-xs text-gray-400">{{ $stats['inProgressCount'] }} in progress · {{ $stats['notStartedCount'] }} not started</p>
            </div>

            <div class="bg-white rounded-xl p-6 border border-gray-100 shadow-sm">
                <div class="flex items-center gap-4">
                    <div class="w-12 h-12 bg-secondary-100 rounded-xl flex items-center justify-center">
                        <svg class="w-6 h-6 text-secondary-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    </div>
                    <div>
                        <p class="text-2xl font-bold text-gray-900">{{ $stats['completedCount'] }}</p>
                        <p class="text-sm text-gray-500">Completed</p>
                    </div>
                </div>
                <p class="mt-3 text-xs text-gray-400">{{ $stats['avgProgress'] }}% average progress</p>
            </div>

            <div class="bg-white rounded-xl p-6 border border-gray-100 shadow-sm">
                <div class="flex items-center gap-4">
                    <div class="w-12 h-12 bg-accent-100 rounded-xl flex items-center justify-center">
                        <svg class="w-6 h-6 text-accent-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M4.26 10.147a60.436 60.436 0 00-.491 6.347A48.627 48.627 0 0112 20.904a48.627 48.627 0 018.232-4.41 60.46 60.46 0 00-.491-6.347m-15.482 0a50.57 50.57 0 00-2.658-.813A59.905 59.905 0 0112 3.493a59.902 59.902 0 0110.399 5.84c-.896.248-1.783.52-2.658.814m-15.482 0A50.697 50.697 0 0112 13.489a50.702 50.702 0 017.74-3.336M6.75 15a.75.75 0 100-1.5.75.75 0 000 1.5zm0 0v-3.675A55.378 55.378 0 0112 8.443m-7.007 11.55A5.981 5.981 0 006.75 15a.75.75 0 100-1.5zm0 0h7.5"/></svg>
                    </div>
                    <div>
                        <p class="text-2xl font-bold text-gray-900">{{ $stats['certificates'] }}</p>
                        <p class="text-sm text-gray-500">Certificates</p>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-xl p-6 border border-gray-100 shadow-sm">
                <div class="flex items-center gap-4">
                    <div class="w-12 h-12 bg-red-50 rounded-xl flex items-center justify-center">
                        <svg class="w-6 h-6 text-red-400" fill="currentColor" viewBox="0 0 24 24"><path d="M11.645 20.91l-.007-.003-.022-.012a15.247 15.247 0 01-.383-.218 25.18 25.18 0 01-4.244-3.17C4.688 15.36 2.25 12.174 2.25 8.25 2.25 5.322 4.714 3 7.688 3A5.5 5.5 0 0112 5.052 5.5 5.5 0 0116.313 3c2.973 0 5.437 2.322 5.437 5.25 0 3.925-2.438 7.111-4.739 9.256a25.175 25.175 0 01-4.244 3.17 15.247 15.247 0 01-.383.219l-.022.012-.007.004-.003.001a.752.752 0 01-.704 0l-.003-.001z"/></svg>
                    </div>
                    <div>
                        <p class="text-2xl font-bold text-gray-900">{{ $stats['wishlist'] }}</p>
                        <p class="text-sm text-gray-500">Wishlist</p>
                    </div>
                </div>
                <button @click="activeTab = 'wishlist'" class="mt-3 text-xs font-medium text-primary-600 hover:text-primary-700">View wishlist →</button>
            </div>
        </div>

        {{-- Tabs --}}
        <div class="border-b border-gray-200">
            <div class="flex gap-6 overflow-x-auto">
                <button @click="activeTab = 'learning'" :class="activeTab === 'learning' ? 'border-primary-600 text-primary-600' : 'border-transparent text-gray-500 hover:text-gray-700'" class="pb-3 text-sm font-medium border-b-2 transition whitespace-nowrap">My Courses ({{ $inProgress->count() }})</button>
                <button @click="activeTab = 'completed'" :class="activeTab === 'completed' ? 'border-primary-600 text-primary-600' : 'border-transparent text-gray-500 hover:text-gray-700'" class="pb-3 text-sm font-medium border-b-2 transition whitespace-nowrap">Completed ({{ $completed->count() }})</button>
                <button @click="activeTab = 'progress'" :class="activeTab === 'progress' ? 'border-primary-600 text-primary-600' : 'border-transparent text-gray-500 hover:text-gray-700'" class="pb-3 text-sm font-medium border-b-2 transition whitespace-nowrap">Progress</button>
                <button @click="activeTab = 'certificates'" :class="activeTab === 'certificates' ? 'border-primary-600 text-primary-600' : 'border-transparent text-gray-500 hover:text-gray-700'" class="pb-3 text-sm font-medium border-b-2 transition whitespace-nowrap">Certificates ({{ $stats['certificates'] }})</button>
                <button @click="activeTab = 'wishlist'" :class="activeTab === 'wishlist' ? 'border-primary-600 text-primary-600' : 'border-transparent text-gray-500 hover:text-gray-700'" class="pb-3 text-sm font-medium border-b-2 transition whitespace-nowrap">Wishlist ({{ $stats['wishlist'] }})</button>
                <button @click="activeTab = 'activity'" :class="activeTab === 'activity' ? 'border-primary-600 text-primary-600' : 'border-transparent text-gray-500 hover:text-gray-700'" class="pb-3 text-sm font-medium border-b-2 transition whitespace-nowrap">Recent Activity</button>
            </div>
        </div>

        {{-- My Courses (in progress) --}}
        <div x-show="activeTab === 'learning'" x-transition>
            <div class="space-y-4">
                @forelse($inProgress as $enrollment)
                    @include('pages.student._course-row', ['enrollment' => $enrollment])
                @empty
                    <div class="bg-white rounded-xl border border-dashed border-gray-200 p-12 text-center">
                        <h3 class="text-lg font-medium text-gray-900 mb-2">No courses in progress</h3>
                        <p class="text-gray-500 mb-6">Start your learning journey by browsing our catalog.</p>
                        <a href="{{ route('courses.index') }}" class="inline-flex items-center gap-2 px-5 py-2.5 bg-primary-600 hover:bg-primary-700 text-white font-medium rounded-lg transition">Browse Courses</a>
                    </div>
                @endforelse
            </div>
        </div>

        {{-- Completed --}}
        <div x-show="activeTab === 'completed'" x-transition style="display:none;">
            <div class="space-y-4">
                @forelse($completed as $enrollment)
                    @include('pages.student._course-row', ['enrollment' => $enrollment])
                @empty
                    <div class="bg-white rounded-xl border border-dashed border-gray-200 p-12 text-center">
                        <h3 class="text-lg font-medium text-gray-900 mb-2">No completed courses yet</h3>
                        <p class="text-gray-500">Finish a course at 100% progress to see it here.</p>
                    </div>
                @endforelse
            </div>
        </div>

        {{-- Progress --}}
        <div x-show="activeTab === 'progress'" x-transition style="display:none;">
            <div class="bg-white rounded-xl border border-gray-100 shadow-sm divide-y divide-gray-50">
                @forelse($enrolledCourses as $enrollment)
                    @php
                        $lessons = $enrollment->course->modules()->with('lessons')->get()->flatMap->lessons->count();
                        $done = \App\Models\LessonProgress::where('user_id', auth()->id())->whereIn('lesson_id', $enrollment->course->modules()->with('lessons')->get()->flatMap->lessons->pluck('id'))->count();
                    @endphp
                    <div class="px-6 py-4 flex items-center gap-4">
                        <div class="min-w-0 flex-1">
                            <p class="text-sm font-medium text-gray-900 truncate">{{ $enrollment->course?->title }}</p>
                            <p class="text-xs text-gray-400 mt-0.5">{{ $done }}/{{ $lessons }} lessons · updated {{ $enrollment->updated_at->diffForHumans() }}</p>
                        </div>
                        <div class="hidden sm:flex items-center gap-2 w-48">
                            <div class="flex-1 h-2 bg-gray-100 rounded-full overflow-hidden">
                                <div class="h-full rounded-full {{ $enrollment->isCompleted() ? 'bg-secondary-500' : 'bg-primary-500' }}" style="width: {{ $enrollment->progress }}%"></div>
                            </div>
                            <span class="text-xs font-semibold text-gray-500 w-9 text-right">{{ $enrollment->progress }}%</span>
                        </div>
                        @if(!$enrollment->isCompleted())
                            <a href="{{ route('learn.start', $enrollment->course) }}" class="shrink-0 text-xs font-semibold text-primary-600 hover:text-primary-700">Resume →</a>
                        @else
                            <span class="shrink-0 text-xs font-semibold text-secondary-600">✓ Done</span>
                        @endif
                    </div>
                @empty
                    <p class="px-6 py-10 text-center text-sm text-gray-400">No enrollments to track yet.</p>
                @endforelse
            </div>
        </div>

        {{-- Certificates --}}
        <div x-show="activeTab === 'certificates'" x-transition style="display:none;">
            <div class="grid sm:grid-cols-2 gap-6">
                @forelse($certificates as $certificate)
                    <div class="bg-white rounded-xl border border-gray-100 shadow-sm overflow-hidden hover:shadow-md transition">
                        <div class="h-28 bg-gradient-to-r from-accent-400 via-accent-500 to-accent-600 flex items-center justify-center relative">
                            <svg class="w-12 h-12 text-white/80" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1"><path stroke-linecap="round" stroke-linejoin="round" d="M4.26 10.147a60.436 60.436 0 00-.491 6.347A48.627 48.627 0 0112 20.904a48.627 48.627 0 018.232-4.41 60.46 60.46 0 00-.491-6.347m-15.482 0a50.57 50.57 0 00-2.658-.813A59.905 59.905 0 0112 3.493a59.902 59.902 0 0110.399 5.84c-.896.248-1.783.52-2.658.814m-15.482 0A50.697 50.697 0 0112 13.489a50.702 50.702 0 017.74-3.336M6.75 15a.75.75 0 100-1.5.75.75 0 000 1.5zm0 0v-3.675A55.378 55.378 0 0112 8.443m-7.007 11.55A5.981 5.981 0 006.75 15a.75.75 0 100-1.5zm0 0h7.5"/></svg>
                            <span class="absolute top-3 right-3 px-2 py-1 bg-white/20 backdrop-blur text-white text-xs font-medium rounded-md">Verified</span>
                        </div>
                        <div class="p-5">
                            <h3 class="font-semibold text-gray-900 mb-1">{{ $certificate->course->title ?? 'Course' }}</h3>
                            <dl class="text-sm text-gray-500 space-y-1 mb-4">
                                <div class="flex items-center gap-2">Issued: {{ $certificate->issued_at->format('M d, Y') }}</div>
                                <div class="flex items-center gap-2">ID: <a href="{{ route('certificates.verify', $certificate->code) }}" target="_blank" class="font-mono hover:text-primary-600 underline decoration-dotted transition">{{ $certificate->code }}</a></div>
                            </dl>
                            <div class="grid grid-cols-2 gap-2">
                                <a href="{{ route('certificates.show', $certificate) }}" target="_blank" class="inline-flex items-center justify-center gap-2 px-4 py-2 bg-primary-50 hover:bg-primary-100 text-primary-700 font-medium rounded-lg text-sm transition">View</a>
                                <a href="{{ route('certificates.download', $certificate) }}" class="inline-flex items-center justify-center gap-2 px-4 py-2 bg-primary-600 hover:bg-primary-700 text-white font-medium rounded-lg text-sm transition">PDF</a>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="sm:col-span-2 bg-white rounded-xl border border-dashed border-gray-200 p-12 text-center">
                        <h3 class="text-lg font-medium text-gray-900 mb-2">No certificates yet</h3>
                        <p class="text-gray-500">Complete a course to earn your first certificate.</p>
                    </div>
                @endforelse
            </div>
        </div>

        {{-- Wishlist --}}
        <div x-show="activeTab === 'wishlist'" x-transition style="display:none;">
            <div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-5">
                @forelse($wishlist as $item)
                    <div class="bg-white rounded-xl border border-gray-100 shadow-sm overflow-hidden hover:shadow-md transition group">
                        <div class="h-32 bg-gradient-to-br from-primary-500 to-primary-700 relative flex items-center justify-center">
                            <svg class="w-10 h-10 text-white/70" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6.042A8.967 8.967 0 006 3.75c-1.052 0-2.062.18-3 .512v14.25A8.987 8.987 0 016 18c2.305 0 4.408.867 6 2.292m0-14.25a8.966 8.966 0 016-2.292c1.052 0 2.062.18 3 .512v14.25A8.987 8.987 0 0018 18a8.967 8.967 0 00-6 2.292m0-14.25v14.25"/></svg>
                            <form method="POST" action="{{ route('courses.wishlist', $item->course) }}" class="absolute top-2 right-2">
                                @csrf
                                <button type="submit" title="Remove from wishlist" class="p-1.5 bg-white/20 backdrop-blur rounded-full text-white hover:bg-red-500 transition">
                                    <svg class="w-4 h-4 fill-current" viewBox="0 0 24 24"><path d="M11.645 20.91l-.007-.003-.022-.012a15.247 15.247 0 01-.383-.218 25.18 25.18 0 01-4.244-3.17C4.688 15.36 2.25 12.174 2.25 8.25 2.25 5.322 4.714 3 7.688 3A5.5 5.5 0 0112 5.052 5.5 5.5 0 0116.313 3c2.973 0 5.437 2.322 5.437 5.25 0 3.925-2.438 7.111-4.739 9.256a25.175 25.175 0 01-4.244 3.17 15.247 15.247 0 01-.383.219l-.022.012-.007.004-.003.001a.752.752 0 01-.704 0l-.003-.001z"/></svg>
                                </button>
                            </form>
                        </div>
                        <div class="p-5">
                            <h3 class="font-semibold text-gray-900 truncate">{{ $item->course?->title ?? 'Course unavailable' }}</h3>
                            <p class="text-sm text-gray-500 mt-1 mb-4">{{ $item->course?->instructor?->name }} · ${{ number_format($item->course?->price ?? 0, 2) }}</p>
                            @if($item->course)
                                <a href="{{ route('courses.show', $item->course->slug) }}" class="block text-center px-4 py-2 bg-primary-600 hover:bg-primary-700 text-white text-sm font-medium rounded-lg transition">View Course</a>
                            @endif
                        </div>
                    </div>
                @empty
                    <div class="sm:col-span-2 lg:col-span-3 bg-white rounded-xl border border-dashed border-gray-200 p-12 text-center">
                        <svg class="w-14 h-14 mx-auto mb-4 text-gray-300" fill="currentColor" viewBox="0 0 24 24"><path d="M11.645 20.91l-.007-.003-.022-.012a15.247 15.247 0 01-.383-.218 25.18 25.18 0 01-4.244-3.17C4.688 15.36 2.25 12.174 2.25 8.25 2.25 5.322 4.714 3 7.688 3A5.5 5.5 0 0112 5.052 5.5 5.5 0 0116.313 3c2.973 0 5.437 2.322 5.437 5.25 0 3.925-2.438 7.111-4.739 9.256a25.175 25.175 0 01-4.244 3.17 15.247 15.247 0 01-.383.219l-.022.012-.007.004-.003.001a.752.752 0 01-.704 0l-.003-.001z"/></svg>
                        <h3 class="text-lg font-medium text-gray-900 mb-2">Your wishlist is empty</h3>
                        <p class="text-gray-500 mb-6">Tap the ♥ on any course to save it for later.</p>
                        <a href="{{ route('courses.index') }}" class="inline-flex items-center gap-2 px-5 py-2.5 bg-primary-600 hover:bg-primary-700 text-white font-medium rounded-lg transition">Browse Courses</a>
                    </div>
                @endforelse
            </div>
        </div>

        {{-- Recent Activity --}}
        <div x-show="activeTab === 'activity'" x-transition style="display:none;">
            <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-6">
                <ul class="space-y-1">
                    @forelse($recentActivity as $activity)
                        @php
                            $icons = [
                                'enrolled' => ['bg-primary-100 text-primary-600', 'M12 6.042A8.967 8.967 0 006 3.75c-1.052 0-2.062.18-3 .512v14.25A8.987 8.987 0 016 18c2.305 0 4.408.867 6 2.292m0-14.25a8.966 8.966 0 016-2.292c1.052 0 2.062.18 3 .512v14.25A8.987 8.987 0 0018 18a8.967 8.967 0 00-6 2.292m0-14.25v14.25'],
                                'passed' => ['bg-secondary-100 text-secondary-600', 'M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z'],
                                'failed' => ['bg-red-50 text-red-500', 'M12 9v3.75m9.303 3.376c-.866 1.5.217 3.374 1.948 3.374H4.455c-1.73 0-2.813-1.874-1.948-3.374L10.05 3.378c.866-1.5 3.032-1.5 3.898 0l7.355 12.748zM12 15.75h.007v.008H12v-.008z'],
                                'certificate' => ['bg-accent-100 text-accent-700', 'M4.26 10.147a60.436 60.436 0 00-.491 6.347A48.627 48.627 0 0112 20.904a48.627 48.627 0 018.232-4.41 60.46 60.46 0 00-.491-6.347m-15.482 0a50.57 50.57 0 00-2.658-.813A59.905 59.905 0 0112 3.493a59.902 59.902 0 0110.399 5.84c-.896.248-1.783.52-2.658.814m-15.482 0A50.697 50.697 0 0112 13.489a50.702 50.702 0 017.74-3.336M6.75 15a.75.75 0 100-1.5.75.75 0 000 1.5zm0 0v-3.675A55.378 55.378 0 0112 8.443m-7.007 11.55A5.981 5.981 0 006.75 15'],
                                'review' => ['bg-amber-100 text-amber-600', 'M11.48 3.499a.562.562 0 011.04 0l2.125 5.111a.563.563 0 00.475.345l5.518.442c.499.04.701.663.321.988l-4.204 3.602a.563.563 0 00-.182.557l1.285 5.385a.562.562 0 01-.84.61l-4.725-2.885a.563.563 0 00-.586 0L6.982 20.54a.562.562 0 01-.84-.61l1.285-5.386a.562.562 0 00-.182-.557l-4.204-3.602a.563.563 0 01.321-.988l5.518-.442a.563.563 0 00.475-.345L11.48 3.5z'],
                            ];
                            [$iconBg, $iconPath] = $icons[$activity['type']] ?? $icons['enrolled'];
                        @endphp
                        <li class="flex items-start gap-4 py-3 {{ !$loop->last ? 'border-b border-gray-50' : '' }}">
                            <span class="w-9 h-9 rounded-lg {{ $iconBg }} grid place-items-center shrink-0">
                                <svg class="w-4.5 h-4.5 w-[18px]" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="{{ $iconPath }}"/></svg>
                            </span>
                            <div class="min-w-0 flex-1">
                                <p class="text-sm text-gray-700">{!! $activity['text'] !!}</p>
                                <p class="text-xs text-gray-400 mt-0.5">{{ $activity['meta']?->diffForHumans() }}</p>
                            </div>
                            @if($activity['href'])
                                <a href="{{ $activity['href'] }}" class="text-xs font-medium text-primary-600 hover:text-primary-700 shrink-0 mt-1">Open →</a>
                            @endif
                        </li>
                    @empty
                        <li class="py-8 text-center text-sm text-gray-400">No activity yet — enroll in a course to get started!</li>
                    @endforelse
                </ul>
            </div>
        </div>
    </div>
@endsection
