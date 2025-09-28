<?php
session_start();
include_once('config.php');

if (!isset($_POST['submit'])) {
    header('Location: login.php');
    exit;
}

// Limpa completamente qualquer sessão anterior
session_unset();

$tipo_acesso = $_POST['tipo_acesso'] ?? '';
$senha = $_POST['senha'] ?? '';

try {
    if ($tipo_acesso === 'ceo') {
        $cnpj = trim($_POST['cnpj']);
        if (empty($cnpj) || empty($senha)) {
            $_SESSION['erro_login'] = 'CNPJ e senha são obrigatórios.';
            header('Location: login.php'); exit;
        }

        $stmt = $pdo->prepare('SELECT * FROM usuarios WHERE cnpj = :cnpj');
        $stmt->execute([':cnpj' => $cnpj]);
        $usuario = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($usuario && password_verify($senha, $usuario['senha'])) {
            session_regenerate_id();
            $_SESSION['id'] = $usuario['id'];
            $_SESSION['nome_empresa'] = $usuario['nome_empresa'];
            $_SESSION['role'] = 'ceo';
            header('Location: sistema.php'); exit;
        } else {
            $_SESSION['erro_login'] = 'CNPJ ou senha incorretos.';
            header('Location: login.php'); exit;
        }

    } elseif ($tipo_acesso === 'funcionario') {
        $email = trim($_POST['email']);
        if (empty($email) || empty($senha)) {
            $_SESSION['erro_login'] = 'E-mail e senha são obrigatórios.';
            header('Location: login.php'); exit;
        }
        
        $stmt = $pdo->prepare('SELECT * FROM funcionarios WHERE email = :email AND status = "ativo"');
        $stmt->execute([':email' => $email]);
        $funcionario = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($funcionario && password_verify($senha, $funcionario['senha'])) {
            $company_stmt = $pdo->prepare('SELECT id, nome_empresa FROM usuarios WHERE id = :id');
            $company_stmt->execute([':id' => $funcionario['usuario_id']]);
            $empresa = $company_stmt->fetch(PDO::FETCH_ASSOC);
            
            session_regenerate_id();
            $_SESSION['id'] = $empresa['id'];
            $_SESSION['nome_empresa'] = $empresa['nome_empresa'];
            $_SESSION['funcionario_id'] = $funcionario['id'];
            $_SESSION['funcionario_nome'] = $funcionario['nome'];
            $_SESSION['role'] = 'funcionario';
            header('Location: sistema.php'); exit;
        } else {
            $_SESSION['erro_login'] = 'E-mail de funcionário ou senha incorretos.';
            header('Location: login.php'); exit;
        }

    } elseif ($tipo_acesso === 'fornecedor') {
        $email = trim($_POST['email']);
        if (empty($email) || empty($senha)) {
            $_SESSION['erro_login'] = 'E-mail e senha são obrigatórios.';
            header('Location: login.php'); exit;
        }

        $stmt = $pdo->prepare('SELECT * FROM fornecedores WHERE email = :email AND status = "ativo"');
        $stmt->execute([':email' => $email]);
        $fornecedor = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($fornecedor && password_verify($senha, $fornecedor['senha'])) {
            session_regenerate_id();
            $_SESSION['id_fornecedor'] = $fornecedor['id'];
            $_SESSION['nome_fornecedor'] = $fornecedor['razao_social'];
            header('Location: gerenciar_fornecimento.php'); exit;
        } else {
            $_SESSION['erro_login'] = 'E-mail de fornecedor ou senha incorretos.';
            header('Location: login.php'); exit;
        }

    } else {
        $_SESSION['erro_login'] = 'Tipo de acesso inválido.';
        header('Location: login.php'); exit;
    }
} catch (PDOException $e) {
    $_SESSION['erro_login'] = 'Ocorreu um erro no sistema. Tente novamente mais tarde.';
    header('Location: login.php'); exit;
}
?>