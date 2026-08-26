<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;

class Quiz extends Model
{
    use SoftDeletes;

    public const TYPES = [
        'quiz' => 'Quiz',
        'assignment' => 'Assignment',
        'final_exam' => 'Final Exam',
    ];

    protected $fillable = [
        'course_id',
        'title',
        'type',
        'description',
        'passing_score',
        'duration_minutes',
        'max_attempts',
        'shuffle_questions',
        'shuffle_options',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
            'shuffle_questions' => 'boolean',
            'shuffle_options' => 'boolean',
        ];
    }

    public function course(): BelongsTo
    {
        return $this->belongsTo(Course::class);
    }

    public function questions(): HasMany
    {
        return $this->hasMany(QuizQuestion::class)->orderBy('sort_order');
    }

    public function attempts(): HasMany
    {
        return $this->hasMany(QuizAttempt::class);
    }

    public function totalPoints(): int
    {
        return (int) $this->questions->sum('points');
    }

    public function completedAttemptsFor(int $userId): int
    {
        return $this->attempts()
            ->where('user_id', $userId)
            ->whereNotNull('completed_at')
            ->count();
    }

    public function attemptsLeftFor(int $userId): ?int
    {
        if (empty($this->max_attempts)) {
            return null; // unlimited
        }

        return max(0, $this->max_attempts - $this->completedAttemptsFor($userId));
    }

    public function bestAttemptFor(int $userId): ?QuizAttempt
    {
        return $this->attempts()
            ->where('user_id', $userId)
            ->whereNotNull('completed_at')
            ->orderByDesc('score')
            ->first();
    }
}
