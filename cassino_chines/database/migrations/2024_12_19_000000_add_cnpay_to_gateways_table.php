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
            // CNPay - Novo provedor de gateway
            $table->string('cnpay_uri')->nullable()->comment('URL da API do CNPay');
            $table->string('cnpay_public_key')->nullable()->comment('Chave pública do CNPay (x-public-key)');
            $table->string('cnpay_secret_key')->nullable()->comment('Chave privada do CNPay (x-secret-key)');
            $table->string('cnpay_webhook_url')->nullable()->comment('URL do webhook do CNPay');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('gateways', function (Blueprint $table) {
            $table->dropColumn([
                'cnpay_uri',
                'cnpay_public_key', 
                'cnpay_secret_key',
                'cnpay_webhook_url'
            ]);
        });
    }
};
