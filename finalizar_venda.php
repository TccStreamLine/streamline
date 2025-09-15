<?php
session_start();
include_once('config.php');

header('Content-Type: application/json');

if (empty($_SESSION['id'])) {
    echo json_encode(['error' => 'Usuário não logado.']);
    exit;
}

if (empty($_SESSION['carrinho'])) {
    echo json_encode(['error' => 'O carrinho está vazio.']);
    exit;
}

$carrinho = $_SESSION['carrinho'];

try {
    $pdo->beginTransaction();

    $sql_check_estoque = "SELECT nome, quantidade_estoque, quantidade_minima FROM produtos WHERE id = ?";
    $stmt_check_estoque = $pdo->prepare($sql_check_estoque);

    foreach ($carrinho as $produto_id => $item) {
        $stmt_check_estoque->execute([$produto_id]);
        $produto = $stmt_check_estoque->fetch(PDO::FETCH_ASSOC);

        $estoque_restante = $produto['quantidade_estoque'] - $item['quantidade'];

        if (!$produto || $estoque_restante < $produto['quantidade_minima']) {
            $pdo->rollBack();
            $mensagem_erro = "Venda bloqueada para o produto: " . htmlspecialchars($item['nome']) . ". A venda deixaria o estoque abaixo do mínimo permitido (" . $produto['quantidade_minima'] . " unidades).";
            echo json_encode(['error' => $mensagem_erro]);
            exit;
        }
    }

    $valor_total_venda = 0;
    foreach ($carrinho as $item) {
        $valor_total_venda += $item['quantidade'] * $item['valor_unitario'];
    }

    $sql_venda = "INSERT INTO vendas (usuario_id, valor_total) VALUES (?, ?)";
    $stmt_venda = $pdo->prepare($sql_venda);
    $stmt_venda->execute([$_SESSION['id'], $valor_total_venda]);
    $venda_id = $pdo->lastInsertId();

    $sql_item = "INSERT INTO venda_itens (venda_id, produto_id, quantidade, valor_unitario, valor_total) VALUES (?, ?, ?, ?, ?)";
    $stmt_item = $pdo->prepare($sql_item);

    $sql_update_estoque = "UPDATE produtos SET quantidade_estoque = ? WHERE id = ?";
    $stmt_update_estoque = $pdo->prepare($sql_update_estoque);

    foreach ($carrinho as $produto_id => $item) {
        $stmt_check_estoque->execute([$produto_id]);
        $produto_atual = $stmt_check_estoque->fetch(PDO::FETCH_ASSOC);
        $novo_estoque = $produto_atual['quantidade_estoque'] - $item['quantidade'];

        $valor_total_item = $item['quantidade'] * $item['valor_unitario'];
        $stmt_item->execute([
            $venda_id,
            $produto_id,
            $item['quantidade'],
            $item['valor_unitario'],
            $valor_total_item
        ]);
        
        $stmt_update_estoque->execute([$novo_estoque, $produto_id]);
    }

    $pdo->commit();

    unset($_SESSION['carrinho']);
    $_SESSION['msg_sucesso_caixa'] = "Venda finalizada com sucesso!";
    echo json_encode(['success' => true]);

} catch (Exception $e) {
    $pdo->rollBack();
    echo json_encode(['error' => 'Falha ao finalizar a venda: ' . $e->getMessage()]);
}
?>