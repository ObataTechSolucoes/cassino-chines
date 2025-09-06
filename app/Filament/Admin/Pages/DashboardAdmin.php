<?php

namespace App\Filament\Admin\Pages;

use App\Filament\Admin\Widgets\StatsOverview;
use App\Livewire\AdminWidgets;
use App\Filament\Admin\Widgets\UserGrowthChart;
use App\Filament\Admin\Widgets\DepositGrowthChart;
use App\Filament\Admin\Widgets\UserListWidget;
use App\Filament\Admin\Widgets\GameHistoryWidget;
use App\Livewire\WalletOverview;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Form;
use Filament\Pages\Dashboard\Actions\FilterAction;
use Filament\Pages\Dashboard\Concerns\HasFiltersAction;
use Filament\Pages\Dashboard\Concerns\HasFiltersForm;

class DashboardAdmin extends \Filament\Pages\Dashboard
{
    use HasFiltersForm, HasFiltersAction;

    public function getHeading(): string
    {
        return ''; // Remove o título
    }

    public function filtersForm(Form $form): Form
    {
        return $form
            ->schema([
                DatePicker::make('startDate')->label('Data Inicial'),
                DatePicker::make('endDate')->label('Data Final')
            ]); // Fechamento correto do schema e do return
    }

    protected function getHeaderActions(): array
    {
        return [
            FilterAction::make()
                ->label('Filtro')
                ->form([
                    DatePicker::make('startDate')->label('Data Inicial'),
                    DatePicker::make('endDate')->label('Data Final')
                ]) // Fechamento correto do form
        ]; // Fechamento correto do array
    }

    public static function canAccess(): bool
    {
        return auth()->user()->hasRole('admin');
    }

    public function getWidgets(): array
    {
        return [
            AdminWidgets::class,
            StatsOverview::class,
            WalletOverview::class,
            UserGrowthChart::class,
            DepositGrowthChart::class,
            UserListWidget::class,
            GameHistoryWidget::class
        ]; // Fechamento correto do array
    }
}