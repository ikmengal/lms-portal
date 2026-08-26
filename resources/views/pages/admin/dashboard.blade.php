@extends('layouts.dashboard')
@section('title', 'Admin Dashboard')
@section('content')
    <div class="space-y-8">
        {{-- Header --}}
        <div class="flex flex-wrap items-center gap-4">
            <x-avatar :user="auth()->user()" size="w-12 h-12 text-lg" />
            <div class="flex-1 min-w-0">
                <h1 class="text-2xl font-bold text-gray-900">Admin Dashboard</h1>
                <p class="text-gray-500 mt-1">Welcome back, {{ auth()->user()->name }}. Here's what's happening on your platform.</p>
            </div>
            <a href="{{ route('admin.analytics') }}" class="inline-flex items-center gap-2 px-4 py-2.5 text-sm font-semibold text-white bg-primary-600 hover:bg-primary-700 rounded-lg transition shadow-sm whitespace-nowrap">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M7.5 14.25v2.25m3-4.5v4.5m3-6.75v6.75m3-9v9M6 20.25h12A2.25 2.25 0 0020.25 18V6A2.25 2.25 0 0018 3.75H6A2.25 2.25 0 003.75 6v12A2.25 2.25 0 006 20.25z"/></svg>
                View Analytics
            </a>
        </div>

        {{-- Stats Grid --}}
        <div class="grid sm:grid-cols-2 lg:grid-cols-4 gap-6">
            <div class="bg-white rounded-xl p-6 border border-gray-100 shadow-sm">
                <div class="flex items-center gap-4">
                    <div class="w-12 h-12 bg-primary-100 rounded-xl flex items-center justify-center">
                        <svg class="w-6 h-6 text-primary-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19.128a9.38 9.38 0 002.625.372 9.337 9.337 0 004.121-.952 4.125 4.125 0 00-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 018.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0111.964-3.07M12 6.375a3.375 3.375 0 11-6.75 0 3.375 3.375 0 016.75 0zm8.25 2.25a2.625 2.625 0 11-5.25 0 2.625 2.625 0 015.25 0z"/></svg>
                    </div>
                    <div>
                        <p class="text-2xl font-bold text-gray-900">{{ $stats['totalUsers'] }}</p>
                        <p class="text-sm text-gray-500">Total Users</p>
                    </div>
                </div>
                <div class="mt-4 flex items-center gap-2 text-sm">
                    <span class="text-secondary-600 font-medium">+{{ $stats['newUsersToday'] }}</span>
                    <span class="text-gray-400">joined today</span>
                </div>
            </div>

            <div class="bg-white rounded-xl p-6 border border-gray-100 shadow-sm">
                <div class="flex items-center gap-4">
                    <div class="w-12 h-12 bg-accent-100 rounded-xl flex items-center justify-center">
                        <svg class="w-6 h-6 text-accent-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6.042A8.967 8.967 0 006 3.75c-1.052 0-2.062.18-3 .512v14.25A8.987 8.987 0 016 18c2.305 0 4.408.867 6 2.292m0-14.25a8.966 8.966 0 016-2.292c1.052 0 2.062.18 3 .512v14.25A8.987 8.987 0 0018 18a8.967 8.967 0 00-6 2.292m0-14.25v14.25"/></svg>
                    </div>
                    <div>
                        <p class="text-2xl font-bold text-gray-900">{{ $stats['totalCourses'] }}</p>
                        <p class="text-sm text-gray-500">Total Courses</p>
                    </div>
                </div>
                <div class="mt-4 flex items-center gap-2 text-sm">
                    <span class="text-secondary-600 font-medium">{{ $stats['activeCourses'] }}</span>
                    <span class="text-gray-400">active</span>
                </div>
            </div>

            <div class="bg-white rounded-xl p-6 border border-gray-100 shadow-sm">
                <div class="flex items-center gap-4">
                    <div class="w-12 h-12 bg-secondary-100 rounded-xl flex items-center justify-center">
                        <svg class="w-6 h-6 text-secondary-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M4.26 10.147a60.436 60.436 0 00-.491 6.347A48.627 48.627 0 0112 20.904a48.627 48.627 0 018.232-4.41 60.46 60.46 0 00-.491-6.347m-15.482 0a50.57 50.57 0 00-2.658-.813A59.905 59.905 0 0112 3.493a59.902 59.902 0 0110.399 5.84c-.896.248-1.783.52-2.658.814m-15.482 0A50.697 50.697 0 0112 13.489a50.702 50.702 0 017.74-3.336M6.75 15a.75.75 0 100-1.5.75.75 0 000 1.5zm0 0v-3.675A55.378 55.378 0 0112 8.443m-7.007 11.55A5.981 5.981 0 006.75 15.75v-1.5"/></svg>
                    </div>
                    <div>
                        <p class="text-2xl font-bold text-gray-900">{{ $stats['instructors'] }}</p>
                        <p class="text-sm text-gray-500">Instructors</p>
                    </div>
                </div>
                <div class="mt-4 flex items-center gap-2 text-sm">
                    <span class="text-secondary-600 font-medium">{{ $stats['students'] }}</span>
                    <span class="text-gray-400">students</span>
                </div>
            </div>

            <div class="bg-white rounded-xl p-6 border border-gray-100 shadow-sm">
                <div class="flex items-center gap-4">
                    <div class="w-12 h-12 bg-purple-100 rounded-xl flex items-center justify-center">
                        <svg class="w-6 h-6 text-purple-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M3.75 3v11.25A2.25 2.25 0 006 16.5h2.25M3.75 3h-1.5m1.5 0h16.5m0 0h1.5m-1.5 0v11.25A2.25 2.25 0 0118 16.5h-2.25m-7.5 0h7.5m-7.5 0l-1 3m8.5-3l1 3m0 0l.5 1.5m-.5-1.5h-9.5m0 0l-.5 1.5m.75-9l3-3 2.148 2.148A12.061 12.061 0 0116.5 7.605"/></svg>
                    </div>
                    <div>
                        <p class="text-2xl font-bold text-gray-900">{{ $stats['enrollments'] }}</p>
                        <p class="text-sm text-gray-500">Enrollments</p>
                    </div>
                </div>
                <div class="mt-4 flex items-center gap-2 text-sm">
                    <span class="text-secondary-600 font-medium">+{{ $stats['enrollmentsToday'] }}</span>
                    <span class="text-gray-400">today</span>
                </div>
            </div>
        </div>

        {{-- Two Column Layout --}}
        <div class="grid lg:grid-cols-3 gap-8">
            {{-- Recent Users --}}
            <div class="lg:col-span-2 bg-white rounded-xl border border-gray-100 shadow-sm">
                <div class="p-6 border-b border-gray-100 flex items-center justify-between">
                    <h2 class="text-lg font-semibold text-gray-900">Recent Users</h2>
                    <a href="{{ route('admin.users') }}" class="text-sm text-primary-600 hover:text-primary-700 font-medium">View All</a>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead>
                            <tr class="border-b border-gray-50">
                                <th class="text-left text-xs font-medium text-gray-500 uppercase tracking-wider px-6 py-3">User</th>
                                <th class="text-left text-xs font-medium text-gray-500 uppercase tracking-wider px-6 py-3">Role</th>
                                <th class="text-left text-xs font-medium text-gray-500 uppercase tracking-wider px-6 py-3">Joined</th>
                                <th class="text-left text-xs font-medium text-gray-500 uppercase tracking-wider px-6 py-3">Status</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-50">
                            @forelse($recentUsers as $user)
                                <tr class="hover:bg-gray-50 transition">
                                    <td class="px-6 py-4">
                                        <div class="flex items-center gap-3">
                                            <div class="w-9 h-9 bg-primary-100 rounded-full flex items-center justify-center text-primary-700 text-xs font-bold">{{ strtoupper(substr($user->name, 0, 2)) }}</div>
                                            <div>
                                                <p class="text-sm font-medium text-gray-900">{{ $user->name }}</p>
                                                <p class="text-xs text-gray-500">{{ $user->email }}</p>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4">
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                            {{ $user->getRoleNames()->first() === 'admin' ? 'bg-purple-100 text-purple-800' : '' }}
                                            {{ $user->getRoleNames()->first() === 'instructor' ? 'bg-accent-100 text-accent-800' : '' }}
                                            {{ $user->getRoleNames()->first() === 'student' ? 'bg-primary-100 text-primary-800' : '' }}">
                                            {{ ucfirst($user->getRoleNames()->first() ?? 'none') }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 text-sm text-gray-500">{{ $user->created_at->diffForHumans() }}</td>
                                    <td class="px-6 py-4">
                                        <span class="inline-flex items-center gap-1 text-xs font-medium text-secondary-700">
                                            <span class="w-1.5 h-1.5 bg-secondary-500 rounded-full"></span> Active
                                        </span>
                                    </td>
                                </tr>
                            @empty
                                <tr><td colspan="4" class="px-6 py-8 text-center text-sm text-gray-400">No users yet.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            {{-- Quick Actions & Role Breakdown --}}
            <div class="space-y-6">
                {{-- Contact Messages --}}
                <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-6">
                    <div class="flex items-center justify-between mb-4">
                        <h2 class="text-lg font-semibold text-gray-900">Contact Messages</h2>
                        @if($unreadMessages > 0)
                            <span class="px-2 py-0.5 rounded-full text-xs font-bold bg-red-100 text-red-600">{{ $unreadMessages }} new</span>
                        @endif
                    </div>
                    <div class="space-y-3">
                        @forelse($recentMessages as $msg)
                            <a href="{{ route('admin.messages.index') }}" class="flex items-start gap-3 p-3 rounded-lg border border-gray-50 hover:border-primary-200 hover:bg-primary-50/40 transition">
                                <span class="w-8 h-8 shrink-0 rounded-full grid place-items-center text-[10px] font-bold {{ $msg->is_read ? 'bg-gray-100 text-gray-500' : 'bg-primary-600 text-white' }}">{{ strtoupper(substr($msg->name, 0, 2)) }}</span>
                                <span class="min-w-0 flex-1">
                                    <span class="block text-sm font-medium text-gray-900 truncate">{{ $msg->subject }}</span>
                                    <span class="block text-xs text-gray-400 truncate">{{ $msg->name }} · {{ $msg->created_at->diffForHumans() }}</span>
                                </span>
                                @if(!$msg->is_read)
                                    <span class="w-2 h-2 bg-red-500 rounded-full mt-1.5 shrink-0"></span>
                                @endif
                            </a>
                        @empty
                            <p class="text-sm text-gray-400 py-2">No messages yet.</p>
                        @endforelse
                    </div>
                    <a href="{{ route('admin.messages.index') }}" class="mt-4 inline-flex items-center gap-1.5 text-sm font-medium text-primary-600 hover:text-primary-700 transition">
                        View All Messages
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5L21 12m0 0l-7.5 7.5M21 12H3"/></svg>
                    </a>
                </div>

                {{-- Quick Actions --}}
                <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-6">
                    <h2 class="text-lg font-semibold text-gray-900 mb-4">Quick Actions</h2>
                    <div class="space-y-3">
                        <a href="#" class="flex items-center gap-3 px-4 py-3 bg-primary-50 hover:bg-primary-100 text-primary-700 rounded-lg transition">
                            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v6m3-3H9m12 0a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            Add New Course
                        </a>
                        <a href="#" class="flex items-center gap-3 px-4 py-3 bg-accent-50 hover:bg-accent-100 text-accent-700 rounded-lg transition">
                            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v6m3-3H9m12 0a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            Add New Category
                        </a>
                        <a href="#" class="flex items-center gap-3 px-4 py-3 bg-secondary-50 hover:bg-secondary-100 text-secondary-700 rounded-lg transition">
                            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M3.75 3v11.25A2.25 2.25 0 006 16.5h2.25M3.75 3h-1.5m1.5 0h16.5m0 0h1.5m-1.5 0v11.25A2.25 2.25 0 0118 16.5h-2.25m-7.5 0h7.5m-7.5 0l-1 3m8.5-3l1 3m0 0l.5 1.5m-.5-1.5h-9.5m0 0l-.5 1.5m.75-9l3-3 2.148 2.148A12.061 12.061 0 0116.5 7.605"/></svg>
                            View Reports
                        </a>
                    </div>
                </div>

                {{-- Role Breakdown --}}
                <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-6">
                    <h2 class="text-lg font-semibold text-gray-900 mb-4">User Roles</h2>
                    <div class="space-y-4">
                        <div>
                            <div class="flex justify-between text-sm mb-1">
                                <span class="text-gray-600">Admins</span>
                                <span class="font-medium text-gray-900">{{ $stats['admins'] }}</span>
                            </div>
                            <div class="w-full bg-gray-100 rounded-full h-2">
                                <div class="bg-purple-500 h-2 rounded-full" style="width: {{ $stats['totalUsers'] > 0 ? ($stats['admins'] / $stats['totalUsers']) * 100 : 0 }}%"></div>
                            </div>
                        </div>
                        <div>
                            <div class="flex justify-between text-sm mb-1">
                                <span class="text-gray-600">Instructors</span>
                                <span class="font-medium text-gray-900">{{ $stats['instructors'] }}</span>
                            </div>
                            <div class="w-full bg-gray-100 rounded-full h-2">
                                <div class="bg-accent-500 h-2 rounded-full" style="width: {{ $stats['totalUsers'] > 0 ? ($stats['instructors'] / $stats['totalUsers']) * 100 : 0 }}%"></div>
                            </div>
                        </div>
                        <div>
                            <div class="flex justify-between text-sm mb-1">
                                <span class="text-gray-600">Students</span>
                                <span class="font-medium text-gray-900">{{ $stats['students'] }}</span>
                            </div>
                            <div class="w-full bg-gray-100 rounded-full h-2">
                                <div class="bg-primary-500 h-2 rounded-full" style="width: {{ $stats['totalUsers'] > 0 ? ($stats['students'] / $stats['totalUsers']) * 100 : 0 }}%"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
