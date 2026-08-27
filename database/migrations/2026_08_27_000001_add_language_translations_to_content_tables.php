<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('courses', function (Blueprint $table) {
            $table->string('subtitle')->nullable()->after('description');
            $table->string('language_code', 5)->default('en')->after('language');
            $table->json('translations')->nullable()->after('language_code');
        });

        Schema::table('course_modules', function (Blueprint $table) {
            $table->json('translations')->nullable()->after('title');
        });

        Schema::table('lessons', function (Blueprint $table) {
            $table->json('translations')->nullable()->after('title');
        });
    }

    public function down(): void
    {
        Schema::table('lessons', function (Blueprint $table) {
            $table->dropColumn('translations');
        });

        Schema::table('course_modules', function (Blueprint $table) {
            $table->dropColumn('translations');
        });

        Schema::table('courses', function (Blueprint $table) {
            $table->dropColumn(['translations', 'language_code', 'subtitle']);
        });
    }
};