<?php

namespace App\Models;

use App\Models\Concerns\HasLocalizedFields;
use App\Models\LiveClass;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class Course extends Model
{
    use SoftDeletes;
    use HasLocalizedFields;

    protected $fillable = [
        'title',
        'subtitle',
        'slug',
        'description',
        'course_category_id',
        'course_level_id',
        'duration_hours',
        'language',
        'language_code',
        'translations',
        'unlocks_at',
        'price',
        'thumbnail',
        'instructor_id',
    ];

    protected $casts = [
        'translations' => 'array',
        'unlocks_at' => 'datetime',
    ];

    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    public function categoryTerm(): BelongsTo
    {
        return $this->belongsTo(CourseCategory::class, 'course_category_id');
    }

    public function levelTerm(): BelongsTo
    {
        return $this->belongsTo(CourseLevel::class, 'course_level_id');
    }

    /**
     * Legacy-friendly accessors: $course->category / $course->level
     * resolve to the term name from the database.
     */
    public function getCategoryAttribute(): ?string
    {
        return $this->categoryTerm?->name;
    }

    public function getLevelAttribute(): ?string
    {
        return $this->levelTerm?->name;
    }

    public function instructor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'instructor_id');
    }

    public function enrollments(): HasMany
    {
        return $this->hasMany(Enrollment::class);
    }

    public function certificates(): HasMany
    {
        return $this->hasMany(Certificate::class);
    }

    public function modules(): HasMany
    {
        return $this->hasMany(CourseModule::class)->orderBy('sort_order');
    }

    public function reviews(): HasMany
    {
        return $this->hasMany(Review::class)->latest();
    }

    public function quizzes(): HasMany
    {
        return $this->hasMany(Quiz::class);
    }

    public function liveClasses(): HasMany
    {
        return $this->hasMany(LiveClass::class)->orderBy('scheduled_at');
    }

    public function discussions(): HasMany
    {
        return $this->hasMany(Discussion::class);
    }

    public function scopeTrashed(Builder $query): Builder
    {
        return $query->onlyTrashed();
    }
}
