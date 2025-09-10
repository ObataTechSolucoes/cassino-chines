<?php

namespace App\Filament\Admin\Widgets;

use App\Models\Deposit;
use App\Models\Withdrawal;
use App\Models\GGRGames;
use App\Models\GGRGamesDrakon;
use App\Models\GGRGamesFiver;
use App\Models\GgrGamesWorldSlot;
use Carbon\Carbon;
use Filament\Widgets\LineChartWidget;
use Filament\Widgets\Concerns\InteractsWithPageFilters;

class KpiTrendsChart extends LineChartWidget
{
    use InteractsWithPageFilters;

    protected static ?string $heading = 'Tendências';
    protected static ?int $sort = 2;

    protected function getData(): array
    {
        [$start, $end, $days] = $this->getRangeDays();

        $labels = [];
        $deposits = [];
        $withdrawals = [];
        $ggrs = [];

        for ($i = $days - 1; $i >= 0; $i--) {
            $day = Carbon::parse($end)->copy()->subDays($i);
            $labels[] = $day->format('d/m');

            $deposits[] = (float) Deposit::whereDate('created_at', $day)
                ->where('status', 1)->sum('amount');

            $withdrawals[] = (float) Withdrawal::whereDate('created_at', $day)
                ->where('status', 1)->sum('amount');

            $ggrs[] = (float) $this->dailyGgr($day);
        }

        return [
            'datasets' => [
                [
                    'label' => 'Depósitos Pagos',
                    'data' => $deposits,
                    'borderColor' => 'rgba(34,197,94,1)',
                    'backgroundColor' => 'rgba(34,197,94,0.15)',
                    'tension' => 0.3,
                    'fill' => true,
                ],
                [
                    'label' => 'Saques Pagos',
                    'data' => $withdrawals,
                    'borderColor' => 'rgba(239,68,68,1)',
                    'backgroundColor' => 'rgba(239,68,68,0.15)',
                    'tension' => 0.3,
                    'fill' => true,
                ],
                [
                    'label' => 'GGR',
                    'data' => $ggrs,
                    'borderColor' => 'rgba(99,102,241,1)',
                    'backgroundColor' => 'rgba(99,102,241,0.15)',
                    'tension' => 0.3,
                    'fill' => true,
                ],
            ],
            'labels' => $labels,
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }

    private function dailyGgr(Carbon $day): float
    {
        $sum = function ($model) use ($day) {
            return $model::whereDate('created_at', $day)
                ->selectRaw('COALESCE(SUM(balance_bet - balance_win), 0) as ggr')
                ->value('ggr') ?? 0;
        };

        return (float) ($sum(GGRGames::class)
            + $sum(GGRGamesDrakon::class)
            + $sum(GGRGamesFiver::class)
            + $sum(GgrGamesWorldSlot::class));
    }

    private function getRangeDays(): array
    {
        $now = Carbon::now();
        $range = $this->filters['range'] ?? null;
        $start = $this->filters['startDate'] ?? null;
        $end = $this->filters['endDate'] ?? null;

        if ($range) {
            [$start, $end] = match ($range) {
                'today' => [$now->copy()->startOfDay(), $now->copy()->endOfDay()],
                'yesterday' => [$now->copy()->subDay()->startOfDay(), $now->copy()->subDay()->endOfDay()],
                '7' => [$now->copy()->subDays(6)->startOfDay(), $now->copy()->endOfDay()],
                '30' => [$now->copy()->subDays(29)->startOfDay(), $now->copy()->endOfDay()],
                '90' => [$now->copy()->subDays(89)->startOfDay(), $now->copy()->endOfDay()],
                'all' => [$now->copy()->subDays(29)->startOfDay(), $now->copy()->endOfDay()], // cap 30 dias p/ gráfico
                default => [$now->copy()->subDays(13)->startOfDay(), $now->copy()->endOfDay()],
            };
        }

        if (! $start || ! $end) {
            $start = $now->copy()->subDays(13)->startOfDay();
            $end = $now->copy()->endOfDay();
        }

        $start = Carbon::parse($start);
        $end = Carbon::parse($end);

        $days = $start->diffInDays($end) + 1;
        $days = max(1, min($days, 90)); // limitar a 90 pontos por legibilidade

        return [$start, $end, $days];
    }
}

