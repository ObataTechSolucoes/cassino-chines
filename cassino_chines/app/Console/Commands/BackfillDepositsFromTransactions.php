<?php

namespace App\Console\Commands;

use App\Models\Deposit;
use App\Models\Transaction;
use App\Models\Wallet;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class BackfillDepositsFromTransactions extends Command
{
    protected $signature = 'kpi:backfill-deposits {--dry-run : Apenas simula sem escrever no banco}';
    protected $description = 'Cria registros em deposits a partir de transactions pagas que não geraram depósito';

    public function handle(): int
    {
        $dry = (bool) $this->option('dry-run');

        $this->info('Procurando transações pagas sem depósito correspondente...');

        $paidTx = Transaction::where('status', 1)->get();
        $created = 0;

        foreach ($paidTx as $tx) {
            $exists = Deposit::where('payment_id', $tx->payment_id)->where('status', 1)->exists();
            if ($exists) {
                continue;
            }

            $wallet = Wallet::where('user_id', $tx->user_id)->first();

            if ($dry) {
                $this->line("[DRY] Criaria depósito para payment_id={$tx->payment_id}, user_id={$tx->user_id}, amount={$tx->price}");
                $created++;
                continue;
            }

            Deposit::create([
                'payment_id' => $tx->payment_id,
                'user_id' => $tx->user_id,
                'amount' => $tx->price,
                'type' => $tx->payment_method ?? 'pix',
                'currency' => $wallet->currency ?? 'BRL',
                'symbol' => $wallet->symbol ?? 'R$',
                'status' => 1,
                'paid_at' => $tx->updated_at ?? now(),
                'created_at' => $tx->created_at,
                'updated_at' => now(),
            ]);
            $created++;
        }

        $this->info("Registros criados: {$created}");
        return Command::SUCCESS;
    }
}

