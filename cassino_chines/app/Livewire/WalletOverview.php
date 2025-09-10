<?php

namespace App\Livewire;

use App\Models\AffiliateHistory;
use App\Models\Deposit;
use App\Models\Withdrawal;
use Carbon\Carbon;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Filament\Widgets\Concerns\InteractsWithPageFilters;
use App\Models\Bau;
use App\Helpers\Core as Helper;

class WalletOverview extends BaseWidget
{
    protected static ?int $sort = -2;
    use InteractsWithPageFilters;

    protected function getStats(): array
    {
        $range = $this->filters['range'] ?? null;
        $startDate = $this->filters['startDate'] ?? null;
        $endDate = $this->filters['endDate'] ?? null;

        if ($range) {
            [$startDate, $endDate] = $this->resolveQuickRange($range);
        }

        $depositQuery = Deposit::query();
        $withdrawalQuery = Withdrawal::query();
        $affiliateQuery = AffiliateHistory::query();
        $bauQuery = Bau::query();

        $allRange = ($range === 'all');
        if (!$allRange && (empty($startDate) || empty($endDate))) {
            $currentMonth = Carbon::now()->month;
            $depositQuery->whereMonth('created_at', $currentMonth);
            $withdrawalQuery->whereMonth('created_at', $currentMonth);
            $affiliateQuery->whereMonth('created_at', $currentMonth);
            $bauQuery->whereMonth('created_at', $currentMonth);
        } elseif (!$allRange) {
            $depositQuery->whereBetween('created_at', [$startDate, $endDate]);
            $withdrawalQuery->whereBetween('created_at', [$startDate, $endDate]);
            $affiliateQuery->whereBetween('created_at', [$startDate, $endDate]);
            $bauQuery->whereBetween('created_at', [$startDate, $endDate]);
        }

        $totalDeposits = $depositQuery->sum('amount');
        $paidDeposits = $depositQuery->clone()->where('status', 1)->sum('amount');
        $unpaidDeposits = $depositQuery->clone()->where('status', 0)->sum('amount');
        
        $totalWithdrawals = $withdrawalQuery->sum('amount');
        $paidWithdrawals = $withdrawalQuery->clone()->where('status', 1)->sum('amount');
        $unpaidWithdrawals = $withdrawalQuery->clone()->where('status', 0)->sum('amount');

        $grossEarnings = $paidDeposits - $paidWithdrawals;
        $revshareCommission = $affiliateQuery->where('commission_type', 'revshare')->sum('commission_paid');
        $cpaCommission = $affiliateQuery->where('commission_type', 'cpa')->sum('commission_paid');
        $bausValue = $bauQuery->where('status', 3)->sum('value_mostrar');

        return [
           Stat::make('Depósitos Pendentes', Helper::amountFormatDecimal($unpaidDeposits))
                ->description('Depósitos em processamento')
                ->descriptionIcon('heroicon-m-clock')
                ->color('warning')
                ->extraAttributes([
                    'class' => 'dashboard-card',
                    'style' => 'background: linear-gradient(135deg, rgba(234,179,8,0.1) 0%, rgba(234,179,8,0.2) 100%);
                                border-left: 4px solid rgb(234,179,8);
                                border-radius: 8px;'
                ]),

            // Card Principal - Ganhos Brutos
            Stat::make('Ganhos Brutos', Helper::amountFormatDecimal($grossEarnings))
                ->description($grossEarnings >= 0 ? 'Lucro positivo' : 'Prejuízo')
                ->descriptionIcon($grossEarnings >= 0 ? 'heroicon-m-arrow-trending-up' : 'heroicon-m-arrow-trending-down')
                ->color($grossEarnings >= 0 ? 'success' : 'danger')
                ->chart($this->getMonthlyTrend('amount', Deposit::class, Withdrawal::class))
                ->extraAttributes([
                    'class' => 'dashboard-card',
                    'style' => 'background: linear-gradient(135deg, rgba(34,197,94,0.1) 0%, rgba(34,197,94,0.2) 100%);
                                border-left: 4px solid rgb(34,197,94);
                                border-radius: 8px;'
                ]),

            // Seção de Depósitos
            Stat::make('Total Depósitos', Helper::amountFormatDecimal($totalDeposits))
                ->description('Todos os depósitos realizados')
                ->descriptionIcon('heroicon-m-arrow-down-tray')
                ->color('primary')
                ->extraAttributes([
                    'class' => 'dashboard-card',
                    'style' => 'background: linear-gradient(135deg, rgba(59,130,246,0.1) 0%, rgba(59,130,246,0.2) 100%);
                                border-left: 4px solid rgb(59,130,246);
                                border-radius: 8px;'
                ]),

            Stat::make('Depósitos Pagos', Helper::amountFormatDecimal($paidDeposits))
                ->description('Depósitos concluídos')
                ->descriptionIcon('heroicon-m-check-circle')
                ->color('success')
                ->extraAttributes([
                    'class' => 'dashboard-card',
                    'style' => 'background: linear-gradient(135deg, rgba(22,163,74,0.1) 0%, rgba(22,163,74,0.2) 100%);
                                border-left: 4px solid rgb(22,163,74);
                                border-radius: 8px;'
                ]),
Stat::make('Pendências', '') // Segundo parâmetro vazio
            ->extraAttributes([
                'class' => 'col-span-full',
                'style' => 'grid-column: 1 / -1;
                            background: transparent;
                            box-shadow: none;
                            padding: 0.5rem 1rem;
                            margin-bottom: -0.5rem;
                            font-weight: 600;
                            font-size: 0.875rem;
                            color: #6b7280;
                            text-transform: uppercase;
                            letter-spacing: 0.05em;
                            border-bottom: 1px solid #e5e7eb;' // Linha divisória opcional
            ]),
            
            // Seção de Saques
            Stat::make('Total Saques', Helper::amountFormatDecimal($totalWithdrawals))
                ->description('Todos os saques solicitados')
                ->descriptionIcon('heroicon-m-arrow-up-tray')
                ->color('primary')
                ->extraAttributes([
                    'class' => 'dashboard-card',
                    'style' => 'background: linear-gradient(135deg, rgba(239,68,68,0.1) 0%, rgba(239,68,68,0.2) 100%);
                                border-left: 4px solid rgb(239,68,68);
                                border-radius: 8px;'
                ]),

            Stat::make('Saques Pagos', Helper::amountFormatDecimal($paidWithdrawals))
                ->description('Saques concluídos')
                ->descriptionIcon('heroicon-m-check-circle')
                ->color('success')
                ->extraAttributes([
                    'class' => 'dashboard-card',
                    'style' => 'background: linear-gradient(135deg, rgba(22,163,74,0.1) 0%, rgba(22,163,74,0.2) 100%);
                                border-left: 4px solid rgb(22,163,74);
                                border-radius: 8px;'
                ]),

            Stat::make('Saques Pendentes', Helper::amountFormatDecimal($unpaidWithdrawals))
                ->description('Saques em processamento')
                ->descriptionIcon('heroicon-m-clock')
                ->color('warning')
                ->extraAttributes([
                    'class' => 'dashboard-card',
                    'style' => 'background: linear-gradient(135deg, rgba(234,179,8,0.1) 0%, rgba(234,179,8,0.2) 100%);
                                border-left: 4px solid rgb(234,179,8);
                                border-radius: 8px;'
                ]),

            // Seção de Comissões
            Stat::make('Comissão Revshare', Helper::amountFormatDecimal($revshareCommission))
                ->description('Comissões por revenda')
                ->descriptionIcon('heroicon-m-arrows-right-left')
                ->color('info')
                ->extraAttributes([
                    'class' => 'dashboard-card',
                    'style' => 'background: linear-gradient(135deg, rgba(6,182,212,0.1) 0%, rgba(6,182,212,0.2) 100%);
                                border-left: 4px solid rgb(6,182,212);
                                border-radius: 8px;'
                ]),

            Stat::make('Comissão CPA', Helper::amountFormatDecimal($cpaCommission))
                ->description('Comissões por ação')
                ->descriptionIcon('heroicon-m-user-plus')
                ->color('info')
                ->extraAttributes([
                    'class' => 'dashboard-card',
                    'style' => 'background: linear-gradient(135deg, rgba(6,182,212,0.1) 0%, rgba(6,182,212,0.2) 100%);
                                border-left: 4px solid rgb(6,182,212);
                                border-radius: 8px;'
                ]),

            Stat::make('Total BAÚ', Helper::amountFormatDecimal($bausValue))
                ->description('Valor total em BAÚS')
                ->descriptionIcon('heroicon-m-gift')
                ->color('info')
                ->extraAttributes([
                    'class' => 'dashboard-card',
                    'style' => 'background: linear-gradient(135deg, rgba(168,85,247,0.1) 0%, rgba(168,85,247,0.2) 100%);
                                border-left: 4px solid rgb(168,85,247);
                                border-radius: 8px;'
                ]),
        ];
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
            'all' => [null, null],
            default => [null, null],
        };
    }

    protected function getMonthlyTrend($amountField, $depositModel, $withdrawalModel)
    {
        $data = [];
        $now = Carbon::now();
        
        for ($i = 6; $i >= 0; $i--) {
            $date = $now->copy()->subDays($i);
            $deposits = $depositModel::whereDate('created_at', $date)->sum($amountField);
            $withdrawals = $withdrawalModel::whereDate('created_at', $date)->sum($amountField);
            $data[] = $deposits - $withdrawals;
        }
        
        return $data;
    }

    public static function canView(): bool
    {
        return auth()->user()->hasRole('admin');
    }
}
