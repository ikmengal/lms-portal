<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\{
    DB, Auth
};
use Illuminate\Http\Request;
use App\Support\ContentDrip;
use App\Services\{
    GamificationService,
    Notifier
};
use App\Models\{
    CouponUsage,
    Enrollment,
    Course,
    Coupon,
    Payment
};

class CheckoutController extends Controller
{
    // ---------------- Cart (session based) ----------------

    public function addToCart(Request $request, Course $course)
    {
        if (ContentDrip::courseComingSoon($course)) {
            if ($request->expectsJson()) {
                return response()->json(['error' => 'This course is coming soon and cannot be added to your cart yet.'], 403);
            }

            return back()->with('error', 'This course opens on ' . $course->unlocks_at->format('M j, Y') . ' and cannot be purchased yet.');
        }

        $cart = session()->get('cart', []);

        if (! in_array($course->id, $cart)) {
            $cart[] = $course->id;
            session()->put('cart', $cart);
        }

        if ($request->expectsJson()) {
            return response()->json(['count' => count($cart)]);
        }

        return back()->with('success', '"' . $course->title . '" added to your cart.');
    }

    public function removeFromCart(Course $course)
    {
        $cart = array_values(array_diff(session()->get('cart', []), [$course->id]));
        session()->put('cart', $cart);

        return redirect()->route('cart.index')->with('success', 'Course removed from your cart.');
    }

    public function cartIndex()
    {
        $courses = Course::whereIn('id', (array) session()->get('cart', []))->get();
        $total = $courses->sum('price');

        return view('pages.cart', compact('courses', 'total'));
    }

    // ---------------- Checkout ----------------

    public function checkoutCourse(Course $course)
    {
        if ($this->alreadyEnrolled($course)) {
            return redirect()->route('learn.start', $course)
                ->with('success', 'You are already enrolled in this course.');
        }

        if (ContentDrip::courseComingSoon($course)) {
            return redirect()->route('courses.show', $course)
                ->with('error', 'This course opens on ' . $course->unlocks_at->format('M j, Y') . '. Enrollment is not available yet.');
        }

        $subtotal = $course->price;
        $discount = 0;
        $couponCode = session('applied_coupon');

        if ($couponCode) {
            $coupon = Coupon::where('code', $couponCode)->first();
            if ($coupon && $coupon->isValid()) {
                $discount = $coupon->calculateDiscount($subtotal);
            } else {
                session()->forget('applied_coupon');
            }
        }

        return view('pages.checkout', [
            'items' => collect([$course]),
            'isCart' => false,
            'subtotal' => $subtotal,
            'discount' => $discount,
            'total' => max(0, $subtotal - $discount),
        ]);
    }

    public function checkoutCart()
    {
        $items = Course::whereIn('id', (array) session()->get('cart', []))->get();

        if ($items->isEmpty()) {
            return redirect()->route('cart.index')->with('error', 'Your cart is empty.');
        }

        $notYetOpen = $items->first(fn ($c) => ContentDrip::courseComingSoon($c));
        if ($notYetOpen) {
            return redirect()->route('cart.index')
                ->with('error', '"' . $notYetOpen->title . '" opens on ' . $notYetOpen->unlocks_at->format('M j, Y') . ' and cannot be purchased yet.');
        }

        $subtotal = $items->sum('price');
        $discount = 0;
        $couponCode = session('applied_coupon');

        if ($couponCode) {
            $coupon = Coupon::where('code', $couponCode)->first();
            if ($coupon && $coupon->isValid()) {
                $discount = $coupon->calculateDiscount($subtotal);
            } else {
                session()->forget('applied_coupon');
            }
        }

        return view('pages.checkout', [
            'items' => $items,
            'isCart' => true,
            'subtotal' => $subtotal,
            'discount' => $discount,
            'total' => max(0, $subtotal - $discount),
        ]);
    }

