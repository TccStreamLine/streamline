<?php
session_start();
include_once('config.php');

header('Content-Type: application/json');

// Inicializa o carrinho na sessão se ele ainda não existir
if (!isset($_SESSION['carrinho'])) {
    $_SESSION['carrinho'] = [];
}

// Pega os dados enviados pelo JavaScript
$data = json_decode(file_get_contents('php://input'), true);
$acao = $data['acao'] ?? '';

if ($acao === 'adicionar') {
    $produto_id = $data['produto_id'] ?? 0;
    $quantidade = $data['quantidade'] ?? 0;

    if ($produto_id > 0 && $quantidade > 0) {
        // Busca os dados do produto para garantir que o preço está correto
        $stmt = $pdo->prepare("SELECT nome, valor_venda FROM produtos WHERE id = ?");
        $stmt->execute([$produto_id]);
        $produto = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($produto) {
            // Se o produto já está no carrinho, apenas soma a quantidade
            if (isset($_SESSION['carrinho'][$produto_id])) {
                $_SESSION['carrinho'][$produto_id]['quantidade'] += $quantidade;
            } else {
                // Se não está, adiciona o produto ao carrinho
                $_SESSION['carrinho'][$produto_id] = [
                    'id' => $produto_id,
                    'nome' => $produto['nome'],
                    'quantidade' => $quantidade,
                    'valor_unitario' => $produto['valor_venda']
                ];
            }
        }
    }
}

// Devolve o carrinho atualizado em formato JSON para o JavaScript
echo json_encode(array_values($_SESSION['carrinho'])); // array_values para reindexar o array
?>