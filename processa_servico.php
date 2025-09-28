<?php
session_start();
include_once('config.php');

if (empty($_SESSION['id'])) {
    header('Location: login.php');
    exit;
}

function format_value_for_db($value) {
    $value = str_replace('.', '', $value);
    $value = str_replace(',', '.', $value);
    return (float)$value;
}

$usuario_id = $_SESSION['id'];
$acao = $_POST['acao'] ?? '';
$id = $_POST['id'] ?? 0;

$nome_servico = trim($_POST['nome_servico'] ?? '');
$produtos_usados = trim($_POST['produtos_usados'] ?? '');
$especificacao = trim($_POST['especificacao'] ?? '');
$data_prestacao = $_POST['data_prestacao'] ?? date('Y-m-d H:i:s');

$gastos = format_value_for_db($_POST['gastos'] ?? '0');
$horas_gastas = format_value_for_db($_POST['horas_gastas'] ?? '0');
$valor_venda = format_value_for_db($_POST['valor_venda'] ?? '0');

if ($acao === 'cadastrar') {
    $stmt = $pdo->prepare("INSERT INTO servicos_prestados (usuario_id, nome_servico, especificacao, horas_gastas, data_prestacao, gastos, valor_venda, produtos_usados) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->execute([$usuario_id, $nome_servico, $especificacao, $horas_gastas, $data_prestacao, $gastos, $valor_venda, $produtos_usados]);
} elseif ($acao === 'editar' && $id) {
    $stmt = $pdo->prepare("UPDATE servicos_prestados SET nome_servico=?, especificacao=?, horas_gastas=?, data_prestacao=?, gastos=?, valor_venda=?, produtos_usados=? WHERE id = ? AND usuario_id = ?");
    $stmt->execute([$nome_servico, $especificacao, $horas_gastas, $data_prestacao, $gastos, $valor_venda, $produtos_usados, $id, $usuario_id]);
}

header('Location: servicos.php');
exit;