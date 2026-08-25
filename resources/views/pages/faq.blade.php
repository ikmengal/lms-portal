@extends('layouts.app')

@section('title', 'FAQ')

@php
    $groupIcons = [
        'Getting Started' => ['bg-primary-100', 'text-primary-600', 'M5.25 8.25h15m-16.5 7.5h15m-1.8-13.5l-3.9 19.5m-2.1-19.5l-3.9 19.5'],
        'Courses & Learning' => ['bg-accent-100', 'text-accent-600', 'M12 6.042A8.967 8.967 0 006 3.75c-1.052 0-2.062.18-3 .512v14.25A8.987 8.987 0 016 18c2.305 0 4.408.867 6 2.292m0-14.25a8.966 8.966 0 016-2.292c1.052 0 2.062.18 3 .512v14.25A8.987 8.987 0 0018 18a8.967 8.967 0 00-6 2.292m0-14.25v14.25'],
        'Payments & Pricing' => ['bg-secondary-100', 'text-secondary-600', 'M2.25 8.25h19.5M2.25 9h19.5m-16.5 5.25h6m-6 2.25h3m-3.75 3h15a2.25 2.25 0 002.25-2.25V6.75A2.25 2.25 0 0019.5 4.5h-15a2.25 2.25 0 00-2.25 2.25v10.5A2.25 2.25 0 004.5 19.5z'],
        'Certificates' => ['bg-purple-100', 'text-purple-600', 'M4.26 10.147a60.436 60.436 0 00-.491 6.347A48.627 48.627 0 0112 20.904a48.627 48.627 0 018.232-4.41 60.46 60.46 0 00-.491-6.347m-15.482 0a50.57 50.57 0 00-2.658-.813A59.905 59.905 0 0112 3.493a59.902 59.902 0 0110.399 5.84c-.896.248-1.783.52-2.658.814m-15.482 0A50.697 50.697 0 0112 13.489a50.702 50.702 0 017.74-3.342M6.75 15a.75.75 0 100-1.5.75.75 0 000 1.5zm0 0v-3.675A55.378 55.378 0 0112 8.443m-7.007 11.55A5.981 5.981 0 006.75 15.75v-1.5'],
        'Account & Support' => ['bg-blue-100', 'text-blue-600', 'M21.75 6.75v10.5a2.25 2.25 0 01-2.25 2.25h-15a2.25 2.25 0 01-2.25-2.25V6.75m19.5 0A2.25 2.25 0 0019.5 4.5h-15a2.25 2.25 0 00-2.25 2.25m19.5 0v.243a2.25 2.25 0 01-1.07 1.916l-7.5 4.615a2.25 2.25 0 01-2.36 0L3.32 8.91a2.25 2.25 0 01-1.07-1.916V6.75'],
    ];
@endphp

