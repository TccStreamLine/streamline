<?php
session_start();
include_once('config.php');

if (empty($_SESSION['id'])) {
    header('Location: login.php');
    exit;
}

$produtos = [];
$categorias = [];
$fornecedores = [];
$erro_busca = null;

try {
    $stmt_produtos = $pdo->prepare("SELECT p.*, c.nome as categoria_nome FROM produtos p LEFT JOIN categorias c ON p.categoria_id = c.id WHERE p.status = 'ativo' ORDER BY p.nome ASC");
    $stmt_produtos->execute();
    $produtos = $stmt_produtos->fetchAll(PDO::FETCH_ASSOC);

    $stmt_categorias = $pdo->prepare("SELECT * FROM categorias ORDER BY nome ASC");
    $stmt_categorias->execute();
    $categorias = $stmt_categorias->fetchAll(PDO::FETCH_ASSOC);

    $stmt_fornecedores = $pdo->prepare("SELECT * FROM fornecedores ORDER BY razao_social ASC");
    $stmt_fornecedores->execute();
    $fornecedores = $stmt_fornecedores->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $erro_busca = "Erro ao buscar dados: " . $e->getMessage();
}

$nome_empresa = $_SESSION['nome_empresa'] ?? 'Empresa';
?>
<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Estoque - Sistema de Gerenciamento</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <link rel="stylesheet" href="css/sistema.css">
    <link rel="stylesheet" href="css/estoque.css">
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
                <li><a href="#"><i class="fas fa-chart-bar"></i> Vendas</a></li>
                <li><a href="caixa.php"><i class="fas fa-cash-register"></i> Caixa</a></li>
                <li><a href="#"><i class="fas fa-tachometer-alt"></i> Dashboard</a></li>
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
            <h2>Estoque > Cadastro de Produtos</h2>
            <div class="user-profile"><span><?= htmlspecialchars($nome_empresa) ?></span>
                <div class="avatar"><i class="fas fa-user"></i></div>
            </div>
        </header>

        <div class="message-container">
            <?php if (isset($_SESSION['msg_sucesso'])): ?>
                <div class="alert alert-success">
                    <?= $_SESSION['msg_sucesso'] ?>
                </div>
                <?php unset($_SESSION['msg_sucesso']); ?>
            <?php endif; ?>
            <?php if (isset($_SESSION['msg_erro'])): ?>
                <div class="alert alert-danger">
                    <?= $_SESSION['msg_erro'] ?>
                </div>
                <?php unset($_SESSION['msg_erro']); ?>
            <?php endif; ?>
        </div>

        <div class="actions-container">
            <div class="search-bar">
                <i class="fas fa-search"></i>
                <input type="text" placeholder="Pesquisar Produto...">
            </div>

            <div class="actions-buttons">
                <a href="categorias.php" class="btn-secondary">Gerenciar Categorias</a>

                <a href="produto_formulario.php" class="btn-primary">
                    <i class="fas fa-plus"></i> Cadastrar Produto
                </a>
            </div>
        </div>

        <div class="table-container">
            <table>
                <thead>
                    <tr>
                        <th>Código</th>
                        <th>Nome</th>
                        <th>Estoque</th>
                        <th>Valor Venda</th>
                        <th>Categoria</th>
                        <th>Ações</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($erro_busca): ?>
                        <tr>
                            <td colspan="6" class="text-center"><?= htmlspecialchars($erro_busca) ?></td>
                        </tr>
                    <?php elseif (empty($produtos)): ?>
                        <tr>
                            <td colspan="6" class="text-center">Nenhum produto cadastrado ainda.</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($produtos as $produto): ?>
                            <tr>
                                <td><?= htmlspecialchars($produto['id']) ?></td>
                                <td><?= htmlspecialchars($produto['nome']) ?></td>
                                <td><?= htmlspecialchars($produto['quantidade_estoque']) ?></td>
                                <td>R$ <?= number_format((float) $produto['valor_venda'], 2, ',', '.') ?></td>
                                <td><?= htmlspecialchars($produto['categoria_nome'] ?? 'N/A') ?></td>
                                <td class="actions">
                                    <a href="produto_formulario.php?id=<?= $produto['id'] ?>" class="btn-action btn-edit">
                                        <i class="fas fa-pencil-alt"></i>
                                    </a>

                                    <a href="excluir_produto.php?id=<?= $produto['id'] ?>" class="btn-action btn-delete">
                                        <i class="fas fa-trash-alt"></i>
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </main>

    <script>
        const modalContainer = document.getElementById('modalCadastro');
        const openModalBtn = document.getElementById('btnCadastrarProduto');
        const closeModalBtn = document.getElementById('closeModalBtn');
        const cancelModalBtn = document.getElementById('cancelModalBtn');
        const closeModal = () => {
            modalContainer.style.display = 'none';
        };
        closeModalBtn.addEventListener('click', closeModal);
        cancelModalBtn.addEventListener('click', closeModal);
        modalContainer.addEventListener('click', (e) => {
            if (e.target === modalContainer) {
                closeModal();
            }
        });
        openModalBtn.addEventListener('click', (e) => {
            e.preventDefault();
            document.querySelector('.modal-form').reset();
            document.getElementById('modalTitle').innerText = 'Cadastrar Novo Produto';
            document.getElementById('formAcao').value = 'cadastrar';
            document.getElementById('produto_id').value = '';
            modalContainer.style.display = 'flex';
        });
        const editButtons = document.querySelectorAll('.btn-edit');
        editButtons.forEach(button => {
            button.addEventListener('click', (e) => {
                e.preventDefault();
                const id = button.dataset.id;
                const nome = button.dataset.nome;
                const especificacao = button.dataset.especificacao;
                const qtdEstoque = button.dataset.qtd_estoque;
                const qtdMinima = button.dataset.qtd_minima;
                const valorCompra = button.dataset.valor_compra;
                const valorVenda = button.dataset.valor_venda;
                const categoriaId = button.dataset.categoria_id;
                const fornecedorId = button.dataset.fornecedor_id;
                document.getElementById('modalTitle').innerText = 'Editar Produto';
                document.getElementById('formAcao').value = 'editar';
                document.getElementById('produto_id').value = id;
                document.getElementById('nome').value = nome;
                document.getElementById('especificacao').value = especificacao;
                document.getElementById('quantidade_estoque').value = qtdEstoque;
                document.getElementById('quantidade_minima').value = qtdMinima;
                document.getElementById('valor_compra').value = valorCompra.replace('.', ',');
                document.getElementById('valor_venda').value = valorVenda.replace('.', ',');
                document.getElementById('categoria_id').value = categoriaId;
                document.getElementById('fornecedor_id').value = fornecedorId;
                modalContainer.style.display = 'flex';
            });
        });
        const modalContainer = document.getElementById('modalCadastro');
        const openModalBtn = document.getElementById('btnCadastrarProduto');
        const closeModalBtn = document.getElementById('closeModalBtn');
        const cancelModalBtn = document.getElementById('cancelModalBtn');

        const closeModal = () => {
            modalContainer.style.display = 'none';
        };

        closeModalBtn.addEventListener('click', closeModal);
        cancelModalBtn.addEventListener('click', closeModal);
        modalContainer.addEventListener('click', (e) => {
            if (e.target === modalContainer) {
                closeModal();
            }
        });

        openModalBtn.addEventListener('click', (e) => {
            e.preventDefault();
            document.querySelector('.modal-form').reset();
            document.getElementById('modalTitle').innerText = 'Cadastrar Novo Produto';
            document.getElementById('formAcao').value = 'cadastrar';
            document.getElementById('produto_id').value = '';
            modalContainer.style.display = 'flex';
        });

        const editButtons = document.querySelectorAll('.btn-edit');
        editButtons.forEach(button => {
            button.addEventListener('click', (e) => {
                e.preventDefault();
                const id = button.dataset.id;
                const nome = button.dataset.nome;
                const especificacao = button.dataset.especificacao;
                const qtdEstoque = button.dataset.qtd_estoque;
                const qtdMinima = button.dataset.qtd_minima;
                const valorCompra = button.dataset.valor_compra;
                const valorVenda = button.dataset.valor_venda;
                const categoriaId = button.dataset.categoria_id;
                const fornecedorId = button.dataset.fornecedor_id;

                document.getElementById('modalTitle').innerText = 'Editar Produto';
                document.getElementById('formAcao').value = 'editar';
                document.getElementById('produto_id').value = id;
                document.getElementById('nome').value = nome;
                document.getElementById('especificacao').value = especificacao;
                document.getElementById('quantidade_estoque').value = qtdEstoque;
                document.getElementById('quantidade_minima').value = qtdMinima;
                document.getElementById('valor_compra').value = valorCompra.replace('.', ',');
                document.getElementById('valor_venda').value = valorVenda.replace('.', ',');
                document.getElementById('categoria_id').value = categoriaId;
                document.getElementById('fornecedor_id').value = fornecedorId;

                modalContainer.style.display = 'flex';
            });
        });

        const deleteButtons = document.querySelectorAll('.btn-delete');
        deleteButtons.forEach(button => {
            button.addEventListener('click', function (event) {
                event.preventDefault();
                const url = this.href;

                Swal.fire({
                    title: 'Tem certeza?',
                    text: "Você não poderá reverter esta ação!",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#6B7280',
                    confirmButtonText: 'Sim',
                    cancelButtonText: 'Cancelar'
                }).then((result) => {
                    if (result.isConfirmed) {
                        window.location.href = url;
                    }
                });
            });
        });
    </script>
</body>

</html>