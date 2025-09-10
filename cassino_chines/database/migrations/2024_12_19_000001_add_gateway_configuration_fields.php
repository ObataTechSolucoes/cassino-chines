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
        Schema::table('gateways', function (Blueprint $table) {
            // Campos para configuração de gateways ativos
            $table->string('gateway_padrao_saque')->default('suitpay')->comment('Gateway padrão para saques');
            $table->json('gateways_ativos')->nullable()->comment('Lista de gateways ativos');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('gateways', function (Blueprint $table) {
            $table->dropColumn([
                'gateway_padrao_saque',
                'gateways_ativos'
            ]);
        });
    }
};
