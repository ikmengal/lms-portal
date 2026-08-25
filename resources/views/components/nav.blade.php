{{-- Top Bar --}}
<div class="bg-primary-950 text-white text-xs py-2">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 flex justify-between items-center">
        <div class="flex items-center gap-4">
            @if(!empty($site['contact_phone']))
                <span class="hidden sm:inline">Call us: {{ $site['contact_phone'] }}</span>
                <span class="hidden sm:inline">|</span>
            @endif
            @if(!empty($site['support_email']))
                <span class="hidden sm:inline">Email: {{ $site['support_email'] }}</span>
            @endif
        </div>
        <div class="flex items-center gap-4">
            <a href="{{ route('pricing') }}" class="hover:text-accent-400 transition">Enterprise Training</a>
            <a href="{{ route('instructors') }}" class="hover:text-accent-400 transition">Become an Instructor</a>
        </div>
    </div>
</div>

{{-- Main Navigation --}}
<header class="bg-white shadow-sm sticky top-0 z-50" x-data="{ mobileOpen: false, searchOpen: false }">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex items-center justify-between h-16 lg:h-18">

            {{-- Logo --}}
            <a href="{{ route('home') }}" class="flex items-center gap-2 shrink-0">
                @if(!empty($site['logo']))
                    <img src="{{ asset('storage/' . $site['logo']) }}" alt="{{ $site['site_name'] ?? 'LMS Portal' }}" class="h-9 w-auto max-w-[180px] object-contain">
                @else
                    <div class="w-9 h-9 bg-gradient-to-br from-primary-600 to-primary-800 rounded-lg flex items-center justify-center">
                        <svg class="w-5 h-5 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                        </svg>
                    </div>
                @endif
                @if(empty($site['logo']))
                    <span class="text-xl font-bold text-primary-900 hidden sm:block">{{ $site['site_name'] ?? 'LMS Portal' }}</span>
                @endif
            </a>

            {{-- Desktop Nav Links --}}
            <nav class="hidden lg:flex items-center gap-1">
                <a href="{{ route('home') }}" class="px-3 py-2 text-sm font-medium rounded-lg hover:bg-primary-50 hover:text-primary-700 transition {{ request()->routeIs('home') ? 'text-primary-700 bg-primary-50' : 'text-gray-700' }}">Home</a>

                {{-- Categories Dropdown --}}
                <div class="relative" x-data="{ open: false }" @mouseenter="open = true" @mouseleave="open = false">
                    <button class="px-3 py-2 text-sm font-medium rounded-lg hover:bg-primary-50 hover:text-primary-700 transition text-gray-700 flex items-center gap-1">
                        Categories
                        <svg class="w-4 h-4 transition" :class="open && 'rotate-180'" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                    </button>
                        <div x-show="open" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 translate-y-1" x-transition:enter-end="opacity-100 translate-y-0" x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100 translate-y-0" x-transition:leave-end="opacity-0 translate-y-1" class="absolute top-full left-0 w-64 bg-white rounded-xl shadow-xl border border-gray-100 py-2 mt-1 max-h-[420px] overflow-y-auto" style="display:none;">
                            @foreach(\App\Models\CourseCategory::where('is_active', true)->withCount(['courses as courses_count' => fn ($q) => $q->whereNull('deleted_at')])->orderByDesc('courses_count')->take(7)->get() as $navCat)
                                <a href="{{ route('courses.index', ['category' => $navCat->slug]) }}" class="flex items-center gap-3 px-4 py-2.5 text-sm text-gray-700 hover:bg-primary-50 hover:text-primary-700 transition">
                                    <span class="w-8 h-8 bg-primary-50 rounded-lg flex items-center justify-center text-primary-600 text-[10px] font-bold">{{ collect(explode(' ', $navCat->name))->map(fn ($w) => mb_substr($w, 0, 1))->take(2)->implode('') }}</span>
                                    {{ $navCat->name }}
                                </a>
                            @endforeach
                            <div class="border-t border-gray-100 mt-1 pt-1">
                                <a href="{{ route('categories') }}" class="block px-4 py-2.5 text-sm font-medium text-primary-600 hover:bg-primary-50 transition">View All Categories &rarr;</a>
                            </div>
                        </div>
                </div>

                <a href="{{ route('courses.index') }}" class="px-3 py-2 text-sm font-medium rounded-lg hover:bg-primary-50 hover:text-primary-700 transition {{ request()->routeIs('courses.*') ? 'text-primary-700 bg-primary-50' : 'text-gray-700' }}">Courses</a>
                <a href="{{ route('instructors') }}" class="px-3 py-2 text-sm font-medium rounded-lg hover:bg-primary-50 hover:text-primary-700 transition {{ request()->routeIs('instructors*') ? 'text-primary-700 bg-primary-50' : 'text-gray-700' }}">Instructors</a>
                <a href="{{ route('pricing') }}" class="px-3 py-2 text-sm font-medium rounded-lg hover:bg-primary-50 hover:text-primary-700 transition {{ request()->routeIs('pricing') ? 'text-primary-700 bg-primary-50' : 'text-gray-700' }}">Pricing</a>
                <a href="{{ route('blog') }}" class="px-3 py-2 text-sm font-medium rounded-lg hover:bg-primary-50 hover:text-primary-700 transition {{ request()->routeIs('blog*') ? 'text-primary-700 bg-primary-50' : 'text-gray-700' }}">Blog</a>
                <a href="{{ route('about') }}" class="px-3 py-2 text-sm font-medium rounded-lg hover:bg-primary-50 hover:text-primary-700 transition {{ request()->routeIs('about') ? 'text-primary-700 bg-primary-50' : 'text-gray-700' }}">About</a>
                <a href="{{ route('contact') }}" class="px-3 py-2 text-sm font-medium rounded-lg hover:bg-primary-50 hover:text-primary-700 transition {{ request()->routeIs('contact') ? 'text-primary-700 bg-primary-50' : 'text-gray-700' }}">Contact</a>
            </nav>

            {{-- Right Side --}}
            <div class="flex items-center gap-3">
                {{-- Search --}}
                <button @click="searchOpen = !searchOpen" class="p-2 text-gray-500 hover:text-primary-600 hover:bg-primary-50 rounded-lg transition">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                </button>

                {{-- Auth Buttons / User Menu --}}
                @auth
                    {{-- Cart --}}
                    <a href="{{ route('cart.index') }}" class="relative p-2 text-gray-500 hover:text-primary-600 hover:bg-primary-50 rounded-lg transition" title="Cart">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 3h1.386c.51 0 .955.343 1.087.835l.383 1.437M7.5 14.25a3 3 0 00-3 3h15.75m-12.75-3h11.218c1.121-2.3 2.1-4.684 2.924-7.138a60.114 60.114 0 00-16.536-1.84M7.5 14.25L5.106 5.272M6 20.25a.75.75 0 11-1.5 0 .75.75 0 011.5 0zm12.75 0a.75.75 0 11-1.5 0 .75.75 0 011.5 0z"/></svg>
                        @if(count(session('cart', [])))
                            <span class="absolute -top-0.5 -right-0.5 w-4 h-4 bg-accent-500 text-white text-[10px] font-bold rounded-full grid place-items-center">{{ count(session('cart', [])) }}</span>
                        @endif
                    </a>

                    {{-- User Dropdown --}}
                    <div class="relative" x-data="{ open: false }">
                        <button @click="open = !open" class="flex items-center gap-2 px-3 py-1.5 rounded-lg hover:bg-gray-100 transition">
                            <div class="w-8 h-8 bg-primary-600 rounded-full flex items-center justify-center text-white text-xs font-bold">{{ auth()->user()->initials }}</div>
                            <span class="hidden sm:block text-sm font-medium text-gray-700 max-w-[120px] truncate">{{ auth()->user()->name }}</span>
                            <svg class="w-4 h-4 text-gray-400 transition" :class="open && 'rotate-180'" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                        </button>
                        <div x-show="open" @click.away="open = false" x-transition class="absolute top-full right-0 w-56 bg-white rounded-xl shadow-xl border border-gray-100 py-2 mt-1" style="display:none;">
                            <div class="px-4 py-3 border-b border-gray-100">
                                <p class="text-sm font-semibold text-gray-900 truncate">{{ auth()->user()->name }}</p>
                                <p class="text-xs text-gray-500 truncate">{{ auth()->user()->email }}</p>
                                <span class="inline-block mt-1 px-2 py-0.5 bg-primary-100 text-primary-700 text-xs font-medium rounded capitalize">{{ auth()->user()->getRoleNames()->first() ?? 'User' }}</span>
                            </div>
                            <a href="{{ route('dashboard') }}" class="flex items-center gap-3 px-4 py-2.5 text-sm text-gray-700 hover:bg-primary-50 hover:text-primary-700 transition">
                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zm10 0a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zm10 0a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"/></svg>
                                Dashboard
                            </a>
                            <a href="{{ route('profile.edit') }}" class="flex items-center gap-3 px-4 py-2.5 text-sm text-gray-700 hover:bg-primary-50 hover:text-primary-700 transition">
                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                                My Profile
                            </a>
                            <div class="border-t border-gray-100 mt-1 pt-1">
                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <button type="submit" class="w-full flex items-center gap-3 px-4 py-2.5 text-sm text-red-600 hover:bg-red-50 transition">
                                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
                                        Log Out
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                @else
                    <a href="{{ route('cart.index') }}" class="relative p-2 text-gray-500 hover:text-primary-600 hover:bg-primary-50 rounded-lg transition" title="Cart">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 3h1.386c.51 0 .955.343 1.087.835l.383 1.437M7.5 14.25a3 3 0 00-3 3h15.75m-12.75-3h11.218c1.121-2.3 2.1-4.684 2.924-7.138a60.114 60.114 0 00-16.536-1.84M7.5 14.25L5.106 5.272M6 20.25a.75.75 0 11-1.5 0 .75.75 0 011.5 0zm12.75 0a.75.75 0 11-1.5 0 .75.75 0 011.5 0z"/></svg>
                        @if(count(session('cart', [])))
                            <span class="absolute -top-0.5 -right-0.5 w-4 h-4 bg-accent-500 text-white text-[10px] font-bold rounded-full grid place-items-center">{{ count(session('cart', [])) }}</span>
                        @endif
                    </a>
                    <a href="{{ route('login') }}" class="hidden sm:inline-flex items-center px-4 py-2 text-sm font-medium text-primary-700 rounded-lg hover:bg-primary-50 transition">Log In</a>
                    <a href="{{ route('register') }}" class="hidden sm:inline-flex items-center px-4 py-2 text-sm font-medium text-white bg-primary-600 rounded-lg hover:bg-primary-700 transition shadow-sm">Sign Up Free</a>
                @endauth

                {{-- Mobile Menu Button --}}
                <button @click="mobileOpen = !mobileOpen" class="lg:hidden p-2 text-gray-500 hover:text-primary-600 hover:bg-primary-50 rounded-lg transition">
                    <svg x-show="!mobileOpen" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h16"/></svg>
                    <svg x-show="mobileOpen" style="display:none" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>
        </div>
    </div>

    {{-- Search Overlay --}}
    <div x-show="searchOpen" x-transition class="absolute top-full left-0 right-0 bg-white border-t border-gray-100 shadow-lg" style="display:none;">
        <div class="max-w-3xl mx-auto px-4 py-6">
            <div class="relative">
                <svg class="absolute left-4 top-1/2 -translate-y-1/2 w-5 h-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                <input type="text" placeholder="Search courses, topics, instructors..." class="w-full pl-12 pr-4 py-3 text-lg border-2 border-primary-200 rounded-xl focus:border-primary-500 focus:ring-0 outline-none transition" autofocus>
            </div>
            <div class="mt-4 flex flex-wrap gap-2">
                <span class="text-xs text-gray-500">Popular:</span>
                <a href="#" class="text-xs px-3 py-1 bg-gray-100 text-gray-600 rounded-full hover:bg-primary-50 hover:text-primary-700 transition">Python</a>
                <a href="#" class="text-xs px-3 py-1 bg-gray-100 text-gray-600 rounded-full hover:bg-primary-50 hover:text-primary-700 transition">Machine Learning</a>
                <a href="#" class="text-xs px-3 py-1 bg-gray-100 text-gray-600 rounded-full hover:bg-primary-50 hover:text-primary-700 transition">AWS</a>
                <a href="#" class="text-xs px-3 py-1 bg-gray-100 text-gray-600 rounded-full hover:bg-primary-50 hover:text-primary-700 transition">PMP</a>
                <a href="#" class="text-xs px-3 py-1 bg-gray-100 text-gray-600 rounded-full hover:bg-primary-50 hover:text-primary-700 transition">DevOps</a>
            </div>
        </div>
    </div>

    {{-- Mobile Menu --}}
    <div x-show="mobileOpen" x-transition class="lg:hidden border-t border-gray-100" style="display:none;">
        <div class="max-w-7xl mx-auto px-4 py-4 space-y-1">
            <a href="{{ route('home') }}" class="block px-4 py-2.5 text-sm font-medium text-gray-700 hover:bg-primary-50 hover:text-primary-700 rounded-lg transition">Home</a>
            <a href="{{ route('categories') }}" class="block px-4 py-2.5 text-sm font-medium text-gray-700 hover:bg-primary-50 hover:text-primary-700 rounded-lg transition">Categories</a>
            <a href="{{ route('courses.index') }}" class="block px-4 py-2.5 text-sm font-medium text-gray-700 hover:bg-primary-50 hover:text-primary-700 rounded-lg transition">All Courses</a>
            <a href="{{ route('instructors') }}" class="block px-4 py-2.5 text-sm font-medium text-gray-700 hover:bg-primary-50 hover:text-primary-700 rounded-lg transition">Instructors</a>
            <a href="{{ route('pricing') }}" class="block px-4 py-2.5 text-sm font-medium text-gray-700 hover:bg-primary-50 hover:text-primary-700 rounded-lg transition">Pricing</a>
            <a href="{{ route('blog') }}" class="block px-4 py-2.5 text-sm font-medium text-gray-700 hover:bg-primary-50 hover:text-primary-700 rounded-lg transition">Blog</a>
            <a href="{{ route('certificates.index') }}" class="block px-4 py-2.5 text-sm font-medium text-gray-700 hover:bg-primary-50 hover:text-primary-700 rounded-lg transition">Verify Certificate</a>
            <a href="{{ route('faq') }}" class="block px-4 py-2.5 text-sm font-medium text-gray-700 hover:bg-primary-50 hover:text-primary-700 rounded-lg transition">FAQ</a>
            <a href="{{ route('about') }}" class="block px-4 py-2.5 text-sm font-medium text-gray-700 hover:bg-primary-50 hover:text-primary-700 rounded-lg transition">About</a>
            <a href="{{ route('contact') }}" class="block px-4 py-2.5 text-sm font-medium text-gray-700 hover:bg-primary-50 hover:text-primary-700 rounded-lg transition">Contact</a>
            <div class="border-t border-gray-100 pt-3 mt-3 space-y-2">
                @auth
                    <div class="px-4 py-2 text-center">
                        <div class="w-10 h-10 bg-primary-600 rounded-full flex items-center justify-center text-white text-sm font-bold mx-auto mb-2">{{ auth()->user()->initials }}</div>
                        <p class="text-sm font-semibold text-gray-900">{{ auth()->user()->name }}</p>
                        <span class="inline-block px-2 py-0.5 bg-primary-100 text-primary-700 text-xs font-medium rounded capitalize mt-1">{{ auth()->user()->getRoleNames()->first() ?? 'User' }}</span>
                    </div>
                    <a href="{{ route('dashboard') }}" class="block px-4 py-2.5 text-sm font-medium text-primary-700 bg-primary-50 rounded-lg transition text-center">Dashboard</a>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="w-full px-4 py-2.5 text-sm font-medium text-center text-red-600 bg-red-50 rounded-lg transition">Log Out</button>
                    </form>
                @else
                    <a href="{{ route('login') }}" class="block px-4 py-2.5 text-sm font-medium text-center text-primary-700 bg-primary-50 rounded-lg transition">Log In</a>
                    <a href="{{ route('register') }}" class="block px-4 py-2.5 text-sm font-medium text-center text-white bg-primary-600 rounded-lg transition">Sign Up Free</a>
                @endauth
            </div>
        </div>
    </div>
</header>
