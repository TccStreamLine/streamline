<?php
session_start();
include_once('config.php');

// Proteção: Apenas usuários logados podem processar um plano
if (empty($_SESSION['id'])) {
    header('Location: login.php');
    exit;
}

// Verifica se o formulário foi enviado via POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $usuario_id = $_SESSION['id'];
    $plano_escolhido = $_POST['plano_escolhido'] ?? 'indefinido';
    $email_contato = filter_input(INPUT_POST, 'email', FILTER_VALIDATE_EMAIL); // Captura o email do formulário

    // Validação básica
    if ($plano_escolhido === 'indefinido' || !$email_contato) {
        $_SESSION['msg_erro'] = "Ocorreu um erro ao processar sua solicitação. Dados incompletos.";
        header('Location: loja_planos.php');
        exit;
    }

    // ===================================================================
    // == LÓGICA DE PAGAMENTO REAL (GATEWAY) SERIA INSERIDA AQUI       ==
    // ===================================================================
    // Para seu TCC, vamos simular que o pagamento foi um sucesso.

    try {
        // Atualiza o plano e a data de assinatura do usuário no banco de dados
        $stmt = $pdo->prepare("UPDATE usuarios SET plano = ?, data_assinatura = NOW() WHERE id = ?");
        $stmt->execute([$plano_escolhido, $usuario_id]);
        
        // Define uma mensagem de sucesso para ser exibida na página inicial
        $_SESSION['msg_sucesso'] = "Parabéns! Sua assinatura do plano '" . ucfirst($plano_escolhido) . "' foi confirmada com sucesso!";

    } catch (PDOException $e) {
        $_SESSION['msg_erro'] = "Ocorreu um erro ao atualizar seu plano no banco de dados. Por favor, tente novamente.";
        // Log do erro real para depuração (opcional, mas recomendado)
        // error_log('Erro ao atualizar plano: ' . $e->getMessage());
    }

    // Redireciona o usuário de volta para a página inicial do sistema
    header('Location: sistema.php');
    exit;

} else {
    // Se alguém tentar acessar este arquivo diretamente (sem ser via POST), redireciona
    header('Location: loja_planos.php');
    exit;
}
?>