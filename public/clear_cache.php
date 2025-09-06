<?php

echo "<h1>🧹 Limpeza de Cache - Laravel</h1>";

// Navegar para a pasta raiz do Laravel (subir um nível da pasta public)
$laravelRoot = dirname(__DIR__);
$artisanPath = $laravelRoot . '/artisan';

echo "<p><strong>📁 Pasta atual:</strong> " . __DIR__ . "</p>";
echo "<p><strong>🔍 Procurando Laravel em:</strong> {$laravelRoot}</p>";
echo "<p><strong>🎯 Caminho do artisan:</strong> {$artisanPath}</p>";

// Verificar se é Laravel
if (!file_exists($artisanPath)) {
    echo "<p style='color: red;'>❌ Laravel não encontrado em {$laravelRoot}</p>";
    echo "<p>💡 Verifique se o script está na pasta <code>public</code> do projeto Laravel</p>";
    exit;
}

echo "<p style='color: green;'>✅ Laravel encontrado em {$laravelRoot}</p>";

echo "<h2>📋 Status do Sistema:</h2>";

// Verificar permissões (usando caminhos relativos à raiz do Laravel)
$storagePath = $laravelRoot . '/storage';
$bootstrapPath = $laravelRoot . '/bootstrap/cache';

echo "<p><strong>Storage:</strong> " . (is_writable($storagePath) ? "✅ Gravável" : "❌ Não gravável") . "</p>";
echo "<p><strong>Bootstrap Cache:</strong> " . (is_writable($bootstrapPath) ? "✅ Gravável" : "❌ Não gravável") . "</p>";

echo "<h2>🗑️ Limpando Cache:</h2>";

// 1. Limpar cache de configuração
if (file_exists($bootstrapPath . '/config.php')) {
    if (unlink($bootstrapPath . '/config.php')) {
        echo "<p>✅ Cache de configuração limpo</p>";
    } else {
        echo "<p>❌ Erro ao limpar cache de configuração</p>";
    }
} else {
    echo "<p>ℹ️ Cache de configuração já estava limpo</p>";
}

// 2. Limpar cache de rotas
if (file_exists($bootstrapPath . '/routes.php')) {
    if (unlink($bootstrapPath . '/routes.php')) {
        echo "<p>✅ Cache de rotas limpo</p>";
    } else {
        echo "<p>❌ Erro ao limpar cache de rotas</p>";
    }
} else {
    echo "<p>ℹ️ Cache de rotas já estava limpo</p>";
}

// 3. Limpar cache de views
if (file_exists($storagePath . '/framework/views')) {
    $viewFiles = glob($storagePath . '/framework/views/*');
    $deletedCount = 0;
    
    foreach ($viewFiles as $file) {
        if (is_file($file) && unlink($file)) {
            $deletedCount++;
        }
    }
    
    if ($deletedCount > 0) {
        echo "<p>✅ Cache de views limpo ({$deletedCount} arquivos removidos)</p>";
    } else {
        echo "<p>ℹ️ Cache de views já estava limpo</p>";
    }
} else {
    echo "<p>ℹ️ Pasta de views não encontrada</p>";
}

// 4. Limpar cache de aplicação
if (file_exists($storagePath . '/framework/cache')) {
    $cacheFiles = glob($storagePath . '/framework/cache/*');
    $deletedCount = 0;
    
    foreach ($cacheFiles as $file) {
        if (is_file($file) && unlink($file)) {
            $deletedCount++;
        }
    }
    
    if ($deletedCount > 0) {
        echo "<p>✅ Cache de aplicação limpo ({$deletedCount} arquivos removidos)</p>";
    } else {
        echo "<p>ℹ️ Cache de aplicação já estava limpo</p>";
    }
} else {
    echo "<p>ℹ️ Pasta de cache não encontrada</p>";
}

// 5. Verificar variáveis de ambiente
echo "<h2>🔍 Verificando Variáveis de Ambiente:</h2>";

$envFile = $laravelRoot . '/.env';
if (file_exists($envFile)) {
    echo "<p>✅ Arquivo .env encontrado</p>";
    
    $envContent = file_get_contents($envFile);
    
    $proxyVars = [
        'HTTP_PROXY',
        'HTTPS_PROXY', 
        'PROXY_USER',
        'PROXY_PASS',
        'PROXY_PORT',
        'PROXY_TYPE'
    ];
    
    echo "<p><strong>Variáveis de proxy encontradas:</strong></p><ul>";
    foreach ($proxyVars as $var) {
        if (strpos($envContent, $var) !== false) {
            echo "<li>✅ {$var}</li>";
        } else {
            echo "<li>❌ {$var} - NÃO encontrada</li>";
        }
    }
    echo "</ul>";
    
} else {
    echo "<p>❌ Arquivo .env não encontrado em {$laravelRoot}</p>";
}

// 6. Tentar executar comandos Artisan via PHP
echo "<h2>⚡ Executando Comandos Artisan:</h2>";

try {
    // Mudar para o diretório raiz do Laravel
    chdir($laravelRoot);
    
    // Carregar o Laravel
    require_once 'vendor/autoload.php';
    $app = require_once 'bootstrap/app.php';
    $app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();
    
    echo "<p>✅ Laravel carregado com sucesso</p>";
    
    // Verificar se as variáveis estão sendo lidas
    echo "<p><strong>Variáveis via Laravel:</strong></p><ul>";
    foreach ($proxyVars as $var) {
        $value = getenv($var);
        if ($value !== false) {
            if ($var === 'PROXY_PASS') {
                echo "<li>✅ {$var}: " . str_repeat('*', strlen($value)) . "</li>";
            } else {
                echo "<li>✅ {$var}: {$value}</li>";
            }
        } else {
            echo "<li>❌ {$var}: NÃO definida</li>";
        }
    }
    echo "</ul>";
    
} catch (Exception $e) {
    echo "<p style='color: red;'>❌ Erro ao carregar Laravel: " . $e->getMessage() . "</p>";
}

echo "<h2>🎯 Próximos Passos:</h2>";
echo "<ol>";
echo "<li>Recarregue a página para ver se o cache foi limpo</li>";
echo "<li>Teste novamente a funcionalidade do CNPay</li>";
echo "<li>Verifique os logs para confirmar se o proxy está sendo usado</li>";
echo "</ol>";

echo "<h2>🔧 Teste do Proxy:</h2>";
echo "<p><a href='test_proxy.php' target='_blank'>Clique aqui para testar o proxy</a></p>";

echo "<p><strong>⚠️ IMPORTANTE:</strong> Após usar este script, delete-o por segurança!</p>";
echo "<p><strong>💡 Dica:</strong> Se o problema persistir, pode ser necessário reiniciar o servidor web</p>";

echo "<hr>";
echo "<p><small>Script executado em: " . date('Y-m-d H:i:s') . "</small></p>";
echo "<p><small>Pasta raiz do Laravel: {$laravelRoot}</small></p>";
