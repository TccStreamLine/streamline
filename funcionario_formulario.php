<?php
session_start();
include_once('config.php');

if (empty($_SESSION['id']) || $_SESSION['role'] !== 'ceo') {
    header('Location: sistema.php');
    exit;
}

// Variáveis da página
$pagina_ativa = 'funcionarios'; 
$titulo_header = 'Funcionários > Cadastro de Funcionário';
$nome_empresa = $_SESSION['nome_empresa'] ?? 'Sua empresa';
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cadastro de Funcionários - Streamline</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="css/sistema.css">
    <style>
        .form-container-figma { background-color: #fff; padding: 2.5rem 3rem; border-radius: 16px; box-shadow: var(--card-shadow); border: 1px solid #F3F4F6; width: 100%; margin-top: 1.5rem; }
        .form-header { text-align: left; margin-bottom: 2.5rem; }
        .form-header h2 { font-size: 1.8rem; font-weight: 700; color: #4C1D95; margin: 0 0 0.5rem 0; }
        .form-header p { font-size: 1.1rem; color: #6B7280; margin: 0; }
        .form-grid { display: grid; grid-template-columns: repeat(2, 1fr); gap: 1.5rem; margin-bottom: 3rem; }
        .input-group { background-color: #F8F7FF; border: 1px solid #E5E7EB; border-radius: 12px; display: flex; align-items: center; padding: 0.75rem 1.5rem; transition: all 0.2s ease; }
        .input-group:focus-within { border-color: #6D28D9; background-color: #fff; box-shadow: 0 0 0 3px rgba(109, 40, 217, 0.2); }
        .input-group i { font-size: 1.2rem; color: var(--accent-color); margin-right: 1rem; }
        .input-group input { width: 100%; border: none; background: none; outline: none; padding: 0.75rem 0; font-size: 1rem; font-weight: 500; color: #374151; font-family: 'Inter', sans-serif; }
        .input-group input::placeholder { color: #374151; font-weight: 500; }
        .form-actions { text-align: center; }
        .btn-submit-custom { background: linear-gradient(90deg, #6D28D9, #8B5CF6); color: white; font-weight: 600; font-size: 1rem; border: none; border-radius: 10px; padding: 1.1rem 0; width: 100%; max-width: 450px; cursor: pointer; transition: all 0.2s ease; box-shadow: 0 4px 12px rgba(109, 40, 217, 0.2); }
        .btn-submit-custom:hover { transform: translateY(-2px); box-shadow: 0 6px 16px rgba(109, 40, 217, 0.3); }
        

        .message-container { margin-bottom: 1.5rem; }
        .alert { padding: 1rem; border-radius: 8px; color: white; font-weight: 500; text-align: center; }
        .alert-success { background-color: #10B981; }
        .alert-danger { background-color: #EF4444; }

        @media (max-width: 768px) { .form-grid { grid-template-columns: 1fr; } }
    </style>
</head>
<body>
    <?php include 'sidebar.php'; ?>

    <main class="main-content">
        <?php include 'header.php'; ?>

        <div class="form-container-figma">
            <div class="form-header">
                <h2>CADASTRO DE FUNCIONÁRIOS</h2>
                <p>Olá, <?= htmlspecialchars($nome_empresa) ?>! Cadastre seus usuários aqui.</p>
            </div>

            <div class="message-container">
                <?php if (isset($_SESSION['msg_sucesso_funcionario'])): ?>
                    <div class="alert alert-success"><?= $_SESSION['msg_sucesso_funcionario']; unset($_SESSION['msg_sucesso_funcionario']); ?></div>
                <?php endif; ?>
                <?php if (isset($_SESSION['msg_erro_funcionario'])): ?>
                    <div class="alert alert-danger"><?= $_SESSION['msg_erro_funcionario']; unset($_SESSION['msg_erro_funcionario']); ?></div>
                <?php endif; ?>
            </div>

            <form action="processa_config_funcionarios.php" method="POST">
                <div class="form-grid">
                    <div class="input-group">
                        <i class="fas fa-users"></i>
                        <input type="number" name="quantidade_funcionarios" placeholder="Quantidade de Funcionários" required min="1">
                    </div>
                    <div class="input-group">
                        <i class="fas fa-envelope"></i>
                        <input type="email" name="email_ceo" placeholder="Email do CEO" required>
                    </div>
                    <div class="input-group">
                        <i class="fas fa-id-card"></i>
                        <input type="text" name="cnpj" placeholder="Confirme CNPJ da empresa" required>
                    </div>
                    <div class="input-group">
                        <i class="fas fa-key"></i>
                        <input type="password" name="senha_funcionarios" placeholder="Crie a senha dos seus funcionários" required>
                    </div>
                </div>
                <div class="form-actions">
                    <button type="submit" name="submit" class="btn-submit-custom">CADASTRAR AQUI</button>
                </div>
            </form>
        </div>
    </main>
</body>
</html>