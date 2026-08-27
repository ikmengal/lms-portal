<?php

namespace Database\Seeders;

use App\Models\Coupon;
use App\Models\CouponUsage;
use App\Models\Course;
use App\Models\Payment;
use App\Models\User;
use Illuminate\Database\Seeder;

class PaymentsSeeder extends Seeder
{
    /**
     * user email => course slug => [method, coupon code or null, months ago paid]
     */
    private const PLANS = [
        'student@lmsportal.com' => [
            'complete-web-development-bootcamp' => ['stripe', 'WELCOME10', 2],
            'php-laravel-for-beginners' => ['paypal', 'SAVE20', 2],
            'javascript-es6-mastery' => ['credit_card', null, 1],
            'mysql-database-design-administration' => ['jazzcash', null, 1],
            'uiux-design-fundamentals-with-figma' => ['easypaisa', null, 0],
        ],
        'emma.wilson@example.com' => [
            'php-laravel-for-beginners' => ['stripe', 'WELCOME10', 3],
            'mysql-database-design-administration' => ['bank_account', null, 2],
            'mobile-app-development-with-flutter' => ['paypal', null, 1],
        ],
        'david.kim@example.com' => [
            'complete-web-development-bootcamp' => ['credit_card', null, 2],
            'javascript-es6-mastery' => ['square', null, 2],
            'cloud-computing-with-aws' => ['stripe', 'SUMMER15', 1],
        ],
        'sofia.garcia@example.com' => [
            'python-for-data-science-analytics' => ['paypal', 'SUMMER15', 2],
            'uiux-design-fundamentals-with-figma' => ['credit_card', null, 1],
            'javascript-es6-mastery' => ['jazzcash', null, 1],
        ],
        'james.patel@example.com' => [
            'complete-web-development-bootcamp' => ['bank_account', null, 1],
            'mobile-app-development-with-flutter' => ['credit_card', null, 0],
        ],
    ];

    public function run(): void
    {
        $coupons = $this->seedCoupons();

        foreach (self::PLANS as $email => $courses) {
            $user = User::where('email', $email)->first();
            if (!$user) {
                continue;
            }

            foreach ($courses as $courseSlug => [$method, $couponCode, $monthsAgo]) {
                $course = Course::where('slug', $courseSlug)->first();
                if (!$course) {
                    continue;
                }

                $coupon = $couponCode ? ($coupons[$couponCode] ?? null) : null;

                $amount = (float) $course->price;
                $discount = $coupon ? $coupon->calculateDiscount($amount) : 0.0;
                $finalAmount = round($amount - $discount, 2);

                $paidAt = now()->subMonths($monthsAgo)->subDays(rand(1, 20));

                $payment = Payment::firstOrCreate(
                    ['user_id' => $user->id, 'course_id' => $course->id],
                    [
                        'receipt_no' => Payment::generateReceiptNo(),
                        'amount' => $amount,
                        'currency' => 'USD',
                        'method' => $method,
                        'coupon_id' => $coupon?->id,
                        'discount_amount' => $discount,
                        'final_amount' => $finalAmount,
                        'transaction_ref' => Payment::generateTransactionRef(),
                        'status' => 'paid',
                        'payer_info' => ['name' => $user->name, 'email' => $user->email],
                        'paid_at' => $paidAt,
                    ]
                );

                if ($coupon && $discount > 0) {
                    CouponUsage::firstOrCreate(
                        ['coupon_id' => $coupon->id, 'user_id' => $user->id],
                        ['payment_id' => $payment->id, 'discount_amount' => $discount]
                    );
                }
            }
        }

        // Keep used_count in sync with actual usage rows.
        foreach ($coupons as $coupon) {
            $coupon->update(['used_count' => $coupon->usages()->count()]);
        }
    }

    private function seedCoupons(): array
    {
        $blueprint = [
            [
                'code' => 'WELCOME10', 'description' => '10% off your first course',
                'type' => 'percentage', 'value' => 10, 'min_purchase' => 20,
                'usage_limit' => 100, 'starts_at' => now()->subMonths(2), 'expires_at' => now()->addMonths(2),
            ],
            [
                'code' => 'SAVE20', 'description' => '$20 off any course over $30',
                'type' => 'fixed', 'value' => 20, 'min_purchase' => 30,
                'usage_limit' => 50, 'starts_at' => now()->subMonths(2), 'expires_at' => now()->addMonth(),
            ],
            [
                'code' => 'SUMMER15', 'description' => 'Summer sale: 15% off everything',
                'type' => 'percentage', 'value' => 15, 'min_purchase' => 0,
                'usage_limit' => null, 'starts_at' => now()->subMonth(), 'expires_at' => now()->addMonths(3),
            ],
            [
                'code' => 'LAUNCH25', 'description' => 'Launch week: 25% off (expired)',
                'type' => 'percentage', 'value' => 25, 'min_purchase' => 10,
                'usage_limit' => 10, 'starts_at' => now()->subMonths(3), 'expires_at' => now()->subDays(10),
            ],
        ];

        $coupons = [];

        foreach ($blueprint as $data) {
            $coupon = Coupon::firstOrCreate(['code' => $data['code']], $data);
            $coupons[$coupon->code] = $coupon;
        }

        return $coupons;
    }
}