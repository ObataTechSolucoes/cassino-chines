<?php

namespace App\Filament\Admin\Resources\SettingResource\Pages;

use App\Filament\Admin\Resources\SettingResource;
use App\Models\Setting;
use AymanAlhattami\FilamentPageWithSidebar\Traits\HasPageSidebar;
use Filament\Forms\Components\Group;
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

class RolloverSetting extends Page implements HasForms
{
    use HasPageSidebar, InteractsWithForms;

    protected static string $resource = SettingResource::class;
    protected static string $view = 'filament.resources.setting-resource.pages.rollover-setting';

    public Setting $record;
    public ?array $data = [];

    public function getTitle(): string|Htmlable
    {
        return __('Configurações de Rollover');
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
                    ->body('As configurações de rollover foram salvas com sucesso!')
                    ->success()
                    ->send();

                redirect(route('filament.admin.resources.settings.rollover', ['record' => $this->record->id]));
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
                Section::make('Configurações de Rollover')
                    ->description('Ajuste os multiplicadores e regras de rollover')
                    ->schema([
                        TextInput::make('rollover_deposit')
                            ->label('Multiplicador de Depósito')
                            ->numeric()
                            ->default(1)
                            ->required()
                            ->suffix('x')
                            ->helperText('Quantas vezes o valor depositado deve ser movimentado para saque')
                            ->maxLength(191),
                            
                        Group::make()->schema([
                            TextInput::make('rollover')
                                ->label('Multiplicador de Bônus')
                                ->numeric()
                                ->default(1)
                                ->required()
                                ->suffix('x')
                                ->helperText('Quantas vezes o valor do bônus deve ser movimentado')
                                ->maxLength(191),
                                
                            TextInput::make('rollover_protection')
                                ->label('Proteção de Rollover')
                                ->numeric()
                                ->default(1)
                                ->required()
                                ->suffix('x')
                                ->helperText('Número mínimo de transações para zerar o rollover')
                                ->maxLength(191),
                        ])->columns(2),
                        
                        Toggle::make('disable_rollover')
                            ->label('Desativar Sistema de Rollover')
                            ->inline(false)
                            ->helperText('Quando ativado, desabilita todas as regras de rollover')
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