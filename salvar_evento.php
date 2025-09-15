<?php
session_start();
include_once('config.php');

if (empty($_SESSION['id'])) {
    http_response_code(401);
    echo json_encode(['status' => 'error', 'message' => 'Usuário não autenticado']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $titulo = $_POST['titulo'] ?? '';
    $data = $_POST['data'] ?? '';
    $usuario_id = $_SESSION['id'];

    if (!$titulo || !$data) {
        echo json_encode(['status' => 'error', 'message' => 'Dados incompletos']);
        exit;
    }

    $stmt = $pdo->prepare("INSERT INTO eventos (titulo, inicio, usuario_id) VALUES (?, ?, ?)");
    $stmt->execute([$titulo, $data, $usuario_id]);

    echo json_encode(['status' => 'success', 'message' => 'Evento salvo com sucesso!']);
    exit;
}

echo json_encode(['status' => 'error', 'message' => 'Método inválido']);