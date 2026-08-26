<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Certificate extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'code',
        'user_id',
        'course_id',
        'issued_at',
    ];

    protected function casts(): array
    {
        return [
            'issued_at' => 'datetime',
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

    /**
     * Human-friendly, URL-safe certificate number in the documented
     * "LMS-XXXXXXXXXX" format. Ambiguous characters (0/O, 1/I/L) are excluded.
     * Retries until a collision-free code is found (unique DB index is the guard).
     */
    public static function generateCode(): string
    {
        $alphabet = '23456789ABCDEFGHJKMNPQRSTUVWXYZ';

        do {
            $code = 'LMS-' . collect(range(1, 10))
                ->map(fn () => $alphabet[random_int(0, strlen($alphabet) - 1)])
                ->implode('');
        } while (self::withTrashed()->where('code', $code)->exists());

        return $code;
    }

    public function verificationUrl(): string
    {
        return url('/verify-certificate/' . $this->code);
    }
}
