@extends('layouts.app')

@section('title', 'Certificates')

@section('content')
    {{-- Page Header --}}
    <div class="bg-gradient-to-r from-primary-900 to-primary-800 py-14">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <nav class="flex items-center gap-2 text-sm text-primary-300 mb-6">
                <a href="{{ route('home') }}" class="hover:text-white transition">Home</a>
                <span>/</span>
                <span class="text-white">Certificates</span>
            </nav>
            <div class="grid lg:grid-cols-2 gap-10 items-center">
                <div>
                    <span class="text-sm font-semibold text-accent-400 uppercase tracking-widest">Verified Credentials</span>
                    <h1 class="text-3xl md:text-4xl font-bold text-white mt-2 mb-4">Certificates That Prove Your Skills</h1>
                    <p class="text-lg text-primary-200 max-w-xl">Every certificate we issue carries a unique, tamper-proof verification code. Employers and anyone else can confirm its authenticity in seconds.</p>

                    <ul class="mt-6 space-y-3 text-sm text-primary-100">
                        @foreach([
                            'Unique verification ID on every certificate',
                            'Instant public verification — no account needed',
                            'Issued automatically upon course completion',
                            'Shareable with employers worldwide',
                        ] as $point)
                            <li class="flex items-center gap-2.5">
                                <svg class="w-5 h-5 text-secondary-400 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                {{ $point }}
                            </li>
                        @endforeach
                    </ul>
                </div>

                {{-- Verify form card --}}
                <form method="GET" action="{{ route('certificates.lookup') }}" class="bg-white rounded-2xl shadow-2xl p-7 max-w-md w-full lg:ml-auto">
                    <div class="w-12 h-12 bg-primary-50 rounded-xl grid place-items-center mb-4">
                        <svg class="w-6 h-6 text-primary-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M16.5 18.75h-6m5.25-4.5h-7.5m7.5-4.5h-9m6.75-4.5H6.75A2.25 2.25 0 004.5 6.75v13.5A2.25 2.25 0 009 22.5h10.5a2.25 2.25 0 002.25-2.25V9a2.25 2.25 0 00-.66-1.59l-4.5-4.5A2.25 2.25 0 0015 2.25z"/></svg>
                    </div>
                    <h2 class="text-xl font-bold text-gray-900 mb-1">Verify a Certificate</h2>
                    <p class="text-sm text-gray-500 mb-5">Enter the certificate ID found at the bottom of any {{ $site['site_name'] ?? 'LMS Portal' }} certificate.</p>

                    @if(session('success') || session('error'))
                        <p class="hidden">{{ session('success') }}{{ session('error') }}</p>
                    @endif

                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Certificate ID</label>
                    <input type="text" name="code" placeholder="e.g. LMS-A1B2C3D4E5" required
                        pattern=".{4,}" title="Enter the full certificate ID"
                        class="w-full px-4 py-3 border border-gray-200 rounded-xl font-mono text-sm uppercase placeholder-gray-300 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-primary-500 transition mb-4">

                    <button type="submit" class="w-full inline-flex items-center justify-center gap-2 px-6 py-3.5 bg-primary-600 hover:bg-primary-700 text-white font-bold rounded-xl transition shadow-lg shadow-primary-600/25">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607z"/></svg>
                        Verify Now
                    </button>

                    <p class="mt-4 text-xs text-gray-400 flex items-start gap-1.5">
                        <svg class="w-3.5 h-3.5 mt-0.5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M16.5 10.5V6.75a4.5 4.5 0 10-9 0v3.75m-.75 11.25h10.5a2.25 2.25 0 002.25-2.25v-6.75a2.25 2.25 0 00-2.25-2.25H6.75a2.25 2.25 0 00-2.25 2.25v6.75a2.25 2.25 0 002.25 2.25z"/></svg>
                        Verification is public and does not reveal any personal data beyond the holder's name.
                    </p>
                </form>
            </div>

            {{-- Stats --}}
            <div class="mt-14 grid grid-cols-3 gap-4 max-w-2xl">
                <div class="bg-white/10 backdrop-blur rounded-xl px-4 py-4 text-center">
                    <div class="text-2xl font-bold text-white">{{ number_format($stats['issued']) }}</div>
                    <div class="text-xs text-primary-200 uppercase tracking-wide mt-0.5">Issued</div>
                </div>
                <div class="bg-white/10 backdrop-blur rounded-xl px-4 py-4 text-center">
                    <div class="text-2xl font-bold text-white">{{ number_format($stats['holders']) }}</div>
                    <div class="text-xs text-primary-200 uppercase tracking-wide mt-0.5">Certificate Holders</div>
                </div>
                <div class="bg-white/10 backdrop-blur rounded-xl px-4 py-4 text-center">
                    <div class="text-2xl font-bold text-white">{{ number_format($stats['courses']) }}</div>
                    <div class="text-xs text-primary-200 uppercase tracking-wide mt-0.5">Certifying Courses</div>
                </div>
            </div>
        </div>
    </div>

    {{-- How it works --}}
    <section class="py-16 bg-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-12">
                <span class="text-sm font-semibold text-primary-600 uppercase tracking-wider">How It Works</span>
                <h2 class="text-3xl font-bold text-gray-900 mt-2">From Enrollment to Verified Credential</h2>
            </div>
            <div class="grid md:grid-cols-4 gap-8 relative">
                @foreach([
                    ['step' => '1', 'title' => 'Enroll & Learn', 'desc' => 'Join any course and work through the video lessons and hands-on projects at your own pace.', 'color' => 'primary'],
                    ['step' => '2', 'title' => 'Pass the Final Exam', 'desc' => 'Complete all lessons and pass the course final exam to demonstrate your mastery.', 'color' => 'accent'],
                    ['step' => '3', 'title' => 'Get Certified', 'desc' => 'Your certificate is generated instantly with a unique ID like LMS-XK29FJ41QP.', 'color' => 'secondary'],
                    ['step' => '4', 'title' => 'Verify & Share', 'desc' => 'Anyone can confirm it here using your certificate ID. Share it with pride!', 'color' => 'purple'],
                ] as $i => $item)
                    <div class="relative text-center px-4">
                        @if($i < 3)
                            <div class="hidden md:block absolute top-8 left-[calc(50%+40px)] w-[calc(100%-80px)] h-0.5 bg-gradient-to-r from-primary-200 to-primary-100"></div>
                        @endif
                        <div class="w-16 h-16 mx-auto rounded-2xl grid place-items-center text-white font-extrabold text-xl mb-4 shadow-lg
                            {{ ['bg-primary-500 shadow-primary-500/30', 'bg-accent-500 shadow-accent-500/30', 'bg-secondary-500 shadow-secondary-500/30', 'bg-purple-500 shadow-purple-500/30'][$i] }}">
                            {{ $item['step'] }}
                        </div>
                        <h3 class="font-bold text-gray-900 mb-2">{{ $item['title'] }}</h3>
                        <p class="text-sm text-gray-500 leading-relaxed">{{ $item['desc'] }}</p>
                    </div>
                @endforeach
            </div>
        </div>
    </section>

    {{-- Recently issued --}}
    @if($recent->isNotEmpty())
        <section class="py-16 bg-gray-50">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex items-end justify-between mb-8 flex-wrap gap-3">
                    <div>
                        <span class="text-sm font-semibold text-primary-600 uppercase tracking-wider">Live Feed</span>
                        <h2 class="text-3xl font-bold text-gray-900 mt-2">Recently Issued Certificates</h2>
                    </div>
                    <a href="{{ route('courses.index') }}" class="inline-flex items-center gap-1.5 text-sm font-semibold text-primary-600 hover:text-primary-700 transition">
                        Earn yours — browse courses
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5L21 12m0 0l-7.5 7.5M21 12H3"/></svg>
                    </a>
                </div>

                <div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-6">
                    @foreach($recent as $cert)
                        <a href="{{ route('certificates.verify', $cert->code) }}" class="group block bg-white rounded-2xl border border-gray-100 shadow-sm hover:shadow-lg hover:border-secondary-200 transition-all duration-300 p-6 relative overflow-hidden">
                            <div class="absolute top-0 left-0 right-0 h-1 bg-gradient-to-r from-secondary-400 to-primary-500"></div>
                            <div class="flex items-start justify-between mb-4">
                                <div class="w-12 h-12 bg-secondary-50 rounded-xl grid place-items-center shrink-0">
                                    <svg class="w-6 h-6 text-secondary-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12c0 1.268-.63 2.39-1.593 3.068a3.745 3.745 0 01-1.043 3.296 3.745 3.745 0 01-3.296 1.043A3.745 3.745 0 0112 21c-1.268 0-2.39-.63-3.068-1.593a3.746 3.746 0 01-3.296-1.043 3.745 3.745 0 01-1.043-3.296A3.745 3.745 0 013 12c0-1.268.63-2.39 1.593-3.068a3.745 3.745 0 011.043-3.296 3.746 3.746 0 013.296-1.043A3.746 3.746 0 0112 3c1.268 0 2.39.63 3.068 1.593a3.746 3.746 0 013.296 1.043 3.746 3.746 0 011.043 3.296A3.745 3.745 0 0121 12z"/></svg>
                                </div>
                                <span class="px-2.5 py-1 bg-secondary-50 text-secondary-700 text-[10px] font-bold uppercase tracking-wide rounded-full">Verified</span>
                            </div>
                            <h3 class="font-bold text-gray-900 group-hover:text-primary-700 transition leading-snug line-clamp-2 mb-2 min-h-[2.5rem]">{{ $cert->course?->title ?? 'Course' }}</h3>
                            <p class="text-sm text-gray-500">Awarded to <span class="font-semibold text-gray-700">{{ $cert->user?->name ?? 'Learner' }}</span></p>
                            <div class="mt-4 pt-4 border-t border-gray-50 flex items-center justify-between text-xs text-gray-400">
                                <span>{{ $cert->issued_at->format('M d, Y') }}</span>
                                <span class="font-mono font-semibold text-gray-500 group-hover:text-primary-600 transition">{{ $cert->code }}</span>
                            </div>
                        </a>
                    @endforeach
                </div>
            </div>
        </section>
    @endif

    {{-- CTA --}}
    <section class="py-16 bg-white">
        <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="bg-gradient-to-r from-primary-600 to-primary-800 rounded-3xl px-8 py-12 text-center relative overflow-hidden">
                <div class="absolute inset-0 opacity-10" style="background-image: radial-gradient(circle at 25% 30%, white 2px, transparent 2px); background-size: 32px 32px;"></div>
                <div class="relative">
                    <h2 class="text-2xl md:text-3xl font-bold text-white mb-3">Ready to Earn Yours?</h2>
                    <p class="text-primary-200 max-w-xl mx-auto mb-8">Join thousands of learners who turned course completions into career opportunities with verified certificates.</p>
                    <div class="flex flex-wrap justify-center gap-3">
                        <a href="{{ route('courses.index') }}" class="px-7 py-3.5 bg-white text-primary-700 hover:bg-primary-50 font-bold rounded-xl text-sm transition shadow-lg">Find a Course</a>
                        <a href="{{ route('pricing') }}" class="px-7 py-3.5 bg-primary-700/40 hover:bg-primary-700/60 text-white font-semibold rounded-xl text-sm transition border border-white/20">See Pricing</a>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
