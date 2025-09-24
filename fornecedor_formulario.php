<?php
session_start();
include_once('config.php');

if (empty($_SESSION['id'])) {
    header('Location: login.php');
    exit;
}

$modo_edicao = false;
$fornecedor_para_editar = [];
$titulo_pagina = "Cadastrar Novo Fornecedor";

if (isset($_GET['id']) && !empty($_GET['id'])) {
    $modo_edicao = true;
    $id = filter_var($_GET['id'], FILTER_VALIDATE_INT);
    $titulo_pagina = "Editar Fornecedor";

    $stmt = $pdo->prepare("SELECT * FROM fornecedores WHERE id = :id");
    $stmt->execute([':id' => $id]);
    $fornecedor_para_editar = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$fornecedor_para_editar) {
        $_SESSION['msg_erro'] = "Fornecedor não encontrado.";
        header('Location: fornecedores.php');
        exit;
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
    <link rel="stylesheet" href="css/formulario_fornecedor.css">
</head>

<body>
    <nav class="sidebar">
        <div class="sidebar-logo">
            <img class="logo" src="img/relplogo.png" alt="Relp! Logo" style="width: 100px;">
        </div>
        <div class="menu-section">
            <h6>MENU</h6>
            <ul class="menu-list">
                <li><a href="sistema.php"><i class="fas fa-home"></i> Início</a></li>
                <li><a href="estoque.php"><i class="fas fa-box"></i> Estoque</a></li>
                <li><a href="agenda.php"><i class="fas fa-calendar-alt"></i> Agenda</a></li>
                <li><a href="fornecedores.php" class="active"><i class="fas fa-truck"></i> Fornecimento</a></li>
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
            <h2>Fornecimento > <?= $titulo_pagina ?></h2>
            <div class="user-profile"><span><?= htmlspecialchars($nome_empresa) ?></span>
                <div class="avatar"><i class="fas fa-user"></i></div>
            </div>
        </header>

        <div class="form-container-figma">
            <h3 class="form-title-figma"><?= $modo_edicao ? 'EDITAR DADOS DO FORNECEDOR' : 'CADASTRAR NOVO FORNECEDOR' ?></h3>
            <form action="processa_fornecedor.php" method="POST">
                <input type="hidden" name="acao" value="<?= $modo_edicao ? 'editar' : 'cadastrar' ?>">
                <input type="hidden" name="fornecedor_id" value="<?= $fornecedor_para_editar['id'] ?? '' ?>">

                <div class="input-group-figma">
                    <i class="fas fa-building"></i>
                    <input type="text" name="razao_social" placeholder="Razão Social*" required value="<?= htmlspecialchars($fornecedor_para_editar['razao_social'] ?? '') ?>">
                </div>
                <div class="input-group-figma">
                    <i class="fas fa-id-card"></i>
                    <input type="text" name="cnpj" placeholder="CNPJ*" required value="<?= htmlspecialchars($fornecedor_para_editar['cnpj'] ?? '') ?>">
                </div>
                <div class="input-group-figma">
                    <i class="fas fa-envelope"></i>
                    <input type="email" name="email" placeholder="E-mail" value="<?= htmlspecialchars($fornecedor_para_editar['email'] ?? '') ?>">
                </div>
                <div class="input-group-figma">
                    <i class="fas fa-phone"></i>
                    <input type="text" name="telefone" placeholder="Telefone" value="<?= htmlspecialchars($fornecedor_para_editar['telefone'] ?? '') ?>">
                </div>

                <div class="form-actions-figma">
                    <button type="submit" class="btn-figma-primary">
                        <?= $modo_edicao ? 'SALVAR ALTERAÇÕES' : 'CADASTRAR AQUI' ?>
                    </button>
                </div>
            </form>
        </div>
    </main>
</body>

</html>