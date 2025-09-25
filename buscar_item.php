<?php
include_once('config.php');
session_start();

header('Content-Type: application/json');

$termo_busca = $_GET['termo'] ?? '';
$tipo = $_GET['tipo'] ?? 'produto';
$usuario_id = $_SESSION['id'];

if (empty($termo_busca)) {
    echo json_encode(['error' => 'Termo de busca não fornecido.']);
    exit;
}

try {
    if ($tipo === 'produto') {
        $stmt = $pdo->prepare("SELECT id, nome, valor_venda FROM produtos WHERE (codigo_barras = :termo OR nome LIKE :termo_like) AND status = 'ativo'");
    } else { // tipo === 'servico'
        $stmt = $pdo->prepare("SELECT id, nome_servico as nome, valor_venda FROM servicos_prestados WHERE (id = :termo OR nome_servico LIKE :termo_like) AND usuario_id = :usuario_id AND status = 'ativo'");
        $stmt->bindParam(':usuario_id', $usuario_id);
    }
    
    $stmt->bindValue(':termo', $termo_busca);
    $stmt->bindValue(':termo_like', '%' . $termo_busca . '%');
    $stmt->execute();
    $item = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($item) {
        $item['tipo'] = $tipo; // Adiciona o tipo ao retorno
        echo json_encode($item);
    } else {
        echo json_encode(['error' => ucfirst($tipo) . ' não encontrado.']);
    }
} catch (PDOException $e) {
    echo json_encode(['error' => 'Erro no banco de dados.']);
}
?>