@section('content')
    {{-- Page Header --}}
    <div class="bg-gradient-to-r from-primary-900 to-primary-800 py-14">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
            <span class="inline-flex items-center gap-2 px-4 py-1.5 bg-white/10 border border-white/20 text-accent-300 text-xs font-bold uppercase tracking-widest rounded-full mb-5">Help Center</span>
            <h1 class="text-3xl md:text-4xl font-bold text-white mb-3">Frequently Asked Questions</h1>
            <p class="text-lg text-primary-200 max-w-2xl mx-auto">Everything you need to know about learning on {{ $site['site_name'] ?? 'LMS Portal' }}. Can't find an answer? We're here to help.</p>
        </div>
    </div>

    <section class="py-12 bg-gray-50 min-h-[50vh]" x-data="{
        query: '',
        open: null,
        shown: 0,
        toggle(id) { this.open = this.open === id ? null : id },
        haystack: '',
    }">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

            {{-- Search --}}
            <div class="max-w-2xl mx-auto relative mb-12">
                <svg class="absolute left-5 top-1/2 -translate-y-1/2 w-5 h-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                <input type="text" x-model.debounce.150ms="query" placeholder="Search questions... e.g. refund, certificate, password"
                    class="w-full pl-12 pr-10 py-4 rounded-xl border border-gray-200 bg-white shadow-sm text-gray-900 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-primary-500 transition">
                <button type="button" x-show="query" @click="query = ''" class="absolute right-4 top-1/2 -translate-y-1/2 p-1 text-gray-400 hover:text-gray-600 transition" style="display:none;" aria-label="Clear search">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>

            <div class="grid lg:grid-cols-3 gap-10">
                {{-- FAQ groups --}}
                <div class="lg:col-span-2 space-y-10">
                    @foreach($faqs as $groupName => $groupItems)
                        <div id="faq-{{ $loop->index + 1 }}" class="faq-group scroll-mt-28">
                            <div class="flex items-center gap-3 mb-5">
                                <div class="w-10 h-10 {{ $groupIcons[$groupName][0] ?? 'bg-primary-100' }} rounded-xl grid place-items-center shrink-0">
                                    <svg class="w-5 h-5 {{ $groupIcons[$groupName][1] ?? 'text-primary-600' }}" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="{{ $groupIcons[$groupName][2] ?? $groupIcons['Getting Started'][2] }}"/></svg>
                                </div>
                                <h2 class="text-xl font-bold text-gray-900">{{ $groupName }}</h2>
                            </div>

                            <div class="space-y-3">
                                @foreach($groupItems as $i => $item)
                                    @php
                                        $id = "faq-{$loop->parent->index}-{$i}";
                                        $haystack = strtolower(addslashes($item['q'] . ' ' . strip_tags($item['a'])));
                                    @endphp
                                    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden"
                                         x-data="{ prev: false }"
                                         x-effect="
                                            const v = ('{{ $haystack }}').includes(query.trim().toLowerCase());
                                            if (v) $el.style.removeProperty('display'); else $el.style.display = 'none';
                                            if (v !== prev) { prev = v; shown += v ? 1 : -1 }
                                         "
                                         style="display:none;">
                                        <button type="button" class="w-full flex items-center justify-between gap-4 px-6 py-5 text-left group" @click="toggle('{{ $id }}')">
                                            <span class="font-semibold text-gray-900 group-hover:text-primary-700 transition">{{ $item['q'] }}</span>
                                            <span class="w-8 h-8 shrink-0 rounded-full grid place-items-center transition-all duration-200"
                                                  :class="open === '{{ $id }}' ? 'rotate-180 bg-primary-600 !text-white' : 'bg-gray-100 text-gray-400'">
                                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 8.25l-7.5 7.5-7.5-7.5"/></svg>
                                            </span>
                                        </button>
                                        <div x-show="open === '{{ $id }}'" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 -translate-y-1" x-transition:enter-end="opacity-100 translate-y-0" x-cloak>
                                            <div class="px-6 pb-6 pl-[52px] text-sm text-gray-500 leading-relaxed">{{ $item['a'] }}</div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endforeach

                    {{-- No results --}}
                    <div x-show="query.trim() !== '' && shown === 0" style="display:none;" class="py-16 text-center bg-white rounded-2xl border border-gray-100">
                        <svg class="w-14 h-14 mx-auto text-gray-200 mb-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607z"/></svg>
                        <h3 class="font-semibold text-gray-900 mb-1">No matching questions</h3>
                        <p class="text-sm text-gray-500 mb-5">Try different keywords or reach out to our team directly.</p>
                        <button type="button" @click="query = ''" class="inline-flex items-center px-5 py-2.5 bg-primary-600 hover:bg-primary-700 text-white text-sm font-semibold rounded-lg transition">Show All Questions</button>
                    </div>
                </div>

                {{-- Sidebar --}}
                <aside class="space-y-6">
                    <div class="lg:sticky lg:top-28 space-y-6">
                        {{-- Quick links --}}
                        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6">
                            <h3 class="font-bold text-gray-900 mb-4">Jump to Section</h3>
                            <nav class="space-y-2">
                                @foreach(array_keys($faqs) as $idx => $groupName)
                                    <a href="#faq-{{ $idx + 1 }}" class="flex items-center justify-between px-4 py-2.5 rounded-xl text-sm font-medium text-gray-600 hover:bg-primary-50 hover:text-primary-700 transition">
                                        {{ $groupName }}
                                        <span class="text-xs text-gray-300">{{ count($groupItems) }}</span>
                                    </a>
                                @endforeach
                            </nav>
                        </div>

                        {{-- Still need help --}}
                        <div class="bg-gradient-to-br from-primary-600 to-primary-800 rounded-2xl p-7 text-center">
                            <div class="w-14 h-14 mx-auto bg-white/15 rounded-2xl grid place-items-center mb-4">
                                <svg class="w-7 h-7 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 12.76c0 1.6 1.123 2.994 2.707 3.227 1.087.16 2.185.283 3.293.369V21l4.076-4.076a1.526 1.526 0 011.037-.443 48.282 48.282 0 005.68-.494c1.584-.233 2.707-1.626 2.707-3.228V6.741c0-1.602-1.123-2.995-2.707-3.228A48.394 48.394 0 0012 3c-2.392 0-4.744.175-7.043.513C3.373 3.746 2.25 5.14 2.25 6.741v6.018z"/></svg>
                            </div>
                            <h3 class="text-lg font-bold text-white mb-2">Still Need Help?</h3>
                            <p class="text-sm text-primary-200 mb-5">{{ $totalFaqs }} questions covered — but if yours isn't here, message us and we'll reply within 24 hours.</p>
                            <a href="{{ route('contact') }}" class="inline-flex w-full justify-center items-center px-5 py-3 bg-white text-primary-700 font-bold rounded-xl text-sm transition hover:bg-primary-50">Contact Support</a>
                            <a href="{{ route('certificates.index') }}" class="mt-3 inline-flex w-full justify-center items-center px-5 py-2.5 bg-white/10 border border-white/20 text-white font-semibold rounded-xl text-sm transition hover:bg-white/20">Verify a Certificate</a>
                        </div>

                        {{-- Popular articles --}}
                        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6">
                            <h3 class="font-bold text-gray-900 mb-4">Related Guides</h3>
                            <ul class="space-y-3">
                                @foreach(['10 Study Habits of Highly Successful Online Learners', 'How to Choose the Right Programming Language to Learn First'] as $title)
                                    <li>
                                        <a href="{{ route('blog.show', \Illuminate\Support\Str::slug($title)) }}" class="flex items-center gap-3 text-sm font-medium text-gray-700 hover:text-primary-700 transition group">
                                            <svg class="w-4 h-4 text-gray-300 group-hover:text-primary-600 transition shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5L21 12m0 0l-7.5 7.5M21 12H3"/></svg>
                                            <span>{{ Str::limit($title, 55) }}</span>
                                        </a>
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                </aside>
            </div>
        </div>
    </section>
@endsection
