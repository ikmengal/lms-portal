<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('discussions', function (Blueprint $table) {
            $table->boolean('is_answered')->default(false)->after('body');
            $table->foreignId('answered_by')->nullable()->after('is_answered')->constrained('users')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('discussions', function (Blueprint $table) {
            $table->dropColumn(['is_answered', 'answered_by']);
        });
    }
};
