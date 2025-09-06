<?php
// Configuração do banco de dados
$host = 'localhost';
$dbname = 'u378605157_bet_leo'; // Altere para o nome do seu banco
$username = 'u378605157_bet_leo'; // Altere para seu usuário do banco
$password = '@Dgp265744'; // Altere para sua senha do banco

// Conectar ao banco
try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch(PDOException $e) {
    die("Erro na conexão: " . $e->getMessage());
}

$message = '';
$error = '';

// Processar formulário
if ($_POST) {
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $cpf = trim($_POST['cpf'] ?? '');
    $phone = trim($_POST['phone'] ?? '');
    
    // Validações básicas
    if (empty($name) || empty($email) || empty($password) || empty($cpf)) {
        $error = "Todos os campos obrigatórios devem ser preenchidos!";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "Email inválido!";
    } elseif (strlen($password) < 6) {
        $error = "A senha deve ter pelo menos 6 caracteres!";
    } else {
        try {
            // Verificar se email já existe
            $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ?");
            $stmt->execute([$email]);
            if ($stmt->fetch()) {
                $error = "Este email já está em uso!";
            } else {
                // Verificar se CPF já existe
                $stmt = $pdo->prepare("SELECT id FROM users WHERE cpf = ?");
                $stmt->execute([$cpf]);
                if ($stmt->fetch()) {
                    $error = "Este CPF já está em uso!";
                } else {
                    // Criar o usuário
                    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
                    $inviterCode = substr(str_shuffle('ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789'), 0, 8);
                    
                    $sql = "INSERT INTO users (name, email, password, cpf, phone, role_id, is_admin, email_verified_at, inviter_code, created_at, updated_at) 
                            VALUES (?, ?, ?, ?, ?, 0, 1, NOW(), ?, NOW(), NOW())";
                    
                    $stmt = $pdo->prepare($sql);
                    $stmt->execute([$name, $email, $hashedPassword, $cpf, $phone, $inviterCode]);
                    
                    $userId = $pdo->lastInsertId();
                    
                    // Criar carteira para o usuário
                    try {
                        $walletSql = "INSERT INTO wallets (user_id, currency, symbol, active, created_at, updated_at) 
                                     VALUES (?, 'BRL', 'R$', 1, NOW(), NOW())";
                        $walletStmt = $pdo->prepare($walletSql);
                        $walletStmt->execute([$userId]);
                    } catch (Exception $e) {
                        // Se falhar ao criar carteira, não é crítico
                    }
                    
                    $message = "Administrador criado com sucesso! ID: $userId";
                    
                    // Limpar formulário
                    $_POST = array();
                }
            }
        } catch (Exception $e) {
            $error = "Erro ao criar usuário: " . $e->getMessage();
        }
    }
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Criar Administrador - Sistema</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: Arial, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }
        
        .container {
            background: white;
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.2);
            padding: 30px;
            width: 100%;
            max-width: 500px;
        }
        
        .header {
            text-align: center;
            margin-bottom: 30px;
            color: #333;
        }
        
        .header h1 {
            color: #667eea;
            margin-bottom: 10px;
        }
        
        .form-group {
            margin-bottom: 20px;
        }
        
        label {
            display: block;
            margin-bottom: 5px;
            font-weight: bold;
            color: #555;
        }
        
        input, select {
            width: 100%;
            padding: 12px;
            border: 2px solid #ddd;
            border-radius: 8px;
            font-size: 16px;
            transition: border-color 0.3s;
        }
        
        input:focus, select:focus {
            outline: none;
            border-color: #667eea;
        }
        
        .btn {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 15px 30px;
            border: none;
            border-radius: 8px;
            font-size: 16px;
            font-weight: bold;
            cursor: pointer;
            width: 100%;
            transition: transform 0.2s;
        }
        
        .btn:hover {
            transform: translateY(-2px);
        }
        
        .alert {
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 20px;
            font-weight: bold;
        }
        
        .alert-success {
            background: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }
        
        .alert-danger {
            background: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }
        
        .row {
            display: flex;
            gap: 15px;
        }
        
        .col {
            flex: 1;
        }
        
        .info {
            background: #e7f3ff;
            border: 1px solid #b3d9ff;
            color: #004085;
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 20px;
            font-size: 14px;
        }
        
        .info h3 {
            margin-bottom: 10px;
            color: #004085;
        }
        
        .info ul {
            margin-left: 20px;
        }
        
        .info li {
            margin-bottom: 5px;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>🔐 Criar Administrador</h1>
            <p>Sistema de Gerenciamento</p>
        </div>
        
        <?php if ($message): ?>
            <div class="alert alert-success">✅ <?php echo htmlspecialchars($message); ?></div>
        <?php endif; ?>
        
        <?php if ($error): ?>
            <div class="alert alert-danger">❌ <?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>
        
        <div class="info">
            <h3>📋 Informações Importantes:</h3>
            <ul>
                <li>Este usuário será criado como <strong>Administrador</strong></li>
                <li>Uma carteira será criada automaticamente</li>
                <li>O email será marcado como verificado</li>
                <li>Um código de convite será gerado automaticamente</li>
            </ul>
        </div>
        
        <form method="POST">
            <div class="form-group">
                <label for="name">Nome Completo *</label>
                <input type="text" id="name" name="name" value="<?php echo htmlspecialchars($_POST['name'] ?? ''); ?>" required>
            </div>
            
            <div class="form-group">
                <label for="email">Email *</label>
                <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($_POST['email'] ?? ''); ?>" required>
            </div>
            
            <div class="row">
                <div class="col">
                    <div class="form-group">
                        <label for="cpf">CPF *</label>
                        <input type="text" id="cpf" name="cpf" value="<?php echo htmlspecialchars($_POST['cpf'] ?? ''); ?>" placeholder="000.000.000-00" required>
                    </div>
                </div>
                <div class="col">
                    <div class="form-group">
                        <label for="phone">Telefone</label>
                        <input type="text" id="phone" name="phone" value="<?php echo htmlspecialchars($_POST['phone'] ?? ''); ?>" placeholder="(00) 00000-0000">
                    </div>
                </div>
            </div>
            
            <div class="form-group">
                <label for="password">Senha *</label>
                <input type="password" id="password" name="password" required minlength="6">
            </div>
            
            <button type="submit" class="btn">🚀 Criar Administrador</button>
        </form>
        
        <div style="text-align: center; margin-top: 20px; color: #666; font-size: 14px;">
            <p>⚠️ <strong>IMPORTANTE:</strong> Após criar o admin, remova este arquivo por segurança!</p>
        </div>
    </div>
    
    <script>
        // Máscara para CPF
        document.getElementById('cpf').addEventListener('input', function(e) {
            let value = e.target.value.replace(/\D/g, '');
            if (value.length <= 11) {
                value = value.replace(/(\d{3})(\d{3})(\d{3})(\d{2})/, '$1.$2.$3-$4');
                e.target.value = value;
            }
        });
        
        // Máscara para telefone
        document.getElementById('phone').addEventListener('input', function(e) {
            let value = e.target.value.replace(/\D/g, '');
            if (value.length <= 11) {
                if (value.length <= 10) {
                    value = value.replace(/(\d{2})(\d{4})(\d{4})/, '($1) $2-$3');
                } else {
                    value = value.replace(/(\d{2})(\d{5})(\d{4})/, '($1) $2-$3');
                }
                e.target.value = value;
            }
        });
    </script>
</body>
</html>
