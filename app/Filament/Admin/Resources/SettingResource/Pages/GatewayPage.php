<?php

namespace App\Filament\Admin\Resources\SettingResource\Pages;

use App\Filament\Admin\Resources\SettingResource;
use App\Models\Setting;
use App\Models\ProxySetting;
use AymanAlhattami\FilamentPageWithSidebar\Traits\HasPageSidebar;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
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

class GatewayPage extends Page implements HasForms
{
    use HasPageSidebar, InteractsWithForms;

    protected static string $resource = SettingResource::class;

    protected static string $view = 'filament.resources.setting-resource.pages.gateway-page';

    /**
     * @return string|Htmlable
     */
    public function getTitle(): string|Htmlable
    {
        return 'Gateways';
    }

    public Setting $record;
    public ?array $data = [];

    /**
     * @dev @anonymous
     * @param Model $record
     * @return bool
     */
    public static function canView(Model $record): bool
    {
        return auth()->user()->hasRole('admin');
    }

    /**
     * @dev anonymous - Meu instagram
     * @return void
     */
    public function mount(): void
    {
        $setting = Setting::first();
        $this->record = $setting;
        
        // Carregar configurações de proxy
        $proxySetting = ProxySetting::first();
        if (!$proxySetting) {
            // Criar configuração padrão se não existir
            $proxySetting = ProxySetting::create([
                'proxy_enabled' => false,
                'proxy_host' => null,
                'proxy_port' => null,
                'proxy_username' => null,
                'proxy_password' => null,
                'proxy_type' => 'http',
                'proxy_verify_ssl' => false,
            ]);
        }
        
        // Combinar dados do setting com proxy
        $formData = array_merge($setting->toArray(), $proxySetting->toArray());
        $this->form->fill($formData);
    }

    /**
     * @dev anonymous - Meu instagram
     * @return void
     */
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

            // Verificação da senha
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

            $setting = Setting::find($this->record->id);

            // Separar dados do setting dos dados do proxy
            $settingData = array_intersect_key($this->data, $setting->getFillable());
            $proxyData = array_intersect_key($this->data, [
                'proxy_enabled' => '',
                'proxy_host' => '',
                'proxy_port' => '',
                'proxy_username' => '',
                'proxy_password' => '',
                'proxy_type' => '',
                'proxy_verify_ssl' => '',
            ]);

            // Atualizar configurações principais
            $settingUpdated = $setting->update($settingData);
            
            // Atualizar configurações de proxy
            $proxySetting = ProxySetting::first();
            $proxyUpdated = $proxySetting->update($proxyData);

            if ($settingUpdated || $proxyUpdated) {
                // Limpar cache das configurações
                Cache::put('setting', $setting);
                Cache::forget('proxy_settings'); // Limpar cache do proxy
                
                // Log das alterações
                if (!empty($proxyData)) {
                    \Illuminate\Support\Facades\Log::info('Configurações de proxy atualizadas', [
                        'user_id' => auth()->id(),
                        'proxy_data' => array_merge($proxyData, ['proxy_password' => '***HIDDEN***'])
                    ]);
                }

                Notification::make()
                    ->title('Dados alterados')
                    ->body('Configurações atualizadas com sucesso!')
                    ->success()
                    ->send();
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

    /**
     * @dev anonymous - Meu instagram
     * @param Form $form
     * @return Form
     */
    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Gateway de Pagamento')
                    ->description('Configurações do gateway CPay')
                    ->schema([
                        Select::make('default_gateway')
                            ->label('Gateway Padrão para Saque')
                            ->options([                               
                                'bspay' => 'CPay',
                            ])
                            ->default('bspay')
                            ->disabled()
                            ->columnSpanFull(),
                        Toggle::make('bspay_is_enable')
                            ->label('CPay Ativo')
                            ->default(true),
                    ])->columns(2)
                    ->extraAttributes($this->getSectionStyle('59, 130, 246')), // Azul

                Section::make('Configurações de Proxy')
                    ->description('Ativar ou desativar proxy para conexões externas')
                    ->schema([
                        Toggle::make('proxy_enabled')
                            ->label('Proxy Ativo')
                            ->helperText('Ativa o uso de proxy para todas as conexões externas')
                            ->default(false)
                            ->live()
                            ->afterStateUpdated(function ($state, $set) {
                                if (!$state) {
                                    $set('proxy_host', '');
                                    $set('proxy_port', '');
                                    $set('proxy_username', '');
                                    $set('proxy_password', '');
                                    $set('proxy_type', 'http');
                                    $set('proxy_verify_ssl', false);
                                }
                            }),
                        
                        TextInput::make('proxy_host')
                            ->label('Host do Proxy')
                            ->placeholder('ex: 142.147.128.93')
                            ->helperText('Endereço IP ou domínio do servidor proxy')
                            ->hidden(fn ($get) => !$get('proxy_enabled'))
                            ->required(fn ($get) => $get('proxy_enabled')),
                        
                        TextInput::make('proxy_port')
                            ->label('Porta do Proxy')
                            ->placeholder('ex: 6593')
                            ->helperText('Porta do servidor proxy')
                            ->numeric()
                            ->hidden(fn ($get) => !$get('proxy_enabled'))
                            ->required(fn ($get) => $get('proxy_enabled')),
                        
                        TextInput::make('proxy_username')
                            ->label('Usuário do Proxy')
                            ->placeholder('ex: jdbbvfcd')
                            ->helperText('Nome de usuário para autenticação no proxy')
                            ->hidden(fn ($get) => !$get('proxy_enabled'))
                            ->required(fn ($get) => $get('proxy_enabled')),
                        
                        TextInput::make('proxy_password')
                            ->label('Senha do Proxy')
                            ->password()
                            ->placeholder('ex: 5w6wm2gsfw22')
                            ->helperText('Senha para autenticação no proxy')
                            ->hidden(fn ($get) => !$get('proxy_enabled'))
                            ->required(fn ($get) => $get('proxy_enabled')),
                        
                        Select::make('proxy_type')
                            ->label('Tipo de Proxy')
                            ->options([
                                'http' => 'HTTP',
                                'https' => 'HTTPS',
                                'socks4' => 'SOCKS4',
                                'socks5' => 'SOCKS5',
                            ])
                            ->default('http')
                            ->helperText('Tipo de protocolo do proxy')
                            ->hidden(fn ($get) => !$get('proxy_enabled'))
                            ->required(fn ($get) => $get('proxy_enabled')),
                        
                        Toggle::make('proxy_verify_ssl')
                            ->label('Verificar SSL')
                            ->helperText('Verificar certificados SSL (desativar se houver problemas de conexão)')
                            ->default(false)
                            ->hidden(fn ($get) => !$get('proxy_enabled')),
                    ])->columns(2)
                    ->extraAttributes($this->getSectionStyle('34, 197, 94')), // Verde

                Section::make('Digite a senha de confirmação')
                    ->description('Obrigatório digitar sua senha de confirmação!')
                    ->schema([
                        TextInput::make('approval_password_save')
                            ->label('Senha de Aprovação')
                            ->password()
                            ->required()
                            ->helperText('Digite a senha para salvar as alterações.')
                            ->maxLength(191),
                    ])->columns(3)
                    ->extraAttributes($this->getSectionStyle('239, 68, 68')) // Vermelho
            ])
            ->statePath('data');
    }
}