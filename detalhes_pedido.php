<?php
session_start();
include_once('config.php');

if (empty($_SESSION['id_fornecedor'])) {
    header('Location: login.php');
    exit;
}

$pedido_id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
$fornecedor_id = $_SESSION['id_fornecedor'];

if (!$pedido_id) {
    header('Location: gerenciar_fornecimento.php');
    exit;
}


$sql_pedido = "SELECT pf.*, u.nome_empresa 
               FROM pedidos_fornecedor pf 
               JOIN usuarios u ON pf.usuario_id = u.id
               WHERE pf.id = ? AND pf.fornecedor_id = ?";
$stmt = $pdo->prepare($sql_pedido);
$stmt->execute([$pedido_id, $fornecedor_id]);
$pedido = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$pedido) {
    $_SESSION['msg_erro'] = "Pedido não encontrado.";
    header('Location: gerenciar_fornecimento.php');
    exit;
}


$sql_itens = "SELECT pfi.*, p.nome, p.codigo_barras 
              FROM pedido_fornecedor_itens pfi 
              JOIN produtos p ON pfi.produto_id = p.id
              WHERE pfi.pedido_id = ?";
$stmt_itens = $pdo->prepare($sql_itens);
$stmt_itens->execute([$pedido_id]);
$itens = $stmt_itens->fetchAll(PDO::FETCH_ASSOC);

$pagina_ativa = 'fornecimento';
$titulo_header = 'Processar Pedido #' . str_pad($pedido['id'], 4, '0', STR_PAD_LEFT);
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detalhes do Pedido - Streamline</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="css/sistema.css">
    <link rel="stylesheet" href="css/formulario_fornecedor.css"> <style>
        .resumo-pedido {
            background: #fff;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.05);
            margin-bottom: 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .lista-itens {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
            background: white;
            border-radius: 8px;
            overflow: hidden;
        }
        .lista-itens th, .lista-itens td {
            padding: 12px 15px;
            text-align: left;
            border-bottom: 1px solid #eee;
        }
        .lista-itens th { background-color: #F3F4F6; font-weight: 600; color: #374151; }
        .btn-confirmar {
            background-color: #10B981; /* Verde */
            color: white;
            width: 100%;
            padding: 15px;
            font-size: 1.1rem;
            font-weight: bold;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            transition: 0.3s;
        }
        .btn-confirmar:hover { background-color: #059669; }
        .upload-area {
            border: 2px dashed #ccc;
            padding: 20px;
            text-align: center;
            background: #fafafa;
            border-radius: 8px;
            margin-bottom: 20px;
        }
    </style>
</head>
<body>
    <?php include 'sidebar.php'; ?>
    <main class="main-content">
        <?php include 'header.php'; ?>

        <div class="resumo-pedido">
            <div>
                <h4>Pedido #<?= str_pad($pedido['id'], 4, '0', STR_PAD_LEFT) ?></h4>
                <p>Data: <strong><?= date('d/m/Y', strtotime($pedido['data_pedido'])) ?></strong></p>
                <p>Cliente: <strong><?= htmlspecialchars($pedido['nome_empresa']) ?></strong></p>
            </div>
            <div>
                <h3>Total: R$ <?= number_format($pedido['valor_total_pedido'], 2, ',', '.') ?></h3>
            </div>
        </div>

        <form action="processa_entrega_pedido.php" method="POST" enctype="multipart/form-data">
            <input type="hidden" name="pedido_id" value="<?= $pedido['id'] ?>">
            
            <h3>Itens para Conferência</h3>
            <table class="lista-itens">
                <thead>
                    <tr>
                        <th>Produto</th>
                        <th>Cód. Barras</th>
                        <th>Qtd. Solicitada</th>
                        <th>Valor Unit. (Acordado)</th>
                        <th>Subtotal</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($itens as $item): ?>
                    <tr>
                        <td><?= htmlspecialchars($item['nome']) ?></td>
                        <td><?= htmlspecialchars($item['codigo_barras']) ?></td>
                        <td>
                            <strong><?= $item['quantidade_pedida'] ?></strong>
                            <input type="hidden" name="itens[<?= $item['produto_id'] ?>]" value="<?= $item['quantidade_pedida'] ?>">
                        </td>
                        <td>R$ <?= number_format($item['valor_unitario_pago'], 2, ',', '.') ?></td>
                        <td>R$ <?= number_format($item['quantidade_pedida'] * $item['valor_unitario_pago'], 2, ',', '.') ?></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>

            <div class="upload-area">
                <label for="nota_fiscal">
                    <i class="fas fa-file-invoice-dollar" style="font-size: 2rem; color: #6D28D9;"></i><br>
                    <strong>Anexar Nota Fiscal / Comprovante</strong><br>
                    <span style="font-size: 0.8rem; color: #666;">(Opcional, mas recomendado)</span>
                </label>
                <input type="file" name="nota_fiscal_pedido" id="nota_fiscal" style="display: block; margin: 10px auto;">
            </div>

            <button type="submit" class="btn-confirmar">
                <i class="fas fa-check-circle"></i> Confirmar Entrega e Atualizar Estoque
            </button>
            <br><br>
            <a href="gerenciar_fornecimento.php" style="color: #666; text-decoration: none;">&larr; Voltar</a>
        </form>
    </main>
    <script src="main.js"></script>
</body>
</html>