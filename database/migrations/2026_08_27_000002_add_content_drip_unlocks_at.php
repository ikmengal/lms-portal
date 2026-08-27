<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('courses', function (Blueprint $table) {
            $table->timestamp('unlocks_at')->nullable()->after('translations');
        });

        Schema::table('course_modules', function (Blueprint $table) {
            $table->timestamp('unlocks_at')->nullable()->after('translations');
        });

        Schema::table('lessons', function (Blueprint $table) {
            $table->timestamp('unlocks_at')->nullable()->after('translations');
        });
    }

    public function down(): void
    {
        Schema::table('lessons', function (Blueprint $table) {
            $table->dropColumn('unlocks_at');
        });

        Schema::table('course_modules', function (Blueprint $table) {
            $table->dropColumn('unlocks_at');
        });

        Schema::table('courses', function (Blueprint $table) {
            $table->dropColumn('unlocks_at');
        });
    }
};