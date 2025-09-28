<?php
session_start();
include_once('config.php');
require './phpmailer/src/Exception.php';
require './phpmailer/src/PHPMailer.php';
require './phpmailer/src/SMTP.php';
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

header('Content-Type: application/json');

if (empty($_SESSION['id']) || empty($_SESSION['carrinho'])) {
    echo json_encode(['error' => 'Usuário não logado ou carrinho vazio.']);
    exit;
}

$carrinho = $_SESSION['carrinho'];
$usuario_id = $_SESSION['id'];
$nome_empresa = $_SESSION['nome_empresa'] ?? 'Uma empresa parceira';

try {
    $pdo->beginTransaction();

    $sql_check_produto = "SELECT nome, quantidade_estoque, quantidade_minima, fornecedor_id FROM produtos WHERE id = ?";
    $stmt_check_produto = $pdo->prepare($sql_check_produto);

    $produtos_para_notificar = [];

    foreach ($carrinho as $item) {
        if ($item['tipo'] === 'produto') {
            $stmt_check_produto->execute([$item['id']]);
            $produto = $stmt_check_produto->fetch(PDO::FETCH_ASSOC);

            if (!$produto) {
                throw new Exception('Produto não encontrado no banco de dados: ' . htmlspecialchars($item['nome']));
            }

            $estoque_antes = (int)$produto['quantidade_estoque'];
            $estoque_depois = $estoque_antes - (int)$item['quantidade'];

            if ($estoque_depois < 0) {
                throw new Exception("Estoque insuficiente para o produto: " . htmlspecialchars($item['nome']));
            }
            
            if ($estoque_depois < $produto['quantidade_minima']) {
                $mensagem_erro = "Venda bloqueada para o produto: " . htmlspecialchars($item['nome']) . ". A venda deixaria o estoque abaixo do mínimo permitido (" . $produto['quantidade_minima'] . " unidades).";
                throw new Exception($mensagem_erro);
            }

            if ($estoque_antes > $produto['quantidade_minima'] && $estoque_depois <= $produto['quantidade_minima']) {
                 $produtos_para_notificar[] = [
                    'id' => $item['id'],
                    'nome' => $produto['nome'],
                    'fornecedor_id' => $produto['fornecedor_id'],
                    'estoque_atual' => $estoque_depois
                ];
            }
        }
    }

    $valor_total_venda = 0;
    foreach ($carrinho as $item) {
        $valor_total_venda += $item['quantidade'] * $item['valor_unitario'];
    }

    $sql_venda = "INSERT INTO vendas (usuario_id, valor_total) VALUES (?, ?)";
    $stmt_venda = $pdo->prepare($sql_venda);
    $stmt_venda->execute([$usuario_id, $valor_total_venda]);
    $venda_id = $pdo->lastInsertId();

    $sql_item_produto = "INSERT INTO venda_itens (venda_id, produto_id, quantidade, valor_unitario, valor_total) VALUES (?, ?, ?, ?, ?)";
    $stmt_item_produto = $pdo->prepare($sql_item_produto);
    $sql_item_servico = "INSERT INTO venda_servicos (venda_id, servico_id, valor) VALUES (?, ?, ?)";
    $stmt_item_servico = $pdo->prepare($sql_item_servico);
    $sql_update_estoque = "UPDATE produtos SET quantidade_estoque = quantidade_estoque - ? WHERE id = ?";
    $stmt_update_estoque = $pdo->prepare($sql_update_estoque);

    foreach ($carrinho as $item) {
        if ($item['tipo'] === 'produto') {
            $valor_total_item = $item['quantidade'] * $item['valor_unitario'];
            $stmt_item_produto->execute([$venda_id, $item['id'], $item['quantidade'], $item['valor_unitario'], $valor_total_item]);
            $stmt_update_estoque->execute([$item['quantidade'], $item['id']]);
        } else {
            $stmt_item_servico->execute([$venda_id, $item['id'], $item['valor_unitario']]);
        }
    }

    $pdo->commit();
    unset($_SESSION['carrinho']);
    $_SESSION['msg_sucesso_caixa'] = "Venda finalizada com sucesso!";

    if (!empty($produtos_para_notificar)) {
        $sql_fornecedor = "SELECT email, razao_social FROM fornecedores WHERE id = ? AND email IS NOT NULL AND status = 'ativo'";
        $stmt_fornecedor = $pdo->prepare($sql_fornecedor);

        foreach ($produtos_para_notificar as $produto_notificar) {
            if ($produto_notificar['fornecedor_id']) {
                $stmt_fornecedor->execute([$produto_notificar['fornecedor_id']]);
                $fornecedor = $stmt_fornecedor->fetch(PDO::FETCH_ASSOC);

                if ($fornecedor && !empty($fornecedor['email'])) {
                    $mail = new PHPMailer(true);
                    try {
                        // HABILITE A DEPURAÇÃO AQUI 
                        $mail->SMTPDebug = 0; 
                        $mail->isSMTP();
                        $mail->Host = 'smtp-relay.brevo.com';
                        $mail->SMTPAuth = true;
                        $mail->Username = '9691c1001@smtp-brevo.com';
                        $mail->Password = 'g3BDXcCKG8zWtZRL';
                        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
                        $mail->Port = 587;
                        $mail->CharSet = 'UTF-8';
                        $mail->setFrom('tccstreamline@gmail.com', 'Streamline - Alerta de Estoque');
                        $mail->addAddress($fornecedor['email'], $fornecedor['razao_social']);
                        $mail->isHTML(true);
                        $mail->Subject = 'Alerta de Estoque Baixo';
                        $mail->Body = "Olá, " . htmlspecialchars($fornecedor['razao_social']) . "! O produto " . htmlspecialchars($produto_notificar['nome']) . " atingiu o estoque mínimo.";
                        $mail->send();
                    } catch (Exception $e) {
                        throw new Exception("O e-mail para o fornecedor não pôde ser enviado. Erro: {$mail->ErrorInfo}");
                    }
                }
            }
        }
    }

    echo json_encode(['success' => true]);

} catch (Exception $e) {
    if ($pdo->inTransaction()) {
        $pdo->rollBack();
    }
    echo json_encode(['error' => 'Falha: ' . $e->getMessage()]);
}
?>