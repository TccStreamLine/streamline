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
$usuario_id = $_SESSION['id'];

try {
    $stmt = $pdo->prepare(
        "SELECT * FROM servicos_prestados 
         WHERE usuario_id = :usuario_id AND status = 'ativo' AND nome_servico LIKE :termo
         ORDER BY data_prestacao DESC"
    );
    
    $stmt->bindValue(':usuario_id', $usuario_id);
    $stmt->bindValue(':termo', '%' . $termo_busca . '%');
    $stmt->execute();
    $servicos = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode($servicos);

} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['erro' => 'Erro ao buscar serviços.']);
}
?>