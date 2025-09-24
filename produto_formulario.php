<?php
session_start();
include_once('config.php');

if (empty($_SESSION['id'])) {
    header('Location: login.php');
    exit;
}

$modo_edicao = false;
$produto_para_editar = [];
$titulo_pagina = "Cadastrar Produto";

if (isset($_GET['id']) && !empty($_GET['id'])) {
    $modo_edicao = true;
    $id = filter_var($_GET['id'], FILTER_VALIDATE_INT);
    $titulo_pagina = "Editar Produto";

    $stmt = $pdo->prepare("SELECT * FROM produtos WHERE id = :id");
    $stmt->execute([':id' => $id]);
    $produto_para_editar = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$produto_para_editar) {
        $_SESSION['msg_erro'] = "Produto não encontrado.";
        header('Location: estoque.php');
        exit;
    }
}

$categorias = $pdo->query("SELECT id, nome FROM categorias ORDER BY nome")->fetchAll(PDO::FETCH_ASSOC);
$fornecedores = $pdo->query("SELECT id, razao_social FROM fornecedores ORDER BY razao_social")->fetchAll(PDO::FETCH_ASSOC);

$nome_empresa = $_SESSION['nome_empresa'] ?? 'Empresa';
?>
<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <title><?= $titulo_pagina ?> - Streamline</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="css/sistema.css">
    <link rel="stylesheet" href="css/produto_formulario.css">
</head>

<body>
    <nav class="sidebar">
        <div class="sidebar-logo">
            <img class="logo" src="img/relplogo.png" alt="Relp! Logo" style="width: 100px;">
        </div>
        <div class="menu-section">
            <h6>MENU</h6>
            <ul class="menu-list">
                <li><a href="sistema.php"><i class="fas fa-home"></i> Início</a></li>
                <li><a href="estoque.php" class="active"><i class="fas fa-box"></i> Estoque</a></li>
                <li><a href="agenda.php"><i class="fas fa-calendar-alt"></i> Agenda</a></li>
                <li><a href="fornecedores.php"><i class="fas fa-truck"></i> Fornecimento</a></li>
                <li><a href="vendas.php"><i class="fas fa-chart-bar"></i> Vendas</a></li>
                <li><a href="caixa.php"><i class="fas fa-cash-register"></i> Caixa</a></li>
                <li><a href="dashboard.php"><i class="fas fa-tachometer-alt"></i> Dashboard</a></li>
                <li><a href="#"><i class="fas fa-file-invoice-dollar"></i> Nota Fiscal</a></li>
                <li><a href="#"><i class="fas fa-concierge-bell"></i> Serviços</a></li>
            </ul>
        </div>
        <div class="menu-section outros">
            <h6>OUTROS</h6>
            <ul class="menu-list">
                <li><a href="#"><i class="fas fa-store"></i> Loja de Planos</a></li>
                <li><a href="sair.php"><i class="fas fa-sign-out-alt"></i> Sair</a></li>
            </ul>
        </div>
    </nav>

    <main class="main-content">
        <header class="main-header">
            <h2>Estoque > <?= $titulo_pagina ?></h2>
            <div class="user-profile">
                <span><?= htmlspecialchars($nome_empresa) ?></span>
                <div class="avatar"><i class="fas fa-user"></i></div>
            </div>
        </header>

        <div class="form-produto-container">
            <h3 class="form-produto-title"><?= $modo_edicao ? 'EDITAR PRODUTO' : 'CADASTRE SEU PRODUTO MANUALMENTE' ?></h3>
            <form action="processa_produto.php" method="POST">
                <input type="hidden" name="acao" value="<?= $modo_edicao ? 'editar' : 'cadastrar' ?>">
                <input type="hidden" name="produto_id" value="<?= $produto_para_editar['id'] ?? '' ?>">

                <div class="form-produto-grid">
                    <div class="form-produto-group">
                        <label for="codigo_barras">Código de barras</label>
                        <input type="text" id="codigo_barras" name="codigo_barras" value="<?= htmlspecialchars($produto_para_editar['codigo_barras'] ?? '') ?>">
                    </div>
                    <div class="form-produto-group">
                        <label for="valor_compra">Valor de compra</label>
                        <input type="text" id="valor_compra" name="valor_compra" value="<?= htmlspecialchars($produto_para_editar['valor_compra'] ?? '') ?>">
                    </div>
                    <div class="form-produto-group">
                        <label for="nome">Nome do produto</label>
                        <input type="text" id="nome" name="nome" value="<?= htmlspecialchars($produto_para_editar['nome'] ?? '') ?>" required>
                    </div>
                    <div class="form-produto-group">
                        <label for="categoria_id">Categoria do produto</label>
                        <select id="categoria_id" name="categoria_id" required>
                            <option value="">Selecione</option>
                            <?php foreach ($categorias as $cat): ?>
                                <option value="<?= $cat['id'] ?>" <?= (isset($produto_para_editar['categoria_id']) && $produto_para_editar['categoria_id'] == $cat['id']) ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($cat['nome']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="form-produto-group">
                        <label for="quantidade_estoque">Quantidade estocada</label>
                        <input type="number" id="quantidade_estoque" name="quantidade_estoque" min="0" value="<?= htmlspecialchars($produto_para_editar['quantidade_estoque'] ?? '0') ?>" required>
                    </div>
                    <div class="form-produto-group">
                        <label for="especificacao">Especificações</label>
                        <input type="text" id="especificacao" name="especificacao" value="<?= htmlspecialchars($produto_para_editar['especificacao'] ?? '') ?>">
                    </div>
                    <div class="form-produto-group">
                        <label for="valor_venda">Valor de venda</label>
                        <input type="text" id="valor_venda" name="valor_venda" value="<?= htmlspecialchars($produto_para_editar['valor_venda'] ?? '') ?>" required>
                    </div>
                    <div class="form-produto-group">
                        <label for="quantidade_minima">Quantidade mínima no estoque</label>
                        <input type="number" id="quantidade_minima" name="quantidade_minima" min="0" value="<?= htmlspecialchars($produto_para_editar['quantidade_minima'] ?? '5') ?>">
                    </div>
                </div>
                <div class="form-produto-actions">
                    <button type="submit" class="btn-produto-primary">
                        <?= $modo_edicao ? 'SALVAR ALTERAÇÕES' : 'CADASTRAR AQUI' ?>
                    </button>
                </div>
            </form>
        </div>
    </main>
</body>

</html>