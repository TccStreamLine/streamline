<?php
session_start();
include_once('config.php'); // Garante que você tenha a variável $pdo da sua conexão

// Define o cabeçalho para garantir que o navegador sempre interprete a resposta como JSON
header('Content-Type: application/json');

// --- VERIFICAÇÃO DE SEGURANÇA ---
// Se o usuário não estiver logado, encerra a execução com status 'Proibido'
if (empty($_SESSION['id'])) {
    http_response_code(403); 
    // Envia uma resposta JSON de erro
    echo json_encode(['error' => 'Não autorizado']);
    exit;
}

// Pega o ID do usuário da sessão e a data da requisição GET
$usuario_id = $_SESSION['id'];
$data = $_GET['data'] ?? ''; // Ex: '2025-09-24'

// Se a data não foi fornecida, retorna um array vazio.
if (!$data) {
    echo json_encode([]);
    exit;
}

// --- CONSULTA CORRIGIDA ---
// Prepara a consulta SQL usando DATE(inicio) para comparar apenas a data.
// Isso resolve o problema de comparação entre um campo DATETIME e uma string de data.
// Adicionado ORDER BY para mostrar os eventos em ordem de horário.
$stmt = $pdo->prepare(
    "SELECT id, titulo, inicio, horario, descricao 
     FROM eventos 
     WHERE usuario_id = ? AND DATE(inicio) = ? 
     ORDER BY horario ASC"
);

// Executa a consulta de forma segura, passando os parâmetros
$stmt->execute([$usuario_id, $data]);

// Busca todos os resultados como um array associativo
$eventos = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Converte o array de resultados para o formato JSON e o envia como resposta
echo json_encode($eventos);