<?php
$user_role = null;
$nome_exibicao = '';

if (isset($_SESSION['id_fornecedor']) && !isset($_SESSION['id'])) {
    $user_role = 'fornecedor';
    $nome_exibicao = $_SESSION['nome_fornecedor'] ?? 'Fornecedor';
} elseif (isset($_SESSION['role']) && $_SESSION['role'] === 'funcionario') {
    $user_role = 'funcionario';
    $nome_exibicao = $_SESSION['funcionario_nome'] ?? 'Funcionário';
} elseif (isset($_SESSION['role']) && $_SESSION['role'] === 'ceo') {
    $user_role = 'ceo';
    $nome_exibicao = $_SESSION['nome_empresa'] ?? 'Empresa';
}
?>
<header class="main-header">

    <h2><?= $titulo_header ?? 'Painel' ?></h2>
    
    <div class="user-profile">
        <?php if ($user_role === 'ceo' || $user_role === 'funcionario'): ?>
            <div class="notification-icon" id="notificacao-sino">
                <i class="fas fa-bell"></i>
                <span class="badge" id="notificacao-badge" style="display: none;"></span>
            </div>
            <div class="notificacao-painel" id="notificacao-painel" style="display: none;">
                <div class="painel-header">
                    <a href="agenda.php">
                        <strong>Você tem eventos hoje!</strong><br>
                        Clique para ver sua agenda completa.
                    </a>
                </div>
                <div class="painel-corpo" id="notificacao-lista"></div>
            </div>

        <?php elseif ($user_role === 'fornecedor'): ?>
            <div class="notification-icon" id="notificacao-sino-fornecedor">
                <i class="fas fa-bell"></i>
                <span class="badge" id="notificacao-badge-fornecedor" style="display: none;"></span>
            </div>
            <div class="notificacao-painel" id="notificacao-painel-fornecedor" style="display: none;">
                <div class="painel-header">
                    <a href="gerenciar_fornecimento.php">
                        <strong>Produtos com Estoque Baixo!</strong><br>
                        Clique para ver a lista completa.
                    </a>
                </div>
                <div class="painel-corpo" id="notificacao-lista-fornecedor"></div>
            </div>
        <?php endif; ?>

        <span><?= htmlspecialchars($nome_exibicao) ?></span>
        <div class="avatar"><i class="fas fa-user"></i></div>
    </div>
</header>