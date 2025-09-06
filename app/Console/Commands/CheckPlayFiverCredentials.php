<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\GamesKey;

class CheckPlayFiverCredentials extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'check:playfiver-credentials';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Verificar credenciais do PlayFiver';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info("🔍 Verificando credenciais do PlayFiver...");
        
        $setting = GamesKey::first();
        
        if (!$setting) {
            $this->error("❌ Nenhuma configuração de GamesKey encontrada!");
            return 1;
        }
        
        $this->info("📋 Configurações encontradas:");
        $this->line("   ID: " . $setting->id);
        $this->line("   Criado em: " . $setting->created_at);
        $this->line("   Atualizado em: " . $setting->updated_at);
        
        // Verificar credenciais específicas
        $credentials = [
            'playfiver_secret' => $setting->playfiver_secret ?? 'não definido',
            'playfiver_code' => $setting->playfiver_code ?? 'não definido',
            'playfiver_token' => $setting->playfiver_token ?? 'não definido'
        ];
        
        $this->info("\n🔑 Credenciais do PlayFiver:");
        
        foreach ($credentials as $key => $value) {
            if ($value === 'não definido') {
                $this->error("   ❌ {$key}: {$value}");
            } else {
                $length = strlen($value);
                $preview = substr($value, 0, 10) . (strlen($value) > 10 ? '...' : '');
                $this->line("   ✅ {$key}: {$preview} (tamanho: {$length})");
            }
        }
        
        // Verificar se as credenciais parecem válidas
        $this->info("\n🔍 Validação das credenciais:");
        
        if (empty($credentials['playfiver_secret'])) {
            $this->error("   ❌ playfiver_secret está vazio");
        } elseif (strlen($credentials['playfiver_secret']) < 10) {
            $this->warn("   ⚠️ playfiver_secret parece muito curto");
        } else {
            $this->line("   ✅ playfiver_secret parece válido");
        }
        
        if (empty($credentials['playfiver_code'])) {
            $this->error("   ❌ playfiver_code está vazio");
        } elseif (strlen($credentials['playfiver_code']) < 5) {
            $this->warn("   ⚠️ playfiver_code parece muito curto");
        } else {
            $this->line("   ✅ playfiver_code parece válido");
        }
        
        if (empty($credentials['playfiver_token'])) {
            $this->error("   ❌ playfiver_token está vazio");
        } elseif (strlen($credentials['playfiver_token']) < 20) {
            $this->warn("   ⚠️ playfiver_token parece muito curto");
        } else {
            $this->line("   ✅ playfiver_token parece válido");
        }
        
        // Verificar formato do token (deve ser um UUID)
        if (!empty($credentials['playfiver_token'])) {
            $token = $credentials['playfiver_token'];
            $uuidPattern = '/^[0-9a-f]{8}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{12}$/i';
            
            if (preg_match($uuidPattern, $token)) {
                $this->line("   ✅ playfiver_token tem formato UUID válido");
            } else {
                $this->warn("   ⚠️ playfiver_token não tem formato UUID válido");
            }
        }
        
        $this->info("\n📝 Resumo:");
        $validCredentials = array_filter($credentials, function($value) {
            return $value !== 'não definido' && !empty($value);
        });
        
        if (count($validCredentials) === 3) {
            $this->info("   🎉 Todas as credenciais estão configuradas!");
        } elseif (count($validCredentials) > 0) {
            $this->warn("   ⚠️ " . count($validCredentials) . " de 3 credenciais configuradas");
        } else {
            $this->error("   ❌ Nenhuma credencial configurada!");
        }
        
        return 0;
    }
}
