<?php

namespace App\Filament\Admin\Widgets;

use App\Models\User;
use Illuminate\Support\Facades\DB;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class AdminWidgets extends BaseWidget
{
    protected static ?int $navigationSort = -2;

    /*** @return array|Stat[] */
    protected function getCards(): array
    {
        $totalUsers = User::count();
        $totalDeposited = DB::table('deposits')
            ->where('status', '1') // Filtrar apenas os depósitos aprovados
            ->sum('amount');
        $totalWithdrawn = DB::table('withdrawals')
            ->where('status', '1') // Filtrar apenas os saques aprovados
            ->sum('amount');
        $totalAffiliateRevenue = DB::table('wallets')
            ->sum('refer_rewards'); // Total geral de ganhos dos afiliados

        return [
           Stat::make('Total Usuários', $totalUsers)
    ->description('Total de usuários registrados')
    ->descriptionIcon('heroicon-m-arrow-trending-up')
    ->extraAttributes(['style' => 'background-color: rgba(0, 123, 255, 0.6); color: #ffffff;']),  // Azul com 80% de opacidade

Stat::make('Total Depositado', \Helper::amountFormatDecimal($totalDeposited))
    ->description('Total depositado ao longo do tempo')
    ->descriptionIcon('heroicon-m-arrow-trending-up')
    ->extraAttributes(['style' => 'background-color: rgba(40, 167, 69, 0.6); color: #ffffff important;']),  // Verde com 80% de opacidade

Stat::make('Total Sacado', \Helper::amountFormatDecimal($totalWithdrawn))
    ->description('Total sacado ao longo do tempo')
    ->descriptionIcon('heroicon-m-arrow-trending-up')
    ->extraAttributes(['style' => 'background-color: rgba(255, 193, 8, 0.6); color: #ffffff;']),  // Amarelo com 80% de opacidade

Stat::make('Ganhos Afiliados a Pagar', \Helper::amountFormatDecimal($totalAffiliateRevenue))
    ->description('Total de ganhos dos afiliados ao longo do tempo')
    ->descriptionIcon('heroicon-m-arrow-trending-up')
    ->extraAttributes(['style' => 'background-color: rgba(220, 53, 69, 0.6); color: #ffffff;']),  // Vermelho com 80% de opacidade

        ];
    }

    /*** @return array|mixed[] */
    protected function getWidgets(): array
    {
        return [
            UserGrowthChart::class,
            DepositGrowthChart::class, // Adicione o gráfico de depósitos aqui
        ];
    }

    /*** @return bool */
    public static function canView(): bool
    {
        return auth()->user()->hasRole('admin');
    }
}
