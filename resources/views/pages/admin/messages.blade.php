@extends('layouts.dashboard')
@section('title', 'Contact Messages')
@section('content')
    <div class="max-w-5xl mx-auto space-y-6">
        {{-- Header --}}
        <div class="flex flex-wrap items-center gap-3">
            <div class="flex-1 min-w-0">
                <h1 class="text-2xl font-bold text-gray-900">Contact Messages</h1>
                <p class="text-gray-500 mt-0.5">Messages submitted through the website contact form.</p>
            </div>
            <span class="inline-flex items-center gap-2 px-4 py-2 text-sm font-semibold rounded-lg {{ $unreadCount > 0 ? 'bg-red-50 text-red-700 border border-red-200' : 'bg-gray-100 text-gray-500 border border-gray-200' }}">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M21.75 6.75v10.5a2.25 2.25 0 01-2.25 2.25h-15a2.25 2.25 0 01-2.25-2.25V6.75m19.5 0A2.25 2.25 0 0019.5 4.5h-15a2.25 2.25 0 00-2.25 2.25m19.5 0v.243a2.25 2.25 0 01-1.07 1.916l-7.5 4.615a2.25 2.25 0 01-2.36 0L3.32 8.91a2.25 2.25 0 01-1.07-1.916V6.75"/></svg>
                {{ $unreadCount }} unread
            </span>
        </div>

        {{-- Filters + Search --}}
        <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-4 flex flex-col sm:flex-row gap-3 sm:items-center justify-between">
            <div class="flex items-center gap-2">
                @foreach(['all' => 'All', 'unread' => 'Unread', 'read' => 'Read'] as $key => $label)
                    <a href="{{ route('admin.messages.index', array_filter(['filter' => $key !== 'all' ? $key : null, 'search' => $search ?: null])) }}"
                        class="px-4 py-2 text-sm font-medium rounded-lg transition {{ $filter === $key ? 'bg-primary-600 text-white shadow-sm' : 'text-gray-600 hover:bg-gray-100' }}">
                        {{ $label }}
                        @if($key === 'unread' && $unreadCount > 0)
                            <span class="ml-1 px-1.5 py-0.5 rounded-full text-[10px] font-bold {{ $filter === 'unread' ? 'bg-white/20' : 'bg-red-100 text-red-600' }}">{{ $unreadCount }}</span>
                        @endif
                    </a>
                @endforeach
            </div>
            <form method="GET" action="{{ route('admin.messages.index') }}" class="relative w-full sm:w-72">
                <input type="hidden" name="filter" value="{{ $filter !== 'all' ? $filter : '' }}">
                <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                <input type="text" name="search" value="{{ $search }}" placeholder="Search sender, subject, message..."
                    class="w-full pl-9 pr-4 py-2 border border-gray-200 rounded-lg text-sm focus:ring-2 focus:ring-primary-500 focus:border-primary-500 transition">
            </form>
        </div>

        {{-- List --}}
        <div class="space-y-3">
            @forelse($messages as $msg)
                <div class="bg-white rounded-xl border shadow-sm p-5 {{ $msg->is_read ? 'border-gray-100' : 'border-primary-200 bg-primary-50/30' }}">
                    <div class="flex items-start gap-4">
                        <div class="w-10 h-10 rounded-full shrink-0 grid place-items-center text-xs font-bold {{ $msg->is_read ? 'bg-gray-100 text-gray-500' : 'bg-primary-600 text-white' }}">
                            {{ strtoupper(substr($msg->name, 0, 2)) }}
                        </div>

                        <div class="flex-1 min-w-0" x-data="{ open: false }">
                            <div class="flex items-start justify-between gap-3">
                                <div class="min-w-0">
                                    <p class="text-sm font-semibold text-gray-900 truncate">
                                        {{ $msg->subject }}
                                        @if(!$msg->is_read)
                                            <span class="ml-1.5 inline-flex items-center rounded-full bg-primary-100 px-1.5 py-0.5 text-[10px] font-bold uppercase tracking-wide text-primary-700 align-middle">New</span>
                                        @endif
                                    </p>
                                    <p class="text-xs text-gray-400 mt-0.5 truncate">{{ $msg->name }} · <a href="mailto:{{ $msg->email }}" class="hover:text-primary-600">{{ $msg->email }}</a> · {{ $msg->created_at->diffForHumans() }}</p>
                                </div>
                            </div>

                            <p class="text-sm text-gray-600 mt-2 line-clamp-2" x-show="!open" x-cloak>{{ \Illuminate\Support\Str::limit($msg->message, 160) }}</p>

                            <div x-show="open" x-cloak class="mt-2 p-4 bg-gray-50 rounded-lg text-sm text-gray-700 whitespace-pre-line leading-relaxed border border-gray-100">{{ $msg->message }}</div>

                            <div class="flex items-center gap-2 mt-3">
                                <button type="button" @click="open = !open"
                                    {{ !$msg->is_read ? 'data-mark-read=read-msg-' . $msg->id : '' }}
                                    class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-medium text-primary-700 bg-primary-50 hover:bg-primary-100 rounded-lg transition">
                                    {{ $msg->is_read ? 'View details' : 'Read message' }}
                                </button>
                                <a href="mailto:{{ $msg->email }}?subject=Re: {{ rawurlencode($msg->subject) }}"
                                    class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-medium text-gray-600 bg-white border border-gray-200 hover:border-primary-300 hover:text-primary-700 rounded-lg transition">
                                    Reply
                                </a>
                                <form method="POST" action="{{ route('admin.messages.toggle', $msg) }}" class="ml-auto">
                                    @csrf
                                    <button type="submit" class="p-1.5 text-gray-400 hover:text-primary-600 hover:bg-primary-50 rounded-lg transition" title="{{ $msg->is_read ? 'Mark as unread' : 'Mark as read' }}">
                                        @if($msg->is_read)
                                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M17.593 3.322c1.1.128 1.907 1.077 1.907 2.185V21L12 17.25 4.5 21V5.507c0-1.108.806-2.057 1.907-2.185a48.507 48.507 0 0111.186 0z"/></svg>
                                        @else
                                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                        @endif
                                    </button>
                                </form>
                                <button type="submit" form="delete-msg-{{ $msg->id }}"
                                    data-confirm="Delete this message from '{{ $msg->name }}'? This cannot be undone."
                                    data-confirm-title="Delete message?"
                                    class="p-1.5 text-gray-400 hover:text-red-600 hover:bg-red-50 rounded-lg transition" title="Delete">
                                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0"/></svg>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
                <form id="delete-msg-{{ $msg->id }}" method="POST" action="{{ route('admin.messages.destroy', $msg) }}" class="hidden">@csrf @method('DELETE')</form>

                {{-- Auto mark-as-read when expanding an unread message --}}
                @if(!$msg->is_read)
                    <form id="read-msg-{{ $msg->id }}" method="POST" action="{{ route('admin.messages.toggle', $msg) }}" class="hidden">@csrf</form>
                @endif
            @empty
                <div class="bg-white rounded-xl border border-dashed border-gray-200 p-14 text-center">
                    <svg class="w-12 h-12 text-gray-300 mx-auto mb-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M21.75 6.75v10.5a2.25 2.25 0 01-2.25 2.25h-15a2.25 2.25 0 01-2.25-2.25V6.75m19.5 0A2.25 2.25 0 0019.5 4.5h-15a2.25 2.25 0 00-2.25 2.25m19.5 0v.243a2.25 2.25 0 01-1.07 1.916l-7.5 4.615a2.25 2.25 0 01-2.36 0L3.32 8.91a2.25 2.25 0 01-1.07-1.916V6.75"/></svg>
                    <p class="text-sm font-medium text-gray-500">{{ $search ? "No messages match '$search'" : ($filter === 'unread' ? 'No unread messages' : 'No messages yet') }}</p>
                    <p class="text-sm text-gray-400 mt-1">Messages from the website contact form will appear here.</p>
                </div>
            @endforelse
        </div>

        @if($messages->hasPages())
            <div>{{ $messages->links() }}</div>
        @endif
    </div>

    @push('scripts')
        <script>
            // Auto-mark unread messages read when the user expands them.
            document.addEventListener('click', (e) => {
                const btn = e.target.closest('[data-mark-read]');
                if (!btn) return;
                const form = document.getElementById(btn.dataset.markRead);
                if (!form || form.dataset.sent) return;
                form.dataset.sent = '1';
                setTimeout(() => form.submit(), 600); // let the message open first
            });
        </script>
    @endpush
@endsection
