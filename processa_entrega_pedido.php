<?php
session_start();
include_once('config.php');

if (empty($_SESSION['id_fornecedor']) || $_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: login.php');
    exit;
}

$fornecedor_id = $_SESSION['id_fornecedor'];
$pedido_id = filter_input(INPUT_POST, 'pedido_id', FILTER_VALIDATE_INT);
$itens_post = $_POST['itens'] ?? []; 

if (!$pedido_id || empty($itens_post)) {
    $_SESSION['msg_erro'] = "Dados inválidos para processar a entrega.";
    header('Location: gerenciar_fornecimento.php');
    exit;
}

try {
    $pdo->beginTransaction();


    $path_da_nota_fiscal = NULL;
    if (isset($_FILES['nota_fiscal_pedido']) && $_FILES['nota_fiscal_pedido']['error'] == 0) {
        $target_dir = "uploads/notas_fiscais/";
        if (!is_dir($target_dir)) {
            mkdir($target_dir, 0777, true);
        }
        $file_extension = strtolower(pathinfo($_FILES["nota_fiscal_pedido"]["name"], PATHINFO_EXTENSION));
        $target_file = $target_dir . uniqid('nf_pedido_' . $pedido_id . '_', true) . '.' . $file_extension;
        
        if (move_uploaded_file($_FILES["nota_fiscal_pedido"]["tmp_name"], $target_file)) {
            $path_da_nota_fiscal = $target_file;
        }
    }


    $sql_update_estoque = "UPDATE produtos SET quantidade_estoque = quantidade_estoque + ? WHERE id = ? AND fornecedor_id = ?";
    $stmt_estoque = $pdo->prepare($sql_update_estoque);

    $sql_historico = "INSERT INTO historico_entregas 
                        (produto_id, fornecedor_id, quantidade_entregue, data_entrega, valor_compra_unitario, nota_fiscal_path) 
                      VALUES (?, ?, ?, NOW(), (SELECT valor_compra FROM produtos WHERE id = ?), ?)";
    $stmt_historico = $pdo->prepare($sql_historico);

    foreach ($itens_post as $produto_id => $quantidade) {

        $prod_id = (int)$produto_id;
        $qtd = (int)$quantidade;

        if ($qtd > 0) {

            $stmt_estoque->execute([$qtd, $prod_id, $fornecedor_id]);


            $stmt_historico->execute([$prod_id, $fornecedor_id, $qtd, $prod_id, $path_da_nota_fiscal]);
        }
    }

    $sql_update_pedido = "UPDATE pedidos_fornecedor SET status_pedido = 'Entregue' WHERE id = ? AND fornecedor_id = ?";
    $stmt_update_ped = $pdo->prepare($sql_update_pedido);
    $stmt_update_ped->execute([$pedido_id, $fornecedor_id]);

    $pdo->commit();
    $_SESSION['msg_sucesso'] = "Pedido #$pedido_id processado e estoque atualizado com sucesso!";
    header('Location: gerenciar_fornecimento.php');

} catch (Exception $e) {
    if ($pdo->inTransaction()) {
        $pdo->rollBack();
    }
    $_SESSION['msg_erro'] = "Erro ao processar pedido: " . $e->getMessage();
    header('Location: detalhes_pedido.php?id=' . $pedido_id);
}
exit;
?>