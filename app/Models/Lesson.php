<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;

class Lesson extends Model
{
    use SoftDeletes;

    protected $fillable = ['course_module_id', 'title', 'duration_minutes', 'sort_order', 'video_url', 'description'];

    public function module(): BelongsTo
    {
        return $this->belongsTo(CourseModule::class, 'course_module_id');
    }

    public function resources(): HasMany
    {
        return $this->hasMany(LessonResource::class);
    }

    public function isCompletedBy(User $user): bool
    {
        return LessonProgress::where('user_id', $user->id)->where('lesson_id', $this->id)->exists();
    }
}
