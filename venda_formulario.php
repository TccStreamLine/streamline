<?php
session_start();
include_once('config.php');

if (empty($_SESSION['id'])) {
    header('Location: login.php');
    exit;
}

// Lógica de Edição
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

$produtos = $pdo->query("SELECT id, nome, valor_venda, quantidade_estoque FROM produtos WHERE status = 'ativo' ORDER BY nome")->fetchAll(PDO::FETCH_ASSOC);
$nome_empresa = $_SESSION['nome_empresa'] ?? 'Empresa';
?>
<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <title><?= $modo_edicao ? 'Editar Venda' : 'Cadastrar Venda Manual' ?> - Streamline</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="css/sistema.css">
    <link rel="stylesheet" href="css/produto_formulario.css">
    <style>
        .item-venda {
            display: grid;
            grid-template-columns: 2fr 1fr 1fr auto;
            gap: 1rem;
            align-items: flex-end;
            margin-bottom: 1rem;
        }

        .btn-remover {
            background-color: #EF4444;
            color: white;
            border: none;
            border-radius: 8px;
            padding: 0 12px;
            cursor: pointer;
            height: 54px;
        }

        .readonly {
            background-color: #F3F4F6 !important;
            cursor: not-allowed;
        }
    </style>
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
                <li><a href="estoque.php"><i class="fas fa-box"></i> Estoque</a></li>
                <li><a href="agenda.php"><i class="fas fa-calendar-alt"></i> Agenda</a></li>
                <li><a href="fornecedores.php"><i class="fas fa-truck"></i> Fornecimento</a></li>
                <li><a href="vendas.php" class="active"><i class="fas fa-chart-bar"></i> Vendas</a></li>
                <li><a href="caixa.php"><i class="fas fa-cash-register"></i> Caixa</a></li>
                <li><a href="dashboard.php"><i class="fas fa-tachometer-alt"></i> Dashboard</a></li>
                <li><a href="nota_fiscal.php"><i class="fas fa-file-invoice-dollar"></i> Nota Fiscal</a></li>
                <li><a href="servicos.php"><i class="fas fa-concierge-bell"></i> Serviços</a></li>
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
            <h2>Vendas > <?= $modo_edicao ? 'Editar Venda' : 'Cadastrar Venda Manualmente' ?></h2>
            <div class="user-profile"><span><?= htmlspecialchars($nome_empresa) ?></span>
                <div class="avatar"><i class="fas fa-user"></i></div>
            </div>
        </header>

        <div class="form-produto-container">
            <h3 class="form-produto-title"><?= $modo_edicao ? 'EDITAR VENDA' : 'CADASTRE SUA VENDA MANUALMENTE' ?></h3>
            <form action="processa_venda.php" method="POST">
                <input type="hidden" name="acao" value="<?= $modo_edicao ? 'editar' : 'cadastrar' ?>">
                <input type="hidden" name="venda_id" value="<?= $venda_para_editar['id'] ?? '' ?>">

                <?php if ($modo_edicao): ?>
                    <p style="text-align: center; margin-bottom: 2rem; color: #6B7280;">A alteração de produtos de uma venda já finalizada não é permitida. Apenas a data e a descrição podem ser modificadas.</p>
                <?php else: ?>
                    <div id="itens-container">
                        <div class="item-venda">
                            <div class="form-produto-group"><label>Produto</label><select name="itens[0][produto_id]" class="produto-select" required>
                                    <option value="">Selecione</option><?php foreach ($produtos as $produto): ?><option value="<?= $produto['id'] ?>" data-valor="<?= $produto['valor_venda'] ?>"><?= htmlspecialchars($produto['nome']) ?> (Estoque: <?= $produto['quantidade_estoque'] ?>)</option><?php endforeach; ?>
                                </select></div>
                            <div class="form-produto-group"><label>Quantidade</label><input type="number" name="itens[0][quantidade]" min="1" value="1" class="quantidade-input" required></div>
                            <div class="form-produto-group"><label>Valor Unitário</label><input type="text" name="itens[0][valor_venda]" class="valor-input" placeholder="0,00" required></div>
                            <button type="button" class="btn-remover" style="display:none;">X</button>
                        </div>
                    </div>
                    <button type="button" id="btn-adicionar-item" class="btn-secondary" style="margin-bottom: 1.5rem;">Adicionar Outro Produto</button>
                <?php endif; ?>

                <div class="form-produto-grid" style="grid-template-columns: 1fr 1fr;">
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
    <script>
        document.addEventListener('click', function(e) {
            if (e.target && e.target.classList.contains('produto-select')) {
                e.target.addEventListener('change', preencherValor);
            }
            if (e.target && e.target.classList.contains('btn-remover')) {
                e.target.closest('.item-venda').remove();
            }
        });

        function preencherValor(event) {
            const select = event.target;
            const selectedOption = select.options[select.selectedIndex];
            const valor = selectedOption.getAttribute('data-valor');
            const valorInput = select.closest('.item-venda').querySelector('.valor-input');
            if (valor) {
                valorInput.value = valor.replace('.', ',');
            }
        }

        const btnAdicionar = document.getElementById('btn-adicionar-item');
        if (btnAdicionar) {
            btnAdicionar.addEventListener('click', function() {
                const container = document.getElementById('itens-container');
                const index = container.children.length;
                const novoItem = container.children[0].cloneNode(true);

                novoItem.querySelector('select').name = `itens[${index}][produto_id]`;
                novoItem.querySelector('.quantidade-input').name = `itens[${index}][quantidade]`;
                novoItem.querySelector('.valor-input').name = `itens[${index}][valor_venda]`;

                novoItem.querySelector('select').value = '';
                novoItem.querySelector('.quantidade-input').value = '1';
                novoItem.querySelector('.valor-input').value = '';

                const btnRemover = novoItem.querySelector('.btn-remover');
                btnRemover.style.display = 'block';

                container.appendChild(novoItem);
            });
        }

        const firstSelect = document.querySelector('.produto-select');
        if (firstSelect) {
            firstSelect.addEventListener('change', preencherValor);
        }
    </script>
</body>

</html>