<?php

namespace App\Filament\Admin\Pages;

use App\Models\Gateway;
use App\Models\AproveSaveSetting; // Importação do modelo para verificação de senha
use Filament\Forms\Components\Section;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Support\Exceptions\Halt;
use Illuminate\Support\Facades\Hash; // Importação para verificação da senha
use Illuminate\Support\HtmlString;
use Jackiedo\DotenvEditor\Facades\DotenvEditor;

class GatewayPage extends Page
{
    use InteractsWithForms;

    protected static ?string $navigationIcon = 'heroicon-o-document-text';

    protected static string $view = 'filament.pages.gateway-page';

    public ?array $data = [];
    public Gateway $setting;

    /**
     * @dev @anonymous
     * @return bool
     */
    public static function canAccess(): bool
    {
        return auth()->user()->hasRole('admin');
    }

    /**
     * @return void
     */
    public function mount(): void
    {
        $gateway = Gateway::first();
        if (!empty($gateway)) {
            $this->setting = $gateway;
            $this->form->fill($this->setting->toArray());
        } else {
            $this->form->fill();
        }
    }

    /**
     * @param Form $form
     * @return Form
     */
    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('CNPAY')
                    ->description(new HtmlString('
                                <div style="display: flex; align-items: center;">
                                    Provedor de gateway CNPay - Configure suas credenciais de API:
                                    <a class="dark:text-white"
                                    style="
                                            font-size: 14px;
                                            font-weight: 600;
                                            width: 127px;
                                            display: flex;
                                            background-color: #f800ff;
                                            padding: 10px;
                                            border-radius: 11px;
                                            justify-content: center;
                                            margin-left: 10px;
                                    "
                                    href="https://painel.appcnpay.com/panel/gateway"
                                    target="_blank">
                                        Dashboard
                                    </a>
                                    <a class="dark:text-white"
                                    style="
                                            font-size: 14px;
                                            font-weight: 600;
                                            width: 127px;
                                            display: flex;
                                            background-color: #f800ff;
                                            padding: 10px;
                                            border-radius: 11px;
                                            justify-content: center;
                                            margin-left: 10px;
                                    "
                                    href="https://api.whatsapp.com/message/BPGUMYJ5T5PIN1?autoload=1&app_absent=0"
                                    target="_blank">
                                        Suporte
                                    </a>
                                </div>
                    <b>Seu Webhook:  ' . url("/cnpay/webhook", [], true) . "</b>"))
                    ->schema([
                        TextInput::make('cnpay_uri')
                            ->label('API URL')
                            ->placeholder('Digite a URL da API do CNPay')
                            ->maxLength(191)
                            ->columnSpanFull(),
                        TextInput::make('cnpay_public_key')
                            ->label('Chave Pública (x-public-key)')
                            ->placeholder('Digite a chave pública do CNPay')
                            ->maxLength(191)
                            ->columnSpanFull(),
                        TextInput::make('cnpay_secret_key')
                            ->label('Chave Privada (x-secret-key)')
                            ->placeholder('Digite a chave privada do CNPay')
                            ->maxLength(191)
                            ->columnSpanFull(),
                        TextInput::make('cnpay_webhook_url')
                            ->label('URL do Webhook')
                            ->placeholder('URL para receber notificações do CNPay')
                            ->maxLength(191)
                            ->columnSpanFull(),
                    ]),
                
                // Seção para a senha de aprovação
                Section::make('Digite a senha de confirmação')
                    ->description('Obrigatório digitar sua senha de confirmação!')
                    ->schema([
                        TextInput::make('approval_password_save')
                            ->label('Senha de Aprovação')
                            ->password()
                            ->required()
                            ->helperText('Digite a senha para salvar as alterações.')
                            ->maxLength(191),
                    ])->columns(2),
            ])
            ->statePath('data');
    }


    /**
     * @return void
     */
    public function submit(): void
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

            $setting = Gateway::first();
            if (!empty($setting)) {
                if ($setting->update($this->data)) {
                    Notification::make()
                        ->title('Chaves Alteradas')
                        ->body('Suas chaves foram alteradas com sucesso!')
                        ->success()
                        ->send();
                }
            } else {
                if (Gateway::create($this->data)) {
                    Notification::make()
                        ->title('Chaves Criadas')
                        ->body('Suas chaves foram criadas com sucesso!')
                        ->success()
                        ->send();
                }
            }

        } catch (Halt $exception) {
            Notification::make()
                ->title('Erro ao alterar dados!')
                ->body('Erro ao alterar dados!')
                ->danger()
                ->send();
        }
    }
}
