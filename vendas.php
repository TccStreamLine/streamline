<?php
session_start();
include_once('config.php');

$pagina_ativa = 'vendas';
$titulo_header = 'Gerenciamento de Vendas';

if (empty($_SESSION['id'])) {
    header('Location: login.php');
    exit;
}

$usuario_id = $_SESSION['id'];
$vendas = [];

try {
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
    <?php include 'sidebar.php'; ?>
    <main class="main-content">
        <?php include 'header.php'; ?>

        <div class="message-container">
            <?php if (isset($_SESSION['msg_sucesso'])): ?><div class="alert alert-success"><?= $_SESSION['msg_sucesso'];
                                                                                            unset($_SESSION['msg_sucesso']); ?></div><?php endif; ?>
            <?php if (isset($_SESSION['msg_erro'])): ?><div class="alert alert-danger"><?= $_SESSION['msg_erro'];
                                                                                        unset($_SESSION['msg_erro']); ?></div><?php endif; ?>
        </div>

        <div class="actions-container">
            <div class="search-bar"><i class="fas fa-search"></i><input type="text" placeholder="Pesquisar Venda..."></div>
            <a href="venda_formulario.php" class="btn-primary"><i class="fas fa-plus"></i> Cadastrar Venda</a>
        </div>

        <div class="table-container">
            <table>
                <thead>
                    <tr>
                        <th>Cód. Venda</th>
                        <th>Data</th>
                        <th>Produtos (Quantidade)</th>
                        <th>Descrição</th>
                        <th>Valor Total</th>
                        <th>Ações</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($vendas)): ?>
                        <tr>
                            <td colspan="6" class="text-center">Nenhuma venda registrada.</td>
                        </tr>
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
            button.addEventListener('click', function(event) {
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
    <script src="main.js"></script>
    <script src="notificacoes.js"></script>
    <script src="notificacoes_fornecedor.js"></script>
</body>

</html>