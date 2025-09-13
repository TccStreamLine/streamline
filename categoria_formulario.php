<?php
session_start();
include_once('config.php');

if (empty($_SESSION['id'])) {
    header('Location: login.php');
    exit;
}
$nome_empresa = $_SESSION['nome_empresa'] ?? 'Empresa';
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Cadastrar Categoria - Streamline</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="css/sistema.css">
    <link rel="stylesheet" href="css/formularios.css"> 
</head>
<body>
    <nav class="sidebar">
        </nav>

    <main class="main-content">
        <header class="main-header">
            <h2>Estoque > Cadastro de Categoria</h2>
            <div class="user-profile"><span><?= htmlspecialchars($nome_empresa) ?></span><div class="avatar"><i class="fas fa-user"></i></div></div>
        </header>

        <div class="form-container">
            <h3>CADASTRO DE CATEGORIA</h3>
            <form action="processa_categoria.php" method="POST">
                <input type="hidden" name="acao" value="cadastrar">
                <div class="form-group">
                    <input type="text" id="nome" name="nome" required placeholder=" ">
                    <label for="nome">Crie aqui sua nova categoria</label>
                </div>
                <div class="form-actions">
                    <button type="submit" class="btn-primary">CADASTRAR AQUI</button>
                </div>
            </form>
        </div>
    </main>
</body>
</html>