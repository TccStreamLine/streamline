<?php
session_start();
include_once('config.php');
if (empty($_SESSION['id'])) {
    header('Location: login.php');
    exit;
}
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $acao = $_POST['acao'] ?? '';
    $produto_id = filter_var($_POST['produto_id'] ?? null, FILTER_VALIDATE_INT);
    $codigo_barras = trim($_POST['codigo_barras'] ?? null);
    $nome = trim($_POST['nome'] ?? '');
    $especificacao = trim($_POST['especificacao'] ?? '');
    $quantidade_estoque = filter_var($_POST['quantidade_estoque'] ?? 0, FILTER_VALIDATE_INT);
    $quantidade_minima = filter_var($_POST['quantidade_minima'] ?? 5, FILTER_VALIDATE_INT);
    $valor_compra = str_replace(['.', ','], ['', '.'], $_POST['valor_compra'] ?? '0');
    $valor_venda = str_replace(['.', ','], ['', '.'], $_POST['valor_venda'] ?? '0');
    $categoria_id = !empty($_POST['categoria_id']) ? (int)$_POST['categoria_id'] : null;
    $fornecedor_id = !empty($_POST['fornecedor_id']) ? (int)$_POST['fornecedor_id'] : null;
    if (empty($nome) || empty($valor_venda)) {
        $_SESSION['msg_erro'] = "Nome do produto e Valor de venda são obrigatórios.";
        header('Location: produto_formulario.php' . ($produto_id ? '?id=' . $produto_id : ''));
        exit;
    }
    if ($acao === 'cadastrar') {
        try {
            $sql = "INSERT INTO produtos (codigo_barras, nome, especificacao, quantidade_estoque, quantidade_minima, valor_compra, valor_venda, categoria_id, fornecedor_id) VALUES (:codigo_barras, :nome, :especificacao, :quantidade_estoque, :quantidade_minima, :valor_compra, :valor_venda, :categoria_id, :fornecedor_id)";
            $stmt = $pdo->prepare($sql);
            $_SESSION['msg_sucesso'] = "Produto cadastrado com sucesso!";
        } catch (PDOException $e) {
            $_SESSION['msg_erro'] = "Erro ao cadastrar produto.";
        }
    } elseif ($acao === 'editar') {
        if (!$produto_id) {
            $_SESSION['msg_erro'] = "ID do produto inválido para edição.";
            header('Location: estoque.php');
            exit;
        }
        try {
            $sql = "UPDATE produtos SET codigo_barras = :codigo_barras, nome = :nome, especificacao = :especificacao, quantidade_estoque = :quantidade_estoque, quantidade_minima = :quantidade_minima, valor_compra = :valor_compra, valor_venda = :valor_venda, categoria_id = :categoria_id, fornecedor_id = :fornecedor_id WHERE id = :produto_id";
            $stmt = $pdo->prepare($sql);
            $stmt->bindParam(':produto_id', $produto_id, PDO::PARAM_INT);
            $_SESSION['msg_sucesso'] = "Produto atualizado com sucesso!";
        } catch (PDOException $e) {
            $_SESSION['msg_erro'] = "Erro ao atualizar produto.";
        }
    }
    if (isset($stmt)) {
        $stmt->bindParam(':codigo_barras', $codigo_barras, PDO::PARAM_STR);
        $stmt->bindParam(':nome', $nome, PDO::PARAM_STR);
        $stmt->bindParam(':especificacao', $especificacao, PDO::PARAM_STR);
        $stmt->bindParam(':quantidade_estoque', $quantidade_estoque, PDO::PARAM_INT);
        $stmt->bindParam(':quantidade_minima', $quantidade_minima, PDO::PARAM_INT);
        $stmt->bindParam(':valor_compra', $valor_compra);
        $stmt->bindParam(':valor_venda', $valor_venda);
        $stmt->bindParam(':categoria_id', $categoria_id, $categoria_id === null ? PDO::PARAM_NULL : PDO::PARAM_INT);
        $stmt->bindParam(':fornecedor_id', $fornecedor_id, $fornecedor_id === null ? PDO::PARAM_NULL : PDO::PARAM_INT);
        if (!$stmt->execute()) {
            $_SESSION['msg_erro'] = "Ocorreu um erro na operação com o banco de dados.";
            unset($_SESSION['msg_sucesso']);
        }
    }
}
header('Location: estoque.php');
exit;
?>