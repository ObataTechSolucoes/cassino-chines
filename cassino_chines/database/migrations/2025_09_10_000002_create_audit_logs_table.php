<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('audit_logs', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id')->nullable()->index();
            $table->string('event', 50)->index(); // created, updated, deleted, action
            $table->string('module', 120)->nullable()->index(); // e.g., settings, roles, gateway
            $table->string('target_type', 160)->nullable(); // morph: Model class
            $table->string('target_id', 64)->nullable()->index();
            $table->string('route')->nullable();
            $table->string('method', 10)->nullable();
            $table->string('ip', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->longText('request')->nullable(); // json (text for sqlite compat)
            $table->longText('before')->nullable();  // json (text for sqlite compat)
            $table->longText('after')->nullable();   // json (text for sqlite compat)
            $table->text('message')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('audit_logs');
    }
};

