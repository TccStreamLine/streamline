<?php
session_start();
include_once('config.php');
if (empty($_SESSION['id'])) {
    header('Location: login.php');
    exit;
}
// Alteração aqui para buscar apenas fornecedores com status 'ativo'
$sql = "SELECT * FROM fornecedores WHERE status = 'ativo' ORDER BY razao_social ASC";
$stmt = $pdo->prepare($sql);
$stmt->execute();
$fornecedores = $stmt->fetchAll(PDO::FETCH_ASSOC);
$nome_empresa = $_SESSION['nome_empresa'] ?? 'Empresa';
?>
<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Fornecedores - Sistema de Gerenciamento</title>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="css/sistema.css">
    <link rel="stylesheet" href="css/estoque.css">
</head>

<body>
    <nav class="sidebar">
        <div class="sidebar-logo">
            <img class="logo" src="img/relplogo2.png" alt="Relp! Logo" style="width: 100px;">
        </div>
        <div class="menu-section">
            <h6>MENU</h6>
            <ul class="menu-list">
                <li><a href="sistema.php"><i class="fas fa-home"></i> Início</a></li>
                <li><a href="estoque.php"><i class="fas fa-box"></i> Estoque</a></li>
                <li><a href="agenda.php"><i class="fas fa-calendar-alt"></i> Agenda</a></li>
                <li><a href="fornecedores.php" class="active"><i class="fas fa-truck"></i> Fornecimento</a></li>
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
            <h2>Gerenciamento de Fornecedores</h2>
            <div class="user-profile"><span><?= htmlspecialchars($nome_empresa) ?></span>
                <div class="avatar"><i class="fas fa-user"></i></div>
            </div>
        </header>
        <div class="message-container">
            <?php if (isset($_SESSION['msg_sucesso'])): ?>
                <div class="alert alert-success"><?= $_SESSION['msg_sucesso'];
                unset($_SESSION['msg_sucesso']); ?></div>
            <?php endif; ?>
            <?php if (isset($_SESSION['msg_erro'])): ?>
                <div class="alert alert-danger"><?= $_SESSION['msg_erro'];
                unset($_SESSION['msg_erro']); ?></div>
            <?php endif; ?>
        </div>
        <div class="actions-container">
            <div class="search-bar"><i class="fas fa-search"></i><input type="text"
                    placeholder="Pesquisar Fornecedor..."></div>
            <a href="fornecedor_formulario.php" class="btn-primary"><i class="fas fa-plus"></i> Cadastrar Fornecedor</a>
        </div>
        <div class="table-container">
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Razão Social</th>
                        <th>CNPJ</th>
                        <th>E-mail</th>
                        <th>Telefone</th>
                        <th>Ações</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($fornecedores)): ?>
                        <tr>
                            <td colspan="6" class="text-center">Nenhum fornecedor cadastrado.</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($fornecedores as $fornecedor): ?>
                            <tr>
                                <td><?= htmlspecialchars($fornecedor['id']) ?></td>
                                <td><?= htmlspecialchars($fornecedor['razao_social']) ?></td>
                                <td><?= htmlspecialchars($fornecedor['cnpj']) ?></td>
                                <td><?= htmlspecialchars($fornecedor['email']) ?></td>
                                <td><?= htmlspecialchars($fornecedor['telefone']) ?></td>
                                <td class="actions">
                                    <a href="fornecedor_formulario.php?id=<?= $fornecedor['id'] ?>" class="btn-action btn-edit">
                                        <i class="fas fa-pencil-alt"></i> </a>
                                    <a href="excluir_fornecedor.php?id=<?= $fornecedor['id'] ?>" class="btn-action btn-delete">
                                        <i class="fas fa-trash-alt"></i> </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </main>
    <script>
        const deleteButtons = document.querySelectorAll('.btn-delete');
        deleteButtons.forEach(button => {
            button.addEventListener('click', function (event) {
                event.preventDefault();
                const url = this.href;
                Swal.fire({
                    title: 'Tem certeza?',
                    text: "O fornecedor será inativado e não aparecerá mais nas listas.",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#6B7280',
                    confirmButtonText: 'Sim, inativar!',
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
