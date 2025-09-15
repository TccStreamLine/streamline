<?php
session_start();
include_once('config.php');

if (empty($_SESSION['id'])) {
    http_response_code(401);
    echo json_encode([]);
    exit;
}

$usuario_id = $_SESSION['id'];
$stmt = $pdo->prepare("SELECT id, titulo, inicio AS data_evento FROM eventos WHERE usuario_id = ?");
$stmt->execute([$usuario_id]);
$eventos = $stmt->fetchAll(PDO::FETCH_ASSOC);

echo json_encode($eventos);