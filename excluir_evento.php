<?php
session_start();
include_once('config.php');

if (empty($_SESSION['id'])) {
    http_response_code(401);
    echo json_encode(['status' => 'error', 'message' => 'Usuário não autenticado']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $evento_id = $_POST['id'] ?? '';
    $usuario_id = $_SESSION['id'];

    if (!$evento_id) {
        echo json_encode(['status' => 'error', 'message' => 'ID não informado']);
        exit;
    }

    $stmt = $pdo->prepare("DELETE FROM eventos WHERE id = ? AND usuario_id = ?");
    $stmt->execute([$evento_id, $usuario_id]);

    echo json_encode(['status' => 'success']);
    exit;
}
echo json_encode(['status' => 'error', 'message' => 'Método inválido']);