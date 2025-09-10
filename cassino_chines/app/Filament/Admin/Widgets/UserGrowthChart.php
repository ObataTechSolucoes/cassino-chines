<?php

namespace App\Filament\Admin\Widgets;

use App\Models\User;
use App\Models\Deposit;
use Carbon\Carbon;
use Filament\Widgets\BarChartWidget;

class UserGrowthChart extends BarChartWidget
{
    protected static ?string $heading = 'Distribuição de Jogadores 🔥';

    protected function getData(): array
    {
        $data = $this->getUserDepositData();
        $usersWithDeposits = $data['usersWithDeposits'];
        $usersWithoutDeposits = $data['usersWithoutDeposits'];

        return [
            'datasets' => [
                [
                    'label' => 'Jogadores',
                    'data' => [$usersWithDeposits, $usersWithoutDeposits],
                    'backgroundColor' => [
                        'rgba(74, 222, 128, 0.8)',  // Verde com transparência
                        'rgba(248, 113, 113, 0.8)', // Vermelho com transparência
                    ],
                    'borderColor' => [
                        'rgba(74, 222, 128, 1)',   // Verde sólido para borda
                        'rgba(248, 113, 113, 1)',   // Vermelho sólido para borda
                    ],
                    'borderWidth' => 1,
                    'borderRadius' => 4, // Cantos levemente arredondados
                    'barThickness' => 40, // Largura das barras
                ],
            ],
            'labels' => ['Com Depósito', 'Sem Depósito'],
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
                        'display' => false, // Removemos a legenda pois já temos os labels
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

    private function getUserDepositData(): array
    {
        $now = Carbon::now();
        
        $usersWithDeposits = Deposit::whereMonth('created_at', $now->month)
            ->distinct('user_id')
            ->count('user_id');

        $totalUsers = User::whereMonth('created_at', $now->month)->count();
        $usersWithoutDeposits = $totalUsers - $usersWithDeposits;

        return [
            'usersWithDeposits' => $usersWithDeposits,
            'usersWithoutDeposits' => $usersWithoutDeposits,
        ];
    }

    public static function canView(): bool
    {
        return auth()->user()->hasRole('admin');
    }
}