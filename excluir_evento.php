<?php
session_start();
include_once('config.php');

header('Content-Type: application/json');

if (empty($_SESSION['id'])) {
    http_response_code(401);
    echo json_encode(['status' => 'error', 'message' => 'Usuário não autenticado']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['status' => 'error', 'message' => 'Método inválido']);
    exit;
}

// --- ALTERAÇÃO AQUI ---
// Mudamos para ler o ID a partir de $_POST, que é mais simples.
$evento_id = $_POST['id'] ?? '';

if (!$evento_id) {
    http_response_code(400);
    echo json_encode(['status' => 'error', 'message' => 'ID do evento não informado']);
    exit;
}

$usuario_id = $_SESSION['id'];

try {
    $stmt = $pdo->prepare("DELETE FROM eventos WHERE id = ? AND usuario_id = ?");
    $stmt->execute([$evento_id, $usuario_id]);

    if ($stmt->rowCount() > 0) {
        echo json_encode(['status' => 'success', 'message' => 'Evento excluído com sucesso']);
    } else {
        http_response_code(404);
        echo json_encode(['status' => 'error', 'message' => 'Evento não encontrado ou sem permissão para excluir']);
    }
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['status' => 'error', 'message' => 'Erro no banco de dados: ' . $e->getMessage()]);
}