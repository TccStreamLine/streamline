<?php
session_start();
include_once('config.php');
if (isset($_POST['submit'])) {
    $email = trim($_POST['email']);
    $senha = $_POST['senha'];
    if (empty($email) || empty($senha)) {
        $_SESSION['erro_login'] = 'E-mail e senha são obrigatórios.';
        header('Location: login_fornecedor.php');
        exit;
    }
    try {
        $stmt = $pdo->prepare('SELECT * FROM fornecedores WHERE email = :email');
        $stmt->bindValue(':email', $email);
        $stmt->execute();
        $fornecedor = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($fornecedor && password_verify($senha, $fornecedor['senha'])) {
            session_regenerate_id();
            $_SESSION['id_fornecedor'] = $fornecedor['id'];
            $_SESSION['nome_fornecedor'] = $fornecedor['razao_social'];
            header('Location: sistema.php');
            exit;
        } else {
            $_SESSION['erro_login'] = 'E-mail ou senha incorretos.';
            header('Location: login_fornecedor.php');
            exit;
        }
    } catch (PDOException $e) {
        $_SESSION['erro_login'] = 'Ocorreu um erro no sistema. Tente novamente.';
        header('Location: login_fornecedor.php');
        exit;
    }
} else {
    header('Location: login_fornecedor.php');
    exit;
}
?>