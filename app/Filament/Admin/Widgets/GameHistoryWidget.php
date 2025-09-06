<?php

namespace App\Filament\Admin\Widgets;

use App\Models\Order;
use Filament\Widgets\TableWidget as BaseWidget;
use Filament\Tables;
use Filament\Tables\Filters\Filter;
use Filament\Forms\Components\DatePicker;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;

class GameHistoryWidget extends BaseWidget
{
    protected static ?int $navigationSort = -1;

    public function getTitle(): string
    {
        return 'Histórico de Partidas'; 
    }

    protected function getTableQuery(): Builder
    {
        return Order::query()->orderBy('created_at', 'desc');
    }

    protected function getTableColumns(): array
    {
        return [
            Tables\Columns\TextColumn::make('user.name')
                ->label('Usuário')
                ->searchable()
                ->sortable()
                ->weight('bold')
                ->color('primary')
                ->size('lg'),

            Tables\Columns\TextColumn::make('session_id')
                ->label('ID da Sessão')
                ->toggleable(isToggledHiddenByDefault: true)
                ->searchable()
                ->color('secondary')
                ->size('sm'),

            Tables\Columns\TextColumn::make('transaction_id')
                ->label('ID da Transação')
                ->toggleable(isToggledHiddenByDefault: true)
                ->searchable()
                ->color('secondary')
                ->size('sm'),

            Tables\Columns\TextColumn::make('game')
                ->label('Jogo')
                ->searchable()
                ->sortable()
                ->weight('bold')
                ->size('md'),

            Tables\Columns\TextColumn::make('game_uuid')
                ->label('UUID do Jogo')
                ->toggleable(isToggledHiddenByDefault: true)
                ->searchable()
                ->size('sm')
                ->color('secondary'),

            Tables\Columns\TextColumn::make('type')
                ->label('Tipo')
                ->searchable()
                ->sortable()
                ->weight('bold')
                ->size('md')
                ->color('info'),

            Tables\Columns\TextColumn::make('amount')
                ->label('Valor')
                ->money('BRL')
                ->sortable()
                ->color('success')
                ->size('lg')
                ->weight('bold'),

            Tables\Columns\IconColumn::make('refunded')
                ->label('Reembolsado')
                ->boolean()
                ->toggleable(isToggledHiddenByDefault: true)
                ->color(fn ($state): string => $state ? 'success' : 'danger')
                ->icon(fn ($state): string => $state ? 'heroicon-o-check-circle' : 'heroicon-o-x-circle'),

            Tables\Columns\IconColumn::make('status')
                ->label('Status')
                ->boolean()
                ->toggleable(isToggledHiddenByDefault: true)
                ->color(fn ($state): string => $state ? 'success' : 'danger')
                ->icon(fn ($state): string => $state ? 'heroicon-o-check-circle' : 'heroicon-o-x-circle'),

            Tables\Columns\TextColumn::make('round_id')
                ->label('ID da Rodada')
                ->toggleable(isToggledHiddenByDefault: true)
                ->searchable()
                ->size('sm')
                ->color('secondary'),

            Tables\Columns\TextColumn::make('created_at')
                ->label('Criado Em')
                ->dateTime()
                ->sortable()
                ->color('secondary')
                ->size('sm'),

        ];
    }

    protected function getTableFilters(): array
    {
        return [
            Filter::make('created_at')
                ->form([
                    DatePicker::make('created_from')->label('Criado a partir de'),
                    DatePicker::make('created_until')->label('Criado até'),
                ])
                ->query(function (Builder $query, array $data): Builder {
                    return $query
                        ->when($data['created_from'], fn (Builder $query, $date): Builder => $query->whereDate('created_at', '>=', $date))
                        ->when($data['created_until'], fn (Builder $query, $date): Builder => $query->whereDate('created_at', '<=', $date));
                })
                ->indicateUsing(function (array $data): array {
                    $indicators = [];

                    if ($data['created_from'] ?? null) {
                        $indicators['created_from'] = 'Criado a partir de ' . Carbon::parse($data['created_from'])->toFormattedDateString();
                    }

                    if ($data['created_until'] ?? null) {
                        $indicators['created_until'] = 'Criado até ' . Carbon::parse($data['created_until'])->toFormattedDateString();
                    }

                    return $indicators;
                }),
        ];
    }

    protected function getTableBulkActions(): array
    {
        return [
            Tables\Actions\BulkActionGroup::make([
                Tables\Actions\DeleteBulkAction::make()
                    ->label('Deletar Selecionados')
                    ->color('danger')
                    ->icon('heroicon-o-trash'),
            ]),
        ];
    }

    protected function getTableEmptyStateActions(): array
    {
        return [
            Tables\Actions\CreateAction::make()
                ->label('Adicionar Novo Registro')
                ->color('success')
                ->icon('heroicon-o-plus'),
        ];
    }

    public static function canView(): bool
{
    return false; // Isso desativa completamente o widget
}
}
