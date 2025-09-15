<?php
session_start();
include_once('config.php');
if (empty($_SESSION['id'])) {
    header('Location: login.php');
    exit;
}
if (isset($_GET['id'])) {
    $id = filter_var($_GET['id'], FILTER_VALIDATE_INT);
    if ($id) {
        try {
            $sql = "DELETE FROM produtos WHERE id = :id";
            $stmt = $pdo->prepare($sql);
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            if ($stmt->execute()) {
                if ($stmt->rowCount() > 0) {
                    $_SESSION['msg_sucesso'] = "Produto excluído com sucesso!";
                } else {
                    $_SESSION['msg_erro'] = "Produto não encontrado ou já foi excluído.";
                }
            } else {
                $_SESSION['msg_erro'] = "Erro ao tentar excluir o produto.";
            }
        } catch (PDOException $e) {
            $_SESSION['msg_erro'] = "Erro de banco de dados ao tentar excluir.";
        }
    } else {
        $_SESSION['msg_erro'] = "ID de produto inválido.";
    }
} else {
    $_SESSION['msg_erro'] = "Nenhum produto selecionado para exclusão.";
}
header('Location: estoque.php');
exit;
?>