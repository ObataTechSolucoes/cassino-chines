<?php

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\AffiliatePlanResource\Pages;
use App\Models\AffiliatePlan;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class AffiliatePlanResource extends Resource
{
    protected static ?string $model = AffiliatePlan::class;

    protected static ?string $navigationIcon = 'heroicon-o-briefcase';
    protected static ?string $navigationGroup = 'Marketing';

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\TextInput::make('name')->required(),
            Forms\Components\Select::make('type')
                ->options(['GGR' => 'GGR', 'REV_CPA' => 'REV+CPA'])
                ->required(),
            Forms\Components\TextInput::make('ggr_share')->numeric()->visible(fn($get)=>$get('type')==='GGR'),
            Forms\Components\TextInput::make('rev_share')->numeric()->visible(fn($get)=>$get('type')==='REV_CPA'),
            Forms\Components\TextInput::make('cpa_amount')->numeric()->visible(fn($get)=>$get('type')==='REV_CPA'),
            Forms\Components\TextInput::make('cpa_ftd_min')->numeric()->visible(fn($get)=>$get('type')==='REV_CPA'),
            Forms\Components\Toggle::make('active')->default(true),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([
            Tables\Columns\TextColumn::make('name')->searchable(),
            Tables\Columns\BadgeColumn::make('type'),
            Tables\Columns\IconColumn::make('active')->boolean(),
        ])
        ->actions([
            Tables\Actions\EditAction::make(),
        ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListAffiliatePlans::route('/'),
            'create' => Pages\CreateAffiliatePlan::route('/create'),
            'edit' => Pages\EditAffiliatePlan::route('/{record}/edit'),
        ];
    }
}
