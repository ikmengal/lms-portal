<!DOCTYPE html>
<html lang="en" class="scroll-smooth">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>@isset($certificate) Certificate Verification - {{ $certificate->code }} @else Certificate Not Found @endisset - LMS Portal</title>
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans antialiased bg-gray-50 text-gray-900 min-h-screen flex flex-col">

        {{-- Header --}}
        <header class="bg-white border-b border-gray-200">
            <div class="max-w-4xl mx-auto flex items-center justify-between h-16 px-4 sm:px-6">
                <a href="{{ route('home') }}" class="flex items-center gap-2">
                    <div class="w-8 h-8 bg-gradient-to-br from-primary-600 to-primary-800 rounded-lg flex items-center justify-center">
                        <svg class="w-4 h-4 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>
                    </div>
                    <span class="text-lg font-bold text-primary-900">LMS<span class="text-accent-500">Portal</span></span>
                </a>
                <a href="{{ route('courses.index') }}" class="inline-flex items-center gap-1.5 px-3 py-1.5 text-sm text-gray-600 hover:text-primary-600 hover:bg-primary-50 rounded-lg transition">
                    Browse Courses
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5L21 12m0 0l-7.5 7.5M21 12H3"/></svg>
                </a>
            </div>
        </header>

        <main class="flex-1 max-w-4xl w-full mx-auto px-4 sm:px-6 py-10">
            @if($certificate)
                {{-- Verified Banner --}}
                <div class="bg-white rounded-xl border border-gray-100 shadow-sm overflow-hidden mb-8">
                    <div class="p-6 sm:p-8 flex flex-col sm:flex-row items-center gap-5 bg-secondary-50/60">
                        <div class="w-16 h-16 bg-secondary-100 rounded-full flex items-center justify-center shrink-0">
                            <svg class="w-9 h-9 text-secondary-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        </div>
                        <div class="text-center sm:text-left">
                            <h1 class="text-2xl font-bold text-gray-900">Certificate Verified</h1>
                            <p class="text-gray-500 mt-1">This certificate is authentic and was issued by {{ $site['site_name'] ?? 'LMS Portal' }}.</p>
                        </div>
                        <div class="sm:ml-auto flex items-center gap-4">
                            <div class="px-4 py-2 bg-secondary-100 text-secondary-800 rounded-lg font-mono text-sm font-semibold">{{ $certificate->code }}</div>
                            <div class="text-center">
                                <div class="bg-white border border-gray-200 rounded-lg p-1.5 shadow-sm">{!! qr_svg($certificate->verificationUrl(), 84) !!}</div>
                                <p class="text-[9px] uppercase tracking-widest text-gray-400 mt-1">Scan to verify</p>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Certificate Holder --}}
                <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-6 sm:p-8 mb-8">
                    <p class="text-xs uppercase tracking-wider text-gray-400 mb-1">This certificate is awarded to</p>
                    <h2 class="text-3xl font-bold text-primary-700">{{ $certificate->user->name }}</h2>
                    <p class="text-gray-500 mt-2">for successfully completing the course</p>
                    <h3 class="text-xl font-semibold text-gray-900 mt-1">{{ $certificate->course->title }}</h3>
                </div>

                {{-- Details Grid --}}
                <div class="grid sm:grid-cols-2 gap-6 mb-8">
                    {{-- Certificate Details --}}
                    <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-6">
                        <h4 class="font-semibold text-gray-900 mb-4 flex items-center gap-2">
                            <svg class="w-5 h-5 text-accent-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M4.26 10.147a60.436 60.436 0 00-.491 6.347A48.627 48.627 0 0112 20.904a48.627 48.627 0 018.232-4.41 60.46 60.46 0 00-.491-6.347m-15.482 0a50.57 50.57 0 00-2.658-.813A59.905 59.905 0 0112 3.493a59.902 59.902 0 0110.399 5.84c-.896.248-1.783.52-2.658.814m-15.482 0A50.697 50.697 0 0112 13.489a50.702 50.702 0 017.74-3.336M6.75 15a.75.75 0 100-1.5.75.75 0 000 1.5zm0 0v-3.675A55.378 55.378 0 0112 8.443m-7.007 11.55A5.981 5.981 0 006.75 15.75v-1.5"/></svg>
                            Certificate Details
                        </h4>
                        <dl class="space-y-3 text-sm">
                            <div class="flex justify-between gap-4">
                                <dt class="text-gray-500">Certificate ID</dt>
                                <dd class="font-mono font-semibold text-gray-900">{{ $certificate->code }}</dd>
                            </div>
                            <div class="flex justify-between gap-4">
                                <dt class="text-gray-500">Issued On</dt>
                                <dd class="font-medium text-gray-900">{{ $certificate->issued_at->format('M d, Y') }}</dd>
                            </div>
                            <div class="flex justify-between gap-4">
                                <dt class="text-gray-500">Completed On</dt>
                                <dd class="font-medium text-gray-900">{{ ($enrollment?->completed_at ?? $certificate->issued_at)->format('M d, Y') }}</dd>
                            </div>
                            <div class="flex justify-between gap-4">
                                <dt class="text-gray-500">Status</dt>
                                <dd><span class="px-2 py-0.5 bg-secondary-100 text-secondary-700 text-xs font-semibold rounded">Valid</span></dd>
                            </div>
                        </dl>
                    </div>

                    {{-- Course Details --}}
                    <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-6">
                        <h4 class="font-semibold text-gray-900 mb-4 flex items-center gap-2">
                            <svg class="w-5 h-5 text-primary-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6.042A8.967 8.967 0 006 3.75c-1.052 0-2.062.18-3 .512v14.25A8.987 8.987 0 016 18c2.305 0 4.408.867 6 2.292m0-14.25a8.966 8.966 0 016-2.292c1.052 0 2.062.18 3 .512v14.25A8.987 8.987 0 0018 18a8.967 8.967 0 00-6 2.292m0-14.25v14.25"/></svg>
                            Course Details
                        </h4>
                        <dl class="space-y-3 text-sm">
                            <div class="flex justify-between gap-4">
                                <dt class="text-gray-500">Instructor</dt>
                                <dd class="font-medium text-gray-900">{{ $certificate->course->instructor->name ?? 'LMS Portal' }}</dd>
                            </div>
                            <div class="flex justify-between gap-4">
                                <dt class="text-gray-500">Category</dt>
                                <dd class="font-medium text-gray-900">{{ $certificate->course->category ?? 'General' }}</dd>
                            </div>
                            <div class="flex justify-between gap-4">
                                <dt class="text-gray-500">Level</dt>
                                <dd class="font-medium text-gray-900">{{ $certificate->course->level ?? 'Beginner' }}</dd>
                            </div>
                            <div class="flex justify-between gap-4">
                                <dt class="text-gray-500">Duration</dt>
                                <dd class="font-medium text-gray-900">{{ $certificate->course->duration_hours ?? 0 }} hours</dd>
                            </div>
                        </dl>
                    </div>
                </div>

                {{-- Course Description --}}
                @if($certificate->course->description)
                    <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-6 sm:p-8 mb-8">
                        <h4 class="font-semibold text-gray-900 mb-3">About This Course</h4>
                        <p class="text-gray-600 leading-relaxed">{{ $certificate->course->description }}</p>
                    </div>
                @endif

                {{-- Note --}}
                <p class="text-xs text-gray-400 text-center">
                    Anyone can verify this certificate by visiting <span class="font-mono">{{ $certificate->verificationUrl() }}</span> or scanning the QR code above.
                </p>
            @else
                {{-- Not Found --}}
                <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-12 text-center max-w-xl mx-auto">
                    <div class="w-16 h-16 bg-red-100 rounded-full flex items-center justify-center mx-auto mb-4">
                        <svg class="w-9 h-9 text-red-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m9-.75a9 9 0 11-18 0 9 9 0 0118 0zm-9 3.75h.008v.008H12v-.008z"/></svg>
                    </div>
                    <h1 class="text-2xl font-bold text-gray-900 mb-2">Certificate Not Found</h1>
                    <p class="text-gray-500 mb-6">We couldn't find a certificate matching this verification link. Please check the certificate ID and try again.</p>
                    <form method="GET" action="{{ url('verify-certificate') }}" onsubmit="event.preventDefault(); window.location = this.action + '/' + encodeURIComponent(this.code.value.trim());" class="flex flex-col sm:flex-row gap-3 justify-center max-w-md mx-auto">
                        <input type="text" name="code" placeholder="Enter certificate ID e.g. LMS-XXXXXXXXXX" required class="flex-1 px-4 py-2.5 border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent font-mono text-sm">
                        <button type="submit" class="px-5 py-2.5 bg-primary-600 hover:bg-primary-700 text-white font-medium rounded-lg transition">Verify</button>
                    </form>
                </div>
            @endif
        </main>

        {{-- Footer --}}
        <footer class="border-t border-gray-200 bg-white">
            <div class="max-w-4xl mx-auto px-4 sm:px-6 py-4 text-center text-xs text-gray-400">
                &copy; {{ date('Y') }} LMS Portal. All rights reserved.
            </div>
        </footer>
    </body>
</html>
