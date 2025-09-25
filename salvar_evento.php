<?php
session_start();
include_once('config.php'); // Garante que $pdo está disponível

if (empty($_SESSION['id'])) {
    header('Location: login.php');
    exit;
}

$usuario_id = $_SESSION['id'];
$evento_id = $_POST['id'] ?? null;
$titulo = $_POST['titulo'];
$data = $_POST['data'];
$horario = $_POST['horario'];
$descricao = $_POST['descricao'];

// Combina a data e o horário para o campo 'inicio' do tipo DATETIME
$inicio = $data . ' ' . $horario . ':00';

try {
    if ($evento_id) {
        // --- LÓGICA DE UPDATE ---
        $sql = "UPDATE eventos SET titulo = ?, inicio = ?, horario = ?, descricao = ? WHERE id = ? AND usuario_id = ?";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$titulo, $inicio, $horario, $descricao, $evento_id, $usuario_id]);
    } else {
        // --- LÓGICA DE INSERT ---
        $sql = "INSERT INTO eventos (titulo, inicio, horario, descricao, usuario_id) VALUES (?, ?, ?, ?, ?)";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$titulo, $inicio, $horario, $descricao, $usuario_id]);
    }
    
    // Se chegou até aqui, a operação foi um sucesso
    echo "ok";

} catch (PDOException $e) {
    // Em caso de erro, retorna a mensagem de erro do banco de dados
    // Isso é útil para depuração
    echo "Erro ao salvar evento: " . $e->getMessage();
}