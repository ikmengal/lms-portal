<footer class="bg-gray-900 text-gray-300">
    {{-- Main Footer --}}
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16">
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-10">

            {{-- Brand --}}
            <div class="lg:col-span-1">
                <a href="{{ route('home') }}" class="flex items-center gap-2 mb-4">
                    @if(!empty($site['dark_logo']))
                        <img src="{{ asset('assets/upload/' . $site['dark_logo']) }}" alt="{{ $site['site_name'] ?? 'LMS Portal' }}" class="h-9 w-auto max-w-[180px] object-contain">
                    @elseif(!empty($site['logo']))
                        <img src="{{ asset('assets/upload/' . $site['logo']) }}" alt="{{ $site['site_name'] ?? 'LMS Portal' }}" class="h-9 w-auto max-w-[180px] object-contain">
                    @else
                        <div class="w-9 h-9 bg-gradient-to-br from-primary-500 to-primary-700 rounded-lg flex items-center justify-center">
                            <svg class="w-5 h-5 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                            </svg>
                        </div>
                        <span class="text-xl font-bold text-white">{{ $site['site_name'] ?? 'LMS Portal' }}</span>
                    @endif
                </a>
                <p class="text-sm text-gray-400 leading-relaxed mb-6">
                    {{ $site['footer_description'] ?? 'Empowering millions of learners worldwide with industry-relevant courses and certifications from expert instructors.' }}
                </p>
                <div class="flex gap-3">
                    @if(!empty($site['facebook_url']))
                        <a href="{{ $site['facebook_url'] }}" target="_blank" rel="noopener" class="w-9 h-9 bg-gray-800 hover:bg-primary-600 rounded-lg flex items-center justify-center transition">
                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M24 4.557c-.883.392-1.832.656-2.828.775 1.017-.609 1.798-1.574 2.165-2.724-.951.564-2.005.974-3.127 1.195-.897-.957-2.178-1.555-3.594-1.555-3.179 0-5.515 2.966-4.797 6.045-4.091-.205-7.719-2.165-10.148-5.144-1.29 2.213-.669 5.108 1.523 6.574-.806-.026-1.566-.247-2.229-.616-.054 2.281 1.581 4.415 3.949 4.89-.693.188-1.452.232-2.224.084.626 1.956 2.444 3.379 4.6 3.419-2.07 1.623-4.678 2.348-7.29 2.04 2.179 1.397 4.768 2.212 7.548 2.212 9.142 0 14.307-7.721 13.995-14.646.962-.695 1.797-1.562 2.457-2.549z"/></svg>
                        </a>
                    @endif
                    @if(!empty($site['twitter_url']))
                        <a href="{{ $site['twitter_url'] }}" target="_blank" rel="noopener" class="w-9 h-9 bg-gray-800 hover:bg-primary-600 rounded-lg flex items-center justify-center transition">
                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M18.244 2.25h3.308l-7.227 8.26 8.502 11.24H16.17l-5.214-6.817L4.99 21.75H1.68l7.73-8.835L1.254 2.25H8.08l4.713 6.231zm-1.161 17.52h1.833L7.084 4.126H5.117z"/></svg>
                        </a>
                    @endif
                    @if(!empty($site['instagram_url']))
                        <a href="{{ $site['instagram_url'] }}" target="_blank" rel="noopener" class="w-9 h-9 bg-gray-800 hover:bg-primary-600 rounded-lg flex items-center justify-center transition">
                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M12 0C5.374 0 0 5.373 0 12c0 5.302 3.438 9.8 8.207 11.387.599.111.793-.261.793-.577v-2.234c-3.338.726-4.033-1.416-4.033-1.416-.546-1.387-1.333-1.756-1.333-1.756-1.089-.745.083-.729.083-.729 1.205.084 1.839 1.237 1.839 1.237 1.07 1.834 2.807 1.304 3.492.997.107-.775.418-1.305.762-1.604-2.665-.305-5.467-1.334-5.467-5.931 0-1.311.469-2.381 1.236-3.221-.124-.303-.535-1.524.117-3.176 0 0 1.008-.322 3.301 1.23A11.509 11.509 0 0112 5.803c1.02.005 2.047.138 3.006.404 2.291-1.552 3.297-1.23 3.297-1.23.653 1.653.242 2.874.118 3.176.77.84 1.235 1.911 1.235 3.221 0 4.609-2.807 5.624-5.479 5.921.43.372.823 1.102.823 2.222v3.293c0 .319.192.694.801.576C20.566 21.797 24 17.3 24 12c0-6.627-5.373-12-12-12z"/></svg>
                        </a>
                    @endif
                    @if(!empty($site['linkedin_url']))
                        <a href="{{ $site['linkedin_url'] }}" target="_blank" rel="noopener" class="w-9 h-9 bg-gray-800 hover:bg-primary-600 rounded-lg flex items-center justify-center transition">
                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M20.447 20.452h-3.554v-5.569c0-1.328-.027-3.037-1.852-3.037-1.853 0-2.136 1.445-2.136 2.939v5.667H9.351V9h3.414v1.561h.046c.477-.9 1.637-1.85 3.37-1.85 3.601 0 4.267 2.37 4.267 5.455v6.286zM5.337 7.433c-1.144 0-2.063-.926-2.063-2.065 0-1.138.92-2.063 2.063-2.063 1.14 0 2.064.925 2.064 2.063 0 1.139-.925 2.065-2.064 2.065zm1.782 13.019H3.555V9h3.564v11.452zM22.225 0H1.771C.792 0 0 .774 0 1.729v20.542C0 23.227.792 24 1.771 24h20.451C23.2 24 24 23.227 24 22.271V1.729C24 .774 23.2 0 22.222 0h.003z"/></svg>
                        </a>
                    @endif
                    @if(!empty($site['youtube_url']))
                        <a href="{{ $site['youtube_url'] }}" target="_blank" rel="noopener" class="w-9 h-9 bg-gray-800 hover:bg-primary-600 rounded-lg flex items-center justify-center transition">
                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M19.615 3.184c-3.604-.246-11.631-.245-15.23 0-3.897.266-4.356 2.62-4.385 8.816.029 6.185.484 8.549 4.385 8.816 3.6.245 11.626.246 15.23 0 3.897-.266 4.356-2.62 4.385-8.816-.029-6.185-.484-8.549-4.385-8.816zM9 16V8l8 3.993L9 16z"/></svg>
                        </a>
                    @endif
                </div>
            </div>

            {{-- Explore --}}
            <div>
                <h3 class="text-white font-semibold text-sm uppercase tracking-wider mb-4">Explore</h3>
                <ul class="space-y-3">
                    <li><a href="{{ route('categories') }}" class="text-sm hover:text-accent-400 transition">Categories</a></li>
                    <li><a href="{{ route('courses.index') }}" class="text-sm hover:text-accent-400 transition">All Courses</a></li>
                    <li><a href="{{ route('instructors') }}" class="text-sm hover:text-accent-400 transition">Instructors</a></li>
                    <li><a href="{{ route('pricing') }}" class="text-sm hover:text-accent-400 transition">Pricing</a></li>
                    <li><a href="{{ route('certificates.index') }}" class="text-sm hover:text-accent-400 transition">Verify a Certificate</a></li>
                </ul>
            </div>

            {{-- Company --}}
            <div>
                <h3 class="text-white font-semibold text-sm uppercase tracking-wider mb-4">Company</h3>
                <ul class="space-y-3">
                    <li><a href="{{ route('about') }}" class="text-sm hover:text-accent-400 transition">About Us</a></li>
                    <li><a href="{{ route('blog') }}" class="text-sm hover:text-accent-400 transition">Blog</a></li>
                    <li><a href="{{ route('contact') }}" class="text-sm hover:text-accent-400 transition">Contact Us</a></li>
                    <li><a href="{{ route('faq') }}" class="text-sm hover:text-accent-400 transition">FAQs</a></li>
                </ul>

                {{-- Newsletter --}}
                <div class="mt-6">
                    <h4 class="text-white font-semibold text-sm mb-3">Stay Updated</h4>
                    <form class="flex">
                        <input type="email" placeholder="Your email" class="flex-1 px-3 py-2 bg-gray-800 border border-gray-700 rounded-l-lg text-sm text-white placeholder-gray-500 focus:outline-none focus:border-primary-500">
                        <button type="submit" class="px-4 py-2 bg-primary-600 hover:bg-primary-700 text-white text-sm font-medium rounded-r-lg transition">
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    {{-- Bottom Bar --}}
    <div class="border-t border-gray-800">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6 flex flex-col sm:flex-row justify-between items-center gap-4">
            <p class="text-sm text-gray-500">&copy; {{ date('Y') }} {{ $site['site_name'] ?? 'LMS Portal' }}. {{ $site['copyright_text'] ?? 'All rights reserved.' }}</p>
            <div class="flex items-center gap-1 text-sm text-gray-500">
                <span>Trusted by</span>
                <span class="text-white font-semibold">10,000+</span>
                <span>learners worldwide</span>
            </div>
        </div>
    </div>
</footer>
