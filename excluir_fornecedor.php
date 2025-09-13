<?php
session_start();
include_once('config.php');

// Segurança: Apenas usuários logados podem excluir
if (empty($_SESSION['id'])) {
    header('Location: login.php');
    exit;
}

// Validação: Verifica se um ID foi passado pela URL
if (isset($_GET['id'])) {
    $id = filter_var($_GET['id'], FILTER_VALIDATE_INT);

    if ($id) {
        try {
            $sql = "DELETE FROM fornecedores WHERE id = :id";
            $stmt = $pdo->prepare($sql);
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            
            if ($stmt->execute()) {
                if ($stmt->rowCount() > 0) {
                    $_SESSION['msg_sucesso'] = "Fornecedor excluído com sucesso!";
                } else {
                    $_SESSION['msg_erro'] = "Fornecedor não encontrado.";
                }
            } else {
                $_SESSION['msg_erro'] = "Erro ao tentar excluir o fornecedor.";
            }
        } catch (PDOException $e) {
            $_SESSION['msg_erro'] = "Erro de banco de dados.";
        }
    } else {
        $_SESSION['msg_erro'] = "ID de fornecedor inválido.";
    }
} else {
    $_SESSION['msg_erro'] = "Nenhum fornecedor selecionado para exclusão.";
}

// Redireciona de volta para a lista
header('Location: fornecedores.php');
exit;
?>