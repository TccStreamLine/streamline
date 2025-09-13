<?php
session_start();
include_once('config.php');

// 1. VERIFICAÇÃO DE SEGURANÇA: O usuário está logado?
if (empty($_SESSION['id'])) {
    header('Location: login.php');
    exit;
}

// 2. VERIFICAÇÃO DE DADOS: O ID do produto foi enviado pela URL?
if (isset($_GET['id'])) {
    // Limpa o ID para garantir que é um número inteiro válido
    $id = filter_var($_GET['id'], FILTER_VALIDATE_INT);

    // Se o ID for válido (não for falso ou zero)
    if ($id) {
        try {
            // 3. PREPARA E EXECUTA A QUERY DE EXCLUSÃO DE FORMA SEGURA
            $sql = "DELETE FROM produtos WHERE id = :id";
            $stmt = $pdo->prepare($sql);
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            
            // Tenta executar a exclusão
            if ($stmt->execute()) {
                // rowCount() retorna o número de linhas afetadas. Se for > 0, o produto foi deletado.
                if ($stmt->rowCount() > 0) {
                    $_SESSION['msg_sucesso'] = "Produto excluído com sucesso!";
                } else {
                    // Se rowCount() for 0, significa que o produto com aquele ID não foi encontrado.
                    $_SESSION['msg_erro'] = "Produto não encontrado ou já foi excluído.";
                }
            } else {
                $_SESSION['msg_erro'] = "Erro ao tentar excluir o produto.";
            }

        } catch (PDOException $e) {
            // Captura um erro de banco de dados, se houver
            $_SESSION['msg_erro'] = "Erro de banco de dados ao tentar excluir.";
            // Para depuração: error_log($e->getMessage());
        }
    } else {
        // Se o ID enviado na URL não for um número válido (ex: ?id=abc)
        $_SESSION['msg_erro'] = "ID de produto inválido.";
    }
} else {
    // Se nenhum ID foi enviado na URL
    $_SESSION['msg_erro'] = "Nenhum produto selecionado para exclusão.";
}

// 4. REDIRECIONAMENTO: Independentemente do resultado, volta para a página de estoque
header('Location: estoque.php');
exit;
?>