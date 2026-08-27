<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('quizzes', function (Blueprint $table) {
            $table->timestamp('due_date')->nullable()->after('shuffle_options');
            $table->unsignedInteger('max_file_size_mb')->default(10)->after('due_date');
            $table->string('allowed_extensions')->nullable()->after('max_file_size_mb');
        });
    }

    public function down(): void
    {
        Schema::table('quizzes', function (Blueprint $table) {
            $table->dropColumn(['due_date', 'max_file_size_mb', 'allowed_extensions']);
        });
    }
};
