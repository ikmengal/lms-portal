<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;

class AssignmentSubmission extends Model
{
    use SoftDeletes;

    public const STATUSES = [
        'submitted' => 'Submitted',
        'graded' => 'Graded',
        'late' => 'Late',
    ];

    protected $fillable = [
        'quiz_id',
        'user_id',
        'file_path',
        'file_original_name',
        'status',
        'marks',
        'feedback',
        'submitted_at',
        'graded_at',
        'graded_by',
    ];

    protected function casts(): array
    {
        return [
            'marks' => 'decimal:2',
            'submitted_at' => 'datetime',
            'graded_at' => 'datetime',
        ];
    }

    public function quiz(): BelongsTo
    {
        return $this->belongsTo(Quiz::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function gradedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'graded_by');
    }

    public function isGraded(): bool
    {
        return $this->status === 'graded';
    }

    public function isLate(): bool
    {
        return $this->status === 'late';
    }
}
