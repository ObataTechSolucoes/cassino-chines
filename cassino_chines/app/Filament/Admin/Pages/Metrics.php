<?php

namespace App\Filament\Admin\Pages;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Get;
use Filament\Forms\Form;
use Filament\Pages\Dashboard\Actions\FilterAction;
use Filament\Pages\Dashboard\Concerns\HasFiltersAction;
use Filament\Pages\Dashboard\Concerns\HasFiltersForm;

class Metrics extends \Filament\Pages\Dashboard
{
    use HasFiltersForm, HasFiltersAction;

    protected static ?string $navigationIcon = 'heroicon-o-chart-bar';
    protected static string $routePath = '/metrics';

    public function getHeading(): string
    {
        return 'Métricas & Insights';
    }

    public function filtersForm(Form $form): Form
    {
        return $form->schema($this->unifiedDateFilterSchema());
    }

    protected function getHeaderActions(): array
    {
        return [
            FilterAction::make()
                ->label('Filtro')
                ->form($this->unifiedDateFilterSchema()),
        ];
    }

    public function getWidgets(): array
    {
        return [
            \App\Filament\Admin\Widgets\AdvancedMetricsOverview::class,
            \App\Filament\Admin\Widgets\FinancialPerformanceChart::class,
            \App\Filament\Admin\Widgets\AiInsightsWidget::class,
        ];
    }

    private function unifiedDateFilterSchema(): array
    {
        return [
            Select::make('range')
                ->label('Período')
                ->options([
                    'today' => 'Hoje',
                    'yesterday' => 'Ontem',
                    '7' => 'Últimos 7 dias',
                    '30' => 'Últimos 30 dias',
                    '90' => 'Últimos 90 dias',
                    'this_month' => 'Este mês',
                    'last_month' => 'Mês passado',
                    'custom' => 'Personalizado',
                    'all' => 'Período total',
                ])
                ->native(false)
                ->placeholder('Selecione o período')
                ->reactive()
                ->default('30')
                ->columnSpanFull(),

            DatePicker::make('startDate')
                ->label('Data inicial')
                ->native(false)
                ->closeOnDateSelection()
                ->visible(fn (Get $get) => $get('range') === 'custom')
                ->required(fn (Get $get) => $get('range') === 'custom'),

            DatePicker::make('endDate')
                ->label('Data final')
                ->native(false)
                ->closeOnDateSelection()
                ->visible(fn (Get $get) => $get('range') === 'custom')
                ->required(fn (Get $get) => $get('range') === 'custom'),
        ];
    }
}
