<?php
session_start();
include_once('config.php');

if (empty($_SESSION['id'])) {
    header('Location: login.php');
    exit;
}

$id = $_GET['id'] ?? 0;
if ($id) {
    $stmt = $pdo->prepare("UPDATE servicos_prestados SET status = 'inativo' WHERE id = ? AND usuario_id = ?");
    $stmt->execute([$id, $_SESSION['id']]);
}

header('Location: servicos.php');
exit;