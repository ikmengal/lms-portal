<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class VideoProgress extends Model
{
    protected $fillable = ['user_id', 'lesson_id', 'watched_seconds', 'percentage', 'last_position_at'];

    protected function casts(): array
    {
        return [
            'percentage' => 'decimal:2',
            'last_position_at' => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function lesson(): BelongsTo
    {
        return $this->belongsTo(Lesson::class);
    }
}
