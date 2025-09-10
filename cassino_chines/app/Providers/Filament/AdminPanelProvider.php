<?php

namespace App\Providers\Filament;

use Althinect\FilamentSpatieRolesPermissions\FilamentSpatieRolesPermissionsPlugin;
use App\Filament\Admin\Pages\AdvancedPage;
use App\Filament\Admin\Pages\DashboardAdmin;
use App\Filament\Admin\Pages\Metrics;
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
use App\Filament\Admin\Resources\AuditLogResource;
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
use App\Filament\Admin\Resources\BenefitResource;
use App\Filament\Admin\Resources\BonusRuleResource;
use App\Filament\Admin\Resources\AffiliatePlanResource;
use App\Filament\Admin\Resources\CommissionLogResource;
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
use App\Http\Middleware\AuditRequest;

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
        ->brandLogo(fn () => view('filament.components.logo'))
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
                // Visão Geral
                NavigationGroup::make('Visão Geral')
                    ->items([
                        NavigationItem::make('dashboard')
                            ->label(fn (): string => __('filament-panels::pages/dashboard.title'))
                            ->url(fn (): string => DashboardAdmin::getUrl())
                            ->icon('icon-dash')
                            ->visible(fn (): bool => auth()->user()->hasRole('admin')),
                        NavigationItem::make('metrics')
                            ->label('Métricas')
                            ->url(fn (): string => Metrics::getUrl())
                            ->icon('heroicon-m-chart-bar')
                            ->visible(fn (): bool => auth()->user()->hasRole('admin')),
                    ]),

                // Financeiro
                NavigationGroup::make('Financeiro')
                    ->items([
                        ...collect(WalletResource::getNavigationItems())->map(fn ($item) => $item->icon('icon-wallet')),
                        ...collect(DepositResource::getNavigationItems())->map(fn ($item) => $item->icon('icon-deposit')),
                        ...collect(WithdrawalResource::getNavigationItems())->map(fn ($item) => $item->icon('icon-saques')),
                        ...collect(AffiliateWithdrawResource::getNavigationItems())->map(fn ($item) => $item->icon('icon-saques')),
                        ...collect(AccountWithdrawResource::getNavigationItems())->map(fn ($item) => $item->icon('icon-pix')),
                        ...collect(SenhaSaqueResource::getNavigationItems())->map(fn ($item) => $item->icon('icon-senuser')),
                        ...collect(AproveWithdrawalResource::getNavigationItems())->map(fn ($item) => $item->icon('icon-senafi')),
                    ]),

                // Usuários
                NavigationGroup::make('Usuários')
                    ->items([
                        ...collect(UserResource::getNavigationItems())->map(fn ($item) => $item->icon('icon-users')),
                        ...collect(AffiliateInfoResource::getNavigationItems())->map(fn ($item) => $item->icon('icon-resultafi')),
                        ...collect(AffiliateHistoryResource::getNavigationItems())->map(fn ($item) => $item->icon('icon-historiafi')),
                        ...collect(VipResource::getNavigationItems())->map(fn ($item) => $item->icon('icon-vip')),
                    ]),

                // Jogos
                NavigationGroup::make('Jogos')
                    ->items([
                        ...collect(CategoryResource::getNavigationItems())->map(fn ($item) => $item->icon('icon-category')),
                        ...collect(ProviderResource::getNavigationItems())->map(fn ($item) => $item->icon('icon-provedor')),
                        ...collect(GameResource::getNavigationItems())->map(fn ($item) => $item->icon('icon-games')),
                    ]),

                // Relatórios
                NavigationGroup::make('Relatórios')
                    ->items([
                        ...collect(GGRGamesResource::getNavigationItems())->map(fn ($item) => $item->icon('icon-historiafi')),
                        ...collect(GGRGamesFiverResource::getNavigationItems())->map(fn ($item) => $item->icon('icon-historiafi')),
                        ...collect(GGRGamesDrakonResource::getNavigationItems())->map(fn ($item) => $item->icon('icon-historiafi')),
                        ...collect(GGRGamesWorldSlotResource::getNavigationItems())->map(fn ($item) => $item->icon('icon-historiafi')),
                        ...collect(OrderResource::getNavigationItems())->map(fn ($item) => $item->icon('icon-historiafi')),
                    ]),

                // Conteúdo
                NavigationGroup::make('Conteúdo')
                    ->items([
                        ...collect(BannerResource::getNavigationItems())->map(fn ($item) => $item->icon('icon-banner')),
                        ...collect(SliderTextResource::getNavigationItems())->map(fn ($item) => $item->icon('icon-slider')),
                        ...collect(MusicResource::getNavigationItems())->map(fn ($item) => $item->icon('icon-music')),
                        ...collect(PlataEventosResource::getNavigationItems())->map(fn ($item) => $item->icon('icon-events')),
                    ]),

                // Marketing
                NavigationGroup::make('Marketing')
                    ->items([
                        ...collect(PostNotificationResource::getNavigationItems())->map(fn ($item) => $item->icon('icon-noti')),
                        ...collect(MissionDepositResource::getNavigationItems())->map(fn ($item) => $item->icon('icon-mission')),
                        ...collect(AffiliatePlanResource::getNavigationItems())->map(fn ($item) => $item->icon('heroicon-o-briefcase')),
                        ...collect(CommissionLogResource::getNavigationItems())->map(fn ($item) => $item->icon('heroicon-o-currency-dollar')),
                    ]),

                // Benefícios
                NavigationGroup::make('Benefícios')
                    ->items([
                        ...collect(BenefitResource::getNavigationItems())->map(fn ($item) => $item->icon('heroicon-o-gift')),
                        ...collect(BonusRuleResource::getNavigationItems())->map(fn ($item) => $item->icon('heroicon-o-adjustments-horizontal')),
                    ]),

                // Sistema
                NavigationGroup::make('Sistema')
                    ->items([
                        NavigationItem::make('settings')
                            ->label(fn (): string => 'Ajustes')
                            ->url(fn (): string => SettingResource::getUrl())
                            ->icon('icon-set')
                            ->visible(fn (): bool => auth()->user()->hasRole('admin')),

                        NavigationItem::make('gateway')
                            ->label(fn (): string => 'Gateways de pagamento')
                            ->url(fn (): string => GatewayPage::getUrl())
                            ->icon('icon-banco')
                            ->visible(fn (): bool => auth()->user()->hasRole('admin')),

                        NavigationItem::make('games-key')
                            ->label(fn (): string => 'API de Jogos')
                            ->url(fn (): string => GamesKeyPage::getUrl())
                            ->icon('icon-api')
                            ->visible(fn (): bool => auth()->user()->hasRole('admin')),

                        NavigationItem::make('custom-layout')
                            ->label(fn (): string => 'Customização')
                            ->url(fn (): string => LayoutCssCustom::getUrl())
                            ->icon('icon-costumer')
                            ->visible(fn (): bool => auth()->user()->hasRole('admin')),

                        NavigationItem::make('setting-mail')
                            ->label(fn (): string => 'SMTP')
                            ->url(fn (): string => SettingMailPage::getUrl())
                            ->icon('icon-mail')
                            ->visible(fn (): bool => auth()->user()->hasRole('admin')),

                        ...collect(AproveSaveSettingResource::getNavigationItems())->map(fn ($item) => $item->icon('icon-senafi')),
                        ...collect(RoleResource::getNavigationItems())->map(fn ($item) => $item->icon('icon-users')),
                        ...collect(PermissionResource::getNavigationItems())->map(fn ($item) => $item->icon('icon-users')),

                        NavigationItem::make('Limpar o cache')
                            ->label('Limpar cache')
                            ->url(url('/clear'), shouldOpenInNewTab: false)
                            ->icon('icon-limp')
                            ->visible(fn (): bool => auth()->user()->hasRole('admin')),
                    ]),

                // Auditoria
                NavigationGroup::make('Auditoria')
                    ->items([
                        ...collect(AuditLogResource::getNavigationItems())->map(fn ($item) => $item->icon('heroicon-o-clipboard-document-check')),
                    ]),
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
            AuditRequest::class,
        ])
        ->authMiddleware([
            Authenticate::class,
            CheckAdmin::class,
        ])
        ->plugin(FilamentSpatieRolesPermissionsPlugin::make());
}

}
