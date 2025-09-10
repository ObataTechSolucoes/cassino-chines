<?php

namespace App\Filament\Admin\Widgets;

use App\Models\AffiliateHistory;
use App\Models\Deposit;
use App\Models\GGRGames;
use App\Models\GGRGamesDrakon;
use App\Models\GGRGamesFiver;
use App\Models\GgrGamesWorldSlot;
use App\Models\Order;
use App\Models\Transaction;
use App\Models\User;
use App\Models\Withdrawal;
use Carbon\Carbon;
use Filament\Widgets\Concerns\InteractsWithPageFilters;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Facades\DB;

class CasinoKpiOverview extends BaseWidget
{
    use InteractsWithPageFilters;

    protected ?string $heading = 'Visão Geral do Negócio';
    protected static ?int $sort = 1;

    protected function getStats(): array
    {
        [$start, $end] = $this->getRange();

        // Core aggregates
        // Deposits paid (prefer deposits; complement with transactions paid without matching deposit)
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
        $withdrawalsPaid = Withdrawal::when($start && $end, fn ($q) => $q->whereBetween(DB::raw('COALESCE(paid_at, created_at)'), [$start, $end]))
            ->where('status', 1)->sum('amount');
        $totalUsers = User::when($start && $end, fn ($q) => $q->whereBetween('created_at', [$start, $end]))->count();

        // FTD (first-time deposit) stats
        $ftdQuery = Deposit::select('user_id', DB::raw('MIN(COALESCE(paid_at, created_at)) as first_time'), DB::raw('MIN(id) as first_id'))
            ->where('status', 1)
            ->when($start && $end, fn ($q) => $q->whereBetween(DB::raw('COALESCE(paid_at, created_at)'), [$start, $end]))
            ->groupBy('user_id');

        $firstDepositIds = DB::query()->fromSub($ftdQuery, 'ftd')->pluck('first_id');
        $ftdCount = $firstDepositIds->count();
        $ftdAverage = $firstDepositIds->isNotEmpty()
            ? (float) Deposit::whereIn('id', $firstDepositIds)->avg('amount')
            : 0.0;

        // Lead time: average time from registration to first approved deposit
        $leadSeconds = 0; $leadCount = 0;
        if ($firstDepositIds->isNotEmpty()) {
            $firstDeposits = Deposit::whereIn('id', $firstDepositIds)->get(['user_id', 'created_at', 'paid_at']);
            $userCreated = User::whereIn('id', $firstDeposits->pluck('user_id'))
                ->pluck('created_at', 'id');
            foreach ($firstDeposits as $dep) {
                if (isset($userCreated[$dep->user_id])) {
                    $leadBase = $dep->paid_at ?? $dep->created_at;
                    $leadSeconds += Carbon::parse($userCreated[$dep->user_id])->diffInSeconds($leadBase);
                    $leadCount++;
                }
            }
        }
        $leadAvgSeconds = $leadCount > 0 ? (int) floor($leadSeconds / $leadCount) : 0;
        $leadHuman = $this->formatDuration($leadAvgSeconds);

        // Affiliates active: count distinct inviters with affiliate activity in range
        $activeAffiliates = AffiliateHistory::when($start && $end, fn ($q) => $q->whereBetween('created_at', [$start, $end]))
            ->distinct('inviter')
            ->count('inviter');

        // GGR across providers (bet - win)
        $ggr = $this->sumGgr($start, $end);

        // NGR estimate: GGR - affiliate commissions
        $affiliateCommissions = AffiliateHistory::when($start && $end, fn ($q) => $q->whereBetween('created_at', [$start, $end]))
            ->sum('commission_paid');
        $ngr = $ggr - $affiliateCommissions;

        // Ratios
        $depositPerRegistration = $totalUsers > 0 ? $depositsPaid / $totalUsers : 0;
        $regToFtdRatio = $ftdCount > 0 ? $totalUsers / $ftdCount : null;

        // ARPPU: deposits per depositor
        $depositors = Deposit::when($start && $end, fn ($q) => $q->whereBetween(DB::raw('COALESCE(paid_at, created_at)'), [$start, $end]))
            ->where('status', 1)
            ->distinct('user_id')->count('user_id');
        if ($depositors === 0) {
            $depositors = Transaction::when($start && $end, fn ($q) => $q->whereBetween('created_at', [$start, $end]))
                ->where('status', 1)
                ->distinct('user_id')->count('user_id');
        }
        $arppu = $depositors > 0 ? $depositsPaid / $depositors : 0;

        // Conversion: FTD / Registrations
        $conversion = $totalUsers > 0 ? round(($ftdCount / $totalUsers) * 100, 2) : 0;

        // Active players (orders in range)
        $activePlayers = Order::when($start && $end, fn ($q) => $q->whereBetween('created_at', [$start, $end]))
            ->distinct('user_id')->count('user_id');

        return [
            Stat::make('Total Depositado', \Helper::amountFormatDecimal($depositsPaid))
                ->description('Aprovados no período')
                ->descriptionIcon('heroicon-o-arrow-down-tray')
                ->color('success')
                ->chart($this->spark('deposit')),

            Stat::make('Total Sacado', \Helper::amountFormatDecimal($withdrawalsPaid))
                ->description('Aprovados no período')
                ->descriptionIcon('heroicon-o-arrow-up-tray')
                ->color('danger')
                ->chart($this->spark('withdraw')),

            Stat::make('FTD Médio', \Helper::amountFormatDecimal($ftdAverage ?? 0))
                ->description($ftdCount . ' FTDs')
                ->descriptionIcon('heroicon-o-user-plus')
                ->color('primary'),

            Stat::make('Afiliados Ativos', (string) $activeAffiliates)
                ->description('Com atividade no período')
                ->descriptionIcon('heroicon-o-users')
                ->color('info'),

            Stat::make('Depósito Total / Registro', \Helper::amountFormatDecimal($depositPerRegistration))
                ->description('Ticket por registro')
                ->descriptionIcon('heroicon-o-calculator')
                ->color('warning'),

            Stat::make('Tempo Médio do Lead', $leadHuman)
                ->description('Registro → 1º depósito')
                ->descriptionIcon('heroicon-o-clock')
                ->color('gray'),

            Stat::make('Registros Totais', (string) $totalUsers)
                ->description('Novos no período')
                ->descriptionIcon('heroicon-o-user')
                ->color('gray'),

            Stat::make('GGR', \Helper::amountFormatDecimal($ggr))
                ->description('Bet − Win')
                ->descriptionIcon('heroicon-o-banknotes')
                ->color('purple'),

            Stat::make('NGR (estimado)', \Helper::amountFormatDecimal($ngr))
                ->description('GGR − Comissões')
                ->descriptionIcon('heroicon-o-scale')
                ->color('purple'),

            Stat::make('Relação Reg./FTD', $regToFtdRatio ? number_format($regToFtdRatio, 2) : '—')
                ->description('Quanto por FTD')
                ->descriptionIcon('heroicon-o-chart-bar')
                ->color('gray'),

            // Extras úteis
            Stat::make('ARPPU', \Helper::amountFormatDecimal($arppu))
                ->description('Depósito por pagante')
                ->descriptionIcon('heroicon-o-chart-pie')
                ->color('success'),

            Stat::make('Conversão Reg → FTD', $conversion . '%')
                ->description($ftdCount . ' FTDs / ' . $totalUsers . ' Regs')
                ->descriptionIcon('heroicon-o-bolt')
                ->color('info'),

            Stat::make('Jogadores Ativos', (string) $activePlayers)
                ->description('Com apostas no período')
                ->descriptionIcon('heroicon-o-fire')
                ->color('warning'),

            Stat::make('Visitas no Site', '—')
                ->description('Conectar Analytics')
                ->descriptionIcon('heroicon-o-link')
                ->color('gray'),
        ];
    }

