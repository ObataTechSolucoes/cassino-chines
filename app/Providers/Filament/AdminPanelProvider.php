<?php

namespace App\Providers\Filament;

use Althinect\FilamentSpatieRolesPermissions\FilamentSpatieRolesPermissionsPlugin;
use App\Filament\Admin\Pages\AdvancedPage;
use App\Filament\Admin\Pages\DashboardAdmin;
use App\Filament\Admin\Pages\GamesKeyPage;
use App\Filament\Admin\Pages\GatewayPage;
use App\Filament\Admin\Pages\LayoutCssCustom;
use App\Filament\Admin\Pages\SettingMailPage;
use App\Filament\Admin\Pages\SelectThemePage;
//use App\Filament\Admin\Pages\SettingSpin;
use App\Filament\Admin\Pages\SuitPayPaymentPage;
use App\Filament\Admin\Resources\AffiliateWithdrawResource;
use App\Filament\Admin\Resources\AffiliateInfoResource;
use App\Filament\Admin\Resources\BannerResource;
use App\Filament\Admin\Resources\CategoryResource;
use App\Filament\Admin\Resources\DepositResource;
use App\Filament\Admin\Resources\GameResource;
use App\Filament\Admin\Resources\PlataEventosResource;
use App\Filament\Admin\Resources\GGRGamesDrakonResource;
use App\Filament\Admin\Resources\GGRGamesResource;
use App\Filament\Admin\Resources\GGRGamesFiverResource;
use App\Filament\Admin\Resources\GGRGamesWorldSlotResource;
//use App\Filament\Admin\Resources\MissionResource;
use App\Filament\Admin\Resources\MissionDepositResource;
use App\Filament\Admin\Resources\MusicResource;
use App\Filament\Admin\Resources\OrderResource;
use App\Filament\Admin\Resources\ProviderResource;
use App\Filament\Admin\Resources\PostNotificationResource;
use App\Filament\Admin\Resources\SettingResource;
use App\Filament\Admin\Resources\UserResource;
use App\Filament\Admin\Resources\VipResource;
use App\Filament\Admin\Resources\WalletResource;
use App\Filament\Admin\Resources\WithdrawalResource;
use App\Filament\Admin\Resources\SliderTextResource;
use App\Filament\Admin\Resources\SenhaSaqueResource;
use App\Filament\Admin\Resources\AproveWithdrawalResource;
use App\Filament\Admin\Resources\AffiliateHistoryResource;
use App\Filament\Admin\Resources\AproveSaveSettingResource;
use App\Filament\Admin\Resources\AccountWithdrawResource;
use App\Filament\Admin\Resources\RoleResource;
use App\Filament\Admin\Resources\PermissionResource;
use App\Http\Middleware\CheckAdmin;
use App\Livewire\AdminWidgets;
use App\Livewire\WalletOverview;
use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Navigation\NavigationBuilder;
use Filament\Navigation\NavigationGroup;
use Filament\Navigation\NavigationItem;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\AuthenticateSession;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;

