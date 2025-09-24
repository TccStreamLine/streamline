<?php
session_start();
include_once('config.php');

if (empty($_SESSION['id'])) {
    header('Location: login.php');
    exit;
}

$modo_edicao = false;
$categoria = [];
$titulo_pagina = "Cadastrar Categoria";
$titulo_formulario = "CADASTRO DE CATEGORIA";
$nome_botao = "CADASTRAR AQUI";

if (isset($_GET['id']) && !empty($_GET['id'])) {
    $id = filter_var($_GET['id'], FILTER_VALIDATE_INT);
    if ($id) {
        $modo_edicao = true;
        $titulo_pagina = "Editar Categoria";
        $titulo_formulario = "EDIÇÃO DE CATEGORIA";
        $nome_botao = "SALVAR ALTERAÇÕES";

        $stmt = $pdo->prepare("SELECT * FROM categorias WHERE id = :id");
        $stmt->execute([':id' => $id]);
        $categoria = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$categoria) {
            $_SESSION['msg_erro'] = "Categoria não encontrada.";
            header('Location: categorias.php');
            exit;
        }
    }
}

$nome_empresa = $_SESSION['nome_empresa'] ?? 'Empresa';
?>
<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <title><?= $titulo_pagina ?> - Streamline</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="css/sistema.css">
    <link rel="stylesheet" href="css/formularios.css">
</head>

<body>
    <nav class="sidebar">
        <div class="sidebar-logo"><img class="logo" src="img/relplogo.png" alt="Relp! Logo" style="width: 100px;"></div>
        <div class="menu-section">
            <h6>MENU</h6>
            <ul class="menu-list">
                <li><a href="sistema.php"><i class="fas fa-home"></i> Início</a></li>
                <li><a href="estoque.php" class="active"><i class="fas fa-box"></i> Estoque</a></li>
                <li><a href="#"><i class="fas fa-calendar-alt"></i> Agenda</a></li>
                <li><a href="fornecedores.php"><i class="fas fa-truck"></i> Fornecimento</a></li>
                <li><a href="vendas.php"><i class="fas fa-chart-bar"></i> Vendas</a></li>
                <li><a href="caixa.php"><i class="fas fa-cash-register"></i> Caixa</a></li>
                <li><a href="dashboard.php"><i class="fas fa-tachometer-alt"></i> Dashboard</a></li>
                <li><a href="#"><i class="fas fa-file-invoice-dollar"></i> Nota Fiscal</a></li>
                <li><a href="#"><i class="fas fa-concierge-bell"></i> Serviços</a></li>
            </ul>
        </div>
        <div class="menu-section outros">
            <h6>OUTROS</h6>
            <ul class="menu-list">
                <li><a href="#"><i class="fas fa-store"></i> Loja de Planos</a></li>
                <li><a href="sair.php"><i class="fas fa-sign-out-alt"></i> Sair</a></li>
            </ul>
        </div>
    </nav>
    <main class="main-content">
        <header class="main-header">
            <h2>Estoque > <?= $titulo_pagina ?></h2>
            <div class="user-profile"><span><?= htmlspecialchars($nome_empresa) ?></span>
                <div class="avatar"><i class="fas fa-user"></i></div>
            </div>
        </header>
        <div class="form-container">
            <h3><?= $titulo_formulario ?></h3>
            <form action="processa_categoria.php" method="POST">
                <input type="hidden" name="acao" value="<?= $modo_edicao ? 'editar' : 'cadastrar' ?>">
                <input type="hidden" name="id" value="<?= $categoria['id'] ?? '' ?>">

                <div class="form-group">
                    <input type="text" id="nome" name="nome" required placeholder=" " value="<?= htmlspecialchars($categoria['nome'] ?? '') ?>">
                    <label for="nome">Nome da categoria</label>
                </div>
                <div class="form-actions">
                    <button type="submit" class="btn-primary"><?= $nome_botao ?></button>
                </div>
            </form>
        </div>
    </main>
</body>

</html>