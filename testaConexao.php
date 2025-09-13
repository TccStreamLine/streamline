<?php
$host = 'localhost';
$db = 'streamline';
$user = 'root';
$pass = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$db;charset=utf8", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    echo " Conexão com PDO funcionando!";
} catch (PDOException $e) {
    echo " Erro na conexão com PDO: " . $e->getMessage();
}
?>