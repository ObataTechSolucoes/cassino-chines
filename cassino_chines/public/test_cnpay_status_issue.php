<?php
/**
 * Teste Específico do Problema de Status CNPay
 * Para identificar onde está ocorrendo o erro
 */

echo "🔍 Teste Específico do Problema de Status CNPay\n";
echo "==============================================\n\n";

// Verificar se o Laravel está carregado
if (!function_exists('app')) {
    echo "❌ Laravel não está carregado\n";
    echo "🔧 Este arquivo deve ser executado através do Laravel\n";
    exit(1);
}

try {
    echo "1️⃣ Verificando se o CNPayTrait está disponível...\n";
    
    if (!trait_exists('App\Traits\Gateways\CNPayTrait')) {
        echo "   ❌ CNPayTrait não encontrado\n";
        exit(1);
    }
    
    echo "   ✅ CNPayTrait encontrado\n";
    
    echo "\n2️⃣ Verificando método checkCNPayPaymentStatus...\n";
    
    $reflection = new ReflectionClass('App\Traits\Gateways\CNPayTrait');
    $methods = $reflection->getMethods(ReflectionMethod::IS_PUBLIC | ReflectionMethod::IS_STATIC);
    
    $hasCheckMethod = false;
    foreach ($methods as $method) {
        if ($method->getName() === 'checkCNPayPaymentStatus') {
            $hasCheckMethod = true;
            echo "   ✅ Método checkCNPayPaymentStatus encontrado\n";
            echo "   - Parâmetros: " . $method->getNumberOfParameters() . "\n";
            echo "   - Estático: " . ($method->isStatic() ? 'Sim' : 'Não') . "\n";
            break;
        }
    }
    
    if (!$hasCheckMethod) {
        echo "   ❌ Método checkCNPayPaymentStatus não encontrado\n";
        exit(1);
    }
    
    echo "\n3️⃣ Testando verificação de status com ID válido...\n";
    
    // Buscar uma transação CNPay válida no banco
    $transaction = \App\Models\Transaction::where('gateway_name', 'cnpay')
        ->where('status', 0) // pending
        ->first();
    
    if (!$transaction) {
        echo "   ⚠️ Nenhuma transação CNPay pendente encontrada\n";
        echo "   - Criando transação de teste...\n";
        
        // Criar transação de teste
        $transaction = \App\Models\Transaction::create([
            'payment_id' => 'test_' . time(),
            'user_id' => 1, // usuário padrão
            'payment_method' => 'cnpay',
            'gateway_name' => 'cnpay',
            'price' => 10.00,
            'currency' => 'BRL',
            'status' => 0,
            'gateway_response' => json_encode(['test' => true])
        ]);
        
        echo "   ✅ Transação de teste criada: {$transaction->payment_id}\n";
    } else {
        echo "   ✅ Transação encontrada: {$transaction->payment_id}\n";
    }
    
    echo "\n4️⃣ Chamando checkCNPayPaymentStatus...\n";
    
    try {
        $status = \App\Traits\Gateways\CNPayTrait::checkCNPayPaymentStatus($transaction->payment_id);
        
        echo "   ✅ Método executado com sucesso\n";
        echo "   - Resposta: " . json_encode($status, JSON_PRETTY_PRINT) . "\n";
        
        if (isset($status['success'])) {
            echo "   - Success: " . ($status['success'] ? 'true' : 'false') . "\n";
            
            if (isset($status['error'])) {
                echo "   - Error: " . $status['error'] . "\n";
            }
            
            if (isset($status['status'])) {
                echo "   - Status: " . $status['status'] . "\n";
            }
        }
        
    } catch (\Exception $e) {
        echo "   ❌ Erro ao executar checkCNPayPaymentStatus:\n";
        echo "   - Tipo: " . get_class($e) . "\n";
        echo "   - Mensagem: " . $e->getMessage() . "\n";
        echo "   - Linha: " . $e->getLine() . "\n";
        echo "   - Arquivo: " . $e->getFile() . "\n";
        
        echo "\n   🔍 Stack trace:\n";
        echo $e->getTraceAsString() . "\n";
    }
    
    echo "\n5️⃣ Verificando se há problemas de configuração...\n";
    
    try {
        $config = \App\Traits\Gateways\CNPayTrait::getCNPayConfig();
        echo "   ✅ Configurações obtidas:\n";
        echo "   - URI: " . ($config['uri'] ?? 'não configurado') . "\n";
        echo "   - Public Key: " . ($config['public_key'] ? 'configurado' : 'não configurado') . "\n";
        echo "   - Secret Key: " . ($config['secret_key'] ? 'configurado' : 'não configurado') . "\n";
        echo "   - Webhook URL: " . ($config['webhook_url'] ?? 'não configurado') . "\n";
        echo "   - Configurado: " . ($config['is_configured'] ? 'Sim' : 'Não') . "\n";
        
    } catch (\Exception $e) {
        echo "   ❌ Erro ao obter configurações:\n";
        echo "   - " . $e->getMessage() . "\n";
    }
    
    echo "\n6️⃣ Verificando se há problemas de proxy...\n";
    
    try {
        $proxyConfig = \App\Traits\Gateways\CNPayTrait::getProxyConfig();
        echo "   ✅ Configuração de proxy:\n";
        echo "   - HTTP Proxy: " . ($proxyConfig['http_proxy'] ?? 'não configurado') . "\n";
        echo "   - HTTPS Proxy: " . ($proxyConfig['https_proxy'] ?? 'não configurado') . "\n";
        echo "   - Usuário: " . ($proxyConfig['proxy_user'] ?? 'não configurado') . "\n";
        echo "   - Porta: " . ($proxyConfig['proxy_port'] ?? 'não configurado') . "\n";
        
    } catch (\Exception $e) {
        echo "   ❌ Erro ao obter configuração de proxy:\n";
        echo "   - " . $e->getMessage() . "\n";
    }
    
    echo "\n🎯 Diagnóstico do Problema de Status\n";
    echo "====================================\n";
    
    echo "✅ Sistema funcionando:\n";
    echo "- CNPayTrait disponível\n";
    echo "- Método checkCNPayPaymentStatus encontrado\n";
    echo "- Transação disponível para teste\n";
    
    echo "\n🔍 Possíveis problemas:\n";
    echo "1. Configurações do CNPay incorretas\n";
    echo "2. Problema de proxy\n";
    echo "3. API do CNPay não responde\n";
    echo "4. Erro no mapeamento de status\n";
    echo "5. Problema de autenticação com a API\n";
    
    echo "\n📋 Próximos passos:\n";
    echo "1. Verificar se as configurações estão corretas\n";
    echo "2. Testar conexão com a API do CNPay\n";
    echo "3. Verificar se o proxy está funcionando\n";
    echo "4. Testar com uma transação real\n";
    
    echo "\n✨ Teste concluído!\n";
    
} catch (Exception $e) {
    echo "❌ Erro no teste: " . $e->getMessage() . "\n";
    echo "🔧 Stack trace:\n";
    echo $e->getTraceAsString() . "\n";
}
?>
