<?php
session_start();
include_once('config.php');

if (empty($_SESSION['id']) || $_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: login.php');
    exit;
}

$produto_id = filter_input(INPUT_POST, 'produto_id', FILTER_VALIDATE_INT);
$quantidade = filter_input(INPUT_POST, 'quantidade', FILTER_VALIDATE_INT);
$usuario_id = $_SESSION['id'];

if (!$produto_id || !$quantidade || $quantidade <= 0) {
    $_SESSION['msg_erro'] = "Dados inválidos.";
    header('Location: vendas.php');
    exit;
}

try {
    $pdo->beginTransaction();

    $stmt_prod = $pdo->prepare("SELECT nome, valor_venda, quantidade_estoque, quantidade_minima FROM produtos WHERE id = ?");
    $stmt_prod->execute([$produto_id]);
    $produto = $stmt_prod->fetch(PDO::FETCH_ASSOC);

    if (!$produto) throw new Exception("Produto não encontrado.");
    if ($produto['quantidade_estoque'] < $quantidade) throw new Exception("Estoque insuficiente.");

    $valor_total = $produto['valor_venda'] * $quantidade;

    // Cria venda e item
    $stmt_venda = $pdo->prepare("INSERT INTO vendas (usuario_id, valor_total, data_venda) VALUES (?, ?, NOW())");
    $stmt_venda->execute([$usuario_id, $valor_total]);
    $venda_id = $pdo->lastInsertId();

    $stmt_item = $pdo->prepare("INSERT INTO venda_itens (venda_id, produto_id, quantidade, valor_unitario) VALUES (?, ?, ?, ?)");
    $stmt_item->execute([$venda_id, $produto_id, $quantidade, $produto['valor_venda']]);

    // Atualiza estoque
    $novo_estoque = $produto['quantidade_estoque'] - $quantidade;
    $pdo->prepare("UPDATE produtos SET quantidade_estoque = ? WHERE id = ?")->execute([$novo_estoque, $produto_id]);

    // Notificação interna apenas
    if ($novo_estoque <= $produto['quantidade_minima']) {
        $msg = "Alerta: O produto '" . $produto['nome'] . "' está com estoque baixo ($novo_estoque).";
        $pdo->prepare("INSERT INTO notificacoes (usuario_id, mensagem, lida, data_criacao) VALUES (?, ?, 0, NOW())")->execute([$usuario_id, $msg]);
    }

    $pdo->commit();
    $_SESSION['msg_sucesso'] = "Venda registrada!";

} catch (Exception $e) {
    $pdo->rollBack();
    $_SESSION['msg_erro'] = $e->getMessage();
}

header('Location: vendas.php');
exit;
?>