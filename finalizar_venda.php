<?php
session_start();
include_once('config.php');

if (empty($_SESSION['id']) || empty($_SESSION['carrinho'])) {
    header('Location: caixa.php');
    exit;
}

$usuario_id = $_SESSION['id'];
$carrinho = $_SESSION['carrinho'];
$valor_total = 0;

try {
    $pdo->beginTransaction();

    // 1. Cria a venda
    foreach ($carrinho as $item) {
        $valor_total += $item['preco'] * $item['quantidade'];
    }

    $sql_venda = "INSERT INTO vendas (usuario_id, valor_total, data_venda) VALUES (?, ?, NOW())";
    $stmt_venda = $pdo->prepare($sql_venda);
    $stmt_venda->execute([$usuario_id, $valor_total]);
    $venda_id = $pdo->lastInsertId();

    // 2. Processa itens e atualiza estoque
    $sql_item_venda = "INSERT INTO venda_itens (venda_id, produto_id, quantidade, valor_unitario) VALUES (?, ?, ?, ?)";
    $stmt_item_venda = $pdo->prepare($sql_item_venda);

    $sql_update_estoque = "UPDATE produtos SET quantidade_estoque = quantidade_estoque - ? WHERE id = ?";
    $stmt_update_estoque = $pdo->prepare($sql_update_estoque);

    // Consulta para verificar estoque minimo
    $sql_check_estoque = "SELECT nome, quantidade_estoque, quantidade_minima FROM produtos WHERE id = ?";
    $stmt_check_estoque = $pdo->prepare($sql_check_estoque);

    // Notificação interna
    $sql_notificacao = "INSERT INTO notificacoes (usuario_id, mensagem, lida, data_criacao) VALUES (?, ?, 0, NOW())";
    $stmt_notificacao = $pdo->prepare($sql_notificacao);

    foreach ($carrinho as $item) {
        // Registra item da venda
        $stmt_item_venda->execute([$venda_id, $item['id'], $item['quantidade'], $item['preco']]);

        // Baixa no estoque
        $stmt_update_estoque->execute([$item['quantidade'], $item['id']]);

        // Verifica se atingiu estoque mínimo (após a baixa)
        $stmt_check_estoque->execute([$item['id']]);
        $produto_atualizado = $stmt_check_estoque->fetch(PDO::FETCH_ASSOC);

        if ($produto_atualizado && $produto_atualizado['quantidade_estoque'] <= $produto_atualizado['quantidade_minima']) {
            // Apenas notificação interna para o CEO
            $msg = "Atenção: O produto '" . $produto_atualizado['nome'] . "' atingiu o nível crítico de estoque (" . $produto_atualizado['quantidade_estoque'] . "). Realize um pedido ao fornecedor.";
            $stmt_notificacao->execute([$usuario_id, $msg]);
        }
    }

    $pdo->commit();
    unset($_SESSION['carrinho']);
    $_SESSION['msg_sucesso'] = "Venda realizada com sucesso!";
    header('Location: vendas.php');

} catch (Exception $e) {
    if ($pdo->inTransaction()) {
        $pdo->rollBack();
    }
    $_SESSION['msg_erro'] = "Erro ao finalizar venda: " . $e->getMessage();
    header('Location: caixa.php');
}
exit;
?>