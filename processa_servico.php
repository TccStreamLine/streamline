<?php
session_start();
include_once('config.php');

if (empty($_SESSION['id'])) {
    header('Location: login.php');
    exit;
}

$usuario_id = $_SESSION['id'];
$acao = $_POST['acao'] ?? '';
$id = $_POST['id'] ?? 0;

$gastos = str_replace(',', '.', trim($_POST['gastos'] ?? '0'));
$nome_servico = trim($_POST['nome_servico'] ?? '');
$produtos_usados = trim($_POST['produtos_usados'] ?? '');
$horas_gastas = str_replace(',', '.', trim($_POST['horas_gastas'] ?? '0'));
$especificacao = trim($_POST['especificacao'] ?? '');
$valor_venda = str_replace(',', '.', trim($_POST['valor_venda'] ?? '0'));
$data_prestacao = $_POST['data_prestacao'] ?? date('Y-m-d H:i:s');

if ($acao === 'cadastrar') {
    $stmt = $pdo->prepare("INSERT INTO servicos_prestados (usuario_id, nome_servico, especificacao, horas_gastas, data_prestacao, gastos, valor_venda, produtos_usados) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->execute([$usuario_id, $nome_servico, $especificacao, $horas_gastas, $data_prestacao, $gastos, $valor_venda, $produtos_usados]);
} elseif ($acao === 'editar' && $id) {
    $stmt = $pdo->prepare("UPDATE servicos_prestados SET nome_servico=?, especificacao=?, horas_gastas=?, data_prestacao=?, gastos=?, valor_venda=?, produtos_usados=? WHERE id = ? AND usuario_id = ?");
    $stmt->execute([$nome_servico, $especificacao, $horas_gastas, $data_prestacao, $gastos, $valor_venda, $produtos_usados, $id, $usuario_id]);
}

header('Location: servicos.php');
exit;