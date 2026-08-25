@extends('layouts.app')

@section('title', 'Contact Us')

@section('content')
    <div class="bg-gradient-to-r from-primary-900 to-primary-800 py-12">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
            <h1 class="text-3xl font-bold text-white mb-2">Contact Us</h1>
            <p class="text-primary-200">Have a question? We'd love to hear from you.</p>
        </div>
    </div>

    <section class="py-16 bg-gray-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid lg:grid-cols-3 gap-12">

                {{-- Contact Info --}}
                <div class="space-y-8">
                    <div>
                        <h3 class="text-lg font-bold text-gray-900 mb-4">Get in Touch</h3>
                        <p class="text-gray-600 text-sm leading-relaxed">Have questions about our courses, pricing, or anything else? Our team is ready to help — we reply within 24 hours.</p>
                    </div>

                    <div class="space-y-6">
                        <a href="mailto:{{ $site['support_email'] ?? 'support@lmsportal.com' }}" class="flex items-start gap-4 group">
                            <div class="w-10 h-10 bg-primary-100 rounded-lg flex items-center justify-center shrink-0">
                                <svg class="w-5 h-5 text-primary-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M21.75 6.75v10.5a2.25 2.25 0 01-2.25 2.25h-15a2.25 2.25 0 01-2.25-2.25V6.75m19.5 0A2.25 2.25 0 0019.5 4.5h-15a2.25 2.25 0 00-2.25 2.25m19.5 0v.243a2.25 2.25 0 01-1.07 1.916l-7.5 4.615a2.25 2.25 0 01-2.36 0L3.32 8.91a2.25 2.25 0 01-1.07-1.916V6.75"/></svg>
                            </div>
                            <div>
                                <p class="font-semibold text-gray-900 text-sm">Email Us</p>
                                <p class="text-gray-600 text-sm group-hover:text-primary-600 transition">{{ $site['support_email'] ?? 'support@lmsportal.com' }}</p>
                            </div>
                        </a>

                        <div class="flex items-start gap-4">
                            <div class="w-10 h-10 bg-accent-100 rounded-lg flex items-center justify-center shrink-0">
                                <svg class="w-5 h-5 text-accent-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 6.75c0 8.284 6.716 15 15 15h2.25a2.25 2.25 0 002.25-2.25v-1.372c0-.516-.351-.966-.852-1.091l-4.423-1.106c-.44-.11-.902.055-1.173.417l-.97 1.293c-.282.376-.769.542-1.21.38a12.035 12.035 0 01-7.143-7.143c-.162-.441.004-.928.38-1.21l1.293-.97c.363-.271.527-.734.417-1.173L6.963 3.102a1.125 1.125 0 00-1.091-.852H4.5A2.25 2.25 0 002.25 4.5v2.25z"/></svg>
                            </div>
                            <div>
                                <p class="font-semibold text-gray-900 text-sm">Call Us</p>
                                <p class="text-gray-600 text-sm">{{ $site['contact_phone'] ?? '+1 (800) 123-4567' }}</p>
                                <p class="text-gray-400 text-xs">Mon-Fri 9AM-6PM EST</p>
                            </div>
                        </div>

                        <div class="flex items-start gap-4">
                            <div class="w-10 h-10 bg-secondary-100 rounded-lg flex items-center justify-center shrink-0">
                                <svg class="w-5 h-5 text-secondary-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M15 10.5a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 10.5c0 7.142-7.5 11.25-7.5 11.25S4.5 17.642 4.5 10.5a7.5 7.5 0 1115 0z"/></svg>
                            </div>
                            <div>
                                <p class="font-semibold text-gray-900 text-sm">Visit Us</p>
                                <p class="text-gray-600 text-sm">123 Learning Street<br>San Francisco, CA 94102</p>
                            </div>
                        </div>
                    </div>

                    {{-- Quick links --}}
                    <div class="bg-primary-50 rounded-2xl p-6">
                        <h4 class="font-bold text-gray-900 text-sm mb-3">Quick Answers</h4>
                        <ul class="space-y-2.5 text-sm">
                            <li><a href="{{ route('faq') }}" class="flex items-center gap-2 text-primary-700 hover:text-primary-900 transition"><svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5L21 12m0 0l-7.5 7.5M21 12H3"/></svg>Browse the FAQ</a></li>
                            <li><a href="{{ route('certificates.index') }}" class="flex items-center gap-2 text-primary-700 hover:text-primary-900 transition"><svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5L21 12m0 0l-7.5 7.5M21 12H3"/></svg>Verify a certificate</a></li>
                            <li><a href="{{ route('pricing') }}" class="flex items-center gap-2 text-primary-700 hover:text-primary-900 transition"><svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5L21 12m0 0l-7.5 7.5M21 12H3"/></svg>Pricing &amp; plans</a></li>
                        </ul>
                    </div>
                </div>

                {{-- Contact Form --}}
                <div class="lg:col-span-2">
                    <div class="bg-white rounded-2xl shadow-lg p-8">
                        <form method="POST" action="{{ route('contact.submit') }}" class="space-y-5">
                            @csrf

                            <div class="grid sm:grid-cols-2 gap-5">
                                <div>
                                    <label for="name" class="block text-sm font-medium text-gray-700 mb-1.5">Full Name <span class="text-red-400">*</span></label>
                                    <input id="name" type="text" name="name" value="{{ old('name') }}" placeholder="John Doe"
                                        class="w-full px-4 py-3 border {{ $errors->has('name') ? 'border-red-300' : 'border-gray-200' }} rounded-xl text-gray-900 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-primary-500 transition">
                                    @error('name')<p class="mt-1.5 text-xs text-red-500">{{ $message }}</p>@enderror
                                </div>
                                <div>
                                    <label for="email" class="block text-sm font-medium text-gray-700 mb-1.5">Email Address <span class="text-red-400">*</span></label>
                                    <input id="email" type="email" name="email" value="{{ old('email') }}" placeholder="you@example.com"
                                        class="w-full px-4 py-3 border {{ $errors->has('email') ? 'border-red-300' : 'border-gray-200' }} rounded-xl text-gray-900 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-primary-500 transition">
                                    @error('email')<p class="mt-1.5 text-xs text-red-500">{{ $message }}</p>@enderror
                                </div>
                            </div>

                            <div>
                                <label for="subject" class="block text-sm font-medium text-gray-700 mb-1.5">Subject <span class="text-red-400">*</span></label>
                                <select id="subject" name="subject"
                                    class="w-full px-4 py-3 border {{ $errors->has('subject') ? 'border-red-300' : 'border-gray-200' }} rounded-xl text-gray-900 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-primary-500 transition">
                                    <option value="" disabled {{ old('subject') ? '' : 'selected' }}>Select a topic</option>
                                    @foreach(['Course Inquiry', 'Technical Support', 'Billing & Payments', 'Partnership', 'Become an Instructor', 'Other'] as $topic)
                                        <option value="{{ $topic }}" {{ old('subject') === $topic ? 'selected' : '' }}>{{ $topic }}</option>
                                    @endforeach
                                </select>
                                @error('subject')<p class="mt-1.5 text-xs text-red-500">{{ $message }}</p>@enderror
                            </div>

                            <div>
                                <label for="message" class="block text-sm font-medium text-gray-700 mb-1.5">Message <span class="text-red-400">*</span></label>
                                <textarea id="message" rows="5" name="message" placeholder="Tell us how we can help..."
                                    class="w-full px-4 py-3 border {{ $errors->has('message') ? 'border-red-300' : 'border-gray-200' }} rounded-xl text-gray-900 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-primary-500 transition resize-none">{{ old('message') }}</textarea>
                                @error('message')<p class="mt-1.5 text-xs text-red-500">{{ $message }}</p>@enderror
                            </div>

                            <button type="submit" class="px-8 py-3.5 bg-primary-600 hover:bg-primary-700 text-white font-bold rounded-xl transition shadow-lg shadow-primary-600/25 inline-flex items-center gap-2">
                                Send Message
                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6 12L3.269 3.126A59.768 59.768 0 0121.485 12 59.77 59.77 0 013.27 20.876L5.999 12zm0 0h7.5"/></svg>
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
