<?php

namespace App\Models;

use App\Models\Concerns\HasLocalizedFields;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;

class Lesson extends Model
{
    use SoftDeletes;
    use HasLocalizedFields;

    protected $fillable = ['course_module_id', 'title', 'translations', 'unlocks_at', 'duration_minutes', 'sort_order', 'video_url', 'description'];

    protected $casts = [
        'translations' => 'array',
        'unlocks_at' => 'datetime',
    ];

    public function module(): BelongsTo
    {
        return $this->belongsTo(CourseModule::class, 'course_module_id');
    }

    public function resources(): HasMany
    {
        return $this->hasMany(LessonResource::class);
    }

    public function discussions(): HasMany
    {
        return $this->hasMany(Discussion::class);
    }

    public function videoProgress(): HasMany
    {
        return $this->hasMany(VideoProgress::class);
    }

    public function isCompletedBy(User $user): bool
    {
        return LessonProgress::where('user_id', $user->id)->where('lesson_id', $this->id)->exists();
    }
}
