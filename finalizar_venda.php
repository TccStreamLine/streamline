<?php
session_start();
include_once('config.php');

header('Content-Type: application/json');

if (empty($_SESSION['id']) || empty($_SESSION['carrinho'])) {
    echo json_encode(['error' => 'Usuário não logado ou carrinho vazio.']);
    exit;
}

$carrinho = $_SESSION['carrinho'];
$usuario_id = $_SESSION['id'];

try {
    $pdo->beginTransaction();

    $sql_check_estoque = "SELECT nome, quantidade_estoque, quantidade_minima FROM produtos WHERE id = ?";
    $stmt_check_estoque = $pdo->prepare($sql_check_estoque);

    foreach ($carrinho as $item) {
        if ($item['tipo'] === 'produto') {
            $stmt_check_estoque->execute([$item['id']]);
            $produto = $stmt_check_estoque->fetch(PDO::FETCH_ASSOC);

            if (!$produto) {
                $pdo->rollBack();
                echo json_encode(['error' => 'Produto não encontrado no banco de dados: ' . htmlspecialchars($item['nome'])]);
                exit;
            }

            $estoque_restante = $produto['quantidade_estoque'] - $item['quantidade'];

            if ($estoque_restante < 0) {
                $pdo->rollBack();
                echo json_encode(['error' => "Estoque insuficiente para o produto: " . htmlspecialchars($item['nome'])]);
                exit;
            }

            if ($estoque_restante < $produto['quantidade_minima']) {
                $pdo->rollBack();
                $mensagem_erro = "Venda bloqueada para o produto: " . htmlspecialchars($item['nome']) . ". A venda deixaria o estoque abaixo do mínimo permitido (" . $produto['quantidade_minima'] . " unidades).";
                echo json_encode(['error' => $mensagem_erro]);
                exit;
            }
        }
    }

    $valor_total_venda = 0;
    foreach ($carrinho as $item) {
        $valor_total_venda += $item['quantidade'] * $item['valor_unitario'];
    }

    $sql_venda = "INSERT INTO vendas (usuario_id, valor_total) VALUES (?, ?)";
    $stmt_venda = $pdo->prepare($sql_venda);
    $stmt_venda->execute([$usuario_id, $valor_total_venda]);
    $venda_id = $pdo->lastInsertId();

    $sql_item_produto = "INSERT INTO venda_itens (venda_id, produto_id, quantidade, valor_unitario, valor_total) VALUES (?, ?, ?, ?, ?)";
    $stmt_item_produto = $pdo->prepare($sql_item_produto);
    
    $sql_item_servico = "INSERT INTO venda_servicos (venda_id, servico_id, valor) VALUES (?, ?, ?)";
    $stmt_item_servico = $pdo->prepare($sql_item_servico);

    $sql_update_estoque = "UPDATE produtos SET quantidade_estoque = quantidade_estoque - ? WHERE id = ?";
    $stmt_update_estoque = $pdo->prepare($sql_update_estoque);

    foreach ($carrinho as $item) {
        if ($item['tipo'] === 'produto') {
            $valor_total_item = $item['quantidade'] * $item['valor_unitario'];
            $stmt_item_produto->execute([$venda_id, $item['id'], $item['quantidade'], $item['valor_unitario'], $valor_total_item]);
            $stmt_update_estoque->execute([$item['quantidade'], $item['id']]);
        } else {
            $stmt_item_servico->execute([$venda_id, $item['id'], $item['valor_unitario']]);
        }
    }

    $pdo->commit();
    unset($_SESSION['carrinho']);
    $_SESSION['msg_sucesso_caixa'] = "Venda finalizada com sucesso!";
    echo json_encode(['success' => true]);

} catch (Exception $e) {
    if ($pdo->inTransaction()) {
        $pdo->rollBack();
    }
    echo json_encode(['error' => 'Falha ao finalizar a venda: ' . $e->getMessage()]);
}
?>