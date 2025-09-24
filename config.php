<?php

date_default_timezone_set('America/Sao_Paulo');

$host = 'localhost';
$db = 'streamline';
$user = 'root';
$pass = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$db;charset=utf8", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->exec("SET lc_time_names = 'pt_BR'"); // Linha adicionada
    echo '';
} catch (PDOException $e) {
    die("Erro na conexão com o banco de dados: " . $e->getMessage());
}
?>