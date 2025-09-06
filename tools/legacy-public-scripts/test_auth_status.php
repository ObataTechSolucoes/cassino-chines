<?php
/**
 * Teste de Status de Autenticação JWT
 * Para verificar se o usuário está autenticado
 */

echo "🔐 Teste de Status de Autenticação JWT\n";
echo "=====================================\n\n";

// Verificar se o Laravel está carregado
if (!function_exists('app')) {
    echo "❌ Laravel não está carregado\n";
    echo "🔧 Este arquivo deve ser executado através do Laravel\n";
    exit(1);
}

try {
    echo "1️⃣ Verificando autenticação da API...\n";
    
    $apiAuth = auth('api')->check();
    $apiUser = auth('api')->user();
    
    echo "   - API Auth Check: " . ($apiAuth ? '✅ Autenticado' : '❌ Não autenticado') . "\n";
    
    if ($apiAuth && $apiUser) {
        echo "   - Usuário API ID: " . $apiUser->id . "\n";
        echo "   - Usuário API Nome: " . ($apiUser->name ?? 'N/A') . "\n";
        echo "   - Usuário API Email: " . ($apiUser->email ?? 'N/A') . "\n";
    }
    
    echo "\n2️⃣ Verificando autenticação web...\n";
    
    $webAuth = auth()->check();
    $webUser = auth()->user();
    
    echo "   - Web Auth Check: " . ($webAuth ? '✅ Autenticado' : '❌ Não autenticado') . "\n";
    
    if ($webAuth && $webUser) {
        echo "   - Usuário Web ID: " . $webUser->id . "\n";
        echo "   - Usuário Web Nome: " . ($webUser->name ?? 'N/A') . "\n";
        echo "   - Usuário Web Email: " . ($webUser->email ?? 'N/A') . "\n";
    }
    
    echo "\n3️⃣ Verificando token JWT...\n";
    
    $request = request();
    $token = $request->bearerToken();
    
    if ($token) {
        echo "   - Token JWT: " . substr($token, 0, 20) . "... (truncado)\n";
        echo "   - Token Length: " . strlen($token) . " caracteres\n";
        
        // Tentar decodificar o token
        try {
            if (class_exists('JWTAuth')) {
                $payload = JWTAuth::getJWTProvider()->decode($token);
                echo "   - Token válido ✅\n";
                echo "   - Payload: " . json_encode($payload) . "\n";
            } else {
                echo "   - JWTAuth não disponível\n";
            }
        } catch (Exception $e) {
            echo "   - Token inválido ❌: " . $e->getMessage() . "\n";
        }
    } else {
        echo "   - Token JWT: ❌ Não fornecido\n";
    }
    
    echo "\n4️⃣ Verificando headers da requisição...\n";
    
    $headers = $request->headers->all();
    $authHeader = $request->header('Authorization');
    
    echo "   - Authorization Header: " . ($authHeader ?: '❌ Não encontrado') . "\n";
    echo "   - Accept Header: " . $request->header('Accept', '❌ Não encontrado') . "\n";
    echo "   - Content-Type Header: " . $request->header('Content-Type', '❌ Não encontrado') . "\n";
    echo "   - User-Agent: " . $request->header('User-Agent', '❌ Não encontrado') . "\n";
    
    echo "\n5️⃣ Verificando sessão...\n";
    
    $sessionId = session()->getId();
    echo "   - Session ID: " . ($sessionId ?: '❌ Não encontrado') . "\n";
    echo "   - Session Status: " . (session()->isStarted() ? '✅ Iniciada' : '❌ Não iniciada') . "\n";
    
    echo "\n🎯 Diagnóstico da Autenticação\n";
    echo "==============================\n";
    
    if ($apiAuth) {
        echo "✅ Usuário está autenticado na API\n";
        echo "✅ Pode acessar rotas protegidas\n";
        echo "✅ Depósito deve funcionar\n";
    } else {
        echo "❌ Usuário NÃO está autenticado na API\n";
        echo "❌ NÃO pode acessar rotas protegidas\n";
        echo "❌ Depósito NÃO funcionará\n";
        echo "\n🔧 Soluções:\n";
        echo "1. Faça login na aplicação\n";
        echo "2. Verifique se o token JWT está sendo enviado\n";
        echo "3. Verifique se o token não expirou\n";
        echo "4. Verifique se o token está no header Authorization\n";
    }
    
    echo "\n📋 Para resolver problemas de autenticação:\n";
    echo "1. Verifique se está logado na aplicação\n";
    echo "2. Verifique se o token JWT está sendo enviado\n";
    echo "3. Verifique se o token não expirou\n";
    echo "4. Verifique se o JavaScript está enviando o token\n";
    echo "5. Verifique se o middleware auth.jwt está funcionando\n";
    
    echo "\n🔍 Comandos úteis:\n";
    echo "- Ver logs de autenticação: tail -f storage/logs/laravel.log | grep -i auth\n";
    echo "- Ver logs de JWT: tail -f storage/logs/laravel.log | grep -i jwt\n";
    echo "- Ver logs de depósito: tail -f storage/logs/laravel.log | grep -i deposit\n";
    
    echo "\n✨ Teste de autenticação concluído!\n";
    
} catch (Exception $e) {
    echo "❌ Erro ao verificar autenticação: " . $e->getMessage() . "\n";
    echo "🔧 Stack trace:\n";
    echo $e->getTraceAsString() . "\n";
}
?>
