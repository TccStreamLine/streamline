<?php
include_once('config.php');

header('Content-Type: application/json');

$codigo_barras = $_GET['codigo_barras'] ?? '';

if (empty($codigo_barras)) {
    echo json_encode(['error' => 'Código de barras não fornecido.']);
    exit;
}

try {
    $stmt = $pdo->prepare("SELECT id, nome, valor_venda FROM produtos WHERE codigo_barras = ?");
    $stmt->execute([$codigo_barras]);
    $produto = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($produto) {
        echo json_encode($produto);
    } else {
        echo json_encode(['error' => 'Produto não encontrado.']);
    }
} catch (PDOException $e) {
    echo json_encode(['error' => 'Erro no banco de dados.']);
}
?>