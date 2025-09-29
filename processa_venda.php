<?php
session_start();
include_once('config.php');

if (empty($_SESSION['id']) || $_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: login.php');
    exit;
}

$usuario_id = $_SESSION['id'];
$acao = $_POST['acao'] ?? '';

try {
    if ($acao === 'cadastrar') {
        $itens = $_POST['itens'] ?? [];
        if (empty($itens)) {
            throw new Exception("Nenhum item foi adicionado à venda.");
        }

        $sql_check_produto = "SELECT nome, quantidade_estoque, quantidade_minima FROM produtos WHERE id = ?";
        $stmt_check_produto = $pdo->prepare($sql_check_produto);

        foreach ($itens as $item) {
            $produto_id = (int)$item['produto_id'];
            $quantidade_vendida = (int)$item['quantidade'];
            $stmt_check_produto->execute([$produto_id]);
            $produto = $stmt_check_produto->fetch(PDO::FETCH_ASSOC);

            if (!$produto) {
                throw new Exception("Produto com ID $produto_id não encontrado.");
            }
            $estoque_final = $produto['quantidade_estoque'] - $quantidade_vendida;
            if ($estoque_final < 0) {
                throw new Exception("Estoque insuficiente para o produto: " . $produto['nome']);
            }
            if ($estoque_final < $produto['quantidade_minima']) {
                $mensagem_erro = "Venda bloqueada para o produto: " . $produto['nome'] . ". A venda deixaria o estoque abaixo do mínimo permitido (" . $produto['quantidade_minima'] . " unidades).";
                throw new Exception($mensagem_erro);
            }
        }

        $pdo->beginTransaction();

        $data_venda = $_POST['data_venda'] ?? date('Y-m-d H:i:s');
        $descricao = trim($_POST['descricao'] ?? '');
        $valor_total_venda = 0;

        foreach ($itens as $item) {
            $valor_unitario = (float)str_replace(',', '.', $item['valor_venda']);
            $quantidade = (int)$item['quantidade'];
            $valor_total_venda += $valor_unitario * $quantidade;
        }

        $sql_venda = "INSERT INTO vendas (usuario_id, valor_total, descricao, data_venda, status) VALUES (?, ?, ?, ?, 'finalizada')";
        $stmt_venda = $pdo->prepare($sql_venda);
        $stmt_venda->execute([$usuario_id, $valor_total_venda, $descricao, $data_venda]);
        $venda_id = $pdo->lastInsertId();

        $sql_item = "INSERT INTO venda_itens (venda_id, produto_id, quantidade, valor_unitario, valor_total) VALUES (?, ?, ?, ?, ?)";
        $stmt_item = $pdo->prepare($sql_item);
        
        $sql_update_estoque = "UPDATE produtos SET quantidade_estoque = quantidade_estoque - ? WHERE id = ?";
        $stmt_update_estoque = $pdo->prepare($sql_update_estoque);

        foreach ($itens as $item) {
            $produto_id = (int)$item['produto_id'];
            $quantidade = (int)$item['quantidade'];
            $valor_unitario = (float)str_replace(',', '.', $item['valor_venda']);
            $valor_total_item = $valor_unitario * $quantidade;
            $stmt_item->execute([$venda_id, $produto_id, $quantidade, $valor_unitario, $valor_total_item]);
            $stmt_update_estoque->execute([$quantidade, $produto_id]);
        }

        $pdo->commit();
        $_SESSION['msg_sucesso'] = "Venda manual cadastrada com sucesso!";
        header('Location: vendas.php');
        exit;

    } elseif ($acao === 'editar') {
-
        $venda_id = filter_input(INPUT_POST, 'venda_id', FILTER_VALIDATE_INT);
        if (!$venda_id) {
            throw new Exception("ID da venda inválido.");
        }

        $data_venda = $_POST['data_venda'] ?? date('Y-m-d H:i:s');
        $descricao = trim($_POST['descricao'] ?? '');

        $sql = "UPDATE vendas SET data_venda = ?, descricao = ? WHERE id = ? AND usuario_id = ?";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$data_venda, $descricao, $venda_id, $usuario_id]);
        
        $_SESSION['msg_sucesso'] = "Venda atualizada com sucesso!";
        header('Location: vendas.php');
        exit;
    }

} catch (Exception $e) {
    if ($pdo->inTransaction()) {
        $pdo->rollBack();
    }
    $_SESSION['msg_erro'] = "Falha na operação: " . $e->getMessage();
    $redirect_url = 'venda_formulario.php' . (isset($_POST['venda_id']) ? '?id='.$_POST['venda_id'] : '');
    header('Location: ' . $redirect_url);
    exit;
}
?>