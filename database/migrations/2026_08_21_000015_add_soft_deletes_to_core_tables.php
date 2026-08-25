<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->softDeletes();
        });

        Schema::table('course_modules', function (Blueprint $table) {
            $table->softDeletes();
        });

        Schema::table('lessons', function (Blueprint $table) {
            $table->softDeletes();
        });

        Schema::table('enrollments', function (Blueprint $table) {
            $table->softDeletes();
        });

        Schema::table('certificates', function (Blueprint $table) {
            $table->softDeletes();
        });

        Schema::table('reviews', function (Blueprint $table) {
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::table('reviews', fn (Blueprint $t) => $t->dropSoftDeletes());
        Schema::table('certificates', fn (Blueprint $t) => $t->dropSoftDeletes());
        Schema::table('enrollments', fn (Blueprint $t) => $t->dropSoftDeletes());
        Schema::table('lessons', fn (Blueprint $t) => $t->dropSoftDeletes());
        Schema::table('course_modules', fn (Blueprint $t) => $t->dropSoftDeletes());
        Schema::table('users', fn (Blueprint $t) => $t->dropSoftDeletes());
    }
};
