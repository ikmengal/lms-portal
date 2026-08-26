<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;

class QuizQuestion extends Model
{
    use SoftDeletes;

    public const TYPES = [
        'multiple_choice' => 'Multiple Choice (single answer)',
        'true_false' => 'True / False',
        'multiple_answers' => 'Multiple Answers (one or more)',
    ];

    protected $fillable = ['quiz_id', 'question', 'type', 'points', 'sort_order'];

    public function quiz(): BelongsTo
    {
        return $this->belongsTo(Quiz::class);
    }

    public function options(): HasMany
    {
        return $this->hasMany(QuizOption::class);
    }

    public function isMultiAnswer(): bool
    {
        return $this->type === 'multiple_answers';
    }
}
