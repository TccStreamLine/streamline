<?php

declare(strict_types=1);

ini_set('display_errors', '1');
ini_set('display_startup_errors', '1');
error_reporting(E_ALL);

session_start();

if (empty($_SESSION['id'])) {
    session_unset();
    session_destroy();
    header('Location: login.php');
    exit;
}

$nome_empresa = $_SESSION['nome_empresa'] ?? 'Empresa';

?>
<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Início - Sistema de Gerenciamento</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <link rel="stylesheet" href="css/sistema.css">
    <link rel="stylesheet" href="css/estoque.css">
</head>

<body>
    <nav class="sidebar">
        <div class="sidebar-logo">
            <img class="logo" src="img/relplogo.png" alt="Relp! Logo" style="width: 100px;">
        </div>
        <div class="menu-section">
            <h6>MENU</h6>
            <ul class="menu-list">
                <li><a href="sistema.php" class="active"><i class="fas fa-home"></i> Início</a></li>
                <li><a href="estoque.php"><i class="fas fa-box"></i> Estoque</a></li>
                <li><a href="agenda.php"><i class="fas fa-calendar-alt"></i> Agenda</a></li>
                <li><a href="fornecedores.php"><i class="fas fa-truck"></i> Fornecimento</a></li>
                <li><a href="vendas.php"><i class="fas fa-chart-bar"></i> Vendas</a></li>
                <li><a href="caixa.php"><i class="fas fa-cash-register"></i> Caixa</a></li>
                <li><a href="dashboard.php"><i class="fas fa-tachometer-alt"></i> Dashboard</a></li>
                <li><a href="#"><i class="fas fa-file-invoice-dollar"></i> Nota Fiscal</a></li>
                <li><a href="servicos.php"><i class="fas fa-concierge-bell"></i> Serviços</a></li>
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
            <h2>Início</h2>
            <div class="user-profile">
                <div class="notification-icon">
                    <i class="fas fa-bell"></i>
                </div>
                <span>Sua empresa</span>
                <div class="avatar">
                    <i class="fas fa-user"></i>
                </div>
            </div>
        </header>

        <section class="welcome-section">
            <h1>Olá, <?= htmlspecialchars($nome_empresa) ?>...</h1>
            <p>Seja bem-vindo ao seu sistema inteligente de gerenciamento!</p>
        </section>

        <section class="action-cards">
            <a href="funcionario_formulario.php" class="action-card">
                <i class="fas fa-user-plus"></i>
                <h3>Cadastre seus funcionários</h3>
            </a>
            <a href="caixa.php" class="action-card">
                <i class="fas fa-cash-register"></i>
                <h3>Acesse seu próprio caixa prático e móvel</h3>
            </a>
            <a href="estoque.php" class="action-card">
                <i class="fas fa-boxes-stacked"></i>
                <h3>Edite seu estoque de forma rápida e inteligente</h3>
            </a>
        </section>

        <section class="pricing-plans">
            <div class="plan-card">
                <h4>Starter</h4>
                <p>Acesso á 7 dias de teste gratuito do sistema de gerenciamento de micro e pequenas empresas.</p>
                <div class="price-free">Gratis</div>
                <form action="processa_plano.php" method="POST">
                    <input type="hidden" name="plano" value="starter">
                    <button type="submit" class="plan-button primary">Comece Aqui!</button>
                </form>
            </div>
            <div class="plan-card pro">
                <div class="recommended-badge">Recomendado</div>
                <h4>Pro</h4>
                <p>Acesso mensal ao sistema de gerenciamento somente com sua versão web.</p>
                <div class="price">R$49,90 <span>/ mês</span></div>
                <form action="processa_plano.php" method="POST">
                    <input type="hidden" name="plano" value="pro">
                    <button type="submit" class="plan-button">Comece Aqui!</button>
                </form>
            </div>
            <div class="plan-card">
                <h4>Business+</h4>
                <p>Acesso ao sistema de gerenciamento web + um aplicativo para CEOs focado na vizualição rápida de
                    informações.</p>
                <div class="price">R$74,90 <span>/ mês</span></div>
                <form action="processa_plano.php" method="POST">
                    <input type="hidden" name="plano" value="business_plus">
                    <button type="submit" class="plan-button primary">Comece Aqui!</button>
                </form>
            </div>
        </section>

    </main>

</body>

</html>