<?php

namespace App\Filament\Admin\Resources\SettingResource\Pages;

use App\Filament\Admin\Resources\SettingResource;
use App\Models\Setting;
use AymanAlhattami\FilamentPageWithSidebar\Traits\HasPageSidebar;
use Filament\Forms\Components\Group;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\TextInput;
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

class PaymentSetting extends Page implements HasForms
{
    use HasPageSidebar, InteractsWithForms;

    protected static string $resource = SettingResource::class;
    protected static string $view = 'filament.resources.setting-resource.pages.payment-setting';

    public Setting $record;
    public ?array $data = [];

    public function getTitle(): string|Htmlable
    {
        return __('Configurações de Pagamento');
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
                    ->title('Configurações atualizadas')
                    ->body('As configurações de pagamento foram salvas com sucesso!')
                    ->success()
                    ->send();

                redirect(route('filament.admin.resources.settings.payment', ['record' => $this->record->id]));
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
                Section::make('Configurações Financeiras')
                    ->description('Defina os limites e parâmetros de pagamento')
                    ->schema([
                        Group::make()->schema([
                            TextInput::make('min_deposit')
                                ->label('Depósito Mínimo')
                                ->numeric()
                                ->required()
                                ->prefixIcon('heroicon-o-arrow-down-circle')
                                ->helperText('Valor mínimo permitido para depósitos')
                                ->maxLength(191),
                            TextInput::make('max_deposit')
                                ->label('Depósito Máximo')
                                ->numeric()
                                ->required()
                                ->prefixIcon('heroicon-o-arrow-up-circle')
                                ->helperText('Valor máximo permitido para depósitos')
                                ->maxLength(191),
                        ])->columns(2),

                        Group::make()->schema([
                            TextInput::make('min_withdrawal')
                                ->label('Saque Mínimo')
                                ->numeric()
                                ->required()
                                ->prefixIcon('heroicon-o-arrow-down-circle')
                                ->helperText('Valor mínimo permitido para saques')
                                ->maxLength(191),
                            TextInput::make('max_withdrawal')
                                ->label('Saque Máximo')
                                ->numeric()
                                ->required()
                                ->prefixIcon('heroicon-o-arrow-up-circle')
                                ->helperText('Valor máximo permitido para saques')
                                ->maxLength(191),
                        ])->columns(2),

                        Group::make()->schema([
                            TextInput::make('initial_bonus')
                                ->label('Bônus Inicial')
                                ->numeric()
                                ->suffix('%')
                                ->helperText('Porcentagem de bônus para novos usuários')
                                ->maxLength(191),
                            TextInput::make('currency_code')
                                ->label('Código da Moeda')
                                ->required()
                                ->helperText('Ex: BRL, USD, EUR')
                                ->maxLength(191),
                        ])->columns(2),

                        Section::make('Comissões de Afiliados')
                            ->schema([
                                Group::make()->schema([
                                    TextInput::make('perc_sub_lv1')
                                        ->label('Nível 1 (%)')
                                        ->numeric()
                                        ->suffix('%')
                                        ->helperText('Comissão para afiliados de primeiro nível')
                                        ->maxLength(191),
                                    TextInput::make('perc_sub_lv2')
                                        ->label('Nível 2 (%)')
                                        ->numeric()
                                        ->suffix('%')
                                        ->helperText('Comissão para afiliados de segundo nível')
                                        ->maxLength(191),
                                ])->columns(2)
                            ])
                            ->extraAttributes($this->getSectionStyle('139, 92, 246')) // Roxo
                    ])
                    ->extraAttributes($this->getSectionStyle('59, 130, 246')), // Azul

                Section::make('Confirmação de Segurança')
                    ->description('Autenticação necessária para salvar alterações')
                    ->schema([
                        TextInput::make('approval_password_save')
                            ->label('Senha de Administrador')
                            ->password()
                            ->required()
                            ->helperText('Digite sua senha para confirmar as alterações')
                            ->maxLength(191),
                    ])
                    ->columns(3)
                    ->extraAttributes($this->getSectionStyle('239, 68, 68')) // Vermelho
            ])
            ->statePath('data');
    }
}