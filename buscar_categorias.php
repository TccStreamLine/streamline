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
    $stmt = $pdo->prepare(
        "SELECT * FROM categorias 
         WHERE nome LIKE :termo
         ORDER BY nome ASC"
    );
    
    $stmt->bindValue(':termo', '%' . $termo_busca . '%');
    $stmt->execute();
    $categorias = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode($categorias);

} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['erro' => 'Erro ao buscar categorias.']);
}
?>