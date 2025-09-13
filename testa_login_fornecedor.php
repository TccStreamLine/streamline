<?php
session_start();
include_once('config.php');

// Verifica se o formulário foi enviado
if (isset($_POST['submit'])) {
    $email = trim($_POST['email']);
    $senha = $_POST['senha'];

    if (empty($email) || empty($senha)) {
        $_SESSION['erro_login'] = 'E-mail e senha são obrigatórios.';
        header('Location: login_fornecedor.php');
        exit;
    }

    try {
        // Prepara a busca na tabela 'fornecedores' pelo e-mail
        $stmt = $pdo->prepare('SELECT * FROM fornecedores WHERE email = :email');
        $stmt->bindValue(':email', $email);
        $stmt->execute();
        $fornecedor = $stmt->fetch(PDO::FETCH_ASSOC);

        // Verifica se encontrou um fornecedor E se a senha digitada corresponde à senha criptografada no banco
        if ($fornecedor && password_verify($senha, $fornecedor['senha'])) {
            
            // Sucesso! A senha está correta.
            session_regenerate_id(); // Medida de segurança
            
            // Cria a sessão para o fornecedor
            $_SESSION['id_fornecedor'] = $fornecedor['id'];
            $_SESSION['nome_fornecedor'] = $fornecedor['razao_social'];
            
            // Redireciona para um painel futuro do fornecedor.
            // Por enquanto, vamos redirecionar para a página principal.
            header('Location: sistema.php'); 
            exit;

        } else {
            // Se não encontrou ou a senha está errada, define a mensagem de erro
            $_SESSION['erro_login'] = 'E-mail ou senha incorretos.';
            header('Location: login_fornecedor.php');
            exit;
        }
    } catch (PDOException $e) {
        $_SESSION['erro_login'] = 'Ocorreu um erro no sistema. Tente novamente.';
        // Para depuração: error_log($e->getMessage());
        header('Location: login_fornecedor.php');
        exit;
    }
} else {
    // Se alguém acessar o arquivo diretamente, redireciona para a página de login
    header('Location: login_fornecedor.php');
    exit;
}
?>