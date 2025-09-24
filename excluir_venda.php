<?php
session_start();
include_once('config.php');

if (empty($_SESSION['id'])) {
    header('Location: login.php');
    exit;
}

$venda_id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
$usuario_id = $_SESSION['id'];

if (!$venda_id) {
    $_SESSION['msg_erro'] = "ID da venda inválido.";
    header('Location: vendas.php');
    exit;
}

try {
    $pdo->beginTransaction();

    // 1. Pega os itens da venda para devolver ao estoque
    $sql_itens = "SELECT produto_id, quantidade FROM venda_itens WHERE venda_id = ?";
    $stmt_itens = $pdo->prepare($sql_itens);
    $stmt_itens->execute([$venda_id]);
    $itens_da_venda = $stmt_itens->fetchAll(PDO::FETCH_ASSOC);

    // 2. Devolve cada item ao estoque
    $sql_update_estoque = "UPDATE produtos SET quantidade_estoque = quantidade_estoque + ? WHERE id = ?";
    $stmt_update_estoque = $pdo->prepare($sql_update_estoque);
    foreach ($itens_da_venda as $item) {
        $stmt_update_estoque->execute([$item['quantidade'], $item['produto_id']]);
    }

    // 3. Inativa a venda
    $sql_inativar = "UPDATE vendas SET status = 'inativo' WHERE id = ? AND usuario_id = ?";
    $stmt_inativar = $pdo->prepare($sql_inativar);
    $stmt_inativar->execute([$venda_id, $usuario_id]);

    $pdo->commit();
    $_SESSION['msg_sucesso'] = "Venda cancelada e estoque estornado com sucesso!";

} catch (Exception $e) {
    $pdo->rollBack();
    $_SESSION['msg_erro'] = "Erro ao cancelar a venda: " . $e->getMessage();
}

header('Location: vendas.php');
exit;