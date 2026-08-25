@extends('layouts.app')
@section('title', 'About Us')
@section('content')
    {{-- Hero --}}
    <section class="bg-gradient-to-r from-primary-900 to-primary-800 py-20">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
            <h1 class="text-4xl md:text-5xl font-bold text-white mb-4">About LMS Portal</h1>
            <p class="text-lg text-primary-200 max-w-2xl mx-auto">Empowering millions of learners worldwide with industry-relevant skills and globally recognized certifications.</p>
        </div>
    </section>

    {{-- Mission --}}
    <section class="py-20 bg-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid lg:grid-cols-2 gap-16 items-center">
                <div>
                    <span class="text-sm font-semibold text-primary-600 uppercase tracking-wider">Our Mission</span>
                    <h2 class="text-3xl font-bold text-gray-900 mt-2 mb-6">Making Quality Education Accessible to Everyone</h2>
                    <div class="space-y-4 text-gray-600 leading-relaxed">
                        <p>Founded in 2020, LMS Portal has grown to become one of the leading online learning platforms, serving over 10,000 students across 150+ countries. Our mission is to bridge the skills gap in the technology industry by providing world-class education at affordable prices.</p>
                        <p>We partner with industry leaders and expert instructors to create courses that are not just theoretically sound, but practically relevant. Every course is designed with input from industry professionals to ensure our learners gain skills that employers actually need.</p>
                    </div>
                </div>
                <div class="bg-gradient-to-br from-primary-50 to-primary-100 rounded-2xl p-10">
                    <div class="grid grid-cols-2 gap-8">
                        <div class="text-center">
                            <div class="text-4xl font-bold text-primary-700 mb-1">10K+</div>
                            <p class="text-sm text-gray-600">Active Learners</p>
                        </div>
                        <div class="text-center">
                            <div class="text-4xl font-bold text-primary-700 mb-1">150+</div>
                            <p class="text-sm text-gray-600">Countries</p>
                        </div>
                        <div class="text-center">
                            <div class="text-4xl font-bold text-primary-700 mb-1">500+</div>
                            <p class="text-sm text-gray-600">Courses</p>
                        </div>
                        <div class="text-center">
                            <div class="text-4xl font-bold text-primary-700 mb-1">200+</div>
                            <p class="text-sm text-gray-600">Expert Instructors</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- Values --}}
    <section class="py-20 bg-gray-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-14">
                <span class="text-sm font-semibold text-primary-600 uppercase tracking-wider">Our Values</span>
                <h2 class="text-3xl font-bold text-gray-900 mt-2">What Drives Us</h2>
            </div>
            <div class="grid md:grid-cols-3 gap-8">
                @foreach([
                    ['title' => 'Quality First', 'desc' => 'Every course undergoes rigorous review to ensure it meets our high standards of content quality, accuracy, and practical relevance.', 'color' => 'primary'],
                    ['title' => 'Learner-Centric', 'desc' => 'We put learners at the center of everything we do. From course design to support, every decision is made with our students in mind.', 'color' => 'accent'],
                    ['title' => 'Innovation', 'desc' => 'We continuously evolve our platform and teaching methods to stay at the forefront of online education technology.', 'color' => 'secondary'],
                ] as $value)
                    <div class="bg-white p-8 rounded-2xl shadow-sm border border-gray-100">
                        <div class="w-12 h-12 bg-{{ $value['color'] }}-100 rounded-xl flex items-center justify-center mb-5">
                            <div class="w-3 h-3 bg-{{ $value['color'] }}-500 rounded-full"></div>
                        </div>
                        <h3 class="font-bold text-gray-900 text-lg mb-3">{{ $value['title'] }}</h3>
                        <p class="text-gray-600 text-sm leading-relaxed">{{ $value['desc'] }}</p>
                    </div>
                @endforeach
            </div>
        </div>
    </section>

    {{-- Team --}}
    <section class="py-20 bg-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-14">
                <span class="text-sm font-semibold text-primary-600 uppercase tracking-wider">Our Team</span>
                <h2 class="text-3xl font-bold text-gray-900 mt-2">Meet the Leadership</h2>
            </div>
            <div class="grid sm:grid-cols-2 lg:grid-cols-4 gap-8">
                @foreach([
                    ['name' => 'David Chen', 'role' => 'CEO & Founder', 'bg' => 'bg-primary-600'],
                    ['name' => 'Sarah Williams', 'role' => 'Head of Content', 'bg' => 'bg-accent-600'],
                    ['name' => 'Michael Torres', 'role' => 'CTO', 'bg' => 'bg-secondary-600'],
                    ['name' => 'Emily Zhang', 'role' => 'Head of Learning', 'bg' => 'bg-purple-600'],
                ] as $member)
                    <div class="text-center group">
                        <div class="w-28 h-28 {{ $member['bg'] }} rounded-2xl mx-auto mb-4 flex items-center justify-center text-white text-3xl font-bold group-hover:scale-105 transition-transform">
                            {{ strtoupper(substr($member['name'], 0, 2)) }}
                        </div>
                        <h3 class="font-bold text-gray-900">{{ $member['name'] }}</h3>
                        <p class="text-sm text-gray-500">{{ $member['role'] }}</p>
                    </div>
                @endforeach
            </div>
        </div>
    </section>
@endsection
