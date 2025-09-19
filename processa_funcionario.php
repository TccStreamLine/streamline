<?php
session_start();
include_once('config.php');

if (empty($_SESSION['id'])) {
    header('Location: login.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $acao = $_POST['acao'] ?? '';

    $nome = trim($_POST['nome'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $cargo = trim($_POST['cargo'] ?? '');
    $telefone = trim($_POST['telefone'] ?? '');

    if (empty($nome) || empty($email)) {
        $_SESSION['msg_erro'] = "Nome e E-mail são obrigatórios.";
        header('Location: funcionarios.php');
        exit;
    }

    if ($acao === 'cadastrar') {
        try {
            $check_sql = "SELECT id FROM funcionarios WHERE email = ?";
            $check_stmt = $pdo->prepare($check_sql);
            $check_stmt->execute([$email]);
            if ($check_stmt->fetch()) {
                $_SESSION['msg_erro'] = "Este E-mail já está cadastrado.";
            } else {
                $sql = "INSERT INTO funcionarios (nome, email, cargo, telefone) VALUES (?, ?, ?, ?)";
                $stmt = $pdo->prepare($sql);
                $stmt->execute([$nome, $email, $cargo, $telefone]);
                $_SESSION['msg_sucesso'] = "Funcionário cadastrado com sucesso!";
            }
        } catch (PDOException $e) {
            $_SESSION['msg_erro'] = "Erro ao cadastrar funcionário.";
        }
    } 
    elseif ($acao === 'editar') {
        $id = filter_var($_POST['funcionario_id'] ?? 0, FILTER_VALIDATE_INT);
        if (!$id) {
            $_SESSION['msg_erro'] = "ID do funcionário inválido.";
        } else {
            try {
                $check_sql = "SELECT id FROM funcionarios WHERE email = ? AND id != ?";
                $check_stmt = $pdo->prepare($check_sql);
                $check_stmt->execute([$email, $id]);
                if ($check_stmt->fetch()) {
                    $_SESSION['msg_erro'] = "Este E-mail já pertence a outro funcionário.";
                } else {
                    $sql = "UPDATE funcionarios SET nome = ?, email = ?, cargo = ?, telefone = ? WHERE id = ?";
                    $stmt = $pdo->prepare($sql);
                    $stmt->execute([$nome, $email, $cargo, $telefone, $id]);
                    $_SESSION['msg_sucesso'] = "Funcionário atualizado com sucesso!";
                }
            } catch (PDOException $e) {
                $_SESSION['msg_erro'] = "Erro ao atualizar funcionário.";
            }
        }
    }
    header('Location: funcionarios.php');
    exit;

} elseif (isset($_GET['acao']) && $_GET['acao'] === 'excluir') {
    $id = filter_var($_GET['id'] ?? 0, FILTER_VALIDATE_INT);
    if ($id) {
        try {
            $sql = "UPDATE funcionarios SET status = 'inativo' WHERE id = ?";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([$id]);
            $_SESSION['msg_sucesso'] = "Funcionário inativado com sucesso!";
        } catch (PDOException $e) {
            $_SESSION['msg_erro'] = "Erro ao inativar funcionário.";
        }
    } else {
        $_SESSION['msg_erro'] = "ID inválido.";
    }
    header('Location: funcionarios.php');
    exit;
}
?>