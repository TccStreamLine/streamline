<?php
session_start();
include_once('config.php');

header('Content-Type: application/json');

if (!isset($_SESSION['carrinho'])) {
    $_SESSION['carrinho'] = [];
}

$data = json_decode(file_get_contents('php://input'), true);
$acao = $data['acao'] ?? $_GET['acao'] ?? '';

if ($acao === 'adicionar') {
    $produto_id = $data['produto_id'] ?? 0;
    $quantidade = $data['quantidade'] ?? 0;

    if ($produto_id > 0 && $quantidade > 0) {
        $stmt = $pdo->prepare("SELECT nome, valor_venda FROM produtos WHERE id = ?");
        $stmt->execute([$produto_id]);
        $produto = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($produto) {
            if (isset($_SESSION['carrinho'][$produto_id])) {
                $_SESSION['carrinho'][$produto_id]['quantidade'] += $quantidade;
            } else {
                $_SESSION['carrinho'][$produto_id] = [
                    'id' => $produto_id,
                    'nome' => $produto['nome'],
                    'quantidade' => $quantidade,
                    'valor_unitario' => $produto['valor_venda']
                ];
            }
        }
    }
} elseif ($acao === 'limpar') {
    $_SESSION['carrinho'] = [];
}

echo json_encode(array_values($_SESSION['carrinho']));
?>