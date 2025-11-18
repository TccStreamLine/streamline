<?php

$user_role = null;
if (isset($_SESSION['role'])) {
    $user_role = $_SESSION['role']; 
} elseif (isset($_SESSION['id_fornecedor'])) {
    $user_role = 'fornecedor'; 
}
?>
<nav class="sidebar" role="navigation" aria-label="Navegação lateral">
    <!-- Regras responsivas locais para garantir empilhamento em telas pequenas
         Inseridas aqui para serem carregadas após os CSS externos e não alterar o visual desktop -->
    <style>
        @media (max-width: 900px) {
            body {
                display: block !important;
                height: auto !important;
                overflow: visible !important;
            }

            .sidebar {
                position: relative !important;
                width: 100% !important;
                height: auto !important;
                padding: 1rem 0.75rem !important;
                border-right: none !important;
                flex-shrink: 0 !important;
            }

            .main-content {
                display: block !important;
                padding: 1rem !important;
                overflow: visible !important;
                height: auto !important;
            }
        }
    </style>
    <div class="sidebar-logo">
        <a href="sistema.php" class="sidebar-home" aria-label="Ir para o início">
            <img class="logo" src="img/relplogo2.png" alt="Relp! Logo">
        </a>
    </div>

    <?php if ($user_role === 'ceo'): ?>
        <div class="menu-section">
            <h6>MENU</h6>
            <ul class="menu-list">
                <li><a href="sistema.php" class="<?= ($pagina_ativa ?? '') == 'inicio' ? 'active' : '' ?>"><i class="fas fa-home"></i> Início</a></li>
                <li><a href="estoque.php" class="<?= ($pagina_ativa ?? '') == 'estoque' ? 'active' : '' ?>"><i class="fas fa-box"></i> Estoque</a></li>
                <li><a href="agenda.php" class="<?= ($pagina_ativa ?? '') == 'agenda' ? 'active' : '' ?>"><i class="fas fa-calendar-alt"></i> Agenda</a></li>
                <li><a href="fornecedores.php" class="<?= ($pagina_ativa ?? '') == 'fornecedores' ? 'active' : '' ?>"><i class="fas fa-truck"></i> Fornecedores</a></li>
                <li><a href="vendas.php" class="<?= ($pagina_ativa ?? '') == 'vendas' ? 'active' : '' ?>"><i class="fas fa-chart-bar"></i> Vendas</a></li>
                <li><a href="caixa.php" class="<?= ($pagina_ativa ?? '') == 'caixa' ? 'active' : '' ?>"><i class="fas fa-cash-register"></i> Caixa</a></li>
                <li><a href="dashboard.php" class="<?= ($pagina_ativa ?? '') == 'dashboard' ? 'active' : '' ?>"><i class="fas fa-tachometer-alt"></i> Dashboard</a></li>
                <li><a href="servicos.php" class="<?= ($pagina_ativa ?? '') == 'servicos' ? 'active' : '' ?>"><i class="fas fa-concierge-bell"></i> Serviços</a></li>
            </ul>
        </div>
        <div class="menu-section outros">
            <h6>OUTROS</h6>
            <ul class="menu-list">
                <li><a href="loja_planos.php" class="<?= ($pagina_ativa ?? '') == 'loja_planos' ? 'active' : '' ?>"><i class="fas fa-store"></i> Loja de Planos</a></li>
                <li><a href="sair.php"><i class="fas fa-sign-out-alt"></i> Sair</a></li>
            </ul>
        </div>

    <?php elseif ($user_role === 'funcionario'): ?>
        <div class="menu-section">
            <h6>MENU</h6>
            <ul class="menu-list">
                <li><a href="sistema.php" class="<?= ($pagina_ativa ?? '') == 'inicio' ? 'active' : '' ?>"><i class="fas fa-home"></i> Início</a></li>
                <li><a href="estoque.php" class="<?= ($pagina_ativa ?? '') == 'estoque' ? 'active' : '' ?>"><i class="fas fa-box"></i> Estoque</a></li>
                <li><a href="agenda.php" class="<?= ($pagina_ativa ?? '') == 'agenda' ? 'active' : '' ?>"><i class="fas fa-calendar-alt"></i> Agenda</a></li>
                <li><a href="vendas.php" class="<?= ($pagina_ativa ?? '') == 'vendas' ? 'active' : '' ?>"><i class="fas fa-chart-bar"></i> Vendas</a></li>
                <li><a href="caixa.php" class="<?= ($pagina_ativa ?? '') == 'caixa' ? 'active' : '' ?>"><i class="fas fa-cash-register"></i> Caixa</a></li>
                 <li><a href="servicos.php" class="<?= ($pagina_ativa ?? '') == 'servicos' ? 'active' : '' ?>"><i class="fas fa-concierge-bell"></i> Serviços</a></li>
            </ul>
        </div>
        <div class="menu-section outros">
            <h6>OUTROS</h6>
            <ul class="menu-list">
                <li><a href="sair.php"><i class="fas fa-sign-out-alt"></i> Sair</a></li>
            </ul>
        </div>

    <?php elseif ($user_role === 'fornecedor'): ?>
        <div class="menu-section">
            <h6>MENU</h6>
            <ul class="menu-list">
                <li><a href="gerenciar_fornecimento.php" class="<?= ($pagina_ativa ?? '') == 'fornecimento' ? 'active' : '' ?>"><i class="fas fa-truck"></i> Painel de Entregas</a></li>
            </ul>
        </div>
        <div class="menu-section outros">
            <h6>OUTROS</h6>
            <ul class="menu-list">
                <li><a href="sair.php"><i class="fas fa-sign-out-alt"></i> Sair</a></li>
            </ul>
        </div>
    <?php endif; ?>
</nav>
