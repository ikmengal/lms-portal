<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('quizzes', function (Blueprint $table) {
            $table->unsignedSmallInteger('max_attempts')->nullable()->after('duration_minutes');
            $table->boolean('shuffle_questions')->default(false)->after('max_attempts');
            $table->boolean('shuffle_options')->default(true)->after('shuffle_questions');
        });

        Schema::table('quiz_questions', function (Blueprint $table) {
            $table->string('type', 30)->default('multiple_choice')->after('question');
        });

        Schema::table('quiz_attempts', function (Blueprint $table) {
            $table->timestamp('started_at')->nullable()->after('answers');
            $table->unsignedInteger('time_spent_seconds')->nullable()->after('started_at');
            $table->json('question_ids')->nullable()->after('time_spent_seconds');
        });
    }

    public function down(): void
    {
        Schema::table('quizzes', function (Blueprint $table) {
            $table->dropColumn(['shuffle_questions', 'shuffle_options', 'max_attempts']);
        });

        Schema::table('quiz_questions', function (Blueprint $table) {
            $table->dropColumn('type');
        });

        Schema::table('quiz_attempts', function (Blueprint $table) {
            $table->dropColumn(['started_at', 'time_spent_seconds', 'question_ids']);
        });
    }
};
