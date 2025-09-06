<?php
/**
 * Página para criar usuário administrador
 * 
 * Uso: Acesse via navegador: http://seudominio.com/create_admin.php
 * 
 * ⚠️ IMPORTANTE: Remova este arquivo após criar o admin por segurança!
 */

// Incluir o autoloader do Laravel
require_once __DIR__ . '/../vendor/autoload.php';

// Carregar o Laravel
$app = require_once __DIR__ . '/../bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

// Verificar se já existe um admin (segurança)
$existingAdmin = \App\Models\User::whereHas('roles', function($query) {
    $query->where('name', 'admin');
})->first();

if ($existingAdmin) {
    die('
    <!DOCTYPE html>
    <html lang="pt-BR">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Admin Já Existe</title>
        <style>
            body { font-family: Arial, sans-serif; background: #f5f5f5; margin: 0; padding: 20px; }
            .container { max-width: 600px; margin: 50px auto; background: white; padding: 30px; border-radius: 10px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
            .alert { background: #d4edda; color: #155724; padding: 15px; border-radius: 5px; border: 1px solid #c3e6cb; }
            .btn { background: #007bff; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px; display: inline-block; margin-top: 15px; }
            .btn:hover { background: #0056b3; }
        </style>
    </head>
    <body>
        <div class="container">
            <h1>🚫 Admin Já Existe</h1>
            <div class="alert">
                <strong>Usuário admin já foi criado!</strong><br>
                Email: ' . $existingAdmin->email . '<br>
                Nome: ' . $existingAdmin->name . '<br>
                                 Criado em: ' . (is_object($existingAdmin->created_at) ? $existingAdmin->created_at->format('d/m/Y H:i:s') : $existingAdmin->created_at) . '
            </div>
            <p>Por segurança, remova este arquivo <code>create_admin.php</code> do servidor.</p>
            <a href="/" class="btn">Voltar ao Site</a>
        </div>
    </body>
    </html>
    ');
}

// Processar formulário
$message = '';
$messageType = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $name = trim($_POST['name'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';
        $confirmPassword = $_POST['confirm_password'] ?? '';
        
        // Validações
        if (empty($name) || empty($email) || empty($password)) {
            throw new Exception('Todos os campos são obrigatórios!');
        }
        
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            throw new Exception('Email inválido!');
        }
        
        if (strlen($password) < 6) {
            throw new Exception('A senha deve ter pelo menos 6 caracteres!');
        }
        
        if ($password !== $confirmPassword) {
            throw new Exception('As senhas não coincidem!');
        }
        
        // Verificar se o email já existe
        $existingUser = \App\Models\User::where('email', $email)->first();
        if ($existingUser) {
            throw new Exception('Este email já está cadastrado no sistema!');
        }
        
        // Verificar se o role admin existe
        $adminRole = \Spatie\Permission\Models\Role::where('name', 'admin')->first();
        
        if (!$adminRole) {
            // Criar role admin se não existir
            $adminRole = \Spatie\Permission\Models\Role::create([
                'name' => 'admin',
                'guard_name' => 'web'
            ]);
        }
        
        // Criar usuário
        $user = \App\Models\User::create([
            'name' => $name,
            'email' => $email,
            'password' => \Illuminate\Support\Facades\Hash::make($password),
            'role_id' => 1, // ID padrão para admin
            'status' => 'active',
            'email_verified_at' => now()
        ]);
        
        // Atribuir role admin
        $user->assignRole('admin');
        
        $message = '✅ Usuário admin criado com sucesso!';
        $messageType = 'success';
        
        // Exibir credenciais
        $credentials = [
            'ID' => $user->id,
            'Nome' => $user->name,
            'Email' => $user->email,
            'Role' => 'admin',
            'Status' => $user->status,
                         'Criado em' => (is_object($user->created_at) ? $user->created_at->format('d/m/Y H:i:s') : $user->created_at)
        ];
        
    } catch (Exception $e) {
        $message = '❌ Erro: ' . $e->getMessage();
        $messageType = 'error';
    }
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Criar Usuário Admin</title>
    <style>
        * { box-sizing: border-box; }
        
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            margin: 0;
            padding: 20px;
            min-height: 100vh;
        }
        
        .container {
            max-width: 600px;
            margin: 50px auto;
            background: white;
            padding: 40px;
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.2);
        }
        
        h1 {
            text-align: center;
            color: #333;
            margin-bottom: 30px;
            font-size: 2.5em;
        }
        
        .form-group {
            margin-bottom: 20px;
        }
        
        label {
            display: block;
            margin-bottom: 8px;
            font-weight: 600;
            color: #555;
        }
        
        input[type="text"],
        input[type="email"],
        input[type="password"] {
            width: 100%;
            padding: 12px 15px;
            border: 2px solid #e1e5e9;
            border-radius: 8px;
            font-size: 16px;
            transition: border-color 0.3s ease;
        }
        
        input[type="text"]:focus,
        input[type="email"]:focus,
        input[type="password"]:focus {
            outline: none;
            border-color: #667eea;
            box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
        }
        
        .btn {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 15px 30px;
            border: none;
            border-radius: 8px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            width: 100%;
            transition: transform 0.2s ease;
        }
        
        .btn:hover {
            transform: translateY(-2px);
        }
        
        .alert {
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 20px;
            font-weight: 500;
        }
        
        .alert-success {
            background: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }
        
        .alert-error {
            background: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }
        
        .credentials {
            background: #f8f9fa;
            padding: 20px;
            border-radius: 8px;
            margin-top: 20px;
            border-left: 4px solid #28a745;
        }
        
        .credentials h3 {
            margin-top: 0;
            color: #28a745;
        }
        
        .credential-item {
            display: flex;
            justify-content: space-between;
            padding: 8px 0;
            border-bottom: 1px solid #e9ecef;
        }
        
        .credential-item:last-child {
            border-bottom: none;
        }
        
        .credential-label {
            font-weight: 600;
            color: #495057;
        }
        
        .credential-value {
            color: #6c757d;
            font-family: monospace;
        }
        
        .warning {
            background: #fff3cd;
            color: #856404;
            padding: 15px;
            border-radius: 8px;
            margin-top: 20px;
            border: 1px solid #ffeaa7;
        }
        
        .warning strong {
            color: #d63031;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>👑 Criar Usuário Admin</h1>
        
        <?php if ($message): ?>
            <div class="alert alert-<?php echo $messageType; ?>">
                <?php echo $message; ?>
            </div>
        <?php endif; ?>
        
        <?php if (isset($credentials) && $messageType === 'success'): ?>
            <div class="credentials">
                <h3>📋 Informações do Usuário Criado</h3>
                <?php foreach ($credentials as $label => $value): ?>
                    <div class="credential-item">
                        <span class="credential-label"><?php echo $label; ?>:</span>
                        <span class="credential-value"><?php echo $value; ?></span>
                    </div>
                <?php endforeach; ?>
            </div>
            
            <div class="credentials">
                <h3>🔐 Credenciais de Acesso</h3>
                <div class="credential-item">
                    <span class="credential-label">Email:</span>
                    <span class="credential-value"><?php echo $email; ?></span>
                </div>
                <div class="credential-item">
                    <span class="credential-label">Senha:</span>
                    <span class="credential-value"><?php echo $password; ?></span>
                </div>
            </div>
            
            <div class="warning">
                <strong>⚠️ IMPORTANTE:</strong><br>
                1. Guarde essas credenciais em local seguro<br>
                2. Remova este arquivo <code>create_admin.php</code> do servidor<br>
                3. Altere a senha após o primeiro login por segurança
            </div>
            
        <?php else: ?>
            <form method="POST" action="">
                <div class="form-group">
                    <label for="name">Nome Completo *</label>
                    <input type="text" id="name" name="name" required 
                           placeholder="Digite o nome completo do administrador"
                           value="<?php echo htmlspecialchars($_POST['name'] ?? ''); ?>">
                </div>
                
                <div class="form-group">
                    <label for="email">Email *</label>
                    <input type="email" id="email" name="email" required 
                           placeholder="Digite o email do administrador"
                           value="<?php echo htmlspecialchars($_POST['email'] ?? ''); ?>">
                </div>
                
                <div class="form-group">
                    <label for="password">Senha *</label>
                    <input type="password" id="password" name="password" required 
                           placeholder="Digite a senha (mínimo 6 caracteres)"
                           minlength="6">
                </div>
                
                <div class="form-group">
                    <label for="confirm_password">Confirmar Senha *</label>
                    <input type="password" id="confirm_password" name="confirm_password" required 
                           placeholder="Confirme a senha"
                           minlength="6">
                </div>
                
                <button type="submit" class="btn">🚀 Criar Usuário Admin</button>
            </form>
            
            <div class="warning" style="margin-top: 30px;">
                <strong>🔒 Segurança:</strong><br>
                • Este formulário cria um usuário com acesso total ao sistema<br>
                • Use apenas em ambiente controlado<br>
                • Remova este arquivo após criar o admin
            </div>
        <?php endif; ?>
    </div>
    
    <script>
        // Validação de senha em tempo real
        document.getElementById('confirm_password').addEventListener('input', function() {
            const password = document.getElementById('password').value;
            const confirmPassword = this.value;
            
            if (password !== confirmPassword) {
                this.setCustomValidity('As senhas não coincidem');
            } else {
                this.setCustomValidity('');
            }
        });
        
        // Validação de email
        document.getElementById('email').addEventListener('input', function() {
            const email = this.value;
            const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            
            if (!emailRegex.test(email)) {
                this.setCustomValidity('Digite um email válido');
            } else {
                this.setCustomValidity('');
            }
        });
    </script>
</body>
</html>
