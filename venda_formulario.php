<?php
session_start();
include_once('config.php');

if (empty($_SESSION['id'])) {
    header('Location: login.php');
    exit;
}

// Variáveis da página - Título dinâmico
$pagina_ativa = 'vendas';
$titulo_header = ''; // Será definido abaixo

// --- CORREÇÃO AQUI ---
// Inicializamos a variável $modo_edicao como false por padrão.
// Isso garante que ela sempre exista, mesmo ao criar uma nova venda.
$modo_edicao = false;
$venda_para_editar = [];

if (isset($_GET['id']) && !empty($_GET['id'])) {
    $modo_edicao = true;
    $venda_id = filter_var($_GET['id'], FILTER_VALIDATE_INT);

    $stmt = $pdo->prepare("SELECT * FROM vendas WHERE id = ? AND usuario_id = ?");
    $stmt->execute([$venda_id, $_SESSION['id']]);
    $venda_para_editar = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$venda_para_editar) {
        $_SESSION['msg_erro'] = "Venda não encontrada.";
        header('Location: vendas.php');
        exit;
    }
}

// Define o título do cabeçalho com base no modo
$titulo_header = $modo_edicao ? 'Vendas > Editar Venda' : 'Vendas > Cadastrar Venda Manual';

$produtos = $pdo->query("SELECT id, nome, valor_venda, quantidade_estoque FROM produtos WHERE status = 'ativo' ORDER BY nome")->fetchAll(PDO::FETCH_ASSOC);

if (isset($_SESSION['role']) && $_SESSION['role'] === 'funcionario') {
    $nome_exibicao = $_SESSION['funcionario_nome'];
} else {
    $nome_exibicao = $_SESSION['nome_empresa'] ?? 'Empresa';
}
?>
<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <title><?= $modo_edicao ? 'Editar Venda' : 'Cadastrar Venda Manual' ?> - Streamline</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="css/sistema.css">
    <link rel="stylesheet" href="css/venda_formulario.css">
</head>

<body>
    <?php include 'sidebar.php'; ?>

    <main class="main-content">
        <?php include 'header.php'; ?>

        <div class="form-produto-container">
            <h3 class="form-produto-title"><?= $modo_edicao ? 'EDITAR VENDA' : 'CADASTRE SUA VENDA MANUALMENTE' ?></h3>
            <form action="processa_venda.php" method="POST">
                <input type="hidden" name="acao" value="<?= $modo_edicao ? 'editar' : 'cadastrar' ?>">
                <input type="hidden" name="venda_id" value="<?= $venda_para_editar['id'] ?? '' ?>">

                <?php if ($modo_edicao): ?>
                    <p class="aviso-edicao">A alteração de produtos de uma venda já finalizada não é permitida. Apenas a data e a descrição podem ser modificadas.</p>
                <?php else: ?>
                    <div id="itens-container">
                        <div class="item-venda">
                            <div class="form-produto-group">
                                <label>Produto</label>
                                <select name="itens[0][produto_id]" class="produto-select" required>
                                    <option value="">Selecione</option>
                                    <?php foreach ($produtos as $produto): ?>
                                        <option value="<?= $produto['id'] ?>" data-valor="<?= $produto['valor_venda'] ?>">
                                            <?= htmlspecialchars($produto['nome']) ?> (Estoque: <?= $produto['quantidade_estoque'] ?>)
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="form-produto-group">
                                <label>Quantidade</label>
                                <input type="number" name="itens[0][quantidade]" min="1" value="1" class="quantidade-input" required>
                            </div>
                            <div class="form-produto-group">
                                <label>Valor Unitário</label>
                                <input type="text" name="itens[0][valor_venda]" class="valor-input" placeholder="0,00" required>
                            </div>
                            <button type="button" class="btn-remover hidden">X</button>
                        </div>
                    </div>
                    <button type="button" id="btn-adicionar-item" class="btn-secondary">Adicionar Outro Produto</button>
                <?php endif; ?>

                <div class="form-produto-grid duas-colunas">
                    <div class="form-produto-group">
                        <label for="data_venda">Data da Venda</label>
                        <input type="datetime-local" name="data_venda" value="<?= $modo_edicao ? date('Y-m-d\TH:i', strtotime($venda_para_editar['data_venda'])) : date('Y-m-d\TH:i') ?>" required>
                    </div>
                    <div class="form-produto-group">
                        <label for="descricao">Descrição (Opcional)</label>
                        <input type="text" name="descricao" placeholder="Ex: Desconto aplicado..." value="<?= htmlspecialchars($venda_para_editar['descricao'] ?? '') ?>">
                    </div>
                </div>

                <div class="form-produto-actions">
                    <button type="submit" name="submit" class="btn-produto-primary"><?= $modo_edicao ? 'SALVAR ALTERAÇÕES' : 'CADASTRAR AQUI' ?></button>
                </div>
            </form>
        </div>
    </main>
    
    <script src="venda_formulario.js"></script>
    <script src="main.js"></script>
    <script src="notificacoes.js"></script>
    <script src="notificacoes_fornecedor.js"></script>
</body>

</html>
