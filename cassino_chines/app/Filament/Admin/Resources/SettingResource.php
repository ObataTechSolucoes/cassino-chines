<?php

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\SettingResource\Pages;
use App\Models\Setting;
use AymanAlhattami\FilamentPageWithSidebar\FilamentPageSidebar;
use AymanAlhattami\FilamentPageWithSidebar\PageNavigationItem;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;

class SettingResource extends Resource
{
    protected static ?string $model = Setting::class;
    protected static ?string $navigationLabel = 'Configurações';
    protected static ?string $modelLabel = 'Configuração';
    protected static ?string $navigationIcon = 'heroicon-o-cog';

    public static function canAccess(): bool
    {
        return auth()->user()->hasRole('admin');
    }

    public static function sidebar(Model $record): FilamentPageSidebar
    {
        return FilamentPageSidebar::make()
            ->setTitle('Configurações Gerais')
            ->setDescription('Gerencie todas as configurações da plataforma')
            ->setNavigationItems([
                self::createNavItem('Ícones & Textos', 'index', 'heroicon-o-cog'),
                self::createNavItem('Bônus VIP', 'bonus', 'heroicon-o-sparkles'),
                self::createNavItem('Rollover', 'rollover', 'heroicon-o-arrow-path'),
                self::createNavItem('Taxas', 'fee', 'heroicon-o-chart-pie'),
                self::createNavItem('Limites', 'limit', 'heroicon-o-adjustments-horizontal'),
                self::createNavItem('Pagamentos', 'payment', 'heroicon-o-credit-card'),
                self::createNavItem('Gateways', 'gateway', 'heroicon-o-globe-alt'),
                self::createNavItem('Afiliados', 'affiliate', 'heroicon-o-users'),
            ]);
    }

    private static function createNavItem(string $label, string $page, string $icon): PageNavigationItem
    {
        return PageNavigationItem::make($label)
            ->url(static::getUrl($page, ['record' => Setting::first()?->id]))
            ->icon($icon)
            ->isActiveWhen(fn () => request()->routeIs(static::getRouteBaseName() . '.' . $page));
    }

    public static function form(Form $form): Form
    {
        return $form->schema([]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([])
            ->filters([])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\DefaultSetting::route('/'),
            'limit' => Pages\LimitSetting::route('/limit/{record}'),
            'bonus' => Pages\BonusSetting::route('/bonus/{record}'),
            'rollover' => Pages\RolloverSetting::route('/rollover/{record}'),
            'details' => Pages\DefaultSetting::route('/details/{record}'),
            'fee' => Pages\FeeSetting::route('/fee/{record}'),
            'payment' => Pages\PaymentSetting::route('/payment/{record}'),
            'affiliate' => Pages\AffiliatePage::route('/affiliate/{record}'),
            'gateway' => Pages\GatewayPage::route('/gateway/{record}'),
        ];
    }
}