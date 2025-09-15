<?php
session_start();
include_once('config.php');

if (empty($_SESSION['id'])) {
    http_response_code(401);
    echo json_encode(['total' => 0, 'titulos' => '']);
    exit;
}

$usuario_id = $_SESSION['id'];
$hoje = date('Y-m-d');
$stmt = $pdo->prepare("SELECT titulo FROM eventos WHERE usuario_id = ? AND inicio = ?");
$stmt->execute([$usuario_id, $hoje]);
$titulos = $stmt->fetchAll(PDO::FETCH_COLUMN);

echo json_encode([
    'total' => count($titulos),
    'titulos' => implode(', ', $titulos)
]);