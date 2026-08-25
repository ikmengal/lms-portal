@extends('layouts.app')

@section('title', 'My Cart')

@section('content')
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
        <h1 class="text-3xl font-bold text-gray-900 mb-8">My Cart</h1>

        @if($courses->isEmpty())
            <div class="bg-white rounded-xl border border-gray-100 shadow-sm py-16 text-center">
                <svg class="w-16 h-16 mx-auto text-gray-200 mb-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 3h1.386c.51 0 .955.343 1.087.835l.383 1.437M7.5 14.25a3 3 0 00-3 3h15.75m-12.75-3h11.218c1.121-2.3 2.1-4.684 2.924-7.138a60.114 60.114 0 00-16.536-1.84M7.5 14.25L5.106 5.272M6 20.25a.75.75 0 11-1.5 0 .75.75 0 011.5 0zm12.75 0a.75.75 0 11-1.5 0 .75.75 0 011.5 0z"/></svg>
                <h2 class="text-lg font-semibold text-gray-900 mb-1">Your cart is empty</h2>
                <p class="text-sm text-gray-500 mb-6">Browse our courses and add the ones you like.</p>
                <a href="{{ route('courses.index') }}" class="inline-flex items-center px-6 py-3 bg-primary-600 hover:bg-primary-700 text-white font-semibold rounded-xl transition">Browse Courses</a>
            </div>
        @else
            <div class="bg-white rounded-xl border border-gray-100 shadow-sm divide-y divide-gray-100">
                @foreach($courses as $course)
                    <div class="p-5 flex items-center gap-4">
                        <div class="w-24 h-16 rounded-lg overflow-hidden bg-gradient-to-br from-primary-100 to-primary-200 shrink-0">
                            @if($course->thumbnail)
                                <img src="{{ asset('storage/' . $course->thumbnail) }}" alt="{{ $course->title }}" class="w-full h-full object-cover">
                            @endif
                        </div>
                        <div class="min-w-0 flex-1">
                            <a href="{{ route('courses.show', $course) }}" class="font-semibold text-gray-900 hover:text-primary-700 transition line-clamp-1">{{ $course->title }}</a>
                            <p class="text-xs text-gray-500 mt-0.5">By {{ $course->instructor?->name ?? 'Instructor' }} · {{ $course->level ?? 'Beginner' }} · {{ $course->duration_hours }} hours</p>
                        </div>
                        <div class="text-right shrink-0">
                            <p class="font-bold text-gray-900">{{ $course->price > 0 ? '$' . number_format($course->price, 2) : 'Free' }}</p>
                            <form method="POST" action="{{ route('cart.remove', $course) }}" class="mt-1">
                                @csrf
                                <button type="submit" class="text-xs text-red-500 hover:text-red-600 font-medium transition">Remove</button>
                            </form>
                        </div>
                    </div>
                @endforeach
            </div>

            <div class="mt-6 bg-gray-50 rounded-xl border border-gray-100 p-6 flex flex-col sm:flex-row items-center justify-between gap-4">
                <div>
                    <p class="text-sm text-gray-500">Total ({{ $courses->count() }} course{{ $courses->count() > 1 ? 's' : '' }})</p>
                    <p class="text-2xl font-bold text-gray-900">${{ number_format($total, 2) }}</p>
                </div>
                <form method="GET" action="{{ route('checkout.cart') }}">
                    @csrf
                    <button type="submit" class="inline-flex items-center gap-2 px-8 py-3.5 bg-primary-600 hover:bg-primary-700 text-white font-bold rounded-xl transition shadow-lg shadow-primary-600/25">
                        Proceed to Checkout
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5L21 12l-7.5 7.5M21 12H3"/></svg>
                    </button>
                </form>
            </div>
        @endif
    </div>
@endsection
