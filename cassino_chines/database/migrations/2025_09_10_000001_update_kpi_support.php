<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Add paid_at and useful indexes to deposits
        if (Schema::hasTable('deposits')) {
            Schema::table('deposits', function (Blueprint $table) {
                if (! Schema::hasColumn('deposits', 'paid_at')) {
                    $table->timestamp('paid_at')->nullable()->after('status');
                }
                $table->index(['status', 'created_at'], 'deposits_status_created_idx');
                $table->index(['user_id', 'status'], 'deposits_user_status_idx');
                $table->index(['paid_at'], 'deposits_paid_at_idx');
            });
        }

        // Add paid_at and indexes to withdrawals
        if (Schema::hasTable('withdrawals')) {
            Schema::table('withdrawals', function (Blueprint $table) {
                if (! Schema::hasColumn('withdrawals', 'paid_at')) {
                    $table->timestamp('paid_at')->nullable()->after('status');
                }
                $table->index(['status', 'created_at'], 'withdrawals_status_created_idx');
                $table->index(['user_id', 'status'], 'withdrawals_user_status_idx');
                $table->index(['paid_at'], 'withdrawals_paid_at_idx');
            });
        }

        // Optional: visits table for dashboard metric
        if (! Schema::hasTable('site_visits')) {
            Schema::create('site_visits', function (Blueprint $table) {
                $table->id();
                $table->string('path')->nullable();
                $table->string('ip', 45)->nullable();
                $table->string('user_agent')->nullable();
                $table->foreignId('user_id')->nullable()->index();
                $table->timestamp('visited_at')->index();
                $table->timestamps();
            });
        }

        // Add helpful indexes to transactions for KPI queries
        if (Schema::hasTable('transactions')) {
            Schema::table('transactions', function (Blueprint $table) {
                $table->index(['status', 'created_at'], 'transactions_status_created_idx');
                $table->index(['user_id', 'status'], 'transactions_user_status_idx');
                $table->index(['payment_id'], 'transactions_payment_id_idx');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('deposits')) {
            Schema::table('deposits', function (Blueprint $table) {
                if (Schema::hasColumn('deposits', 'paid_at')) {
                    $table->dropColumn('paid_at');
                }
                $table->dropIndex('deposits_status_created_idx');
                $table->dropIndex('deposits_user_status_idx');
                $table->dropIndex('deposits_paid_at_idx');
            });
        }
        if (Schema::hasTable('withdrawals')) {
            Schema::table('withdrawals', function (Blueprint $table) {
                if (Schema::hasColumn('withdrawals', 'paid_at')) {
                    $table->dropColumn('paid_at');
                }
                $table->dropIndex('withdrawals_status_created_idx');
                $table->dropIndex('withdrawals_user_status_idx');
                $table->dropIndex('withdrawals_paid_at_idx');
            });
        }

        if (Schema::hasTable('site_visits')) {
            Schema::dropIfExists('site_visits');
        }

        if (Schema::hasTable('transactions')) {
            Schema::table('transactions', function (Blueprint $table) {
                $table->dropIndex('transactions_status_created_idx');
                $table->dropIndex('transactions_user_status_idx');
                $table->dropIndex('transactions_payment_id_idx');
            });
        }
    }
};
