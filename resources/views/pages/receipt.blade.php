@extends('layouts.app')

@php
    $brand = \App\Support\Brand::data();
    $totalDiscount = $relatedPayments->sum('discount_amount');
    $totalAmount = $relatedPayments->sum('amount');
    $totalFinal = $relatedPayments->sum('final_amount') ?: $totalAmount;
@endphp

@section('title', 'Payment Receipt')

@push('styles')
    <style>
        @media print {
            body * { visibility: hidden; }
            #receipt-card, #receipt-card * { visibility: visible; }
            #receipt-card { position: absolute; left: 0; top: 0; width: 100%; box-shadow: none !important; border: none !important; }
            .no-print { display: none !important; }
        }
        @page { margin: 12mm; }
    </style>
@endpush

@section('content')
    <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 py-10">

        {{-- Success banner --}}
        <div class="no-print mb-6 p-5 bg-secondary-50 border border-secondary-200 rounded-xl flex items-center gap-3">
            <span class="w-10 h-10 bg-secondary-500 rounded-full grid place-items-center shrink-0">
                <svg class="w-5 h-5 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3"><path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5"/></svg>
            </span>
            <div>
                <p class="font-bold text-secondary-800">Payment successful — you're enrolled!</p>
                <p class="text-sm text-secondary-700">A copy of this receipt has been saved to your account.</p>
            </div>
        </div>

        {{-- Receipt --}}
        <div id="receipt-card" class="bg-white rounded-2xl border border-gray-100 shadow-lg overflow-hidden">
            {{-- Header --}}
            <div class="px-8 py-6 border-b-4" style="border-color: {{ $brand['primary'] }};">
                <div class="flex items-center justify-between gap-4">
                    <div class="flex items-center gap-2">
                        @if($brand['logoUrl'])
                            <img src="{{ $brand['logoUrl'] }}" alt="{{ $brand['name'] }}" class="h-9 w-auto max-w-[160px] object-contain">
                        @else
                            <span style="color: {{ $brand['primaryDarker'] }}" class="text-xl font-bold">{{ $brand['wordmarkMain'] }}</span><span style="color: {{ $brand['accent'] }}" class="text-xl font-bold">{{ $brand['wordmarkAccent'] ? ' ' . $brand['wordmarkAccent'] : '' }}</span>
                        @endif
                    </div>
                    <div class="text-right">
                        <p class="text-[11px] uppercase tracking-widest font-bold text-gray-400">Payment Receipt</p>
                        <p class="font-mono font-bold text-gray-900">{{ $payment->receipt_no }}</p>
                    </div>
                </div>
            </div>

            <div class="p-8">
                {{-- Meta grid --}}
                <div class="grid sm:grid-cols-2 gap-x-8 gap-y-4 mb-7">
                    <div>
                        <p class="text-[11px] uppercase tracking-wider text-gray-400 mb-0.5">Date</p>
                        <p class="text-sm font-semibold text-gray-900">{{ $payment->paid_at?->format('M d, Y — h:i A') }}</p>
                    </div>
                    <div>
                        <p class="text-[11px] uppercase tracking-wider text-gray-400 mb-0.5">Status</p>
                        <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-bold bg-secondary-100 text-secondary-700 uppercase">
                            <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.857-9.809a.75.75 0 00-1.214-.882l-3.483 4.79-1.88-1.88a.75.75 0 10-1.06 1.061l2.5 2.5a.75.75 0 001.137-.089l4-5.5z" clip-rule="evenodd"/>{{ $payment->status }}</span>
                    </div>
                    <div>
                        <p class="text-[11px] uppercase tracking-wider text-gray-400 mb-0.5">Billed To</p>
                        <p class="text-sm font-semibold text-gray-900">{{ $payment->user->name }}</p>
                        <p class="text-xs text-gray-500">{{ $payment->user->email }}</p>
                    </div>
                    <div>
                        <p class="text-[11px] uppercase tracking-wider text-gray-400 mb-0.5">Payment Method</p>
                        <p class="text-sm font-semibold text-gray-900">{{ $payment->methodLabel() }}</p>
                        @if(!empty($payment->payer_info))
                            <p class="text-xs text-gray-500">
                                @if(isset($payment->payer_info['card']))
                                    Card {{ $payment->payer_info['card'] }}
                                @elseif(isset($payment->payer_info['email']))
                                    {{ $payment->payer_info['email'] }}
                                @elseif(isset($payment->payer_info['mobile']))
                                    {{ $payment->payer_info['mobile'] }}
                                @elseif(isset($payment->payer_info['bank']))
                                    {{ $payment->payer_info['bank'] }} · {{ $payment->payer_info['account_no_masked'] }}
                                @endif
                            </p>
                        @endif
                    </div>
                    <div>
                        <p class="text-[11px] uppercase tracking-wider text-gray-400 mb-0.5">Transaction Reference</p>
                        <p class="text-sm font-mono font-semibold text-gray-900">{{ $payment->transaction_ref }}</p>
                    </div>
                </div>

                {{-- Items table --}}
                <table class="w-full text-sm mb-2">
                    <thead>
                        <tr class="border-y border-gray-200 text-[11px] uppercase tracking-wider text-gray-400">
                            <th class="py-2.5 text-left font-bold">Course</th>
                            <th class="py-2.5 text-center font-bold">Level</th>
                            <th class="py-2.5 text-right font-bold">Amount</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50">
                        @foreach($relatedPayments as $rp)
                            <tr>
                                <td class="py-3 pr-4">
                                    <p class="font-medium text-gray-900">{{ $rp->course->title }}</p>
                                    <p class="text-xs text-gray-400">{{ $rp->course->instructor?->name ?? 'Instructor' }} · Lifetime access</p>
                                </td>
                                <td class="py-3 text-center text-gray-600">{{ $rp->course->level ?? '—' }}</td>
                                <td class="py-3 text-right font-semibold text-gray-900">${{ number_format($rp->amount, 2) }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                    <tfoot>
                        <tr>
                            <td colspan="2" class="pt-4 pb-1 text-right text-gray-500">Subtotal</td>
                            <td class="pt-4 pb-1 text-right text-gray-800">${{ number_format($totalAmount, 2) }}</td>
                        </tr>
                        @if($totalDiscount > 0)
                            <tr>
                                <td colspan="2" class="pb-1 text-right text-secondary-600">Discount</td>
                                <td class="pb-1 text-right text-secondary-600 font-medium">-${{ number_format($totalDiscount, 2) }}</td>
                            </tr>
                            @if($relatedPayments->first()?->coupon)
                                <tr>
                                    <td colspan="2" class="pb-1 text-right text-gray-400 text-xs">Coupon: {{ $relatedPayments->first()->coupon->code }}</td>
                                    <td></td>
                                </tr>
                            @endif
                        @endif
                        <tr>
                            <td colspan="2" class="py-2 text-right font-bold text-gray-900 text-base">Total Paid</td>
                            <td class="py-2 text-right font-bold text-base" style="color: {{ $brand['primary'] }}">${{ number_format($totalFinal > 0 ? $totalFinal : $totalAmount, 2) }} {{ $payment->currency }}</td>
                        </tr>
                    </tfoot>
                </table>

                <p class="mt-6 pt-5 border-t border-dashed border-gray-200 text-[11px] text-gray-400 leading-relaxed">
                    This is a computer-generated receipt for a test-mode transaction; no real funds were captured.
                    Keep it for your records. For support contact
                    <a href="mailto:{{ $brand['supportEmail'] }}" style="color: {{ $brand['primary'] }}">{{ $brand['supportEmail'] }}</a>.
                    &copy; {{ date('Y') }} {{ $brand['name'] }}.
                </p>
            </div>
        </div>

        {{-- Actions --}}
        <div class="no-print mt-6 flex flex-col sm:flex-row items-center justify-between gap-3">
            <button type="button" onclick="window.print()"
                class="inline-flex items-center gap-2 px-6 py-3 bg-white border-2 border-primary-200 text-primary-700 font-semibold rounded-xl hover:bg-primary-50 transition w-full sm:w-auto justify-center">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6.72 13.829c-.24.03-.48.062-.72.096m.72-.096a42.415 42.415 0 0110.56 0m-10.56 0L6.34 18m10.94-4.171c.24.03.48.062.72.096m-.72-.096L17.66 18m0 0l.229 2.523a1.125 1.125 0 01-1.12 1.227H7.231c-.662 0-1.18-.568-1.12-1.227L6.34 18m11.318 0h1.091A2.25 2.25 0 0021 15.75V9.456c0-1.081-.768-2.015-1.837-2.175a48.055 48.055 0 00-1.913-.247M6.34 18H5.25A2.25 2.25 0 013 15.75V9.456c0-1.081.768-2.015 1.837-2.175a48.041 48.041 0 011.913-.247m10.5 0a48.536 48.536 0 00-10.5 0m10.5 0V3.375c0-.621-.504-1.125-1.125-1.125h-8.25c-.621 0-1.125.504-1.125 1.125v3.659M18 10.5h.008v.008H18V10.5z"/></svg>
                Print / Save PDF
            </button>
            <div class="flex items-center gap-3 w-full sm:w-auto">
                @if(count($relatedPayments) > 1)
                    @foreach($relatedPayments->skip(1) as $rp)
                        <a href="{{ route('receipts.show', $rp) }}" class="text-sm text-primary-600 hover:text-primary-700 font-medium underline decoration-dotted hidden sm:inline">Receipt {{ $rp->receipt_no }}</a>
                    @endforeach
                @endif
                <a href="{{ route('learn.start', $payment->course) }}"
                    class="inline-flex items-center justify-center gap-2 px-6 py-3 bg-primary-600 hover:bg-primary-700 text-white font-bold rounded-xl transition shadow-lg shadow-primary-600/25 w-full sm:w-auto">
                    Start Learning
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5L21 12l-7.5 7.5M21 12H3"/></svg>
                </a>
            </div>
        </div>
    </div>
@endsection
