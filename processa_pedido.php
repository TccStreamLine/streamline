<?php
session_start();
include_once('config.php');

// DEBUG: Descomente as linhas abaixo se o erro persistir para ver o que está na sessão
// var_dump($_SESSION); exit;

// VERIFICAÇÃO DE SEGURANÇA CORRIGIDA
// Aceita 'ceo' OU 'empresa' como permissão válida
if (empty($_SESSION['id']) || $_SERVER['REQUEST_METHOD'] !== 'POST') {
    $_SESSION['msg_erro'] = "Acesso inválido.";
    header('Location: login.php');
    exit;
}

// Verifica se o usuário tem permissão (se não for 'ceo' E não for 'empresa', bloqueia)
$role = $_SESSION['role'] ?? '';
if ($role !== 'ceo' && $role !== 'empresa') {
    $_SESSION['msg_erro'] = "Permissão negada. Você precisa ser uma Empresa para realizar pedidos.";
    header('Location: login.php');
    exit;
}

$usuario_id = $_SESSION['id'];
$fornecedor_id = filter_input(INPUT_POST, 'fornecedor_id', FILTER_VALIDATE_INT);
$itens = $_POST['itens'] ?? [];

// Validação básica dos dados recebidos
if (!$fornecedor_id || empty($itens)) {
    $_SESSION['msg_erro'] = "Dados do pedido inválidos ou nenhum item selecionado.";
    // Tenta voltar para a página do fornecedor se possível
    header('Location: fornecedores.php');
    exit;
}

try {
    $pdo->beginTransaction();

    // 1. Calcular o valor total e preparar os dados
    $valor_total_pedido = 0;
    $itens_para_inserir = [];

    foreach ($itens as $item) {
        // Garante que os dados venham limpos
        $produto_id = filter_var($item['produto_id'], FILTER_VALIDATE_INT);
        $quantidade = filter_var($item['quantidade'], FILTER_VALIDATE_INT);
        
        // Tratamento de valor monetário (remove R$, espaços e converte vírgula)
        $valor_compra_str = preg_replace('/[^\d,]/', '', $item['valor_compra']); 
        $valor_compra = (float)str_replace(',', '.', $valor_compra_str);

        if ($produto_id && $quantidade > 0) {
            $valor_total_pedido += ($valor_compra * $quantidade);
            $itens_para_inserir[] = [
                'produto_id' => $produto_id,
                'quantidade' => $quantidade,
                'valor_unitario' => $valor_compra
            ];
        }
    }

    if (empty($itens_para_inserir)) {
        throw new Exception("Nenhum item válido identificado no pedido.");
    }

    // 2. Inserir o PEDIDO (Capa) na tabela `pedidos_fornecedor`
    // Status inicial definido como 'Pendente' para aparecer na tela do fornecedor
    $sql_pedido = "INSERT INTO pedidos_fornecedor (usuario_id, fornecedor_id, valor_total_pedido, status_pedido, data_pedido) 
                   VALUES (?, ?, ?, 'Pendente', NOW())";
    $stmt_pedido = $pdo->prepare($sql_pedido);
    $stmt_pedido->execute([$usuario_id, $fornecedor_id, $valor_total_pedido]);
    
    $pedido_id = $pdo->lastInsertId();

    // 3. Inserir os ITENS na tabela `pedido_fornecedor_itens`
    $sql_item = "INSERT INTO pedido_fornecedor_itens (pedido_id, produto_id, quantidade_pedida, valor_unitario_pago) 
                 VALUES (?, ?, ?, ?)";
    $stmt_item = $pdo->prepare($sql_item);

    foreach ($itens_para_inserir as $item) {
        $stmt_item->execute([
            $pedido_id,
            $item['produto_id'],
            $item['quantidade'],
            $item['valor_unitario']
        ]);
    }
    

    $pdo->commit();
    
    $_SESSION['msg_sucesso'] = "Pedido de compra #$pedido_id enviado ao fornecedor com sucesso!";
    header('Location: fornecedores.php'); 
    exit;

} catch (Exception $e) {
    if ($pdo->inTransaction()) {
        $pdo->rollBack();
    }
    $_SESSION['msg_erro'] = "Erro ao processar o pedido: " . $e->getMessage();
    header('Location: pedido_formulario.php?fornecedor_id=' . $fornecedor_id);
    exit;
}
?>