<?php
session_start();
include_once('config.php');

if (empty($_SESSION['id'])) {
    header('Location: login.php');
    exit;
}

$usuario_id = $_SESSION['id'];
$vendas = [];

try {
    // A alteração está na linha abaixo, dentro do GROUP_CONCAT
    $sql = "SELECT v.id, v.data_venda, v.valor_total, v.descricao, GROUP_CONCAT(CONCAT(p.nome, ' (', vi.quantidade, 'x)') SEPARATOR ', ') as produtos
            FROM vendas v
            LEFT JOIN venda_itens vi ON v.id = vi.venda_id
            LEFT JOIN produtos p ON vi.produto_id = p.id
            WHERE v.usuario_id = ? AND v.status = 'finalizada'
            GROUP BY v.id
            ORDER BY v.data_venda DESC";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$usuario_id]);
    $vendas = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $_SESSION['msg_erro'] = "Erro ao buscar vendas.";
}

$nome_empresa = $_SESSION['nome_empresa'] ?? 'Empresa';
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Vendas - Sistema de Gerenciamento</title>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
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
                <li><a href="sistema.php"><i class="fas fa-home"></i> Início</a></li>
                <li><a href="estoque.php"><i class="fas fa-box"></i> Estoque</a></li>
                <li><a href="agenda.php"><i class="fas fa-calendar-alt"></i> Agenda</a></li>
                <li><a href="fornecedores.php"><i class="fas fa-truck"></i> Fornecimento</a></li>
                <li><a href="vendas.php" class="active"><i class="fas fa-chart-bar"></i> Vendas</a></li>
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
            <h2>Gerenciamento de Vendas</h2>
            <div class="user-profile"><span><?= htmlspecialchars($nome_empresa) ?></span><div class="avatar"><i class="fas fa-user"></i></div></div>
        </header>
        
        <div class="message-container">
            <?php if (isset($_SESSION['msg_sucesso'])): ?><div class="alert alert-success"><?= $_SESSION['msg_sucesso']; unset($_SESSION['msg_sucesso']); ?></div><?php endif; ?>
            <?php if (isset($_SESSION['msg_erro'])): ?><div class="alert alert-danger"><?= $_SESSION['msg_erro']; unset($_SESSION['msg_erro']); ?></div><?php endif; ?>
        </div>
        
        <div class="actions-container">
            <div class="search-bar"><i class="fas fa-search"></i><input type="text" placeholder="Pesquisar Venda..."></div>
            <a href="venda_formulario.php" class="btn-primary"><i class="fas fa-plus"></i> Cadastrar Venda</a>
        </div>

        <div class="table-container">
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Data</th>
                        <th>Produtos (Quantidade)</th>
                        <th>Descrição</th>
                        <th>Valor Total</th>
                        <th>Ações</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($vendas)): ?>
                        <tr><td colspan="6" class="text-center">Nenhuma venda registrada.</td></tr>
                    <?php else: ?>
                        <?php foreach ($vendas as $venda): ?>
                            <tr>
                                <td><?= htmlspecialchars($venda['id']) ?></td>
                                <td><?= date('d/m/Y H:i', strtotime($venda['data_venda'])) ?></td>
                                <td><?= htmlspecialchars($venda['produtos'] ?? 'N/A') ?></td>
                                <td><?= htmlspecialchars($venda['descricao']) ?></td>
                                <td>R$ <?= number_format((float)$venda['valor_total'], 2, ',', '.') ?></td>
                                <td class="actions">
                                    <a href="venda_formulario.php?id=<?= $venda['id'] ?>" class="btn-action btn-edit"><i class="fas fa-pencil-alt"></i></a>
                                    <a href="excluir_venda.php?id=<?= $venda['id'] ?>" class="btn-action btn-delete"><i class="fas fa-trash-alt"></i></a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </main>
    <script>
        document.querySelectorAll('.btn-delete').forEach(button => {
            button.addEventListener('click', function (event) {
                event.preventDefault();
                const url = this.href;
                Swal.fire({
                    title: 'Tem certeza?',
                    text: "A venda será cancelada e os produtos retornarão ao estoque. Esta ação não pode ser desfeita.",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#6B7280',
                    confirmButtonText: 'Sim, cancelar venda!',
                    cancelButtonText: 'Voltar'
                }).then((result) => {
                    if (result.isConfirmed) {
                        window.location.href = url;
                    }
                });
            });
        });
    </script>
</body>
</html>