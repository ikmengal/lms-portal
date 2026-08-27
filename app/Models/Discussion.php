<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Model;

class Discussion extends Model
{
    protected $fillable = ['user_id', 'course_id', 'lesson_id', 'parent_id', 'body', 'is_answered', 'answered_by'];

    protected function casts(): array
    {
        return [
            'is_answered' => 'boolean',
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

    public function lesson(): BelongsTo
    {
        return $this->belongsTo(Lesson::class);
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(Discussion::class, 'parent_id');
    }

    public function replies(): HasMany
    {
        return $this->hasMany(Discussion::class, 'parent_id')->oldest();
    }

    public function upvotes(): HasMany
    {
        return $this->hasMany(DiscussionUpvote::class);
    }

    public function answeredBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'answered_by');
    }

    public function isUpvotedBy(?User $user): bool
    {
        if (!$user) {
            return false;
        }

        return $this->upvotes()->where('user_id', $user->id)->exists();
    }

    public function upvoteCount(): int
    {
        return $this->upvotes()->count();
    }
}