    public function process(Request $request)
    {
        $validated = $request->validate([
            'method' => ['required', 'in:' . implode(',', array_keys(Payment::METHODS))],
            'payer_email' => ['nullable', 'email'],
            'card_number' => ['nullable', 'digits_between:12,19'],
            'card_name' => ['nullable', 'string', 'max:255'],
            'card_expiry' => ['nullable', 'regex:/^(0[1-9]|1[0-2])\/?([0-9]{2})$/'],
            'card_cvc' => ['nullable', 'digits:3,4'],
            'account_title' => ['nullable', 'string', 'max:255'],
            'account_number' => ['nullable', 'string', 'max:30'],
            'bank_name' => ['nullable', 'string', 'max:255'],
            'mobile_number' => ['nullable', 'regex:/^[0-9+\-\s()]{7,20}$/'],
            'course_ids' => ['required', 'array', 'min:1'],
            'course_ids.*' => ['integer', 'exists:courses,id'],
        ]);

        $method = $validated['method'];

        // Dummy per-method validation (testing gateway simulation)
        $this->validateDummyMethod($request, $method);

        $courses = Course::whereIn('id', $request->input('course_ids'))->get();

        $notYetOpen = $courses->first(fn ($c) => ContentDrip::courseComingSoon($c));
        abort_if(
            $notYetOpen !== null,
            403,
            $notYetOpen
                ? 'This course opens on ' . $notYetOpen->unlocks_at->format('M j, Y') . ' and cannot be purchased yet.'
                : 'This course is not available for purchase yet.'
        );

        // Resolve coupon
        $coupon = null;
        $couponCode = session('applied_coupon');
        if ($couponCode) {
            $coupon = Coupon::where('code', $couponCode)->first();
            if (!$coupon || !$coupon->isValid()) {
                $coupon = null;
                session()->forget('applied_coupon');
            }
        }

        $subtotal = (float) $courses->sum('price');
        $totalDiscount = $coupon ? $coupon->calculateDiscount($subtotal) : 0;
        $finalTotal = max(0, $subtotal - $totalDiscount);

        // Per-course discount split proportionally
        $discountPerCourse = [];
        if ($totalDiscount > 0 && $subtotal > 0) {
            foreach ($courses as $course) {
                $ratio = (float) $course->price / $subtotal;
                $discountPerCourse[$course->id] = round($totalDiscount * $ratio, 2);
            }
        }

        [$payments, $newEnrollments] = DB::transaction(function () use ($courses, $method, $validated, $coupon, $discountPerCourse) {
            $payments = [];
            $newEnrollments = [];

            foreach ($courses as $course) {
                $enrollment = Enrollment::firstOrCreate([
                    'user_id' => Auth::id(),
                    'course_id' => $course->id,
                ]);

                if ($enrollment->wasRecentlyCreated) {
                    $newEnrollments[] = $course;
                }

                $courseDiscount = $discountPerCourse[$course->id] ?? 0;
                $courseFinal = max(0, (float) $course->price - $courseDiscount);

                $payment = Payment::updateOrCreate(
                    [
                        'user_id' => Auth::id(),
                        'course_id' => $course->id,
                    ],
                    [
                        'receipt_no' => Payment::generateReceiptNo(),
                        'amount' => $course->price,
                        'currency' => 'USD',
                        'method' => $method,
                        'coupon_id' => $coupon?->id,
                        'discount_amount' => $courseDiscount,
                        'final_amount' => $courseFinal,
                        'transaction_ref' => Payment::generateTransactionRef(),
                        'status' => 'paid',
                        'payer_info' => $this->maskedPayerInfo($method, $validated),
                        'paid_at' => now(),
                    ]
                );

                $payments[] = $payment;
            }

            // Record coupon usage
            if ($coupon && $totalDiscount > 0) {
                CouponUsage::create([
                    'coupon_id' => $coupon->id,
                    'user_id' => Auth::id(),
                    'payment_id' => $payments[0]->id,
                    'discount_amount' => $totalDiscount,
                ]);
                $coupon->increment('used_count');
            }

            return [$payments, $newEnrollments];
        });

        // Notify about brand-new enrollments only (not re-purchases).
        foreach ($newEnrollments as $course) {
            Notifier::courseEnrolled(Auth::user(), $course);
            GamificationService::recordFirstEnrollment(Auth::user());
        }

        // Clear applied coupon and purchased items from cart
        session()->forget('applied_coupon');
        $remaining = array_diff(session()->get('cart', []), $courses->pluck('id')->all());
        session()->put('cart', array_values($remaining));

        // Remember all receipts from this checkout so the receipt page can group them
        session()->put('bulk_receipts', collect($payments)->pluck('id')->all());

        return redirect()->route('receipts.show', $payments[0])
            ->with('success', 'Payment successful! You are now enrolled.');
    }

