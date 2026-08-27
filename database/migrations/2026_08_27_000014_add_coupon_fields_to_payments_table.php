<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('payments', function (Blueprint $table) {
            $table->foreignId('coupon_id')->nullable()->after('method')->constrained('coupons')->nullOnDelete();
            $table->decimal('discount_amount', 8, 2)->default(0)->after('coupon_id');
            $table->decimal('final_amount', 8, 2)->nullable()->after('discount_amount');
        });
    }

    public function down(): void
    {
        Schema::table('payments', function (Blueprint $table) {
            $table->dropForeign(['coupon_id']);
            $table->dropColumn(['coupon_id', 'discount_amount', 'final_amount']);
        });
    }
};
