@extends('layouts.app')
@section('title', 'Home')
@section('content')
    {{-- Hero Section --}}
    <section class="relative bg-gradient-to-br from-primary-900 via-primary-800 to-primary-950 overflow-hidden">
        {{-- Background Pattern --}}
        <div class="absolute inset-0 opacity-10">
            <div class="absolute top-20 left-10 w-72 h-72 bg-accent-400 rounded-full blur-3xl"></div>
            <div class="absolute bottom-10 right-20 w-96 h-96 bg-primary-400 rounded-full blur-3xl"></div>
        </div>

        <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-20 lg:py-28">
            <div class="grid lg:grid-cols-2 gap-12 items-center">
                {{-- Left Content --}}
                <div>
                    @if(setting('hero_badge'))
                        <div class="inline-flex items-center gap-2 px-4 py-2 bg-white/10 backdrop-blur-sm rounded-full text-white/90 text-sm font-medium mb-6">
                            <span class="w-2 h-2 bg-secondary-400 rounded-full animate-pulse"></span>
                            {{ setting('hero_badge', '#1 Platform for Professional Learning') }}
                        </div>
                    @endif
                    <h1 class="text-4xl md:text-5xl lg:text-6xl font-bold text-white leading-tight mb-6">
                        {{ setting('hero_title', 'Upskill for the') }}
                        <span class="text-transparent bg-clip-text bg-gradient-to-r from-accent-400 to-accent-300">{{ setting('hero_title_highlight', 'AI-First World') }}</span>
                    </h1>
                    <p class="text-lg text-primary-200 leading-relaxed mb-8 max-w-lg">
                        {{ setting('hero_description', 'Master in-demand skills with industry-relevant courses, hands-on projects, and globally recognized certifications.') }}
                    </p>

                    {{-- Search Bar --}}
                    <form method="GET" action="{{ route('courses.index') }}" class="bg-white rounded-xl p-2 shadow-2xl max-w-xl flex items-center">
                        <svg class="w-5 h-5 text-gray-400 ml-3 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                        <input type="text" name="search" value="{{ request('search') }}" placeholder="What do you want to learn?" class="flex-1 px-4 py-3 text-gray-900 placeholder-gray-500 focus:outline-none">
                        <button type="submit" class="px-6 py-3 bg-primary-600 hover:bg-primary-700 text-white font-semibold rounded-lg transition shadow-lg shadow-primary-600/30 whitespace-nowrap">
                            Search
                        </button>
                    </form>

                    {{-- Popular Searches --}}
                    <div class="mt-4 flex flex-wrap items-center gap-2">
                        <span class="text-sm text-primary-300">Popular:</span>
                        @foreach($categories->take(4) as $cat)
                            <a href="{{ route('courses.index', ['category' => $cat->slug]) }}" class="text-sm px-3 py-1 bg-white/10 text-white/80 rounded-full hover:bg-white/20 transition">{{ $cat->name }}</a>
                        @endforeach
                    </div>
                </div>

                {{-- Right Content - Hero Image or Stats Card --}}
                <div class="hidden lg:block">
                    @if(setting_image('hero_image'))
                        <div class="rounded-2xl overflow-hidden border border-white/10">
                            <img src="{{ setting_image('hero_image') }}" alt="{{ setting('hero_title', 'LMS Portal') }}" class="w-full h-auto object-cover rounded-2xl">
                        </div>
                    @else
                        <div class="bg-white/10 backdrop-blur-sm rounded-2xl p-8 border border-white/10">
                            <div class="grid grid-cols-2 gap-6">
                                <div class="bg-white/10 rounded-xl p-5 text-center">
                                    <div class="text-3xl font-bold text-white mb-1">{{ $stats['courses'] }}</div>
                                    <div class="text-sm text-primary-200">Courses</div>
                                </div>
                                <div class="bg-white/10 rounded-xl p-5 text-center">
                                    <div class="text-3xl font-bold text-white mb-1">{{ $stats['instructors'] }}</div>
                                    <div class="text-sm text-primary-200">Instructors</div>
                                </div>
                                <div class="bg-white/10 rounded-xl p-5 text-center">
                                    <div class="text-3xl font-bold text-white mb-1">{{ number_format($stats['students']) }}</div>
                                    <div class="text-sm text-primary-200">Students</div>
                                </div>
                                <div class="bg-white/10 rounded-xl p-5 text-center">
                                    <div class="text-3xl font-bold text-white mb-1">{{ $stats['avgRating'] ?: '—' }}</div>
                                    <div class="text-sm text-primary-200">Avg Rating</div>
                                </div>
                            </div>
                            <div class="mt-6 flex items-center gap-3">
                                <div class="flex -space-x-2">
                                    <div class="w-8 h-8 bg-accent-400 rounded-full border-2 border-white/20 flex items-center justify-center text-xs font-bold text-white">A</div>
                                    <div class="w-8 h-8 bg-secondary-400 rounded-full border-2 border-white/20 flex items-center justify-center text-xs font-bold text-white">B</div>
                                    <div class="w-8 h-8 bg-purple-400 rounded-full border-2 border-white/20 flex items-center justify-center text-xs font-bold text-white">C</div>
                                    <div class="w-8 h-8 bg-primary-400 rounded-full border-2 border-white/20 flex items-center justify-center text-xs font-bold text-white">+</div>
                                </div>
                                <p class="text-sm text-primary-200">Join 10,000+ learners today</p>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </section>

    {{-- Trusted By / Logos --}}
    <section class="py-12 bg-gray-50 border-b border-gray-100">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <p class="text-center text-sm text-gray-500 font-medium mb-8">Trusted by 2,000+ companies worldwide</p>
            <div class="flex flex-wrap justify-center items-center gap-8 md:gap-14 opacity-50">
                <div class="text-2xl font-bold text-gray-400 tracking-tight">Google</div>
                <div class="text-2xl font-bold text-gray-400 tracking-tight">Microsoft</div>
                <div class="text-2xl font-bold text-gray-400 tracking-tight">Amazon</div>
                <div class="text-2xl font-bold text-gray-400 tracking-tight">Meta</div>
                <div class="text-2xl font-bold text-gray-400 tracking-tight">Apple</div>
                <div class="text-2xl font-bold text-gray-400 tracking-tight">Netflix</div>
                <div class="text-2xl font-bold text-gray-400 tracking-tight">Spotify</div>
            </div>
        </div>
    </section>

    {{-- Categories Section --}}
    <section class="py-20 bg-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-14">
                <span class="text-sm font-semibold text-primary-600 uppercase tracking-wider">Explore</span>
                <h2 class="text-3xl md:text-4xl font-bold text-gray-900 mt-2 mb-4">Browse Top Categories</h2>
                <p class="text-gray-500 max-w-2xl mx-auto">Choose from {{ $stats['courses'] }} courses across the most in-demand tech domains</p>
            </div>

            <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4 md:gap-6">
                @php
                    $colors = ['primary', 'purple', 'accent', 'red', 'secondary', 'yellow', 'indigo', 'accent'];
                @endphp
                @foreach($categories as $i => $cat)
                    <x-category-card
                        :title="$cat->name"
                        :icon="collect(explode(' ', $cat->name))->map(fn ($w) => mb_substr($w, 0, 1))->take(2)->implode('')"
                        :count="$cat->courses_count"
                        :color="$colors[$i % count($colors)]"
                        :slug="route('courses.index', ['category' => $cat->slug])"
                    />
                @endforeach
            </div>

            <div class="text-center mt-10">
                <a href="{{ route('courses.index') }}" class="inline-flex items-center gap-2 px-6 py-3 text-primary-600 font-semibold border-2 border-primary-200 rounded-xl hover:bg-primary-50 transition">
                    View All Categories
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M17 8l4 4m0 0l-4 4m4-4H3"/></svg>
                </a>
            </div>
        </div>
    </section>

    {{-- Featured Courses --}}
    <section class="py-20 bg-gray-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div x-data="{ active: 'all' }">
                <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-12 gap-4">
                    <div>
                        <span class="text-sm font-semibold text-primary-600 uppercase tracking-wider">Top Picks</span>
                        <h2 class="text-3xl md:text-4xl font-bold text-gray-900 mt-2">Featured Courses</h2>
                    </div>
                    <div class="flex gap-2">
                        <button @click="active = 'all'" :class="active === 'all' ? 'bg-primary-600 text-white' : 'bg-white text-gray-600 hover:bg-gray-100'" class="px-4 py-2 text-sm font-medium rounded-lg transition">All</button>
                        <button @click="active = 'trending'" :class="active === 'trending' ? 'bg-primary-600 text-white' : 'bg-white text-gray-600 hover:bg-gray-100'" class="px-4 py-2 text-sm font-medium rounded-lg transition">Trending</button>
                        <button @click="active = 'new'" :class="active === 'new' ? 'bg-primary-600 text-white' : 'bg-white text-gray-600 hover:bg-gray-100'" class="px-4 py-2 text-sm font-medium rounded-lg transition">New</button>
                    </div>
                </div>

                @php
                    $courseSets = [
                        'all' => $featuredCourses,
                        'trending' => $trendingCourses,
                        'new' => $newCourses,
                    ];
                @endphp
                @foreach($courseSets as $setName => $setCourses)
                    <div x-show="active === '{{ $setName }}'" {{ $setName !== 'all' ? 'style=display:none' : '' }}>
                        @if($setCourses->isNotEmpty())
                            <div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-6">
                                @foreach($setCourses as $course)
                                    <x-course-card
                                        :title="$course->title"
                                        :category="$course->category ?? 'General'"
                                        :instructor="$course->instructor?->name ?? 'Instructor'"
                                        :rating="$course->avg_rating ? round((float) $course->avg_rating, 1) : 0"
                                        :reviews="$course->reviews_count"
                                        :students="number_format($course->students_count)"
                                        :duration="$course->duration_hours . ' hours'"
                                        :price="$course->price > 0 ? '$' . number_format($course->price, 2) : 'Free'"
                                        :image="$course->thumbnail ? asset('assets/upload/' . $course->thumbnail) : null"
                                        :level="$course->level ?? 'Beginner'"
                                        :subtitle="$course->subtitle"
                                        :languages="array_merge([$course->language_code], array_keys($course->translations ?? []))"
                                        :bestseller="$course->students_count >= 5"
                                        :comingSoon="$course->unlocks_at && $course->unlocks_at->isFuture()"
                                        :comingSoonDate="$course->unlocks_at?->isFuture() ? $course->unlocks_at->format('M j, Y') : null"
                                        :slug="route('courses.show', $course)"
                                    />
                                @endforeach
                            </div>
                        @else
                            <div class="py-12 text-center text-gray-400 text-sm">No courses in this section yet.</div>
                        @endif
                    </div>
                @endforeach
            </div>

            <div class="text-center mt-12">
                <a href="{{ route('courses.index') }}" class="inline-flex items-center gap-2 px-8 py-3 bg-primary-600 hover:bg-primary-700 text-white font-semibold rounded-xl transition shadow-lg shadow-primary-600/25">
                    Explore All Courses
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M17 8l4 4m0 0l-4 4m4-4H3"/></svg>
                </a>
            </div>
        </div>
    </section>

    {{-- Stats Section --}}
    <x-stats />

    {{-- Learning Paths --}}
    <section class="py-20 bg-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-14">
                <span class="text-sm font-semibold text-primary-600 uppercase tracking-wider">Curated Journeys</span>
                <h2 class="text-3xl md:text-4xl font-bold text-gray-900 mt-2 mb-4">Popular Learning Paths</h2>
                <p class="text-gray-500 max-w-2xl mx-auto">Follow structured learning paths designed by industry experts to advance your career</p>
            </div>

            <div class="grid md:grid-cols-3 gap-6">
                {{-- Path 1 --}}
                <div class="group relative bg-gradient-to-br from-primary-50 to-primary-100 rounded-2xl p-8 border border-primary-100 hover:shadow-xl hover:border-primary-200 transition-all duration-300">
                    <div class="w-14 h-14 bg-primary-600 rounded-xl flex items-center justify-center mb-6 group-hover:scale-110 transition-transform">
                        <svg class="w-7 h-7 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                    </div>
                    <h3 class="text-xl font-bold text-gray-900 mb-3">Data Scientist</h3>
                    <p class="text-sm text-gray-600 mb-4 leading-relaxed">Master Python, Machine Learning, Deep Learning, and Data Visualization to become a complete Data Scientist.</p>
                    <div class="flex items-center gap-4 text-sm text-gray-500 mb-6">
                        <span class="flex items-center gap-1">
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>
                            8 Courses
                        </span>
                        <span>|</span>
                        <span>120+ Hours</span>
                    </div>
                    <a href="#" class="inline-flex items-center gap-2 text-primary-600 font-semibold text-sm group-hover:gap-3 transition-all">
                        Start Learning
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M17 8l4 4m0 0l-4 4m4-4H3"/></svg>
                    </a>
                </div>

                {{-- Path 2 --}}
                <div class="group relative bg-gradient-to-br from-accent-50 to-accent-100 rounded-2xl p-8 border border-accent-100 hover:shadow-xl hover:border-accent-200 transition-all duration-300">
                    <div class="w-14 h-14 bg-accent-600 rounded-xl flex items-center justify-center mb-6 group-hover:scale-110 transition-transform">
                        <svg class="w-7 h-7 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M3 15a4 4 0 004 4h9a5 5 0 10-.1-9.999 5.002 5.002 0 10-9.78 2.096A4.001 4.001 0 003 15z"/></svg>
                    </div>
                    <h3 class="text-xl font-bold text-gray-900 mb-3">Cloud Architect</h3>
                    <p class="text-sm text-gray-600 mb-4 leading-relaxed">Learn AWS, Azure, and GCP cloud services. Design scalable, secure, and highly available architectures.</p>
                    <div class="flex items-center gap-4 text-sm text-gray-500 mb-6">
                        <span class="flex items-center gap-1">
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>
                            6 Courses
                        </span>
                        <span>|</span>
                        <span>90+ Hours</span>
                    </div>
                    <a href="#" class="inline-flex items-center gap-2 text-accent-600 font-semibold text-sm group-hover:gap-3 transition-all">
                        Start Learning
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M17 8l4 4m0 0l-4 4m4-4H3"/></svg>
                    </a>
                </div>

                {{-- Path 3 --}}
                <div class="group relative bg-gradient-to-br from-secondary-50 to-secondary-100 rounded-2xl p-8 border border-secondary-100 hover:shadow-xl hover:border-secondary-200 transition-all duration-300">
                    <div class="w-14 h-14 bg-secondary-600 rounded-xl flex items-center justify-center mb-6 group-hover:scale-110 transition-transform">
                        <svg class="w-7 h-7 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                    </div>
                    <h3 class="text-xl font-bold text-gray-900 mb-3">Cyber Security Expert</h3>
                    <p class="text-sm text-gray-600 mb-4 leading-relaxed">From ethical hacking to penetration testing. Build a strong foundation in information security.</p>
                    <div class="flex items-center gap-4 text-sm text-gray-500 mb-6">
                        <span class="flex items-center gap-1">
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>
                            7 Courses
                        </span>
                        <span>|</span>
                        <span>100+ Hours</span>
                    </div>
                    <a href="#" class="inline-flex items-center gap-2 text-secondary-600 font-semibold text-sm group-hover:gap-3 transition-all">
                        Start Learning
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M17 8l4 4m0 0l-4 4m4-4H3"/></svg>
                    </a>
                </div>
            </div>
        </div>
    </section>

    {{-- Testimonials --}}
    <section class="py-20 bg-gray-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-14">
                <span class="text-sm font-semibold text-primary-600 uppercase tracking-wider">Success Stories</span>
                <h2 class="text-3xl md:text-4xl font-bold text-gray-900 mt-2 mb-4">What Our Learners Say</h2>
                <p class="text-gray-500 max-w-2xl mx-auto">Hear from thousands of professionals who transformed their careers</p>
            </div>

            <div class="grid md:grid-cols-3 gap-6">
                <x-testimonial-card
                    name="Priya Sharma"
                    role="Data Scientist"
                    company="Microsoft"
                    review="This platform completely transformed my career. The Python and ML courses were incredibly well-structured with real-world projects. I went from a beginner to landing my dream job in just 6 months!"
                    :rating="5"
                    course="Python for Data Science"
                />
                <x-testimonial-card
                    name="John David"
                    role="Cloud Architect"
                    company="Amazon"
                    review="The AWS certification prep was spot-on. The hands-on labs and practice exams gave me the confidence to pass on my first attempt. Highly recommend for anyone in cloud computing!"
                    :rating="5"
                    course="AWS Solutions Architect"
                />
                <x-testimonial-card
                    name="Maria Garcia"
                    role="Security Analyst"
                    company="Deloitte"
                    review="Excellent cyber security curriculum. The ethical hacking modules were engaging and practical. The instructors clearly have real industry experience. Worth every penny!"
                    :rating="4"
                    course="Cyber Security Expert"
                />
            </div>
        </div>
    </section>

    {{-- Why Choose Us --}}
    <section class="py-20 bg-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-14">
                <span class="text-sm font-semibold text-primary-600 uppercase tracking-wider">Why LMS Portal</span>
                <h2 class="text-3xl md:text-4xl font-bold text-gray-900 mt-2 mb-4">Why 10,000+ Learners Choose Us</h2>
            </div>

            <div class="grid md:grid-cols-2 lg:grid-cols-4 gap-8">
                <div class="text-center group">
                    <div class="w-16 h-16 bg-primary-100 group-hover:bg-primary-600 rounded-2xl flex items-center justify-center mx-auto mb-5 transition-all duration-300 group-hover:scale-110">
                        <svg class="w-8 h-8 text-primary-600 group-hover:text-white transition-colors" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M4.26 10.147a60.438 60.438 0 0 0-.491 6.347A48.62 48.62 0 0 1 12 20.904a48.62 48.62 0 0 1 8.232-4.41 60.46 60.46 0 0 0-.491-6.347m-15.482 0a50.636 50.636 0 0 0-2.658-.813A59.906 59.906 0 0 1 12 3.493a59.903 59.903 0 0 1 10.399 5.84c-.896.248-1.783.52-2.658.814m-15.482 0A50.717 50.717 0 0 1 12 13.489a50.702 50.702 0 0 1 7.74-3.342M6.75 15a.75.75 0 1 0 0-1.5.75.75 0 0 0 0 1.5Zm0 0v-3.675A55.378 55.378 0 0 1 12 8.443m-7.007 11.55A5.981 5.981 0 0 0 6.75 15.75v-1.5" /></svg>
                    </div>
                    <h3 class="font-bold text-gray-900 mb-2">Expert Instructors</h3>
                    <p class="text-sm text-gray-500 leading-relaxed">Learn from 200+ industry professionals with real-world experience at top tech companies.</p>
                </div>

                <div class="text-center group">
                    <div class="w-16 h-16 bg-accent-100 group-hover:bg-accent-600 rounded-2xl flex items-center justify-center mx-auto mb-5 transition-all duration-300 group-hover:scale-110">
                        <svg class="w-8 h-8 text-accent-600 group-hover:text-white transition-colors" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M11.42 15.17l-5.657-5.657a8.023 8.023 0 01.387-7.539A8 8 0 1118.24 4.414a8.023 8.023 0 01-4.627 2.794l-5.395.94" /><path stroke-linecap="round" stroke-linejoin="round" d="M15 3l-6 6" /></svg>
                    </div>
                    <h3 class="font-bold text-gray-900 mb-2">Hands-On Projects</h3>
                    <p class="text-sm text-gray-500 leading-relaxed">Build real-world projects and portfolio pieces that impress employers.</p>
                </div>

                <div class="text-center group">
                    <div class="w-16 h-16 bg-secondary-100 group-hover:bg-secondary-600 rounded-2xl flex items-center justify-center mx-auto mb-5 transition-all duration-300 group-hover:scale-110">
                        <svg class="w-8 h-8 text-secondary-600 group-hover:text-white transition-colors" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12c0 1.268-.63 2.39-1.593 3.068a3.745 3.745 0 01-1.043 3.296 3.745 3.745 0 01-3.296 1.043A3.745 3.745 0 0112 21c-1.268 0-2.39-.63-3.068-1.593a3.746 3.746 0 01-3.296-1.043 3.745 3.745 0 01-1.043-3.296A3.745 3.745 0 013 12c0-1.268.63-2.39 1.593-3.068a3.745 3.745 0 011.043-3.296 3.746 3.746 0 013.296-1.043A3.746 3.746 0 0112 3c1.268 0 2.39.63 3.068 1.593a3.746 3.746 0 013.296 1.043 3.746 3.746 0 011.043 3.296A3.745 3.745 0 0121 12z" /></svg>
                    </div>
                    <h3 class="font-bold text-gray-900 mb-2">Certifications</h3>
                    <p class="text-sm text-gray-500 leading-relaxed">Earn globally recognized certifications valued by 2,000+ companies.</p>
                </div>

                <div class="text-center group">
                    <div class="w-16 h-16 bg-purple-100 group-hover:bg-purple-600 rounded-2xl flex items-center justify-center mx-auto mb-5 transition-all duration-300 group-hover:scale-110">
                        <svg class="w-8 h-8 text-purple-600 group-hover:text-white transition-colors" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M20.25 14.15v4.25c0 1.094-.787 2.036-1.872 2.18-2.087.277-4.216.42-6.378.42s-4.291-.143-6.378-.42c-1.085-.144-1.872-1.086-1.872-2.18v-4.25m16.5 0a2.18 2.18 0 00.75-1.661V8.706c0-1.081-.768-2.015-1.837-2.175a48.114 48.114 0 00-3.413-.387m4.5 8.006c-.194.165-.42.295-.673.38A23.978 23.978 0 0112 15.75c-2.648 0-5.195-.429-7.577-1.22a2.016 2.016 0 01-.673-.38m0 0A2.18 2.18 0 013 12.489V8.706c0-1.081.768-2.015 1.837-2.175a48.111 48.111 0 013.413-.387m7.5 0V5.25A2.25 2.25 0 0013.5 3h-3a2.25 2.25 0 00-2.25 2.25v.894m7.5 0a48.667 48.667 0 00-7.5 0" /></svg>
                    </div>
                    <h3 class="font-bold text-gray-900 mb-2">Job Assistance</h3>
                    <p class="text-sm text-gray-500 leading-relaxed">Get resume building, interview prep, and job placement support from our career team.</p>
                </div>
            </div>
        </div>
    </section>

    {{-- CTA Section --}}
    <section class="py-20 bg-gradient-to-r from-primary-600 to-primary-800 relative overflow-hidden">
        <div class="absolute inset-0 opacity-10">
            <div class="absolute -top-20 -right-20 w-80 h-80 bg-white rounded-full blur-3xl"></div>
            <div class="absolute -bottom-20 -left-20 w-80 h-80 bg-accent-400 rounded-full blur-3xl"></div>
        </div>
        <div class="relative max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
            <h2 class="text-3xl md:text-4xl font-bold text-white mb-4">Ready to Start Your Learning Journey?</h2>
            <p class="text-lg text-primary-200 mb-8 max-w-2xl mx-auto">Join 10,000+ learners who are already building the skills of tomorrow. Start for free today.</p>
            <div class="flex flex-col sm:flex-row gap-4 justify-center">
                <a href="{{ route('register') }}" class="px-8 py-4 bg-white text-primary-700 font-bold rounded-xl hover:bg-gray-100 transition shadow-xl">
                    Get Started Free
                </a>
                <a href="{{ route('courses.index') }}" class="px-8 py-4 bg-white/10 text-white font-bold rounded-xl border border-white/20 hover:bg-white/20 transition">
                    Browse Courses
                </a>
            </div>
        </div>
    </section>
@endsection
