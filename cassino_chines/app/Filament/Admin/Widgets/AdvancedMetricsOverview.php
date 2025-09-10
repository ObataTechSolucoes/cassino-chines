<?php

namespace App\Filament\Admin\Widgets;

use App\Models\AffiliateHistory;
use App\Models\Deposit;
use App\Models\Transaction;
use App\Models\User;
use App\Models\Withdrawal;
use Carbon\Carbon;
use Filament\Widgets\Concerns\InteractsWithPageFilters;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Facades\DB;

class AdvancedMetricsOverview extends BaseWidget
{
    use InteractsWithPageFilters;

    protected static ?int $sort = 1;

    protected function getHeading(): ?string
    {
        return 'Resumo Executivo';
    }

    protected function getStats(): array
    {
        [$start, $end] = $this->getRange();

        // Depósitos pagos (preferir deposits; complementar com transactions aprovadas sem matching)
        $depositQuery = Deposit::query()->where('status', 1);
        if ($start && $end) {
            $depositQuery->whereBetween(DB::raw('COALESCE(paid_at, created_at)'), [$start, $end]);
        }
        $depositsPaid = (float) $depositQuery->sum('amount');

        $paidDepositPaymentIds = Deposit::where('status', 1)
            ->when($start && $end, fn ($q) => $q->whereBetween(DB::raw('COALESCE(paid_at, created_at)'), [$start, $end]))
            ->pluck('payment_id')
            ->filter()
            ->all();

        $txQuery = Transaction::query()
            ->where('status', 1)
            ->when($start && $end, fn ($q) => $q->whereBetween('created_at', [$start, $end]));
        if (! empty($paidDepositPaymentIds)) {
            $txQuery->whereNotIn('payment_id', $paidDepositPaymentIds);
        }
        $depositsPaid += (float) $txQuery->sum('price');

        $withdrawalsPaid = (float) Withdrawal::when($start && $end, fn ($q) => $q->whereBetween(DB::raw('COALESCE(paid_at, created_at)'), [$start, $end]))
            ->where('status', 1)->sum('amount');

        // FTD médio
        $ftdQuery = Deposit::select('user_id', DB::raw('MIN(COALESCE(paid_at, created_at)) as first_time'), DB::raw('MIN(id) as first_id'))
            ->where('status', 1)
            ->when($start && $end, fn ($q) => $q->whereBetween(DB::raw('COALESCE(paid_at, created_at)'), [$start, $end]))
            ->groupBy('user_id');

        $firstDepositIds = DB::query()->fromSub($ftdQuery, 'ftd')->pluck('first_id');
        $ftdCount = $firstDepositIds->count();
        $ftdAverage = $firstDepositIds->isNotEmpty()
            ? (float) Deposit::whereIn('id', $firstDepositIds)->avg('amount')
            : 0.0;

        // Afiliados ativos e total de comissões
        $activeAffiliates = AffiliateHistory::when($start && $end, fn ($q) => $q->whereBetween('created_at', [$start, $end]))
            ->distinct('inviter')->count('inviter');
        $affiliateAmount = (float) AffiliateHistory::when($start && $end, fn ($q) => $q->whereBetween('created_at', [$start, $end]))
            ->sum('commission_paid');

        // MRR aproximado: valor de depósitos de usuários recorrentes nos últimos 30 dias do período
        [$mrrAmount, $mrrCustomers] = $this->estimateMrr($start, $end);

        // Taxa de registros (conversão Reg -> FTD) no período
        $totalUsers = User::when($start && $end, fn ($q) => $q->whereBetween('created_at', [$start, $end]))->count();
        $conversion = $totalUsers > 0 ? round(($ftdCount / $totalUsers) * 100, 2) : 0.0;

        return [
            Stat::make('Total Depositado', \Helper::amountFormatDecimal($depositsPaid))
                ->description('Aprovados no período')
                ->descriptionIcon('heroicon-o-arrow-trending-up')
                ->color('success'),

            Stat::make('Total Sacado', \Helper::amountFormatDecimal($withdrawalsPaid))
                ->description('Aprovados no período')
                ->descriptionIcon('heroicon-o-arrow-trending-down')
                ->color('danger'),

            Stat::make('FTD Médio', \Helper::amountFormatDecimal($ftdAverage))
                ->description($ftdCount . ' FTDs')
                ->descriptionIcon('heroicon-o-user-plus')
                ->color('primary'),

            Stat::make('Afiliados Ativos', (string) $activeAffiliates)
                ->description(\Helper::amountFormatDecimal($affiliateAmount))
                ->descriptionIcon('heroicon-o-users')
                ->color('info'),

            Stat::make('Receita Recorrente Mensal', \Helper::amountFormatDecimal($mrrAmount))
                ->description('Recorrentes: ' . $mrrCustomers)
                ->descriptionIcon('heroicon-o-arrow-path')
                ->color('success'),

            Stat::make('Taxa Registros (no período)', $conversion . '%')
                ->description('Conversão Reg → FTD')
                ->descriptionIcon('heroicon-o-bolt')
                ->color($conversion >= 20 ? 'success' : 'warning'),
        ];
    }

