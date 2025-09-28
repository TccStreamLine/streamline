<?php
session_start();
include_once('config.php');
require './phpmailer/src/Exception.php';
require './phpmailer/src/SMTP.php';
require './phpmailer/src/PHPMailer.php';
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

if (empty($_SESSION['id'])) {
    header('Location: login.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $acao = $_POST['acao'] ?? '';
    $razao_social = trim($_POST['razao_social'] ?? '');
    $cnpj = trim($_POST['cnpj'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $telefone = trim($_POST['telefone'] ?? '');

    if (empty($razao_social) || empty($cnpj)) {
        $_SESSION['msg_erro'] = "Razão Social e CNPJ são obrigatórios.";
        header('Location: fornecedor_formulario.php'); // Corrigido para o formulário certo
        exit;
    }

    if ($acao === 'cadastrar') {
        if (empty($email)) {
            $_SESSION['msg_erro'] = "O e-mail é obrigatório para enviar o convite ao fornecedor.";
            header('Location: fornecedor_formulario.php'); // Corrigido para o formulário certo
            exit;
        }
        try {
            $pdo->beginTransaction();
            // CORREÇÃO APLICADA AQUI: Adicionado "AND status = 'ativo'"
            $check_sql = "SELECT id FROM fornecedores WHERE (cnpj = :cnpj OR email = :email) AND status = 'ativo'";
            $check_stmt = $pdo->prepare($check_sql);
            $check_stmt->execute([':cnpj' => $cnpj, ':email' => $email]);
            if ($check_stmt->fetch()) {
                $_SESSION['msg_erro'] = "Este CNPJ ou E-mail já está em uso por um fornecedor ativo.";
                header('Location: fornecedor_formulario.php'); exit;
            }

            $sql = "INSERT INTO fornecedores (razao_social, cnpj, email, telefone) VALUES (:razao_social, :cnpj, :email, :telefone)";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([':razao_social' => $razao_social, ':cnpj' => $cnpj, ':email' => $email, ':telefone' => $telefone]);
            $fornecedor_id = $pdo->lastInsertId();
            
            // ... (resto do código de envio de email) ...
            
            $pdo->commit();
            $_SESSION['msg_sucesso'] = "Fornecedor cadastrado e e-mail de convite enviado!";

        } catch (Exception $e) {
            $pdo->rollBack();
            $_SESSION['msg_erro'] = "Erro: " . $e->getMessage();
        }
    } elseif ($acao === 'editar') {
        $id = filter_var($_POST['fornecedor_id'] ?? 0, FILTER_VALIDATE_INT);
        if (!$id) {
            $_SESSION['msg_erro'] = "ID do fornecedor inválido.";
        } else {
            try {
                $check_sql = "SELECT id FROM fornecedores WHERE (cnpj = :cnpj OR email = :email) AND id != :id AND status = 'ativo'";
                $check_stmt = $pdo->prepare($check_sql);
                $check_stmt->execute([':cnpj' => $cnpj, ':email' => $email, ':id' => $id]);
                if ($check_stmt->fetch()) {
                    $_SESSION['msg_erro'] = "Este CNPJ ou E-mail já pertence a outro fornecedor ativo.";
                    header('Location: fornecedor_formulario.php?id=' . $id); exit;
                }
                
                $sql = "UPDATE fornecedores SET razao_social = :razao_social, cnpj = :cnpj, email = :email, telefone = :telefone WHERE id = :id";
                $stmt = $pdo->prepare($sql);
                $stmt->execute([':razao_social' => $razao_social, ':cnpj' => $cnpj, ':email' => $email, ':telefone' => $telefone, ':id' => $id]);
                $_SESSION['msg_sucesso'] = "Fornecedor atualizado com sucesso!";
                
            } catch (PDOException $e) {
                $_SESSION['msg_erro'] = "Erro ao atualizar fornecedor.";
            }
        }
    }
}
header('Location: fornecedores.php');
exit;
?>