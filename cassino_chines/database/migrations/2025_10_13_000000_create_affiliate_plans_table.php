<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('affiliate_plans', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->enum('type', ['GGR', 'REV_CPA']);
            $table->decimal('ggr_share', 5, 4)->nullable();
            $table->decimal('rev_share', 5, 4)->nullable();
            $table->decimal('cpa_amount', 12, 2)->nullable();
            $table->unsignedInteger('cpa_ftd_min')->nullable();
            $table->boolean('active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('affiliate_plans');
    }
};
