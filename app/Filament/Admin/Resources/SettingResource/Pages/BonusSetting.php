<?php

namespace App\Filament\Admin\Resources\SettingResource\Pages;

use App\Filament\Admin\Resources\SettingResource;
use App\Models\Setting;
use AymanAlhattami\FilamentPageWithSidebar\Traits\HasPageSidebar;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\Page;
use Filament\Support\Exceptions\Halt;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Hash;
use App\Models\AproveSaveSetting;

class BonusSetting extends Page implements HasForms
{
    use HasPageSidebar, InteractsWithForms;

    protected static string $resource = SettingResource::class;
    protected static string $view = 'filament.resources.setting-resource.pages.bonus-setting';

    public Setting $record;
    public ?array $data = [];

    public function getTitle(): string|Htmlable
    {
        return __('Bônus VIP');
    }

    public static function canView(Model $record): bool
    {
        return auth()->user()->hasRole('admin');
    }

    public function mount(): void
    {
        $this->record = Setting::first();
        $this->form->fill($this->record->toArray());
    }

    public function save()
    {
        try {
            if (env('APP_DEMO')) {
                Notification::make()
                    ->title('Atenção')
                    ->body('Você não pode realizar está alteração na versão demo')
                    ->danger()
                    ->send();
                return;
            }

            $approvalSettings = AproveSaveSetting::first();
            $inputPassword = $this->data['approval_password_save'] ?? '';

            if (!Hash::check($inputPassword, $approvalSettings->approval_password_save)) {
                Notification::make()
                    ->title('Erro de Autenticação')
                    ->body('Senha incorreta. Por favor, tente novamente.')
                    ->danger()
                    ->send();
                return;
            }

            if ($this->record->update($this->data)) {
                Cache::put('setting', $this->record);

                Notification::make()
                    ->title('Dados alterados')
                    ->body('Dados alterados com sucesso!')
                    ->success()
                    ->send();

                redirect(route('filament.admin.resources.settings.bonus', ['record' => $this->record->id]));
            }
        } catch (Halt $exception) {
            return;
        }
    }

    private function getSectionStyle(string $color): array
    {
        return [
            'style' => "background: linear-gradient(135deg, rgba({$color}, 0.1) 0%, rgba({$color}, 0.2) 100%);
                      border-left: 4px solid rgb({$color});
                      border-radius: 8px;
                      padding: 1.5rem;"
        ];
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Configurações de Bônus VIP')
                    ->description('Ajuste os parâmetros do sistema de bônus VIP')
                    ->schema([
                        TextInput::make('bonus_vip')
                            ->label('Valor do Bônus VIP')
                            ->placeholder('Ex: 0.10 para 10% de bônus')
                            ->numeric()
                            ->required()
                            ->helperText('Valor de bônus concedido por cada 1 real depositado (ex: 0.10 = 10% de bônus)')
                            ->maxLength(191),
                        Toggle::make('activate_vip_bonus')
                            ->inline(false)
                            ->label('Ativar Sistema de Bônus VIP')
                            ->helperText('Habilita/desabilita o sistema de bônus para todos os usuários'),
                    ])->columns(2)
                    ->extraAttributes($this->getSectionStyle('16, 185, 129')), // Verde

                Section::make('Confirmação de Segurança')
                    ->description('Autenticação necessária para salvar alterações')
                    ->schema([
                        TextInput::make('approval_password_save')
                            ->label('Senha de Administrador')
                            ->password()
                            ->required()
                            ->helperText('Digite sua senha para confirmar as alterações')
                            ->maxLength(191),
                    ])->columns(3)
                    ->extraAttributes($this->getSectionStyle('239, 68, 68')) // Vermelho
            ])
            ->statePath('data');
    }
}