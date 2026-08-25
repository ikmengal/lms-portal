@extends('layouts.app')
@section('title', 'Log In')
@section('content')
    <div class="min-h-[80vh] flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8 bg-gray-50">
        <div class="max-w-md w-full">
            <div class="text-center mb-8">
                <a href="{{ route('home') }}" class="inline-flex items-center gap-2 mb-6">
                    <div class="w-10 h-10 bg-gradient-to-br from-primary-600 to-primary-800 rounded-lg flex items-center justify-center">
                        <svg class="w-6 h-6 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                        </svg>
                    </div>
                    <span class="text-2xl font-bold text-primary-900">LMS<span class="text-accent-500">Portal</span></span>
                </a>
                <h1 class="text-2xl font-bold text-gray-900 mb-2">Welcome back</h1>
                <p class="text-gray-500">Sign in to continue your learning journey</p>
            </div>

            <div class="bg-white rounded-2xl shadow-lg p-8">

                {{-- Validation Errors --}}
                @if($errors->any())
                    <div class="mb-6 p-4 bg-red-50 border border-red-200 rounded-xl">
                        <div class="flex items-start gap-3">
                            <svg class="w-5 h-5 text-red-500 mt-0.5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m9-.75a9 9 0 11-18 0 9 9 0 0118 0zm-9 3.75h.008v.008H12v-.008z"/></svg>
                            <div>
                                @foreach($errors->all() as $error)
                                    <p class="text-sm text-red-600">{{ $error }}</p>
                                @endforeach
                                @if(session('showResend'))
                                    <a href="{{ route('activation.resend.form', ['email' => session('showResend')]) }}"
                                       class="mt-2 inline-flex items-center gap-1 text-sm font-semibold text-primary-600 hover:text-primary-700">
                                        Resend activation email
                                        <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5L21 12l-7.5 7.5M21 12H3"/></svg>
                                    </a>
                                @endif
                            </div>
                        </div>
                    </div>
                @endif

                <form method="POST" action="{{ route('login') }}" class="space-y-4">
                    @csrf
                    <div>
                        <label for="email" class="block text-sm font-medium text-gray-700 mb-1.5">Email Address</label>
                        <input type="email" id="email" name="email" value="{{ old('email') }}" placeholder="you@example.com"
                            autofocus class="w-full px-4 py-3 border rounded-xl text-gray-900 placeholder-gray-400
                            focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-primary-500 transition
                            @error('email') border-red-300 @else border-gray-200 @enderror"
                        >
                        @error('email')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <div class="flex items-center justify-between mb-1.5">
                            <label for="password" class="block text-sm font-medium text-gray-700">Password</label>
                            <a href="{{ route('password.request') }}" class="text-xs text-primary-600 hover:text-primary-700 font-medium">Forgot password?</a>
                        </div>
                        <input type="password" id="password" name="password" placeholder="Enter your password"
                            class="w-full px-4 py-3 border rounded-xl text-gray-900 placeholder-gray-400
                            focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-primary-500
                            transition @error('password') border-red-300 @else border-gray-200 @enderror"
                        >
                        @error('password')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="flex items-center gap-2">
                        <input type="checkbox" id="remember" name="remember" class="w-4 h-4 text-primary-600
                        border-gray-300 rounded focus:ring-primary-500"
                        >
                        <label for="remember" class="text-sm text-gray-600">Remember me</label>
                    </div>

                    <button type="submit" class="w-full py-3.5 bg-primary-600 hover:bg-primary-700 text-white font-bold rounded-xl transition shadow-lg shadow-primary-600/25">
                        Sign In
                    </button>
                </form>
            </div>

            <p class="text-center text-sm text-gray-500 mt-6">
                Don't have an account?
                <a href="{{ route('register') }}" class="font-semibold text-primary-600 hover:text-primary-700"> Sign up free</a>
            </p>
        </div>
    </div>
@endsection