    private function getRange(): array
    {
        $range = $this->filters['range'] ?? null;
        $start = $this->filters['startDate'] ?? null;
        $end = $this->filters['endDate'] ?? null;

        if ($range) {
            [$start, $end] = $this->resolveQuickRange($range);
        }

        // Sem filtro: últimos 30 dias por padrão
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
            'yesterday' => [
                $now->copy()->subDay()->startOfDay(),
                $now->copy()->subDay()->endOfDay(),
            ],
            '7' => [$now->copy()->subDays(6)->startOfDay(), $now->copy()->endOfDay()],
            '30' => [$now->copy()->subDays(29)->startOfDay(), $now->copy()->endOfDay()],
            '90' => [$now->copy()->subDays(89)->startOfDay(), $now->copy()->endOfDay()],
            'all' => [null, null], // interpretado como sem filtro nas queries (when($start && $end))
            default => [null, null],
        };
    }

    private function sumGgr($start, $end): float
    {
        $sum = fn ($model) => $model::when($start && $end, fn ($q) => $q->whereBetween('created_at', [$start, $end]))
            ->selectRaw('COALESCE(SUM(balance_bet - balance_win), 0) as ggr')
            ->value('ggr') ?? 0;

        return (float) ($sum(GGRGames::class)
            + $sum(GGRGamesDrakon::class)
            + $sum(GGRGamesFiver::class)
            + $sum(GgrGamesWorldSlot::class));
    }

    private function spark(string $type): array
    {
        $now = Carbon::now();
        $data = [];
        for ($i = 6; $i >= 0; $i--) {
            $date = $now->copy()->subDays($i);
            if ($type === 'deposit') {
                $data[] = (float) Deposit::whereDate('created_at', $date)->where('status', 1)->sum('amount');
            } elseif ($type === 'withdraw') {
                $data[] = (float) Withdrawal::whereDate('created_at', $date)->where('status', 1)->sum('amount');
            }
        }
        return $data;
    }

    private function formatDuration(int $seconds): string
    {
        if ($seconds <= 0) {
            return '—';
        }
        $days = intdiv($seconds, 86400);
        $seconds %= 86400;
        $hours = intdiv($seconds, 3600);
        $seconds %= 3600;
        $minutes = intdiv($seconds, 60);

        if ($days > 0) return $days . 'd ' . $hours . 'h';
        if ($hours > 0) return $hours . 'h ' . $minutes . 'm';
        return $minutes . 'm';
    }
}
