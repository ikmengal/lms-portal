<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;

class QuizAttempt extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'user_id',
        'quiz_id',
        'answers',
        'score',
        'passed',
        'started_at',
        'completed_at',
        'time_spent_seconds',
        'question_ids',
    ];

    protected function casts(): array
    {
        return [
            'answers' => 'array',
            'question_ids' => 'array',
            'passed' => 'boolean',
            'started_at' => 'datetime',
            'completed_at' => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function quiz(): BelongsTo
    {
        return $this->belongsTo(Quiz::class);
    }

    public function isCompleted(): bool
    {
        return $this->completed_at !== null;
    }

    public function formattedTimeSpent(): string
    {
        if ($this->time_spent_seconds === null) {
            return '—';
        }

        $m = intdiv($this->time_spent_seconds, 60);
        $s = $this->time_spent_seconds % 60;

        return $m > 0 ? sprintf('%dm %02ds', $m, $s) : sprintf('%ds', $s);
    }
}
