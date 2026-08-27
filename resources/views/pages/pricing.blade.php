@extends('layouts.app')

@section('title', 'Pricing')

@php
    $check = '<svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5"/></svg>';
    $cross = '<svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>';
@endphp

@section('content')
    <div x-data="{ billing: 'monthly' }">
        {{-- Page Header --}}
        <div class="bg-gradient-to-r from-primary-900 to-primary-800 py-14">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
                <span class="text-sm font-semibold text-accent-400 uppercase tracking-widest">Pricing Plans</span>
                <h1 class="text-3xl md:text-4xl font-bold text-white mt-2 mb-3">Invest in Yourself</h1>
                <p class="text-lg text-primary-200 max-w-2xl mx-auto">Simple, transparent pricing. Start free, upgrade when you're ready — cancel anytime.</p>

                {{-- Billing toggle --}}
                <div class="mt-8 inline-flex items-center bg-white/10 border border-white/20 rounded-full p-1.5">
                    <button type="button" @click="billing = 'monthly'" :class="billing === 'monthly' ? 'bg-white text-primary-700' : 'text-primary-200 hover:text-white'" class="px-5 py-2 rounded-full text-sm font-bold transition">Monthly</button>
                    <button type="button" @click="billing = 'yearly'" :class="billing === 'yearly' ? 'bg-white text-primary-700' : 'text-primary-200 hover:text-white'" class="px-5 py-2 rounded-full text-sm font-bold transition flex items-center gap-2">
                        Yearly
                        <span class="px-2 py-0.5 bg-accent-500 text-white text-[10px] font-bold uppercase rounded-full">Save ~17%</span>
                    </button>
                </div>
            </div>
        </div>

        {{-- Plans --}}
        <section class="py-16 bg-gray-50">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="grid md:grid-cols-3 gap-8 items-stretch max-w-5xl mx-auto">
                    @foreach($plans as $key => $plan)
                        <div class="relative flex flex-col rounded-3xl p-8 transition-all duration-300 {{ $plan['highlighted'] ? 'bg-white shadow-2xl ring-2 ring-primary-600 md:scale-[1.03] z-10' : 'bg-white shadow-sm border border-gray-100 hover:shadow-lg' }}">
                            @if($plan['highlighted'])
                                <span class="absolute -top-4 left-1/2 -translate-x-1/2 px-4 py-1.5 bg-gradient-to-r from-accent-500 to-accent-600 text-white text-xs font-bold uppercase tracking-wider rounded-full shadow-lg whitespace-nowrap">Most Popular</span>
                            @endif

                            <h3 class="text-lg font-bold {{ $plan['highlighted'] ? 'text-primary-700' : 'text-gray-900' }}">{{ $plan['name'] }}</h3>
                            <p class="text-sm text-gray-500 mt-1">{{ $plan['tagline'] }}</p>

                            <div class="mt-6 mb-6 min-h-[76px]">
                                @if($plan['monthly'] !== null)
                                    <div class="flex items-end gap-1">
                                        <span class="text-5xl font-extrabold text-gray-900">
                                            $<span x-text="billing === 'monthly' ? '{{ number_format($plan['monthly']) }}' : '{{ number_format($plan['yearly']) }}'">{{ number_format($plan['monthly']) }}</span>
                                        </span>
                                        <span class="pb-1.5 pl-0.5 text-sm text-gray-400" x-text="billing === 'monthly' ? '/month' : '/year'">/month</span>
                                    </div>
                                    <p class="text-xs mt-2 h-4">
                                        <span x-show="billing === 'yearly'" style="display:none;" class="text-secondary-600 font-semibold">$ {{ number_format($plan['monthly'] * 12 - $plan['yearly']) }} saved vs monthly</span>
                                    </p>
                                @else
                                    <div class="flex items-end gap-1">
                                        <span class="text-5xl font-extrabold text-gray-900">Custom</span>
                                    </div>
                                    <p class="text-xs text-gray-400 mt-2">Tailored to your organization</p>
                                @endif
                            </div>

                            <ul class="space-y-3 flex-1 mb-8">
                                @foreach($plan['features'] as $feature)
                                    <li class="flex items-start gap-3 text-sm text-gray-600">
                                        <span class="text-secondary-500 mt-0.5 shrink-0">{!! $check !!}</span>
                                        {{ $feature }}
                                    </li>
                                @endforeach
                                @foreach($plan['excluded'] as $feature)
                                    <li class="flex items-start gap-3 text-sm text-gray-300 line-through decoration-gray-200">
                                        <span class="mt-0.5 shrink-0">{!! $cross !!}</span>
                                        {{ $feature }}
                                    </li>
                                @endforeach
                            </ul>

                            <a href="{{ $key === 'team' ? route('contact') : route('register') }}"
                               class="block w-full text-center px-6 py-3.5 font-bold rounded-xl text-sm transition {{ $plan['highlighted'] ? 'bg-primary-600 hover:bg-primary-700 text-white shadow-lg shadow-primary-600/25' : 'bg-primary-50 hover:bg-primary-100 text-primary-700' }}">
                                {{ $plan['cta'] }}
                            </a>
                        </div>
                    @endforeach
                </div>

                {{-- Trust row --}}
                <div class="mt-14 max-w-4xl mx-auto grid grid-cols-1 sm:grid-cols-3 gap-6 text-center">
                    <div class="flex items-center justify-center gap-3 text-sm text-gray-500">
                        <svg class="w-5 h-5 text-secondary-500 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        30-day money-back guarantee
                    </div>
                    <div class="flex items-center justify-center gap-3 text-sm text-gray-500">
                        <svg class="w-5 h-5 text-secondary-500 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        Cancel anytime, no lock-in
                    </div>
                    <div class="flex items-center justify-center gap-3 text-sm text-gray-500">
                        <svg class="w-5 h-5 text-secondary-500 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        Secure SSL checkout
                    </div>
                </div>

                {{-- Stats --}}
                <div class="mt-14 bg-gradient-to-r from-primary-600 to-primary-800 rounded-3xl px-8 py-10 max-w-5xl mx-auto grid grid-cols-1 sm:grid-cols-3 gap-8 text-center">
                    <div>
                        <div class="text-3xl font-extrabold text-white">{{ number_format(max(1, $stats['learners'])) }}+</div>
                        <div class="text-sm text-primary-200 mt-1">Active learners</div>
                    </div>
                    <div>
                        <div class="text-3xl font-extrabold text-white">{{ number_format($stats['courses']) }}</div>
                        <div class="text-sm text-primary-200 mt-1">Courses available</div>
                    </div>
                    <div>
                        <div class="text-3xl font-extrabold text-white">{{ min(99, max(1, $stats['satisfaction'])) }}%</div>
                        <div class="text-sm text-primary-200 mt-1">Satisfaction rate</div>
                    </div>
                </div>
            </div>
        </section>

        {{-- Comparison table --}}
        <section class="py-16 bg-white">
            <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="text-center mb-10">
                    <span class="text-sm font-semibold text-primary-600 uppercase tracking-wider">Compare Plans</span>
                    <h2 class="text-3xl font-bold text-gray-900 mt-2">Find the Right Fit</h2>
                </div>

                <div class="overflow-x-auto rounded-2xl border border-gray-100 shadow-sm">
                    <table class="w-full text-sm min-w-[640px]">
                        <thead>
                            <tr class="bg-gray-50 border-b border-gray-100">
                                <th class="text-left font-semibold text-gray-900 px-6 py-4">Feature</th>
                                <th class="text-center font-semibold text-gray-900 px-4 py-4">Free</th>
                                <th class="text-center font-semibold text-primary-700 px-4 py-4 bg-primary-50/50">Pro</th>
                                <th class="text-center font-semibold text-gray-900 px-4 py-4">Enterprise</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-50">
                            @foreach($comparison as $row)
                                <tr class="hover:bg-gray-50/60 transition">
                                    <td class="px-6 py-3.5 text-gray-600 font-medium">{{ $row['feature'] }}</td>
                                    @foreach(['free', 'pro', 'team'] as $col)
                                        <td class="px-4 py-3.5 text-center {{ $col === 'pro' ? 'bg-primary-50/40' : '' }}">
                                            @if($row[$col] === true)
                                                <span class="inline-grid place-items-center w-6 h-6 rounded-full bg-secondary-50 text-secondary-600">{!! $check !!}</span>
                                            @elseif($row[$col] === false)
                                                <span class="inline-grid place-items-center w-6 h-6 rounded-full bg-gray-50 text-gray-300">{!! $cross !!}</span>
                                            @else
                                                <span class="text-xs font-semibold text-gray-600">{{ $row[$col] }}</span>
                                            @endif
                                        </td>
                                    @endforeach
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </section>

        {{-- Pricing FAQ teaser --}}
        <section class="py-16 bg-gray-50">
            <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="text-center mb-10">
                    <span class="text-sm font-semibold text-primary-600 uppercase tracking-wider">Billing Questions?</span>
                    <h2 class="text-3xl font-bold text-gray-900 mt-2">Before You Decide</h2>
                </div>
                <div class="grid sm:grid-cols-2 gap-6">
                    @foreach([
                        ['q' => 'Can I switch plans later?', 'a' => 'Absolutely. Upgrade or downgrade at any time — changes take effect immediately and billing is prorated automatically.'],
                        ['q' => 'Is there a free trial?', 'a' => 'Yes! Pro comes with a 7-day free trial with full access. No charge until the trial ends, and you can cancel in one click.'],
                        ['q' => 'Do certificates cost extra?', 'a' => 'No — every certificate is included with Pro and Enterprise at no additional cost, including unlimited verifications for employers.'],
                        ['q' => 'How do refunds work?', 'a' => 'Not happy? Get a full refund within 30 days of any purchase — no questions asked, processed in 2–3 business days.'],
                    ] as $qa)
                        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6">
                            <h3 class="font-bold text-gray-900 mb-2 flex items-start gap-2">
                                <svg class="w-5 h-5 text-primary-600 shrink-0 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9.879 7.519c1.171-1.025 3.071-1.025 4.242 0 1.172 1.025 1.172 2.687 0 3.712-.203.179-.43.326-.67.442-.745.361-1.45.999-1.45 1.827v.75M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-9 5.25h.008v.008H12v-.008z"/></svg>
                                {{ $qa['q'] }}
                            </h3>
                            <p class="text-sm text-gray-500 leading-relaxed pl-7">{{ $qa['a'] }}</p>
                        </div>
                    @endforeach
                </div>
                <div class="text-center mt-10">
                    <a href="{{ route('faq') }}" class="inline-flex items-center gap-2 text-sm font-semibold text-primary-600 hover:text-primary-700 transition">
                        See all FAQs
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5L21 12m0 0l-7.5 7.5M21 12H3"/></svg>
                    </a>
                </div>
            </div>
        </section>
    </div>
@endsection
