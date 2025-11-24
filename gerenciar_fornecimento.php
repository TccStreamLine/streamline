<?php
session_start();
include_once('config.php');

if (empty($_SESSION['id_fornecedor'])) {
    header('Location: login.php');
    exit;
}

$pagina_ativa = 'fornecimento';
$titulo_header = 'Fornecimento > Gerenciar Fornecimento';
$fornecedor_id = $_SESSION['id_fornecedor'];

$sql_pedidos = "SELECT 
                    pf.id, 
                    pf.data_pedido, 
                    pf.valor_total_pedido, 
                    pf.status_pedido,
                    u.nome_empresa,
                    (SELECT COUNT(*) FROM pedido_fornecedor_itens WHERE pedido_id = pf.id) as total_itens
                FROM pedidos_fornecedor pf
                JOIN usuarios u ON pf.usuario_id = u.id
                WHERE pf.fornecedor_id = ? AND pf.status_pedido = 'Pendente'
                ORDER BY pf.data_pedido ASC";
$stmt_pedidos = $pdo->prepare($sql_pedidos);
$stmt_pedidos->execute([$fornecedor_id]);
$pedidos_recebidos = $stmt_pedidos->fetchAll(PDO::FETCH_ASSOC);

?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gerenciar Fornecimento - Streamline</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="css/sistema.css">
    <link rel="stylesheet" href="css/estoque.css">
    <style>
        .section-title {
            font-size: 1.2rem;
            color: #333;
            margin: 2rem 0 1rem 0;
            border-left: 5px solid #6D28D9;
            padding-left: 10px;
            font-weight: bold;
        }
        .status-badge {
            padding: 5px 10px;
            border-radius: 15px;
            font-size: 0.85rem;
            font-weight: bold;
        }
        .status-pendente { background-color: #FEF3C7; color: #D97706; }
        .btn-ver-pedido {
            background-color: #6D28D9;
            color: white;
            padding: 8px 15px;
            border-radius: 5px;
            text-decoration: none;
            font-size: 0.9rem;
            display: inline-flex;
            align-items: center;
            gap: 5px;
        }
        .btn-ver-pedido:hover { background-color: #5B21B6; }
    </style>
</head>
<body>
    <?php include 'sidebar.php'; ?>
    
    <main class="main-content">
        <?php include 'header.php'; ?>

        <div class="message-container">
            <?php if (isset($_SESSION['msg_sucesso'])): ?>
                <div class="alert alert-success"><?= $_SESSION['msg_sucesso']; unset($_SESSION['msg_sucesso']); ?></div>
            <?php endif; ?>
            <?php if (isset($_SESSION['msg_erro'])): ?>
                <div class="alert alert-danger"><?= $_SESSION['msg_erro']; unset($_SESSION['msg_erro']); ?></div>
            <?php endif; ?>
        </div>

        <h3 class="section-title">PEDIDOS RECEBIDOS DO CEO</h3>
        <div class="table-container">
            <table>
                <thead>
                    <tr>
                        <th>ID Pedido</th>
                        <th>Data</th>
                        <th>Solicitante</th>
                        <th>Qtd. Itens</th>
                        <th>Valor Total</th>
                        <th>Status</th>
                        <th>Ação</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($pedidos_recebidos)): ?>
                        <tr><td colspan="7" class="text-center">Você não possui novos pedidos pendentes.</td></tr>
                    <?php else: ?>
                        <?php foreach ($pedidos_recebidos as $pedido): ?>
                        <tr>
                            <td>#<?= str_pad($pedido['id'], 4, '0', STR_PAD_LEFT) ?></td>
                            <td><?= date('d/m/Y', strtotime($pedido['data_pedido'])) ?></td>
                            <td><?= htmlspecialchars($pedido['nome_empresa']) ?></td>
                            <td><?= $pedido['total_itens'] ?> produto(s)</td>
                            <td>R$ <?= number_format($pedido['valor_total_pedido'], 2, ',', '.') ?></td>
                            <td><span class="status-badge status-pendente">Pendente</span></td>
                            <td>
                                <a href="detalhes_pedido.php?id=<?= $pedido['id'] ?>" class="btn-ver-pedido">
                                    <i class="fas fa-box-open"></i> Processar Entrega
                                </a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </main>
    <script src="main.js"></script>
</body>
</html>