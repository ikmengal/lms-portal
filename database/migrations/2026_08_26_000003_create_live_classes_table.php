<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('live_classes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('course_id')->constrained()->cascadeOnDelete();
            $table->string('title');
            $table->text('description')->nullable();
            $table->string('join_url', 2048);
            $table->timestamp('scheduled_at');
            $table->unsignedSmallInteger('duration_minutes')->default(60);
            $table->timestamp('reminder_24h_sent_at')->nullable();
            $table->timestamp('reminder_15m_sent_at')->nullable();
            $table->timestamps();

            $table->index(['scheduled_at', 'reminder_24h_sent_at', 'reminder_15m_sent_at'], 'live_classes_reminder_lookup');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('live_classes');
    }
};
