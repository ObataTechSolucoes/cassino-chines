<?php

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\BonusRuleResource\Pages;
use App\Models\BenefitRule;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class BonusRuleResource extends Resource
{
    protected static ?string $model = BenefitRule::class;

    protected static ?string $navigationIcon = 'heroicon-o-adjustments-horizontal';
    protected static ?string $navigationGroup = 'Benefícios';

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Select::make('benefit_id')
                ->relationship('benefit', 'name')
                ->required(),
            Forms\Components\TextInput::make('rule_type')
                ->required(),
            Forms\Components\Textarea::make('config')
                ->rows(3),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('benefit.name')->label('Benefício'),
                Tables\Columns\TextColumn::make('rule_type'),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListBonusRules::route('/'),
            'create' => Pages\CreateBonusRule::route('/create'),
            'edit' => Pages\EditBonusRule::route('/{record}/edit'),
        ];
    }
}
