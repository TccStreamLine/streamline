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
        header('Location: fornecedores.php');
        exit;
    }
    if ($acao === 'cadastrar') {
        if (empty($email)) {
            $_SESSION['msg_erro'] = "O e-mail é obrigatório para enviar o convite ao fornecedor.";
            header('Location: fornecedores.php');
            exit;
        }
        try {
            $pdo->beginTransaction();
            $check_sql = "SELECT id FROM fornecedores WHERE cnpj = :cnpj OR email = :email";
            $check_stmt = $pdo->prepare($check_sql);
            $check_stmt->execute([':cnpj' => $cnpj, ':email' => $email]);
            if ($check_stmt->fetch()) {
                $_SESSION['msg_erro'] = "Este CNPJ ou E-mail já está cadastrado.";
            } else {
                $sql = "INSERT INTO fornecedores (razao_social, cnpj, email, telefone) VALUES (:razao_social, :cnpj, :email, :telefone)";
                $stmt = $pdo->prepare($sql);
                $stmt->execute([':razao_social' => $razao_social, ':cnpj' => $cnpj, ':email' => $email, ':telefone' => $telefone]);
                $fornecedor_id = $pdo->lastInsertId();
                $token = bin2hex(random_bytes(50));
                $expira = date("Y-m-d H:i:s", time() + 86400);
                $token_sql = "UPDATE fornecedores SET reset_token = :token, reset_token_expire = :expira WHERE id = :id";
                $token_stmt = $pdo->prepare($token_sql);
                $token_stmt->execute([':token' => $token, ':expira' => $expira, ':id' => $fornecedor_id]);
                $mail = new PHPMailer(true);
                $mail = new PHPMailer(true);
                $mail->SMTPDebug = 2;
                $mail->isSMTP();
                $mail->Host = 'smtp-relay.brevo.com';
                $mail->SMTPAuth = true;
                $mail->Username = '9691c1001@smtp-brevo.com';
                $mail->Password = 'g3BDXcCKG8zWtZRL';
                $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
                $mail->Port = 587;
                $mail->CharSet = 'UTF-8';
                $mail->setFrom('tccstreamline@gmail.com', 'Streamline Sistema');
                $mail->addAddress($email, $razao_social);
                $mail->isHTML(true);
                $mail->Subject = 'Bem-vindo! Crie sua senha de acesso';
                $link = "http://localhost/streamline/definir_senha_fornecedor.php?token=" . $token;
                $mail->Body = "<h2>Olá, " . htmlspecialchars($razao_social) . "!</h2><p>Você foi cadastrado em nosso sistema. Para começar, por favor, crie sua senha de acesso clicando no link abaixo:</p><p><a href='$link'>Criar Minha Senha</a></p><p>Este link é válido por 24 horas.</p>";
                $mail->send();
                $pdo->commit();
                $_SESSION['msg_sucesso'] = "Fornecedor cadastrado e e-mail de convite enviado!";
            }
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
                $check_sql = "SELECT id FROM fornecedores WHERE cnpj = :cnpj AND id != :id";
                $check_stmt = $pdo->prepare($check_sql);
                $check_stmt->execute([':cnpj' => $cnpj, ':id' => $id]);
                if ($check_stmt->fetch()) {
                    $_SESSION['msg_erro'] = "Este CNPJ já pertence a outro fornecedor.";
                } else {
                    $sql = "UPDATE fornecedores SET razao_social = :razao_social, cnpj = :cnpj, email = :email, telefone = :telefone WHERE id = :id";
                    $stmt = $pdo->prepare($sql);
                    $stmt->execute([':razao_social' => $razao_social, ':cnpj' => $cnpj, ':email' => $email, ':telefone' => $telefone, ':id' => $id]);
                    $_SESSION['msg_sucesso'] = "Fornecedor atualizado com sucesso!";
                }
            } catch (PDOException $e) {
                $_SESSION['msg_erro'] = "Erro ao atualizar fornecedor.";
            }
        }
    }
}
header('Location: fornecedores.php');
exit;
?>