<?php
session_start();
include_once('config.php');

if (empty($_SESSION['id'])) {
    http_response_code(403);
    exit('Não autorizado');
}

$usuario_id = $_SESSION['id'];
$data = $_POST['data'] ?? '';
$titulo = $_POST['titulo'] ?? '';
$horario = $_POST['horario'] ?? '';      // Adicione esta linha
$descricao = $_POST['descricao'] ?? '';  // Adicione esta linha

if ($data && $titulo && $horario) {
    $inicio = $data;
    $stmt = $pdo->prepare("INSERT INTO eventos (usuario_id, titulo, inicio, horario, descricao) VALUES (?, ?, ?, ?, ?)");
    $stmt->execute([$usuario_id, $titulo, $inicio, $horario, $descricao]);
    echo "ok";
} else {
    http_response_code(400);
    echo "Dados inválidos";
}