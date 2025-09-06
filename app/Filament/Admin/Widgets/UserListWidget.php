<?php

namespace App\Filament\Admin\Widgets;

use App\Models\User;
use Filament\Widgets\TableWidget as BaseWidget;
use Filament\Tables;
use Filament\Tables\Filters\Filter;
use Filament\Forms\Components\DatePicker;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;

class UserListWidget extends BaseWidget
{
    protected static ?int $navigationSort = -1;

    public function getTitle(): string
    {
        return 'Novos Usuários';
    }

    protected function getTableQuery(): Builder
    {
        return User::query()->orderBy('created_at', 'desc');
    }

    protected function getTableColumns(): array
    {
        return [
            Tables\Columns\TextColumn::make('email')
                ->label('E-mail')
                ->searchable()
                ->sortable()
                ->weight('bold')
                ->color('primary')
                ->size('lg'),
                
            Tables\Columns\TextColumn::make('wallet.total_balance')
                ->label('Saldo')
                ->money('BRL')
                ->sortable()
                ->color('success')
                ->size('lg')
                ->weight('bold'),

            Tables\Columns\TextColumn::make('email_verified_at')
                ->label('Verificação E-mail')
                ->dateTime()
                ->sortable()
                ->toggleable(isToggledHiddenByDefault: true)
                ->color('secondary')
                ->size('sm'),

            Tables\Columns\TextColumn::make('updated_at')
                ->label('Última Atualização')
                ->dateTime()
                ->sortable()
                ->toggleable(isToggledHiddenByDefault: true)
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

    protected function getTableActions(): array
    {
        return [
            Tables\Actions\Action::make('details')
                ->label('Ver Detalhes')
                ->icon('heroicon-o-eye')
                ->button()
                ->color('primary')
                ->action(fn(User $user) => redirect()->to(route('filament.admin.resources.users.detail', ['record' => $user]))),
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
                ->label('Adicionar Novo Usuário')
                ->color('success')
                ->icon('heroicon-o-plus'),
        ];
    }

    public static function canView(): bool
{
    return false; // Isso desativa completamente o widget
}
}
