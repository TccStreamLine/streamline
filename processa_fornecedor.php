<?php
session_start();
include_once('config.php');

if (empty($_SESSION['id']) || $_SESSION['role'] !== 'ceo') {
    // Redireciona se não for o CEO
    header('Location: sistema.php');
    exit;
}

$usuario_id = $_SESSION['id'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Campos que vêm do formulário com o design do Figma
    $nome = trim($_POST['nome'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $cargo = trim($_POST['cargo'] ?? '');
    $telefone = trim($_POST['telefone'] ?? '');
    $senha = trim($_POST['senha'] ?? '');
    $acao = 'cadastrar'; // Ação é sempre cadastrar nesta tela

    // Validações essenciais
    if (empty($nome) || empty($email) || empty($senha)) {
        $_SESSION['msg_erro_funcionario'] = "Nome, E-mail e Senha são obrigatórios.";
        header('Location: funcionario_formulario.php');
        exit;
    }

    if (strlen($senha) < 6) {
        $_SESSION['msg_erro_funcionario'] = "A senha deve ter no mínimo 6 caracteres.";
        header('Location: funcionario_formulario.php');
        exit;
    }

    try {

        $check_sql = "SELECT id FROM funcionarios WHERE email = ? AND usuario_id = ? AND status = 'ativo'";
        $check_stmt = $pdo->prepare($check_sql);
        $check_stmt->execute([$email, $usuario_id]);
        if ($check_stmt->fetch()) {
            $_SESSION['msg_erro_funcionario'] = "Este e-mail já está em uso por outro funcionário ativo.";
            header('Location: funcionario_formulario.php');
            exit;
        }
        

        $senha_hash = password_hash($senha, PASSWORD_DEFAULT);
        $sql = "INSERT INTO funcionarios (usuario_id, nome, email, cargo, telefone, senha, status) VALUES (?, ?, ?, ?, ?, ?, 'ativo')";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$usuario_id, $nome, $email, $cargo, $telefone, $senha_hash]);
        
        $_SESSION['msg_sucesso'] = "Funcionário cadastrado com sucesso!";
  
        header('Location: funcionarios.php');
        exit;

    } catch (PDOException $e) {
        $_SESSION['msg_erro_funcionario'] = "Ocorreu um erro no banco de dados. Tente novamente.";
        header('Location: funcionario_formulario.php');
        exit;
    }
}


header('Location: funcionario_formulario.php');
exit;
?>