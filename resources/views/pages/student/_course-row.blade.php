@php
    $course = $enrollment->course;
    $hasCertificate = in_array($course?->id, $certificateCourseIds ?? []);
@endphp

<div class="bg-white rounded-xl border border-gray-100 shadow-sm p-5 hover:shadow-md transition">
    <div class="flex flex-col sm:flex-row gap-5">
        <div class="w-full sm:w-48 h-32 bg-gradient-to-br from-primary-500 to-primary-700 rounded-lg flex items-center justify-center shrink-0 relative overflow-hidden">
            <svg class="w-10 h-10 text-white/80" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6.042A8.967 8.967 0 006 3.75c-1.052 0-2.062.18-3 .512v14.25A8.987 8.987 0 016 18c2.305 0 4.408.867 6 2.292m0-14.25a8.966 8.966 0 016-2.292c1.052 0 2.062.18 3 .512v14.25A8.987 8.987 0 0018 18a8.967 8.967 0 00-6 2.292m0-14.25v14.25"/></svg>
            @if($course?->category)
                <span class="absolute top-2 left-2 px-2 py-0.5 bg-white/20 backdrop-blur text-white text-xs font-medium rounded">{{ $course->category }}</span>
            @endif
        </div>
        <div class="flex-1 min-w-0">
            <div class="flex items-start justify-between gap-3">
                <h3 class="font-semibold text-gray-900 text-lg mb-1">{{ $course->title ?? 'Course Title' }}</h3>
                @if($course?->level)
                    <span class="shrink-0 px-2 py-0.5 bg-gray-100 text-gray-600 text-xs font-medium rounded">{{ $course->level }}</span>
                @endif
            </div>
            <p class="text-sm text-gray-500 mb-2">{{ $course->instructor->name ?? 'Instructor' }}</p>
            <div class="flex items-center gap-3 mb-3 text-xs text-gray-500">
                <span>Last accessed: {{ $enrollment->updated_at->diffForHumans() }}</span>
                @if($course?->duration_hours)
                    <span class="flex items-center gap-1">
                        <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        {{ $course->duration_hours }}h
                    </span>
                @endif
            </div>
            <div class="flex items-center gap-3">
                <div class="flex-1 bg-gray-100 rounded-full h-2.5">
                    <div class="{{ $enrollment->isCompleted() ? 'bg-secondary-500' : 'bg-primary-500' }} h-2.5 rounded-full transition-all duration-500" style="width: {{ $enrollment->progress }}%"></div>
                </div>
                <span class="text-sm font-semibold {{ $enrollment->isCompleted() ? 'text-secondary-600' : 'text-primary-600' }}">{{ $enrollment->progress }}%</span>
            </div>
        </div>
        <div class="sm:self-center flex flex-col gap-2 w-25 sm:w-auto">
            @if($enrollment->isCompleted())
                <span class="inline-flex items-center justify-center gap-1.5 px-4 py-2 bg-secondary-100 text-secondary-700 font-medium rounded-lg text-sm">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    Completed
                </span>
                @if($hasCertificate)
                    <a href="{{ route('certificates.show', \App\Models\Certificate::where('user_id', auth()->id())->where('course_id', $course->id)->first()) }}" target="_blank" class="inline-flex items-center justify-center gap-1.5 px-4 py-2 bg-accent-50 hover:bg-accent-100 text-accent-700 font-medium rounded-lg text-sm transition">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M4.26 10.147a60.436 60.436 0 00-.491 6.347A48.627 48.627 0 0112 20.904a48.627 48.627 0 018.232-4.41 60.46 60.46 0 00-.491-6.347m-15.482 0a50.57 50.57 0 00-2.658-.813A59.905 59.905 0 0112 3.493a59.902 59.902 0 0110.399 5.84c-.896.248-1.783.52-2.658.814m-15.482 0A50.697 50.697 0 0112 13.489a50.702 50.702 0 017.74-3.336M6.75 15a.75.75 0 100-1.5.75.75 0 000 1.5zm0 0v-3.675A55.378 55.378 0 0112 8.443m-7.007 11.55A5.981 5.981 0 006.75 15.75v-1.5"/></svg>
                        View Certificate
                    </a>
                @endif
                <a href="{{ route('courses.show', $course->slug ?? '') }}" class="inline-flex items-center justify-center gap-1.5 px-4 py-2 border border-primary-200 hover:bg-primary-50 text-primary-700 font-medium rounded-lg text-sm transition">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178z"/><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                    View Course
                </a>
            @else
                <a href="{{ $course && $course->modules()->count() ? route('learn.start', $course) : route('courses.show', $course->slug ?? '') }}" class="inline-flex items-center justify-center gap-1.5 px-4 py-2 bg-primary-600 hover:bg-primary-700 text-white font-medium rounded-lg text-sm transition">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M5.25 5.653c0-.856.917-1.398 1.667-.986l11.54 6.348a1.125 1.125 0 010 1.971l-11.54 6.347a1.125 1.125 0 01-1.667-.985V5.653z"/></svg>
                    Continue Learning
                </a>
            @endif
        </div>
    </div>
</div>
