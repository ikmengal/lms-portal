<?php

namespace App\Models;

use App\Models\Concerns\HasActivityLog;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class LiveClass extends Model
{
    use HasActivityLog;
    protected $fillable = [
        'course_id',
        'title',
        'description',
        'join_url',
        'scheduled_at',
        'duration_minutes',
        'reminder_24h_sent_at',
        'reminder_15m_sent_at',
    ];

    protected function casts(): array
    {
        return [
            'scheduled_at' => 'datetime',
            'reminder_24h_sent_at' => 'datetime',
            'reminder_15m_sent_at' => 'datetime',
        ];
    }

    public function course(): BelongsTo
    {
        return $this->belongsTo(Course::class);
    }

    public function attendances(): HasMany
    {
        return $this->hasMany(LiveClassAttendance::class);
    }

    public function isUpcoming(): bool
    {
        return $this->scheduled_at->isFuture();
    }
}
