<?php
session_start();
include_once('config.php');

if (empty($_SESSION['id'])) {
    header('Location: login.php');
    exit;
}

function format_value_for_db($value) {
    $value = str_replace('.', '', $value); 
    $value = str_replace(',', '.', $value); 
    return (float)$value;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $acao = $_POST['acao'] ?? '';

    $produto_id = filter_var($_POST['produto_id'] ?? null, FILTER_VALIDATE_INT);
    $codigo_barras = trim($_POST['codigo_barras'] ?? null);
    $nome = trim($_POST['nome'] ?? '');
    $especificacao = trim($_POST['especificacao'] ?? '');
    $quantidade_estoque = filter_var($_POST['quantidade_estoque'] ?? 0, FILTER_VALIDATE_INT);
    $quantidade_minima = filter_var($_POST['quantidade_minima'] ?? 5, FILTER_VALIDATE_INT);
    
    $valor_compra = format_value_for_db($_POST['valor_compra'] ?? '0');
    $valor_venda = format_value_for_db($_POST['valor_venda'] ?? '0');
    
    $categoria_id = !empty($_POST['categoria_id']) ? (int)$_POST['categoria_id'] : null;
    $fornecedor_id = !empty($_POST['fornecedor_id']) ? (int)$_POST['fornecedor_id'] : null;

    if (empty($nome) || empty($valor_venda)) {
        $_SESSION['msg_erro'] = "Nome do produto e Valor de venda são obrigatórios.";
        header('Location: produto_formulario.php' . ($produto_id ? '?id=' . $produto_id : ''));
        exit;
    }

    if ($acao === 'cadastrar') {
        try {
            if (!empty($codigo_barras)) {
               
                $check_sql = "SELECT id FROM produtos WHERE codigo_barras = ? AND status = 'ativo'";
                $check_stmt = $pdo->prepare($check_sql);
                $check_stmt->execute([$codigo_barras]);
                if ($check_stmt->fetch()) {
                    $_SESSION['msg_erro'] = "Este Código de Barras já está em uso por um produto ativo.";
                    header('Location: produto_formulario.php');
                    exit;
                }
            }

            $sql = "INSERT INTO produtos (codigo_barras, nome, especificacao, quantidade_estoque, quantidade_minima, valor_compra, valor_venda, categoria_id, fornecedor_id) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([$codigo_barras, $nome, $especificacao, $quantidade_estoque, $quantidade_minima, $valor_compra, $valor_venda, $categoria_id, $fornecedor_id]);
            $_SESSION['msg_sucesso'] = "Produto cadastrado com sucesso!";

        } catch (PDOException $e) {
            $_SESSION['msg_erro'] = "Erro ao cadastrar produto.";
        }
    } 
    elseif ($acao === 'editar') {
        if (!$produto_id) {
            $_SESSION['msg_erro'] = "ID do produto inválido para edição.";
        } else {
            try {
                if (!empty($codigo_barras)) {
                    
                    $check_sql = "SELECT id FROM produtos WHERE codigo_barras = ? AND id != ? AND status = 'ativo'";
                    $check_stmt = $pdo->prepare($check_sql);
                    $check_stmt->execute([$codigo_barras, $produto_id]);
                    if ($check_stmt->fetch()) {
                        $_SESSION['msg_erro'] = "Este Código de Barras já pertence a outro produto ativo.";
                        header('Location: produto_formulario.php?id=' . $produto_id);
                        exit;
                    }
                }

                $sql = "UPDATE produtos SET codigo_barras = ?, nome = ?, especificacao = ?, quantidade_estoque = ?, quantidade_minima = ?, valor_compra = ?, valor_venda = ?, categoria_id = ?, fornecedor_id = ? WHERE id = ?";
                $stmt = $pdo->prepare($sql);
                $stmt->execute([$codigo_barras, $nome, $especificacao, $quantidade_estoque, $quantidade_minima, $valor_compra, $valor_venda, $categoria_id, $fornecedor_id, $produto_id]);
                $_SESSION['msg_sucesso'] = "Produto atualizado com sucesso!";

            } catch (PDOException $e) {
                $_SESSION['msg_erro'] = "Erro ao atualizar produto.";
            }
        }
    }
}

header('Location: estoque.php');
exit;
?>