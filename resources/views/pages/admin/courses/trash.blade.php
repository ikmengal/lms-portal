@extends('layouts.dashboard')
@section('title', 'Courses Trash')
@section('content')
    <div class="space-y-6">
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Deleted Courses</h1>
            <p class="text-gray-500 mt-1">Restore courses or permanently remove them. Deleted courses are hidden from students.</p>
        </div>
        <a href="{{ route('admin.courses.index') }}" class="inline-flex items-center gap-2 px-4 py-2.5 text-sm font-medium text-primary-700 bg-primary-50 hover:bg-primary-100 rounded-lg transition">
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18"/></svg>
            Back to Courses
        </a>
    </div>

    <div class="bg-white rounded-xl border border-gray-100 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead>
                    <tr class="border-b border-gray-100 bg-gray-50">
                        <th class="text-left text-xs font-medium text-gray-500 uppercase tracking-wider px-6 py-3">Course</th>
                        <th class="text-left text-xs font-medium text-gray-500 uppercase tracking-wider px-6 py-3">Category</th>
                        <th class="text-left text-xs font-medium text-gray-500 uppercase tracking-wider px-6 py-3">Students</th>
                        <th class="text-left text-xs font-medium text-gray-500 uppercase tracking-wider px-6 py-3">Deleted At</th>
                        <th class="text-right text-xs font-medium text-gray-500 uppercase tracking-wider px-6 py-3">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @forelse($courses as $course)
                        <tr class="hover:bg-gray-50 transition">
                            <td class="px-6 py-4 max-w-sm">
                                <p class="text-sm font-medium text-gray-900 truncate">{{ $course->title }}</p>
                                <p class="text-xs text-gray-400">{{ $course->instructor->name ?? 'No instructor' }}</p>
                            </td>
                            <td class="px-6 py-4"><span class="px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-50 text-blue-700">{{ $course->category ?? '—' }}</span></td>
                            <td class="px-6 py-4 text-sm text-gray-500">{{ $course->enrollments->count() }}</td>
                            <td class="px-6 py-4 text-sm text-gray-500">{{ $course->deleted_at->format('M d, Y H:i') }}</td>
                            <td class="px-6 py-4 text-right">
                                <div class="flex items-center justify-end gap-2">
                                    <button type="submit" form="restore-course-{{ $course->id }}"
                                        class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-medium text-secondary-700 bg-secondary-50 hover:bg-secondary-100 rounded-lg transition">
                                        <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M16.023 9.348h4.992v-.001M2.985 19.644v-4.992m0 0h4.992m-4.993 0l3.181 3.183a8.25 8.25 0 0013.803-3.7M4.031 9.865a8.25 8.25 0 0113.803-3.7l3.181 3.182m0-4.991v4.99"/></svg>
                                        Restore
                                    </button>
                                    <button type="submit" form="force-delete-course-{{ $course->id }}"
                                        data-confirm="This will PERMANENTLY delete {{ $course->title }} with all its chapters, lessons, enrollments and test results. This cannot be undone."
                                        data-confirm-title="Permanently delete?"
                                        data-confirm-button="Yes, delete forever"
                                        class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-medium text-red-600 bg-red-50 hover:bg-red-100 rounded-lg transition">
                                        <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0"/></svg>
                                        Delete Forever
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="5" class="px-6 py-10 text-center text-sm text-gray-400">Trash is empty.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($courses->hasPages())
            <div class="px-6 py-4 border-t border-gray-100">{{ $courses->links() }}</div>
        @endif
    </div>
    </div>

    @foreach($courses as $course)
    <form id="restore-course-{{ $course->id }}" method="POST" action="{{ route('admin.courses.restore', $course->id) }}" class="hidden">@csrf</form>
    <form id="force-delete-course-{{ $course->id }}" method="POST" action="{{ route('admin.courses.force-delete', $course->id) }}" class="hidden">
        @csrf
        @method('DELETE')
    </form>
    @endforeach
@endsection
