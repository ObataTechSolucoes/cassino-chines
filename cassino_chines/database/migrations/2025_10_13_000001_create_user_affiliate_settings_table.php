<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('user_affiliate_settings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->unique()->constrained()->cascadeOnDelete();
            $table->foreignId('affiliate_plan_id')->nullable()->constrained('affiliate_plans')->nullOnDelete();
            $table->enum('override_type', ['GGR', 'REV_CPA'])->nullable();
            $table->decimal('override_ggr_share', 5, 4)->nullable();
            $table->decimal('override_rev_share', 5, 4)->nullable();
            $table->decimal('override_cpa_amount', 12, 2)->nullable();
            $table->text('note')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('user_affiliate_settings');
    }
};
