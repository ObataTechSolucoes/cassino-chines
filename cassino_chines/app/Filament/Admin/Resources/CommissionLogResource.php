<?php

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\CommissionLogResource\Pages;
use App\Models\AffiliateCommissionLog;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class CommissionLogResource extends Resource
{
    protected static ?string $model = AffiliateCommissionLog::class;

    protected static ?string $navigationIcon = 'heroicon-o-currency-dollar';
    protected static ?string $navigationGroup = 'Marketing';

    public static function form(Form $form): Form
    {
        return $form->schema([]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('period')->sortable(),
                Tables\Columns\TextColumn::make('affiliate.name')->label('Afiliado'),
                Tables\Columns\TextColumn::make('referred.name')->label('Indicado'),
                Tables\Columns\BadgeColumn::make('calc_type'),
                Tables\Columns\TextColumn::make('commission_amount')->money(),
                Tables\Columns\BadgeColumn::make('status'),
            ])
            ->filters([
                SelectFilter::make('status')->options([
                    'pending' => 'Pending',
                    'processed' => 'Processed',
                    'paid' => 'Paid',
                    'failed' => 'Failed',
                ]),
            ])
            ->actions([
                Tables\Actions\Action::make('reprocess')
                    ->label('Reprocessar')
                    ->action(fn (AffiliateCommissionLog $record) => null),
                Tables\Actions\Action::make('markPaid')
                    ->label('Marcar como pago')
                    ->requiresConfirmation()
                    ->visible(fn() => auth()->user()->hasRole('admin'))
                    ->action(fn (AffiliateCommissionLog $record) => $record->update(['status' => 'paid'])),
            ])
            ->headerActions([
                Tables\Actions\ExportAction::make()
                    ->label('Exportar CSV'),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListCommissionLogs::route('/'),
        ];
    }
}
