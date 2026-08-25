<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Payment extends Model
{
    public const METHODS = [
        'paypal' => 'PayPal',
        'square' => 'Square',
        'stripe' => 'Stripe',
        'credit_card' => 'Credit Card',
        'bank_account' => 'Bank Account',
        'jazzcash' => 'JazzCash',
        'easypaisa' => 'Easypaisa',
    ];

    protected $fillable = [
        'receipt_no',
        'user_id',
        'course_id',
        'amount',
        'currency',
        'method',
        'transaction_ref',
        'status',
        'payer_info',
        'paid_at',
    ];

    protected function casts(): array
    {
        return [
            'payer_info' => 'array',
            'paid_at' => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function course(): BelongsTo
    {
        return $this->belongsTo(Course::class);
    }

    public function methodLabel(): string
    {
        return self::METHODS[$this->method] ?? ucfirst($this->method);
    }

    public static function generateReceiptNo(): string
    {
        do {
            $no = 'RCP-' . now()->format('Ymd') . '-' . strtoupper(substr(uniqid(), -6));
        } while (self::where('receipt_no', $no)->exists());

        return $no;
    }

    public static function generateTransactionRef(): string
    {
        return 'TXN-' . strtoupper(bin2hex(random_bytes(5)));
    }
}
