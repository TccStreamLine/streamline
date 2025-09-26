<?php
session_start();
include_once('config.php');

header('Content-Type: application/json');

if (empty($_SESSION['id'])) {
    http_response_code(401);
    // Retorna um objeto JSON padrão para o estado "não encontrado/sem eventos"
    echo json_encode(['total' => 0, 'eventos' => []]);
    exit;
}

$usuario_id = $_SESSION['id'];
$hoje = date('Y-m-d'); // Pega a data de hoje no formato AAAA-MM-DD

// --- CONSULTA CORRIGIDA ---
// 1. Usamos DATE(inicio) para comparar apenas a data.
// 2. Selecionamos mais campos (horario, descricao) para montar os cards.
// 3. Ordenamos por horário.
$stmt = $pdo->prepare(
    "SELECT id, titulo, horario, descricao 
     FROM eventos 
     WHERE usuario_id = ? AND DATE(inicio) = ? 
     ORDER BY horario ASC"
);

$stmt->execute([$usuario_id, $hoje]);
$eventos = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Retorna o total de eventos e um array com os detalhes de cada um
echo json_encode([
    'total' => count($eventos),
    'eventos' => $eventos
]);