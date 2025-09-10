<?php

namespace App\Filament\Admin\Widgets;

use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Filament\Widgets\BarChartWidget;

class DepositGrowthChart extends BarChartWidget
{
    protected static ?string $heading = 'Distribuição de Depósitos 💰';

    protected function getData(): array
    {
        $data = $this->getDepositStatusData();
        $generatedDeposits = $data['generatedDeposits'];
        $paidDeposits = $data['paidDeposits'];

        return [
            'datasets' => [
                [
                    'label' => 'Depósitos',
                    'data' => [$generatedDeposits, $paidDeposits],
                    'backgroundColor' => [
                        'rgba(59, 130, 246, 0.8)',  // Azul com transparência
                        'rgba(16, 185, 129, 0.8)',  // Verde com transparência
                    ],
                    'borderColor' => [
                        'rgba(59, 130, 246, 1)',    // Azul sólido para borda
                        'rgba(16, 185, 129, 1)',    // Verde sólido para borda
                    ],
                    'borderWidth' => 1,
                    'borderRadius' => 4,
                    'barThickness' => 40,
                ],
            ],
            'labels' => ['Gerados', 'Pagos'],
            'options' => [
                'responsive' => true,
                'maintainAspectRatio' => false,
                'scales' => [
                    'y' => [
                        'beginAtZero' => true,
                        'grid' => [
                            'color' => 'rgba(200, 200, 200, 0.1)',
                        ],
                        'ticks' => [
                            'color' => '#64748b',
                            'callback' => function($value) {
                                return number_format($value);
                            }
                        ],
                    ],
                    'x' => [
                        'grid' => [
                            'display' => false,
                        ],
                        'ticks' => [
                            'color' => '#64748b',
                        ],
                    ],
                ],
                'plugins' => [
                    'legend' => [
                        'display' => false,
                    ],
                    'tooltip' => [
                        'callbacks' => [
                            'label' => function($tooltipItem) {
                                return $tooltipItem->dataset->label . ': ' . number_format($tooltipItem->raw);
                            }
                        ],
                        'backgroundColor' => '#1e293b',
                        'titleColor' => '#f8fafc',
                        'bodyColor' => '#e2e8f0',
                        'cornerRadius' => 8,
                        'displayColors' => true,
                    ],
                ],
                'animation' => [
                    'duration' => 1000,
                    'easing' => 'easeInOutCubic',
                ],
            ],
        ];
    }

    protected function getType(): string
    {
        return 'bar';
    }

    private function getDepositStatusData(): array
    {
        $now = Carbon::now();

        $generatedDeposits = DB::table('deposits')
            ->whereMonth('created_at', $now->month)
            ->count();

        $paidDeposits = DB::table('deposits')
            ->whereMonth('created_at', $now->month)
            ->where('status', 1)
            ->count();

        return [
            'generatedDeposits' => $generatedDeposits,
            'paidDeposits' => $paidDeposits,
        ];
    }

    public static function canView(): bool
    {
        return auth()->user()->hasRole('admin');
    }
}