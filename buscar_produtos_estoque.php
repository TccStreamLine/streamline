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
$filtro = $_GET['filtro'] ?? ''; // Pega o filtro da URL

$where_clause = "status = 'ativo' AND (nome LIKE :termo OR codigo_barras LIKE :termo)";
$params = [':termo' => '%' . $termo_busca . '%'];

if ($filtro === 'estoque_baixo') {
    $where_clause .= " AND quantidade_estoque <= quantidade_minima";
}

try {
    $sql = "SELECT p.*, c.nome as categoria_nome 
            FROM produtos p 
            LEFT JOIN categorias c ON p.categoria_id = c.id 
            WHERE $where_clause
            ORDER BY p.nome ASC";

    $stmt_produtos = $pdo->prepare($sql);
    $stmt_produtos->execute($params);
    $produtos = $stmt_produtos->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode($produtos);

} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['erro' => 'Erro ao buscar produtos.']);
}
?>