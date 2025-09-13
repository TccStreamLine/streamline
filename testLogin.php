<?php
session_start();
// Garante que a sessão de erro seja limpa no início
unset($_SESSION['erro_login']);

// Inclui a configuração do banco de dados
include_once('config.php');

// Verifica se o formulário foi enviado
if (isset($_POST['submit'])) {
    
    // 1. Limpa e obtém os dados do formulário
    $acesso = trim($_POST['acesso']); // Nome da empresa
    $cnpj   = trim($_POST['cnpj']);
    $senha  = $_POST['senha'];

    // Validação básica para não fazer consultas desnecessárias
    if (empty($acesso) || empty($cnpj) || empty($senha)) {
        $_SESSION['erro_login'] = 'Todos os campos são obrigatórios.';
        header('Location: login.php');
        exit;
    }

    try {
        // 2. Prepara a consulta para buscar pelo NOME DA EMPRESA e CNPJ
        $stmt = $pdo->prepare('SELECT * FROM usuarios WHERE nome_empresa = :empresa AND cnpj = :cnpj');
        $stmt->bindValue(':empresa', $acesso);
        $stmt->bindValue(':cnpj', $cnpj);
        $stmt->execute();
        
        // 3. Busca o usuário
        $usuario = $stmt->fetch(PDO::FETCH_ASSOC);

        // 4. Verifica se o usuário foi encontrado E se a senha está correta
        if ($usuario && password_verify($senha, $usuario['senha'])) {
            // Se tudo estiver certo, cria a sessão
            $_SESSION['id'] = $usuario['id'];
            $_SESSION['nome_empresa'] = $usuario['nome_empresa'];
            
            // Redireciona para o sistema
            header('Location: sistema.php');
            exit;

        } else {
            // Se o usuário não existe ou a senha está errada, define a mensagem de erro
            $_SESSION['erro_login'] = 'Nome da empresa, CNPJ ou senha incorretos.';
            header('Location: login.php');
            exit;
        }

    } catch (PDOException $e) {
        // Em caso de erro no banco de dados
        // Para o desenvolvedor: error_log($e->getMessage());
        $_SESSION['erro_login'] = 'Ocorreu um erro no sistema. Tente novamente mais tarde.';
        header('Location: login.php');
        exit;
    }

} else {
    // Se alguém tentar acessar o arquivo diretamente, redireciona para o login
    header('Location: login.php');
    exit;
}