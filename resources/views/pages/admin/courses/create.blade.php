@extends('layouts.dashboard')
@section('title', 'Add Course')
@section('content')
    <div class="max mx-auto space-y-6">
        <div class="flex items-center gap-3">
            <a href="{{ route('admin.courses.index') }}" class="p-2 text-gray-400 hover:text-gray-700 hover:bg-gray-100 rounded-lg transition">
                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18"/></svg>
            </a>
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Add Course</h1>
                <p class="text-gray-500 mt-0.5">Create a new course. A URL slug is generated automatically.</p>
            </div>
        </div>

        <form method="POST" action="{{ route('admin.courses.store') }}" enctype="multipart/form-data">
            @csrf
            @include('pages.admin.courses._form')
        </form>
    </div>
@endsection
