@extends('layouts.dashboard')
@section('title', 'Coupons')

@section('content')
<div class="max-w-4xl mx-auto space-y-6" x-data="{ showCreate: false }">
    {{-- Header --}}
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Coupons</h1>
            <p class="text-sm text-gray-500 mt-0.5">{{ $coupons->total() }} total coupons</p>
        </div>
        <button @click="showCreate = !showCreate" class="inline-flex items-center gap-2 px-4 py-2 text-sm font-medium text-white bg-primary-600 hover:bg-primary-700 rounded-lg transition shadow-sm">
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/></svg>
            New Coupon
        </button>
    </div>

    {{-- Search --}}
    <form method="GET" class="flex gap-2">
        <input type="text" name="search" value="{{ request('search') }}" placeholder="Search coupons..." class="flex-1 px-4 py-2 border border-gray-200 rounded-lg text-sm focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
        <select name="status" class="px-3 py-2 border border-gray-200 rounded-lg text-sm focus:ring-2 focus:ring-primary-500">
            <option value="">All Status</option>
            <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Active</option>
            <option value="inactive" {{ request('status') === 'inactive' ? 'selected' : '' }}>Inactive</option>
            <option value="expired" {{ request('status') === 'expired' ? 'selected' : '' }}>Expired</option>
        </select>
        <button type="submit" class="px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 text-sm font-medium rounded-lg transition">Filter</button>
    </form>

    {{-- Create Form --}}
    <div x-show="showCreate" x-transition class="bg-white border border-gray-100 rounded-xl p-6 shadow-sm">
        <h2 class="font-bold text-gray-900 mb-4">Create New Coupon</h2>
        <form method="POST" action="{{ route('admin.coupons.store') }}">
            @csrf
            <div class="grid sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Code *</label>
                    <input type="text" name="code" value="{{ old('code') }}" required maxlength="30" class="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm uppercase focus:ring-2 focus:ring-primary-500" placeholder="e.g. SUMMER25">
                    @error('code')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Description</label>
                    <input type="text" name="description" value="{{ old('description') }}" maxlength="255" class="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm focus:ring-2 focus:ring-primary-500" placeholder="e.g. Summer sale discount">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Type *</label>
                    <select name="type" class="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm focus:ring-2 focus:ring-primary-500">
                        <option value="percentage" {{ old('type') === 'percentage' ? 'selected' : '' }}>Percentage (%)</option>
                        <option value="fixed" {{ old('type') === 'fixed' ? 'selected' : '' }}>Fixed Amount ($)</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Value *</label>
                    <input type="number" name="value" value="{{ old('value') }}" step="0.01" min="0.01" required class="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm focus:ring-2 focus:ring-primary-500" placeholder="e.g. 25">
                    @error('value')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Min Purchase ($)</label>
                    <input type="number" name="min_purchase" value="{{ old('min_purchase', 0) }}" step="0.01" min="0" class="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm focus:ring-2 focus:ring-primary-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Usage Limit</label>
                    <input type="number" name="usage_limit" value="{{ old('usage_limit') }}" min="1" class="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm focus:ring-2 focus:ring-primary-500" placeholder="Leave empty for unlimited">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Starts At</label>
                    <input type="datetime-local" name="starts_at" value="{{ old('starts_at') }}" class="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm focus:ring-2 focus:ring-primary-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Expires At</label>
                    <input type="datetime-local" name="expires_at" value="{{ old('expires_at') }}" class="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm focus:ring-2 focus:ring-primary-500">
                </div>
            </div>
            <div class="mt-4 flex justify-end gap-2">
                <button type="button" @click="showCreate = false" class="px-4 py-2 text-sm font-medium text-gray-600 hover:text-gray-900 transition">Cancel</button>
                <button type="submit" class="px-5 py-2 text-sm font-medium text-white bg-primary-600 hover:bg-primary-700 rounded-lg transition shadow-sm">Create Coupon</button>
            </div>
        </form>
    </div>

    {{-- Coupons Table --}}
    <div class="bg-white border border-gray-100 rounded-xl shadow-sm overflow-hidden">
        <table class="w-full text-sm">
            <thead>
                <tr class="border-b border-gray-100 text-left text-xs uppercase tracking-wider text-gray-400">
                    <th class="px-5 py-3 font-bold">Code</th>
                    <th class="px-5 py-3 font-bold">Discount</th>
                    <th class="px-5 py-3 font-bold">Min Purchase</th>
                    <th class="px-5 py-3 font-bold">Usage</th>
                    <th class="px-5 py-3 font-bold">Valid</th>
                    <th class="px-5 py-3 font-bold">Status</th>
                    <th class="px-5 py-3 font-bold text-right">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50">
                @forelse($coupons as $coupon)
                    <tr class="hover:bg-gray-50/50">
                        <td class="px-5 py-3">
                            <p class="font-mono font-bold text-gray-900">{{ $coupon->code }}</p>
                            @if($coupon->description)
                                <p class="text-xs text-gray-400 mt-0.5">{{ Str::limit($coupon->description, 40) }}</p>
                            @endif
                        </td>
                        <td class="px-5 py-3 font-semibold text-gray-900">
                            {{ $coupon->type === 'percentage' ? $coupon->value . '%' : '$' . number_format($coupon->value, 2) }}
                        </td>
                        <td class="px-5 py-3 text-gray-600">
                            {{ $coupon->min_purchase > 0 ? '$' . number_format($coupon->min_purchase, 2) : '—' }}
                        </td>
                        <td class="px-5 py-3 text-gray-600">
                            {{ $coupon->used_count }}{{ $coupon->usage_limit ? ' / ' . $coupon->usage_limit : '' }}
                        </td>
                        <td class="px-5 py-3 text-xs text-gray-500">
                            @if($coupon->starts_at)
                                From {{ $coupon->starts_at->format('M j') }}
                            @endif
                            @if($coupon->expires_at)
                                Until {{ $coupon->expires_at->format('M j, Y') }}
                            @endif
                            @if(!$coupon->starts_at && !$coupon->expires_at)
                                No expiry
                            @endif
                        </td>
                        <td class="px-5 py-3">
                            @if(!$coupon->isValid())
                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-700">Expired</span>
                            @elseif($coupon->is_active)
                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-secondary-100 text-secondary-700">Active</span>
                            @else
                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-500">Inactive</span>
                            @endif
                        </td>
                        <td class="px-5 py-3">
                            <div class="flex items-center justify-end gap-1">
                                <form method="POST" action="{{ route('admin.coupons.toggle', $coupon) }}">
                                    @csrf
                                    <button type="submit" class="p-1.5 rounded-lg text-gray-400 hover:text-primary-600 hover:bg-primary-50 transition" title="{{ $coupon->is_active ? 'Deactivate' : 'Activate' }}">
                                        @if($coupon->is_active)
                                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"/></svg>
                                        @else
                                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                        @endif
                                    </button>
                                </form>
                                <form method="POST" action="{{ route('admin.coupons.destroy', $coupon) }}" data-confirm="Delete this coupon?" data-confirm-title="Delete Coupon" data-confirm-icon="warning" data-confirm-button="Delete">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="p-1.5 rounded-lg text-gray-400 hover:text-red-600 hover:bg-red-50 transition" title="Delete">
                                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0"/></svg>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="px-5 py-12 text-center text-gray-400">
                            <svg class="w-10 h-10 mx-auto mb-3 text-gray-300" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M21 11.25v8.25a1.5 1.5 0 01-1.5 1.5H5.25a1.5 1.5 0 01-1.5-1.5v-8.25M12 4.875A2.625 2.625 0 109.375 7.5H12m0-2.625V7.5m0-2.625A2.625 2.625 0 1114.625 7.5H12m0 0V21m-8.625-9.75h18c.621 0 1.125-.504 1.125-1.125v-1.5c0-.621-.504-1.125-1.125-1.125h-18c-.621 0-1.125.504-1.125 1.125v1.5c0 .621.504 1.125 1.125 1.125z"/></svg>
                            <p class="text-sm font-medium">No coupons yet</p>
                            <p class="text-xs mt-1">Create your first coupon to offer discounts to students.</p>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{ $coupons->links() }}
</div>
@endsection