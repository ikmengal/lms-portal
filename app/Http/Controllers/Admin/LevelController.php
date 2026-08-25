<?php

namespace App\Http\Controllers\Admin;

use App\Models\CourseLevel;

class LevelController extends TaxonomyCrudController
{
    protected function model(): string
    {
        return CourseLevel::class;
    }

    protected function label(): string
    {
        return 'Levels';
    }

    protected function singular(): string
    {
        return 'Level';
    }

    protected function fkColumn(): string
    {
        return 'course_level_id';
    }

    protected function routePrefix(): string
    {
        return 'admin.levels';
    }
}
