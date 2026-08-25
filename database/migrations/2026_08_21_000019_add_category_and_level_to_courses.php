<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

return new class extends Migration
{
    private const CATEGORIES = [
        'Web Development', 'Data Science', 'Artificial Intelligence', 'Mobile Development',
        'Cloud Computing', 'Cyber Security', 'DevOps', 'Project Management',
        'Software Development', 'Digital Marketing', 'Business', 'Design',
        'Programming', 'Databases',
    ];

    private const LEVELS = ['Beginner', 'Intermediate', 'Advanced', 'Beginner to Advanced'];

    public function up(): void
    {
        foreach (self::CATEGORIES as $i => $name) {
            DB::table('course_categories')->insert([
                'name' => $name, 'slug' => Str::slug($name), 'sort_order' => $i,
                'is_active' => true, 'created_at' => now(), 'updated_at' => now(),
            ]);
        }

        foreach (self::LEVELS as $i => $name) {
            DB::table('course_levels')->insert([
                'name' => $name, 'slug' => Str::slug($name), 'sort_order' => $i,
                'is_active' => true, 'created_at' => now(), 'updated_at' => now(),
            ]);
        }

        Schema::table('courses', function (Blueprint $table) {
            $table->foreignId('course_category_id')->nullable()->after('title')->constrained('course_categories')->nullOnDelete();
            $table->foreignId('course_level_id')->nullable()->after('course_category_id')->constrained('course_levels')->nullOnDelete();
        });

        $categories = DB::table('course_categories')->get();
        $levels = DB::table('course_levels')->get();

        // If upgrading from legacy string columns, backfill by name.
        if (Schema::hasColumn('courses', 'category')) {
            foreach (DB::table('courses')->select('id', 'category', 'level')->get() as $course) {
                DB::table('courses')->where('id', $course->id)->update([
                    'course_category_id' => $categories->first(fn ($c) => strcasecmp($c->name, $course->category) === 0)?->id,
                    'course_level_id' => $levels->first(fn ($l) => strcasecmp($l->name, $course->level) === 0)?->id,
                ]);
            }
        }

        Schema::table('courses', function (Blueprint $table) {
            if (Schema::hasColumn('courses', 'category')) {
                $table->dropColumn(['category', 'level']);
            }
        });
    }

    public function down(): void
    {
        Schema::table('courses', function (Blueprint $table) {
            $table->dropConstrainedForeignId('course_category_id');
            $table->dropConstrainedForeignId('course_level_id');
        });

        DB::table('course_categories')->whereIn('slug', array_map(fn ($n) => Str::slug($n), self::CATEGORIES))->delete();
        DB::table('course_levels')->whereIn('slug', array_map(fn ($n) => Str::slug($n), self::LEVELS))->delete();
    }
};
