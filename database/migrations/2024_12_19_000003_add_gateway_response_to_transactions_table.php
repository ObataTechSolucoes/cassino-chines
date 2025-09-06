<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('transactions', function (Blueprint $table) {
            $table->json('gateway_response')->nullable()->comment('Resposta completa do gateway de pagamento');
            $table->string('gateway_name')->nullable()->comment('Nome do gateway usado na transação');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('transactions', function (Blueprint $table) {
            $table->dropColumn(['gateway_response', 'gateway_name']);
        });
    }
};