    private function getRange(): array
    {
        $range = $this->filters['range'] ?? null;
        $start = $this->filters['startDate'] ?? null;
        $end = $this->filters['endDate'] ?? null;

        if ($range === 'custom') {
            if ($start && $end) {
                return [Carbon::parse($start)->startOfDay(), Carbon::parse($end)->endOfDay()];
            }
        } elseif ($range) {
            [$start, $end] = $this->resolveQuickRange($range);
        }

        if (! $start || ! $end) {
            $start = Carbon::now()->subDays(29)->startOfDay();
            $end = Carbon::now()->endOfDay();
        }

        return [Carbon::parse($start), Carbon::parse($end)];
    }

    private function resolveQuickRange(string $range): array
    {
        $now = Carbon::now();
        return match ($range) {
            'today' => [$now->copy()->startOfDay(), $now->copy()->endOfDay()],
            'yesterday' => [$now->copy()->subDay()->startOfDay(), $now->copy()->subDay()->endOfDay()],
            '7' => [$now->copy()->subDays(6)->startOfDay(), $now->copy()->endOfDay()],
            '30' => [$now->copy()->subDays(29)->startOfDay(), $now->copy()->endOfDay()],
            '90' => [$now->copy()->subDays(89)->startOfDay(), $now->copy()->endOfDay()],
            'this_month' => [$now->copy()->startOfMonth(), $now->copy()->endOfMonth()],
            'last_month' => [$now->copy()->subMonth()->startOfMonth(), $now->copy()->subMonth()->endOfMonth()],
            'all' => [null, null],
            default => [null, null],
        };
    }

    private function estimateMrr($start, $end): array
    {
        // Janela de 30 dias dentro do período selecionado
        if ($end instanceof Carbon) {
            $windowEnd = $end->copy();
        } elseif ($end) {
            $windowEnd = Carbon::parse($end);
        } else {
            $windowEnd = Carbon::now();
        }
        $windowStart = $windowEnd->copy()->subDays(29)->startOfDay();

        $recurringUserIds = Deposit::where('status', 1)
            ->when($start && $end, function ($q) use ($start, $end) {
                $q->whereBetween(DB::raw('COALESCE(paid_at, created_at)'), [$start, $end]);
            })
            ->whereBetween(DB::raw('COALESCE(paid_at, created_at)'), [$windowStart, $windowEnd])
            ->select('user_id', DB::raw('COUNT(*) as c'))
            ->groupBy('user_id')
            ->having('c', '>=', 2)
            ->pluck('user_id');

        if ($recurringUserIds->isEmpty()) {
            return [0.0, 0];
        }

        $amount = (float) Deposit::where('status', 1)
            ->whereBetween(DB::raw('COALESCE(paid_at, created_at)'), [$windowStart, $windowEnd])
            ->whereIn('user_id', $recurringUserIds)
            ->sum('amount');

        return [$amount, $recurringUserIds->unique()->count()];
    }
}
