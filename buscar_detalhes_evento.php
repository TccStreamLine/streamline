<?php
session_start();
include_once('config.php');

header('Content-Type: application/json');

if (empty($_SESSION['id'])) {
    http_response_code(401);
    echo json_encode(['error' => 'Não autorizado']);
    exit;
}

$evento_id = $_GET['id'] ?? 0;
$usuario_id = $_SESSION['id'];

if (!$evento_id) {
    http_response_code(400);
    echo json_encode(['error' => 'ID do evento não fornecido']);
    exit;
}

$stmt = $pdo->prepare("SELECT id, titulo, horario, descricao, DATE(inicio) as data FROM eventos WHERE id = ? AND usuario_id = ?");
$stmt->execute([$evento_id, $usuario_id]);
$evento = $stmt->fetch(PDO::FETCH_ASSOC);

if ($evento) {
    echo json_encode($evento);
} else {
    http_response_code(404);
    echo json_encode(['error' => 'Evento não encontrado']);
}