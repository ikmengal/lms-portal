<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\Enrollment;
use App\Models\Payment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class CheckoutController extends Controller
{
    // ---------------- Cart (session based) ----------------

    public function addToCart(Request $request, Course $course)
    {
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

        return view('pages.checkout', [
            'items' => collect([$course]),
            'isCart' => false,
        ]);
    }

    public function checkoutCart()
    {
        $items = Course::whereIn('id', (array) session()->get('cart', []))->get();

        if ($items->isEmpty()) {
            return redirect()->route('cart.index')->with('error', 'Your cart is empty.');
        }

        return view('pages.checkout', [
            'items' => $items,
            'isCart' => true,
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

        [$payments, $newEnrollments] = DB::transaction(function () use ($courses, $method, $validated) {
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
                        'transaction_ref' => Payment::generateTransactionRef(),
                        'status' => 'paid',
                        'payer_info' => $this->maskedPayerInfo($method, $validated),
                        'paid_at' => now(),
                    ]
                );

                $payments[] = $payment;
            }

            return [$payments, $newEnrollments];
        });

        // Notify about brand-new enrollments only (not re-purchases).
        foreach ($newEnrollments as $course) {
            \App\Services\Notifier::courseEnrolled(Auth::user(), $course);
        }

        // Clear purchased items from the cart
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

    // ---------------- Free enrollment ----------------

    public function enrollFree(Request $request, Course $course)
    {
        abort_if($course->price > 0, 403, 'This course requires payment.');

        $enrollment = Enrollment::firstOrCreate([
            'user_id' => Auth::id(),
            'course_id' => $course->id,
        ]);

        if ($enrollment->wasRecentlyCreated) {
            \App\Services\Notifier::courseEnrolled(Auth::user(), $course);
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
