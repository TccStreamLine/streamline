<?php
session_start();
include_once('config.php');

// !! CONFIRME QUE SUA CHAVE DE API ESTÁ AQUI !!
define('GEMINI_API_KEY', 'AIzaSyDB41H-kbuT8mkN3JP_2DbHAFyZKkZJQJY');

if (empty($_SESSION['id'])) {
    http_response_code(401);
    echo json_encode(['resposta' => 'Erro: Usuário não autenticado.']);
    exit;
}
$usuario_id = $_SESSION['id'];

// --- COLETA DE DADOS ---
$faturamento_mes_stmt = $pdo->prepare("SELECT SUM(valor_total) as total FROM vendas WHERE usuario_id = ? AND MONTH(data_venda) = MONTH(CURDATE()) AND YEAR(data_venda) = YEAR(CURDATE()) AND status = 'finalizada'");
$faturamento_mes_stmt->execute([$usuario_id]);
$faturamento_mes = $faturamento_mes_stmt->fetchColumn();

$vendas_hoje_stmt = $pdo->prepare("SELECT COUNT(id) as total FROM vendas WHERE usuario_id = ? AND DATE(data_venda) = CURDATE() AND status = 'finalizada'");
$vendas_hoje_stmt->execute([$usuario_id]);
$vendas_hoje = $vendas_hoje_stmt->fetchColumn();

$estoque_baixo_stmt = $pdo->prepare("SELECT COUNT(id) as total FROM produtos WHERE quantidade_estoque <= quantidade_minima");
$estoque_baixo_stmt->execute();
$estoque_baixo = $estoque_baixo_stmt->fetchColumn();

$top_produtos_stmt = $pdo->prepare("SELECT p.nome, SUM(vi.quantidade) as total_vendido FROM venda_itens vi JOIN produtos p ON vi.produto_id = p.id JOIN vendas v ON vi.venda_id = v.id WHERE v.usuario_id = ? AND v.status = 'finalizada' GROUP BY p.nome ORDER BY total_vendido DESC LIMIT 5");
$top_produtos_stmt->execute([$usuario_id]);
$top_produtos = $top_produtos_stmt->fetchAll(PDO::FETCH_ASSOC);

$produto_campeao_stmt = $pdo->prepare("SELECT p.nome FROM venda_itens vi JOIN produtos p ON vi.produto_id = p.id JOIN vendas v ON vi.venda_id = v.id WHERE v.usuario_id = ? AND v.status = 'finalizada' GROUP BY p.nome ORDER BY SUM(vi.quantidade) DESC LIMIT 1");
$produto_campeao_stmt->execute([$usuario_id]);
$produto_campeao = $produto_campeao_stmt->fetchColumn();

// --- PREPARAÇÃO DO CONTEXTO PARA A IA ---
$dados_para_ia = [
    'faturamento_mes_atual' => (float) $faturamento_mes,
    'vendas_hoje' => (int) $vendas_hoje,
    'produtos_com_estoque_baixo' => (int) $estoque_baixo,
    'produto_mais_vendido' => $produto_campeao,
    'top_5_produtos_mais_vendidos' => $top_produtos
];
$contexto_json = json_encode($dados_para_ia, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);

// --- RECEBENDO A PERGUNTA DO USUÁRIO ---
$json_input = file_get_contents('php://input');
$data_input = json_decode($json_input);
$pergunta_usuario = $data_input->pergunta ?? '';

if (empty($pergunta_usuario)) {
    echo json_encode(['resposta' => 'Por favor, faça uma pergunta.']);
    exit;
}

// --- MONTAGEM DO PROMPT PARA O GEMINI ---
$prompt = "Você é 'Relp!', um assistente de IA amigável e especialista em análise de dados de negócios. Sua tarefa é responder à pergunta do usuário de forma clara e objetiva, baseando-se estritamente nos dados fornecidos no contexto JSON abaixo. Não invente informações. Se a resposta não estiver nos dados, diga que você não tem essa informação.

Contexto dos dados da empresa:
{$contexto_json}

Pergunta do usuário:
\"{$pergunta_usuario}\"

Sua resposta:";

// --- COMUNICAÇÃO COM A API DO GEMINI USANDO cURL ---

// AQUI ESTÁ A LINHA CORRIGIDA
$url = 'https://generativelanguage.googleapis.com/v1beta/models/gemini-1.5-flash-latest:generateContent?key=' . GEMINI_API_KEY;

$data_payload = json_encode([
    'contents' => [
        [
            'parts' => [
                ['text' => $prompt]
            ]
        ]
    ]
]);

$ch = curl_init($url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, $data_payload);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); 

$response = curl_exec($ch);
$httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

// --- PROCESSAMENTO DA RESPOSTA E ENVIO PARA O FRONTEND ---
if ($httpcode == 200) {
    $result = json_decode($response, true);
    $texto_da_ia = $result['candidates'][0]['content']['parts'][0]['text'] ?? 'Desculpe, não consegui processar sua pergunta no momento.';
    echo json_encode(['resposta' => $texto_da_ia]);
} else {
    echo json_encode(['resposta' => 'Erro ao contatar a IA. Código: ' . $httpcode . ' | Resposta: ' . $response]);
}