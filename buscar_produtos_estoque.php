<?php
session_start();
include_once('config.php');

header('Content-Type: application/json');

if (empty($_SESSION['id'])) {
    http_response_code(401); 
    echo json_encode(['erro' => 'Usuário não autenticado.']);
    exit;
}

$termo_busca = $_GET['termo'] ?? '';

try {
    $stmt_produtos = $pdo->prepare(
        "SELECT p.*, c.nome as categoria_nome 
         FROM produtos p 
         LEFT JOIN categorias c ON p.categoria_id = c.id 
         WHERE p.status = 'ativo' AND (p.nome LIKE :termo OR p.codigo_barras LIKE :termo)
         ORDER BY p.nome ASC"
    );
    
    $stmt_produtos->bindValue(':termo', '%' . $termo_busca . '%');
    $stmt_produtos->execute();
    $produtos = $stmt_produtos->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode($produtos);

} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['erro' => 'Erro ao buscar produtos.']);
}
?>