<?php
/**
 * Script para criar/recriar senha de aprovação para configurações
 * Coloque este arquivo na pasta public/ e acesse via navegador
 */

// Configurações
$novaSenha = 'lucifinho26'; // ALTERE AQUI SUA NOVA SENHA
$host = '45.151.120.11';
$database = 'u378605157_user92844'; // ALTERE AQUI O NOME DO SEU BANCO
$username = 'u378605157_user92844'; // ALTERE AQUI SEU USUÁRIO DO BANCO
$password = '@Dgp265744'; // ALTERE AQUI SUA SENHA DO BANCO

try {
    // Conectar ao banco
    $pdo = new PDO("mysql:host=$host;dbname=$database;charset=utf8", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    echo "<h2>🔐 Criando/Atualizando Senha de Aprovação</h2>";
    echo "<hr>";
    
    // Verificar se a tabela existe
    $stmt = $pdo->query("SHOW TABLES LIKE 'aprove_save_settings'");
    if ($stmt->rowCount() == 0) {
        // Criar a tabela se não existir
        echo "<p>📋 Tabela 'aprove_save_settings' não existe. Criando...</p>";
        
        $sql = "CREATE TABLE `aprove_save_settings` (
            `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
            `approval_password_save` varchar(255) NOT NULL,
            `last_request_at` timestamp NULL DEFAULT NULL,
            `created_at` timestamp NULL DEFAULT NULL,
            `updated_at` timestamp NULL DEFAULT NULL,
            PRIMARY KEY (`id`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci";
        
        $pdo->exec($sql);
        echo "<p>✅ Tabela criada com sucesso!</p>";
    } else {
        echo "<p>📋 Tabela 'aprove_save_settings' já existe.</p>";
        
        // Verificar se as colunas necessárias existem
        $stmt = $pdo->query("SHOW COLUMNS FROM aprove_save_settings");
        $colunas = $stmt->fetchAll(PDO::FETCH_COLUMN);
        
        $colunasNecessarias = ['last_request_at', 'created_at', 'updated_at'];
        $colunasFaltando = array_diff($colunasNecessarias, $colunas);
        
        if (!empty($colunasFaltando)) {
            echo "<p>🔧 Adicionando colunas que estão faltando...</p>";
            
            foreach ($colunasFaltando as $coluna) {
                try {
                    if ($coluna === 'last_request_at') {
                        $pdo->exec("ALTER TABLE aprove_save_settings ADD COLUMN last_request_at timestamp NULL DEFAULT NULL");
                    } elseif ($coluna === 'created_at') {
                        $pdo->exec("ALTER TABLE aprove_save_settings ADD COLUMN created_at timestamp NULL DEFAULT NULL");
                    } elseif ($coluna === 'updated_at') {
                        $pdo->exec("ALTER TABLE aprove_save_settings ADD COLUMN updated_at timestamp NULL DEFAULT NULL");
                    }
                    echo "<p>✅ Coluna '$coluna' adicionada com sucesso!</p>";
                } catch (Exception $e) {
                    echo "<p>⚠️ Erro ao adicionar coluna '$coluna': " . $e->getMessage() . "</p>";
                }
            }
        }
    }
    
    // Verificar se já existe um registro
    $stmt = $pdo->query("SELECT COUNT(*) as total FROM aprove_save_settings");
    $total = $stmt->fetch(PDO::FETCH_ASSOC)['total'];
    
    if ($total > 0) {
        echo "<p>🗑️ Removendo registro existente...</p>";
        $pdo->exec("DELETE FROM aprove_save_settings");
        echo "<p>✅ Registro antigo removido!</p>";
    }
    
    // Criar novo registro
    echo "<p>🔑 Criando nova senha de aprovação...</p>";
    
    $senhaCriptografada = password_hash($novaSenha, PASSWORD_DEFAULT);
    $dataAtual = date('Y-m-d H:i:s');
    
    // Verificar quais colunas existem para fazer o INSERT correto
    $stmt = $pdo->query("SHOW COLUMNS FROM aprove_save_settings");
    $colunas = $stmt->fetchAll(PDO::FETCH_COLUMN);
    
    if (in_array('last_request_at', $colunas) && in_array('created_at', $colunas) && in_array('updated_at', $colunas)) {
        // Todas as colunas existem
        $sql = "INSERT INTO aprove_save_settings (approval_password_save, last_request_at, created_at, updated_at) 
                VALUES (?, ?, ?, ?)";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$senhaCriptografada, $dataAtual, $dataAtual, $dataAtual]);
    } elseif (in_array('approval_password_save', $colunas)) {
        // Apenas a coluna principal existe
        $sql = "INSERT INTO aprove_save_settings (approval_password_save) VALUES (?)";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$senhaCriptografada]);
    } else {
        throw new Exception("Coluna 'approval_password_save' não encontrada na tabela");
    }
    
    echo "<p>✅ Nova senha criada com sucesso!</p>";
    echo "<hr>";
    echo "<h3>📝 Informações da Nova Senha:</h3>";
    echo "<p><strong>Senha:</strong> <code>$novaSenha</code></p>";
    echo "<p><strong>Hash:</strong> <code>$senhaCriptografada</code></p>";
    echo "<p><strong>Data de Criação:</strong> $dataAtual</p>";
    
    echo "<hr>";
    echo "<p>🎯 <strong>IMPORTANTE:</strong> Guarde esta senha em um local seguro!</p>";
    echo "<p>⚠️ <strong>ATENÇÃO:</strong> Delete este arquivo após o uso por segurança!</p>";
    
} catch (PDOException $e) {
    echo "<h2>❌ Erro na Conexão</h2>";
    echo "<p><strong>Erro:</strong> " . $e->getMessage() . "</p>";
    echo "<p>Verifique suas configurações de banco de dados no início do script.</p>";
} catch (Exception $e) {
    echo "<h2>❌ Erro Geral</h2>";
    echo "<p><strong>Erro:</strong> " . $e->getMessage() . "</p>";
}
?>

<style>
body { font-family: Arial, sans-serif; margin: 20px; background: #f5f5f5; }
h2 { color: #333; }
h3 { color: #666; }
p { margin: 10px 0; }
code { background: #f0f0f0; padding: 2px 5px; border-radius: 3px; }
hr { border: none; border-top: 1px solid #ddd; margin: 20px 0; }
</style>