    public function showReceipt(Payment $payment)
    {
        abort_unless($payment->user_id === Auth::id() || Auth::user()->hasRole('admin'), 403);

        if ($bulkIds = session()->pull('bulk_receipts')) {
            $relatedPayments = Payment::whereIn('id', $bulkIds)->where('user_id', $payment->user_id)->orderBy('course_id')->get();
        } else {
            $relatedPayments = collect([$payment]);
        }

        $total = $relatedPayments->sum('amount');

        return view('pages.receipt', compact('payment', 'relatedPayments', 'total'));
    }

    // ---------------- Coupon ----------------

    public function applyCoupon(Request $request)
    {
        $request->validate(['coupon_code' => 'required|string']);

        $coupon = Coupon::where('code', strtoupper($request->input('coupon_code')))->first();

        if (!$coupon || !$coupon->isValid()) {
            return back()->with('coupon_error', 'Invalid or expired coupon code.');
        }

        if ($coupon->isUsedBy(Auth::id())) {
            return back()->with('coupon_error', 'You have already used this coupon.');
        }

        $subtotal = $this->cartSubtotal();
        if ($coupon->min_purchase > 0 && $subtotal < $coupon->min_purchase) {
            return back()->with('coupon_error', 'Minimum purchase of $' . number_format($coupon->min_purchase, 2) . ' required for this coupon.');
        }

        $discount = $coupon->calculateDiscount($subtotal);
        session()->put('applied_coupon', $coupon->code);

        return back()->with('coupon_success', "Coupon \"{$coupon->code}\" applied! You save $" . number_format($discount, 2) . '.');
    }

    public function removeCoupon()
    {
        session()->forget('applied_coupon');
        return back()->with('success', 'Coupon removed.');
    }

    private function cartSubtotal(): float
    {
        $cartIds = session()->get('cart', []);
        if (empty($cartIds)) return 0;
        return (float) Course::whereIn('id', $cartIds)->sum('price');
    }

    // ---------------- Free enrollment ----------------

    public function enrollFree(Request $request, Course $course)
    {
        abort_if($course->price > 0, 403, 'This course requires payment.');
        abort_if(ContentDrip::courseComingSoon($course), 403, 'This course is not available for enrollment yet.');

        $enrollment = Enrollment::firstOrCreate([
            'user_id' => Auth::id(),
            'course_id' => $course->id,
        ]);

        if ($enrollment->wasRecentlyCreated) {
            Notifier::courseEnrolled(Auth::user(), $course);
        }

        if ($request->boolean('from_cart')) {
            session()->put('cart', array_values(array_diff(session()->get('cart', []), [$course->id])));
        }

        return redirect()->route('learn.start', $course)
            ->with('success', 'You are enrolled! Start learning now.');
    }

    // ---------------- Helpers ----------------

    private function alreadyEnrolled(Course $course): bool
    {
        return Enrollment::where('user_id', Auth::id())->where('course_id', $course->id)->exists();
    }

    private function validateDummyMethod(Request $request, string $method): void
    {
        $rules = match ($method) {
            'paypal', 'stripe' => ['payer_email' => ['required', 'email']],
            'square', 'credit_card' => [
                'card_number' => ['required', 'digits_between:12,19'],
                'card_name' => ['required', 'string', 'max:255'],
                'card_expiry' => ['required', 'regex:/^(0[1-9]|1[0-2])\/?([0-9]{2})$/'],
                'card_cvc' => ['required', 'digits_between:3,4'],
            ],
            'bank_account' => [
                'account_title' => ['required', 'string', 'max:255'],
                'account_number' => ['required', 'string', 'max:30'],
                'bank_name' => ['required', 'string', 'max:255'],
            ],
            'jazzcash', 'easypaisa' => ['mobile_number' => ['required', 'regex:/^(\+92|0)?3[0-9]{9}$|^0?3[0-9]{2}[- ]?[0-9]{7}$/']],
            default => [],
        };

        $request->validate($rules);
    }

    private function maskedPayerInfo(string $method, array $data): array
    {
        return match ($method) {
            'paypal', 'stripe' => ['email' => $data['payer_email']],
            'square', 'credit_card' => [
                'card' => '**** **** **** ' . substr(preg_replace('/\D/', '', $data['card_number'] ?? ''), -4),
                'name' => $data['card_name'] ?? null,
            ],
            'bank_account' => [
                'bank' => $data['bank_name'],
                'account_title' => $data['account_title'],
                'account_no_masked' => '****' . substr($data['account_number'], -4),
            ],
            'jazzcash', 'easypaisa' => ['mobile' => '****' . substr(preg_replace('/\D/', '', $data['mobile_number']), -4)],
            default => [],
        };
    }
}
