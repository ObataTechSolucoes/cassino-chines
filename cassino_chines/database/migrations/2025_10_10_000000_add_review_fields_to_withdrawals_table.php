<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('withdrawals', function (Blueprint $table) {
            $table->text('review_notes')->nullable();
            $table->text('denial_reason')->nullable();
            $table->string('review_attachment')->nullable();
            $table->timestamp('reviewed_at')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('withdrawals', function (Blueprint $table) {
            $table->dropColumn(['review_notes', 'denial_reason', 'review_attachment', 'reviewed_at']);
        });
    }
};

