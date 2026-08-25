<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\Concerns\IsCourseTaxonomy;
use Illuminate\Database\Eloquent\Model;

class CourseLevel extends Model
{
    use IsCourseTaxonomy;
    use SoftDeletes;

    protected $fillable = ['name', 'slug', 'sort_order', 'is_active'];

    public function courses(): HasMany
    {
        return $this->hasMany(Course::class, 'course_level_id');
    }
}
