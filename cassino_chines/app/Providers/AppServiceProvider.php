<?php

namespace App\Providers;

use Filament\Facades\Filament;
use Filament\Support\Facades\FilamentAsset;
use Filament\Support\Facades\FilamentView;
use Filament\View\PanelsRenderHook;
use Illuminate\Contracts\View\View as ViewContract;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Arr;
use Filament\Support\Assets\Js;
use App\Observers\ModelAuditObserver;
use App\Models\{Setting, SettingMail, Gateway, GamesKey, ConfigPlayFiver, Game, SpinConfigs, Role, Permission, User, Vip, PostNotification, Mission, MissionDeposit, Benefit, BenefitRule, UserBenefit};


class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Schema::defaultStringLength(191);

        Builder::macro('whereLike', function ($attributes, string $searchTerm) {
            $this->where(function (Builder $query) use ($attributes, $searchTerm) {
                foreach (Arr::wrap($attributes) as $attribute) {
                    $query->when(
                        str_contains($attribute, '.'),
                        function (Builder $query) use ($attribute, $searchTerm) {
                            $buffer = explode('.', $attribute);
                            $attributeField = array_pop($buffer);
                            $relationPath = implode('.', $buffer);
                            $query->orWhereHas($relationPath, function (Builder $query) use ($attributeField, $searchTerm) {
                                $query->where($attributeField, 'LIKE', "%{$searchTerm}%");
                            });
                        },
                        function (Builder $query) use ($attribute, $searchTerm) {
                            $query->orWhere($attribute, 'LIKE', "%{$searchTerm}%");
                        }
                    );
                }
            });
            return $this;
        });

        // Admin: inject a custom theme for Filament panels without publishing vendor views
        FilamentView::registerRenderHook(
            PanelsRenderHook::HEAD_END,
            /** @return ViewContract */
            fn (): ViewContract => view('filament.admin-theme'),
        );

        // Registra auditoria nos modelos críticos
        foreach ([
            Setting::class,
            SettingMail::class,
            Gateway::class,
            GamesKey::class,
            ConfigPlayFiver::class,
            Game::class,
            SpinConfigs::class,
            Role::class,
            Permission::class,
            User::class,
            Vip::class,
            PostNotification::class,
            Mission::class,
            MissionDeposit::class,
            Benefit::class,
            BenefitRule::class,
            UserBenefit::class,
        ] as $modelClass) {
            if (class_exists($modelClass)) {
                $modelClass::observe(ModelAuditObserver::class);
            }
        }
    }
}
