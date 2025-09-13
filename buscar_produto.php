<?php
// Não precisa de session_start() aqui, pois é uma API interna
include_once('config.php');

// Define o cabeçalho da resposta como JSON, para o JavaScript entender
header('Content-Type: application/json');

// Pega o código de barras enviado pela URL (via JavaScript)
$codigo_barras = $_GET['codigo_barras'] ?? '';

if (empty($codigo_barras)) {
    // Se nenhum código foi enviado, retorna um erro
    echo json_encode(['error' => 'Código de barras não fornecido.']);
    exit;
}

try {
    // Prepara a consulta para buscar o produto pelo código de barras
    $stmt = $pdo->prepare("SELECT id, nome, valor_venda FROM produtos WHERE codigo_barras = ?");
    $stmt->execute([$codigo_barras]);
    $produto = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($produto) {
        // Se encontrou, retorna os dados do produto em formato JSON
        echo json_encode($produto);
    } else {
        // Se não encontrou, retorna um erro em formato JSON
        echo json_encode(['error' => 'Produto não encontrado.']);
    }
} catch (PDOException $e) {
    // Em caso de erro no banco, retorna um erro em formato JSON
    echo json_encode(['error' => 'Erro no banco de dados.']);
}
?>