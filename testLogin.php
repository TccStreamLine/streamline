<?php
session_start();
unset($_SESSION['erro_login']);
include_once('config.php');
if (isset($_POST['submit'])) {
    $acesso = trim($_POST['acesso']);
    $cnpj   = trim($_POST['cnpj']);
    $senha  = $_POST['senha'];
    if (empty($acesso) || empty($cnpj) || empty($senha)) {
        $_SESSION['erro_login'] = 'Todos os campos são obrigatórios.';
        header('Location: login.php');
        exit;
    }
    try {
        $stmt = $pdo->prepare('SELECT * FROM usuarios WHERE nome_empresa = :empresa AND cnpj = :cnpj');
        $stmt->bindValue(':empresa', $acesso);
        $stmt->bindValue(':cnpj', $cnpj);
        $stmt->execute();
        $usuario = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($usuario && password_verify($senha, $usuario['senha'])) {
            $_SESSION['id'] = $usuario['id'];
            $_SESSION['nome_empresa'] = $usuario['nome_empresa'];
            header('Location: sistema.php');
            exit;
        } else {
            $_SESSION['erro_login'] = 'Nome da empresa, CNPJ ou senha incorretos.';
            header('Location: login.php');
            exit;
        }
    } catch (PDOException $e) {
        $_SESSION['erro_login'] = 'Ocorreu um erro no sistema. Tente novamente mais tarde.';
        header('Location: login.php');
        exit;
    }
} else {
    header('Location: login.php');
    exit;
}