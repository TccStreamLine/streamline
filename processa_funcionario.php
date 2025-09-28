<?php
session_start();
include_once('config.php');

if (empty($_SESSION['id']) || $_SESSION['role'] !== 'ceo') {
    exit('Acesso negado.');
}

$usuario_id = $_SESSION['id'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $acao = $_POST['acao'] ?? '';
    $id = filter_var($_POST['id'] ?? 0, FILTER_VALIDATE_INT);
    $nome = trim($_POST['nome'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $cargo = trim($_POST['cargo'] ?? '');
    $telefone = trim($_POST['telefone'] ?? '');
    $senha = trim($_POST['senha'] ?? '');

    if (empty($nome) || empty($email)) {
        $_SESSION['msg_erro_funcionario'] = "Nome e E-mail são obrigatórios.";
        header('Location: funcionario_formulario.php' . ($id ? '?id=' . $id : ''));
        exit;
    }

    try {
        if ($acao === 'cadastrar') {
            if (empty($senha) || strlen($senha) < 6) {
                $_SESSION['msg_erro_funcionario'] = "A senha é obrigatória e deve ter no mínimo 6 caracteres.";
                header('Location: funcionario_formulario.php');
                exit;
            }

            // CORREÇÃO APLICADA AQUI: Adicionado "AND status = 'ativo'"
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

        } elseif ($acao === 'editar' && $id > 0) {
            // CORREÇÃO APLICADA AQUI: Adicionado "AND status = 'ativo'"
            $check_sql = "SELECT id FROM funcionarios WHERE email = ? AND usuario_id = ? AND id != ? AND status = 'ativo'";
            $check_stmt = $pdo->prepare($check_sql);
            $check_stmt->execute([$email, $usuario_id, $id]);
            if ($check_stmt->fetch()) {
                $_SESSION['msg_erro_funcionario'] = "Este e-mail já pertence a outro funcionário ativo.";
                header('Location: funcionario_formulario.php?id=' . $id);
                exit;
            }

            if (!empty($senha)) {
                if (strlen($senha) < 6) {
                    $_SESSION['msg_erro_funcionario'] = "A nova senha deve ter no mínimo 6 caracteres.";
                    header('Location: funcionario_formulario.php?id=' . $id);
                    exit;
                }
                $senha_hash = password_hash($senha, PASSWORD_DEFAULT);
                $sql = "UPDATE funcionarios SET nome = ?, email = ?, cargo = ?, telefone = ?, senha = ? WHERE id = ? AND usuario_id = ?";
                $stmt = $pdo->prepare($sql);
                $stmt->execute([$nome, $email, $cargo, $telefone, $senha_hash, $id, $usuario_id]);
            } else {
                $sql = "UPDATE funcionarios SET nome = ?, email = ?, cargo = ?, telefone = ? WHERE id = ? AND usuario_id = ?";
                $stmt = $pdo->prepare($sql);
                $stmt->execute([$nome, $email, $cargo, $telefone, $id, $usuario_id]);
            }
            $_SESSION['msg_sucesso'] = "Funcionário atualizado com sucesso!";
        }
    } catch (PDOException $e) {
        $_SESSION['msg_erro'] = "Ocorreu um erro no banco de dados.";
        header('Location: funcionarios.php');
        exit;
    }
    
    header('Location: funcionarios.php');
    exit;
} elseif (isset($_GET['acao']) && $_GET['acao'] === 'excluir') {
    $id = filter_var($_GET['id'] ?? 0, FILTER_VALIDATE_INT);

    if ($id) {
        try {
            $sql = "UPDATE funcionarios SET status = 'inativo' WHERE id = ? AND usuario_id = ?";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([$id, $usuario_id]);

            if ($stmt->rowCount() === 0) {
                $sql_fallback = "UPDATE funcionarios SET status = 'inativo' WHERE id = ?";
                $stmt_fallback = $pdo->prepare($sql_fallback);
                $stmt_fallback->execute([$id]);
            }

            $_SESSION['msg_sucesso'] = "Funcionário inativado com sucesso!";
        } catch (PDOException $e) {
            $_SESSION['msg_erro'] = "Erro ao inativar funcionário.";
        }
    } else {
        $_SESSION['msg_erro'] = "ID de funcionário inválido.";
    }
    header('Location: funcionarios.php');
    exit;
}

header('Location: funcionarios.php');
exit;
