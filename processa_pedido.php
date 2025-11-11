<?php
session_start();
include_once('config.php');

if (empty($_SESSION['id_fornecedor']) || $_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: login.php');
    exit;
}

$fornecedor_id = $_SESSION['id_fornecedor'];
$produto_id = filter_input(INPUT_POST, 'produto_id', FILTER_VALIDATE_INT);
$quantidade_entregue = filter_input(INPUT_POST, 'quantidade_entregue', FILTER_VALIDATE_INT);
$data_entrega = $_POST['data_entrega'] ?? date('Y-m-d H:i:s');
$valor_compra_unitario = 0;
$path_da_nota_fiscal = NULL;

if (!$produto_id || !$quantidade_entregue || $quantidade_entregue <= 0) {
    $_SESSION['msg_erro'] = "Dados inválidos para registrar a entrega.";
    header('Location: gerenciar_fornecimento.php');
    exit;
}

try {
    $pdo->beginTransaction();

    // 1. Buscar o valor de compra atual do produto
    $stmt_prod = $pdo->prepare("SELECT valor_compra FROM produtos WHERE id = ?");
    $stmt_prod->execute([$produto_id]);
    $produto = $stmt_prod->fetch(PDO::FETCH_ASSOC);
    if ($produto) {
        $valor_compra_unitario = $produto['valor_compra'];
    }

    // 2. Adiciona a quantidade entregue ao estoque atual
    $sql_update_estoque = "UPDATE produtos SET quantidade_estoque = quantidade_estoque + ? WHERE id = ? AND fornecedor_id = ?";
    $stmt_estoque = $pdo->prepare($sql_update_estoque);
    $stmt_estoque->execute([$quantidade_entregue, $produto_id, $fornecedor_id]);

    // 3. Lógica para upload de arquivo
    if (isset($_FILES['nota_fiscal_entrega']) && $_FILES['nota_fiscal_entrega']['error'] == 0) {
        $target_dir = "uploads/notas_fiscais/";
        if (!is_dir($target_dir)) {
            mkdir($target_dir, 0777, true);
        }
        // Garante um nome de arquivo único
        $file_extension = strtolower(pathinfo($_FILES["nota_fiscal_entrega"]["name"], PATHINFO_EXTENSION));
        $target_file = $target_dir . uniqid('nf_', true) . '.' . $file_extension;
        
        if (move_uploaded_file($_FILES["nota_fiscal_entrega"]["tmp_name"], $target_file)) {
            $path_da_nota_fiscal = $target_file;
        }
    }

    // 4. Insere o registro no histórico de entregas
    $sql_historico = "INSERT INTO historico_entregas 
                        (produto_id, fornecedor_id, quantidade_entregue, data_entrega, valor_compra_unitario, nota_fiscal_path) 
                      VALUES (?, ?, ?, ?, ?, ?)";
    $stmt_historico = $pdo->prepare($sql_historico);
    $stmt_historico->execute([
        $produto_id,
        $fornecedor_id,
        $quantidade_entregue,
        $data_entrega,
        $valor_compra_unitario,
        $path_da_nota_fiscal
    ]);

    $pdo->commit();
    $_SESSION['msg_sucesso'] = "Entrega registrada e estoque atualizado com sucesso!";

} catch (PDOException $e) {
    $pdo->rollBack();
    $_SESSION['msg_erro'] = "Erro ao registrar entrega: " . $e->getMessage();
}

header('Location: gerenciar_fornecimento.php');
exit;
?>