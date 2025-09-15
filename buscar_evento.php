<?php
session_start();
include_once('config.php');

if (empty($_SESSION['id'])) {
    http_response_code(403);
    exit('Não autorizado');
}

$usuario_id = $_SESSION['id'];
$data = $_GET['data'] ?? '';


if ($data) {
    $stmt = $pdo->prepare("SELECT id, titulo, inicio, horario, descricao FROM eventos WHERE usuario_id = ? AND inicio = ?");
    $stmt->execute([$usuario_id, $data]);
    echo json_encode($stmt->fetchAll(PDO::FETCH_ASSOC));
} else {
    echo json_encode([]);
}