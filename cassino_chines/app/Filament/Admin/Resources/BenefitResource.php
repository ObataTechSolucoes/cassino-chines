<?php

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\BenefitResource\Pages;
use App\Models\Benefit;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class BenefitResource extends Resource
{
    protected static ?string $model = Benefit::class;

    protected static ?string $navigationIcon = 'heroicon-o-gift';
    protected static ?string $navigationGroup = 'Benefícios';

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\TextInput::make('name')
                ->required()
                ->maxLength(255),
            Forms\Components\Select::make('type')
                ->options([
                    'cash' => 'Cash',
                    'freebet' => 'Freebet',
                    'cashback' => 'Cashback',
                ])
                ->required(),
            Forms\Components\TextInput::make('priority')
                ->numeric()
                ->default(0),
            Forms\Components\Textarea::make('stacking_rules')
                ->rows(3),
            Forms\Components\TextInput::make('rollover')
                ->numeric()
                ->default(0),
            Forms\Components\TextInput::make('cap')
                ->numeric(),
            Forms\Components\Textarea::make('conflicts')
                ->rows(3),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')->searchable(),
                Tables\Columns\TextColumn::make('type'),
                Tables\Columns\TextColumn::make('priority')->sortable(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\Action::make('simulate')
                    ->label('Simular')
                    ->icon('heroicon-o-play')
                    ->requiresConfirmation()
                    ->action(fn (Benefit $record) => null),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListBenefits::route('/'),
            'create' => Pages\CreateBenefit::route('/create'),
            'edit' => Pages\EditBenefit::route('/{record}/edit'),
        ];
    }
}