class AdminPanelProvider extends PanelProvider
{
    /**
     * @param Panel $panel
     * @return Panel
     */
   public function panel(Panel $panel): Panel
{
    return $panel
        ->default()
        ->id('admin')
        ->path(env("FILAMENT_BASE_URL", "admin"))
        ->login()
        ->colors([
            'danger' => Color::Red,       // Vermelho (para alertas ou erros)
            'gray' => Color::Slate,       // Cinza escuro (para fundos ou bordas discretas)
            'info' => Color::Sky,         // Azul claro (para informações)
            'primary' => Color::Indigo,   // Azul indigo (para destacar elementos principais)
            'success' => Color::Green,    // Verde (para ações bem-sucedidas ou sucesso)
            'warning' => '#FF4500',       // Âmbar (para alertas de aviso)
        ])
        ->font('Prompt')
        ->discoverResources(in: app_path('Filament/Admin/Resources'), for: 'App\\Filament\\Admin\\Resources')
        ->discoverPages(in: app_path('Filament/Admin/Pages'), for: 'App\\Filament\\Admin\\Pages')
        ->pages([
            DashboardAdmin::class,
        ])
        ->globalSearchKeyBindings(['command+k', 'ctrl+k'])
        ->sidebarCollapsibleOnDesktop()
        ->collapsibleNavigationGroups(true)
        ->discoverWidgets(in: app_path('Filament/Admin/Widgets'), for: 'App\\Filament\\Admin\\Widgets')
        ->widgets([
            WalletOverview::class,
            AdminWidgets::class,
        ])
        ->navigation(function (NavigationBuilder $builder): NavigationBuilder {
            return $builder->groups([
                NavigationGroup::make()
                    ->items([
                        NavigationItem::make('dashboard')
                            ->label(fn(): string => __('filament-panels::pages/dashboard.title'))
                            ->url(fn(): string => DashboardAdmin::getUrl())
                            ->icon('icon-dash')
                            ->isActiveWhen(fn() => request()->routeIs('filament.pages.settings'))
                            ->visible(fn(): bool => auth()->user()->hasRole('admin')),

                        NavigationItem::make('settings')
                            ->label(fn(): string => 'Ajustes')
                            ->url(fn(): string => SettingResource::getUrl())
                            ->icon('icon-set')
                            ->isActiveWhen(fn() => request()->routeIs('filament.pages.settings'))
                            ->visible(fn(): bool => auth()->user()->hasRole('admin')),
NavigationItem::make('Selecionar Tema')
                            ->label('Temas')
                            ->url(fn(): string => SelectThemePage::getUrl())
                            ->icon('icon-themes')
                            ->isActiveWhen(fn () => request()->routeIs('filament.admin.pages.select-theme.*'))
                            ->visible(fn(): bool => auth()->user()->hasRole('admin')),
                            
...collect(BannerResource::getNavigationItems())->map(fn($item) => $item->icon('icon-banner')),
 ...collect(MusicResource::getNavigationItems())->map(fn($item) => $item->icon('icon-music')),
                        NavigationItem::make('games-key')
                            ->label(fn(): string => 'API DE JOGOS')
                            ->url(fn(): string => GamesKeyPage::getUrl())
                             ->icon('icon-api')
                            ->isActiveWhen(fn() => request()->routeIs('filament.pages.games-key-page'))
                            ->visible(fn(): bool => auth()->user()->hasRole('admin')),
                        NavigationItem::make('gateway')
                            ->label(fn(): string => 'Gateways de pagamento')
                            ->url(fn(): string => GatewayPage::getUrl())
                             ->icon('icon-banco')
                            ->isActiveWhen(fn() => request()->routeIs('filament.pages.gateway-page'))
                            ->visible(fn(): bool => auth()->user()->hasRole('admin')),

                      

                       ...collect(AproveWithdrawalResource::getNavigationItems())->map(fn($item) => $item->icon('icon-senafi')),

                       ...collect(AproveSaveSettingResource::getNavigationItems())->map(fn($item) => $item->icon('icon-senafi')),
                        ...collect(UserResource::getNavigationItems())->map(fn($item) => $item->icon('icon-users')),
                        ...collect(WalletResource::getNavigationItems())->map(fn($item) => $item->icon('icon-wallet')),
                        ...collect(DepositResource::getNavigationItems())->map(fn($item) => $item->icon('icon-deposit')),
...collect(WithdrawalResource::getNavigationItems())->map(fn($item) => $item->icon('icon-saques')),
...collect(AffiliateWithdrawResource::getNavigationItems())->map(fn($item) => $item->icon('icon-saques')),
...collect(AccountWithdrawResource::getNavigationItems())->map(fn($item) => $item->icon('icon-pix')),
...collect(SenhaSaqueResource::getNavigationItems())->map(fn($item) => $item->icon('icon-senuser')),
...collect(AffiliateInfoResource::getNavigationItems())->map(fn($item) => $item->icon('icon-resultafi')),
...collect(AffiliateHistoryResource::getNavigationItems())->map(fn($item) => $item->icon('icon-historiafi')),
...collect(VipResource::getNavigationItems())->map(fn($item) => $item->icon('icon-vip')),
...collect(MissionDepositResource::getNavigationItems())->map(fn($item) => $item->icon('icon-mission')),
...collect(PostNotificationResource::getNavigationItems())->map(fn($item) => $item->icon('icon-noti')),
...collect(SliderTextResource::getNavigationItems())->map(fn($item) => $item->icon('icon-slider')),
...collect(PlataEventosResource::getNavigationItems())->map(fn($item) => $item->icon('icon-events')),
...collect(CategoryResource::getNavigationItems())->map(fn($item) => $item->icon('icon-category')),
...collect(ProviderResource::getNavigationItems())->map(fn($item) => $item->icon('icon-provedor')),
...collect(GameResource::getNavigationItems())->map(fn($item) => $item->icon('icon-games')),
...collect(OrderResource::getNavigationItems())->map(fn($item) => $item->icon('icon-historiafi')),

                        NavigationItem::make('custom-layout')
                            ->label(fn(): string => 'Customização')
                            ->url(fn(): string => LayoutCssCustom::getUrl())
                          ->icon('icon-costumer')
                            ->isActiveWhen(fn() => request()->routeIs('filament.pages.layout-css-custom'))
                            ->visible(fn(): bool => auth()->user()->hasRole('admin')),


                        NavigationItem::make('setting-mail')
                            ->label(fn(): string => 'SMTP')
                            ->url(fn(): string => SettingMailPage::getUrl())
                            ->icon('icon-mail')
                            ->isActiveWhen(fn() => request()->routeIs('filament.pages.setting-mail-page'))
                            ->visible(fn(): bool => auth()->user()->hasRole('admin')),
                        NavigationItem::make('Limpar o cache')
                                ->url(url('/clear'), shouldOpenInNewTab: false)
                                ->icon('icon-limp')
                                ->visible(fn(): bool => auth()->user()->hasRole('admin')),

                    ])
            ]);
        })
        ->middleware([
            EncryptCookies::class,
            AddQueuedCookiesToResponse::class,
            StartSession::class,
            AuthenticateSession::class,
            ShareErrorsFromSession::class,
            VerifyCsrfToken::class,
            SubstituteBindings::class,
            DisableBladeIconComponents::class,
            DispatchServingFilamentEvent::class,
        ])
        ->authMiddleware([
            Authenticate::class,
            CheckAdmin::class,
        ])
        ->plugin(FilamentSpatieRolesPermissionsPlugin::make());
}

}