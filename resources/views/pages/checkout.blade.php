@extends('layouts.app')

@php
    $subtotal = $subtotal ?? $items->sum('price');
    $discount = $discount ?? 0;
    $total = $total ?? max(0, $subtotal - $discount);
    $appliedCoupon = session('applied_coupon');
    $methods = \App\Models\Payment::METHODS;
@endphp

@section('title', 'Checkout')

@section('content')
    <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-12" x-data="{ method: 'paypal' }">
        <h1 class="text-3xl font-bold text-gray-900 mb-2">Checkout</h1>
        <p class="text-gray-500 mb-8">
            {{ $isCart ? 'You are checking out your cart items.' : 'Complete your enrollment below.' }}
            <span class="inline-flex items-center gap-1 ml-2 px-2 py-0.5 bg-accent-50 text-accent-700 text-xs font-semibold rounded-full">Test mode — no real money is charged</span>
        </p>

        @if(session('coupon_error'))
            <div class="mb-4 p-3 bg-red-50 border border-red-200 rounded-lg text-sm text-red-700">{{ session('coupon_error') }}</div>
        @endif
        @if(session('coupon_success'))
            <div class="mb-4 p-3 bg-secondary-50 border border-secondary-200 rounded-lg text-sm text-secondary-700">{{ session('coupon_success') }}</div>
        @endif

        <form method="POST" action="{{ route('checkout.process') }}">
            @csrf
            <div class="flex flex-col lg:flex-row gap-8">

                {{-- Left: payment methods --}}
                <div class="flex-1 min-w-0 space-y-6">
                    <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-6">
                        <h2 class="font-bold text-gray-900 mb-5">Payment Method</h2>

                        <div class="grid sm:grid-cols-2 gap-3">
                            @foreach($methods as $key => $label)
                                <label class="relative flex items-center gap-3 p-4 rounded-xl border-2 cursor-pointer transition
                                    {{ $key === 'paypal' ? 'border-primary-500 bg-primary-50/50' : 'border-gray-200 hover:border-gray-300' }}"
                                    :class="method === '{{ $key }}' ? 'border-primary-500 bg-primary-50/50' : 'border-gray-200'">
                                    <input type="radio" name="method" value="{{ $key }}" class="sr-only" x-model="method" {{ $key === 'paypal' ? 'checked' : '' }}>
                                    <span class="w-10 h-10 rounded-lg bg-primary-100 text-primary-700 grid place-items-center font-bold text-xs shrink-0">
                                        {{ strtoupper(substr(str_replace('_', '', $label), 0, 2)) }}
                                    </span>
                                    <span class="text-sm font-semibold text-gray-800">{{ $label }}</span>
                                    <svg x-show="method === '{{ $key }}'" class="w-5 h-5 text-primary-600 absolute top-2 right-2" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.857-9.809a.75.75 0 00-1.214-.882l-3.483 4.79-1.88-1.88a.75.75 0 10-1.06 1.061l2.5 2.5a.75.75 0 001.137-.089l4-5.5z" clip-rule="evenodd"/></svg>
                                </label>
                            @endforeach
                        </div>

                        {{-- Dynamic method fields (dummy) --}}
                        <div class="mt-6 pt-6 border-t border-gray-100">
                            {{-- PayPal / Stripe --}}
                            <div x-show="method === 'paypal' || method === 'stripe'" class="space-y-4">
                                <p class="text-sm text-gray-500">Enter your email to simulate the {{ '' }}{{ $methods['paypal'] }}/{{ $methods['stripe'] }} flow.</p>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Account Email</label>
                                    <input type="email" name="payer_email" value="{{ old('payer_email', auth()->user()->email) }}" placeholder="you@example.com"
                                        class="w-full px-4 py-2.5 border border-gray-200 rounded-lg text-sm focus:ring-2 focus:ring-primary-500 focus:border-primary-500 transition">
                                    @error('payer_email')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
                                </div>
                            </div>

                            {{-- Square / Credit Card --}}
                            <div x-show="method === 'square' || method === 'credit_card'" x-cloak class="space-y-4">
                                <p class="text-sm text-gray-500">Card details are simulated — use any numbers, e.g. 4242 4242 4242 4242.</p>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Card Number</label>
                                    <input type="text" inputmode="numeric" name="card_number" placeholder="4242 4242 4242 4242"
                                        class="w-full px-4 py-2.5 border border-gray-200 rounded-lg text-sm focus:ring-2 focus:ring-primary-500 focus:border-primary-500 transition">
                                    @error('card_number')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Name on Card</label>
                                    <input type="text" name="card_name" placeholder="JOHN DOE"
                                        class="w-full px-4 py-2.5 border border-gray-200 rounded-lg text-sm focus:ring-2 focus:ring-primary-500 focus:border-primary-500 transition">
                                    @error('card_name')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
                                </div>
                                <div class="grid grid-cols-2 gap-4">
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Expiry (MM/YY)</label>
                                        <input type="text" name="card_expiry" placeholder="12/28"
                                            class="w-full px-4 py-2.5 border border-gray-200 rounded-lg text-sm focus:ring-2 focus:ring-primary-500 focus:border-primary-500 transition">
                                        @error('card_expiry')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">CVC</label>
                                        <input type="text" inputmode="numeric" name="card_cvc" placeholder="123"
                                            class="w-full px-4 py-2.5 border border-gray-200 rounded-lg text-sm focus:ring-2 focus:ring-primary-500 focus:border-primary-500 transition">
                                        @error('card_cvc')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
                                    </div>
                                </div>
                            </div>

                            {{-- Bank Account --}}
                            <div x-show="method === 'bank_account'" x-cloak class="space-y-4">
                                <p class="text-sm text-gray-500">Direct bank transfer (simulated confirmation).</p>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Bank Name</label>
                                    <input type="text" name="bank_name" placeholder="e.g. Meezan Bank"
                                        class="w-full px-4 py-2.5 border border-gray-200 rounded-lg text-sm focus:ring-2 focus:ring-primary-500 focus:border-primary-500 transition">
                                    @error('bank_name')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Account Title</label>
                                    <input type="text" name="account_title" placeholder="Account holder name"
                                        class="w-full px-4 py-2.5 border border-gray-200 rounded-lg text-sm focus:ring-2 focus:ring-primary-500 focus:border-primary-500 transition">
                                    @error('account_title')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Account Number</label>
                                    <input type="text" name="account_number" placeholder="PK00 XXXX 0000 0000 0000"
                                        class="w-full px-4 py-2.5 border border-gray-200 rounded-lg text-sm focus:ring-2 focus:ring-primary-500 focus:border-primary-500 transition">
                                    @error('account_number')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
                                </div>
                            </div>

                            {{-- JazzCash / Easypaisa --}}
                            <div x-show="method === 'jazzcash' || method === 'easypaisa'" x-cloak class="space-y-4">
                                <p class="text-sm text-gray-500">You will receive a simulated OTP on your mobile number.</p>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Mobile Number</label>
                                    <input type="tel" name="mobile_number" placeholder="03001234567"
                                        class="w-full px-4 py-2.5 border border-gray-200 rounded-lg text-sm focus:ring-2 focus:ring-primary-500 focus:border-primary-500 transition">
                                    @error('mobile_number')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Right: order summary --}}
                <aside class="lg:w-96 shrink-0">
                    <div class="sticky top-24 bg-white rounded-xl border border-gray-100 shadow-sm p-6">
                        <h2 class="font-bold text-gray-900 mb-4">Order Summary</h2>

                        <div class="space-y-4 mb-5">
                            @foreach($items as $item)
                                <input type="hidden" name="course_ids[]" value="{{ $item->id }}">
                                <div class="flex gap-3 items-start">
                                    <div class="w-20 h-12 rounded-lg overflow-hidden bg-gradient-to-br from-primary-100 to-primary-200 shrink-0">
                                        @if($item->thumbnail)
                                            <img src="{{ asset('assets/upload/' . $item->thumbnail) }}" alt="" class="w-full h-full object-cover">
                                        @endif
                                    </div>
                                    <div class="min-w-0 flex-1">
                                        <p class="text-sm font-medium text-gray-900 line-clamp-2">{{ $item->title }}</p>
                                        <p class="text-xs text-gray-400">{{ $item->instructor?->name ?? 'Instructor' }}</p>
                                    </div>
                                    <span class="text-sm font-bold text-gray-900 shrink-0">{{ $item->price > 0 ? '$' . number_format($item->price, 2) : 'Free' }}</span>
                                </div>
                            @endforeach
                        </div>

                        {{-- Coupon Input --}}
                        <div class="border-t border-gray-100 pt-4 mb-4">
                            @if($appliedCoupon)
                                <div class="flex items-center justify-between bg-secondary-50 border border-secondary-200 rounded-lg px-3 py-2">
                                    <div class="flex items-center gap-2">
                                        <svg class="w-4 h-4 text-secondary-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                        <span class="text-sm font-semibold text-secondary-700">{{ $appliedCoupon }}</span>
                                    </div>
                                    <a href="{{ route('checkout.remove-coupon') }}" class="text-xs text-red-500 hover:text-red-700 font-medium">Remove</a>
                                </div>
                            @else
                                <form method="POST" action="{{ route('checkout.apply-coupon') }}" class="flex gap-2">
                                    @csrf
                                    <input type="text" name="coupon_code" placeholder="Coupon code" class="flex-1 px-3 py-2 border border-gray-200 rounded-lg text-sm focus:ring-2 focus:ring-primary-500 focus:border-primary-500 transition uppercase" maxlength="30">
                                    <button type="submit" class="px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 text-sm font-semibold rounded-lg transition">Apply</button>
                                </form>
                            @endif
                        </div>

                        <div class="border-t border-gray-100 pt-4 space-y-2 text-sm mb-5">
                            <div class="flex justify-between text-gray-600"><span>Subtotal</span><span>${{ number_format($subtotal, 2) }}</span></div>
                            @if($discount > 0)
                                <div class="flex justify-between text-secondary-600 font-medium">
                                    <span>Discount</span>
                                    <span>-${{ number_format($discount, 2) }}</span>
                                </div>
                            @else
                                <div class="flex justify-between text-gray-600"><span>Discount</span><span>$0.00</span></div>
                            @endif
                            <div class="flex justify-between text-base font-bold text-gray-900 border-t border-gray-100 pt-2"><span>Total Due</span><span>${{ number_format($total, 2) }}</span></div>
                        </div>

                        <button type="submit"
                            onclick="this.disabled = true; this.innerText = 'Processing...'; this.form.submit();"
                            class="w-full py-3.5 bg-secondary-600 hover:bg-secondary-700 disabled:opacity-60 text-white font-bold rounded-xl transition shadow-lg shadow-secondary-600/25 inline-flex items-center justify-center gap-2">
                            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M16.5 10.5V6.75a4.5 4.5 0 10-9 0v3.75m-.75 11.25h10.5a2.25 2.25 0 002.25-2.25v-6.75a2.25 2.25 0 00-2.25-2.25H6.75a2.25 2.25 0 00-2.25 2.25v6.75a2.25 2.25 0 002.25 2.25z"/></svg>
                            Pay ${{ number_format($total, 2) }} Securely
                        </button>
                        <p class="text-center text-[11px] text-gray-400 mt-3 flex items-center justify-center gap-1">
                            <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M16.5 10.5V6.75a4.5 4.5 0 10-9 0v3.75m-.75 11.25h10.5a2.25 2.25 0 002.25-2.25v-6.75a2.25 2.25 0 00-2.25-2.25H6.75a2.25 2.25 0 00-2.25 2.25v6.75a2.25 2.25 0 002.25 2.25z"/></svg>
                            Dummy gateway — for testing only
                        </p>
                    </div>
                </aside>
            </div>
        </form>
    </div>
@endsection
