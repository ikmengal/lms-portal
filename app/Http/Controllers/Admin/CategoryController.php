<?php

namespace App\Http\Controllers\Admin;

use App\Models\CourseCategory;

class CategoryController extends TaxonomyCrudController
{
    protected function model(): string
    {
        return CourseCategory::class;
    }

    protected function label(): string
    {
        return 'Categories';
    }

    protected function singular(): string
    {
        return 'Category';
    }

    protected function fkColumn(): string
    {
        return 'course_category_id';
    }

    protected function routePrefix(): string
    {
        return 'admin.categories';
    }
}
