<?php
session_start();
include_once('config.php');

$pagina_ativa = 'funcionarios';
$titulo_header = 'Gerenciamento de Funcionários';

if (empty($_SESSION['id']) || $_SESSION['role'] !== 'ceo') {
    header('Location: sistema.php');
    exit;
}

$sql = "SELECT * FROM funcionarios WHERE status = 'ativo' ORDER BY nome ASC";
$stmt = $pdo->prepare($sql);
$stmt->execute();
$funcionarios = $stmt->fetchAll(PDO::FETCH_ASSOC);

$nome_empresa = $_SESSION['nome_empresa'] ?? 'Empresa';
?>
<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Funcionários - Sistema de Gerenciamento</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="css/sistema.css">
    <link rel="stylesheet" href="css/estoque.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>

<body>
    <?php include 'sidebar.php'; ?>

    <main class="main-content">
        <?php include 'header.php'; ?>

        <div class="message-container">
            <?php if (isset($_SESSION['msg_sucesso'])): ?><div class="alert alert-success"><?= $_SESSION['msg_sucesso'];
                                                                                            unset($_SESSION['msg_sucesso']); ?></div><?php endif; ?>
            <?php if (isset($_SESSION['msg_erro'])): ?><div class="alert alert-danger"><?= $_SESSION['msg_erro'];
                                                                                        unset($_SESSION['msg_erro']); ?></div><?php endif; ?>
        </div>

        <div class="actions-container">
            <div class="search-bar"><i class="fas fa-search"></i><input type="text" placeholder="Pesquisar Funcionário..."></div>
            <a href="funcionario_formulario.php" class="btn-primary"><i class="fas fa-plus"></i> Cadastrar Funcionário</a>
        </div>

        <div class="table-container">
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Nome</th>
                        <th>E-mail</th>
                        <th>Cargo</th>
                        <th>Telefone</th>
                        <th>Ações</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($funcionarios)): ?>
                        <tr>
                            <td colspan="6" class="text-center">Nenhum funcionário cadastrado.</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($funcionarios as $funcionario): ?>
                            <tr>
                                <td><?= htmlspecialchars($funcionario['id']) ?></td>
                                <td><?= htmlspecialchars($funcionario['nome']) ?></td>
                                <td><?= htmlspecialchars($funcionario['email']) ?></td>
                                <td><?= htmlspecialchars($funcionario['cargo']) ?></td>
                                <td><?= htmlspecialchars($funcionario['telefone']) ?></td>
                                <td class="actions">
                                    <a href="funcionario_formulario.php?id=<?= $funcionario['id'] ?>" class="btn-action btn-edit"><i class="fas fa-pencil-alt"></i></a>
                                    <a href="processa_funcionario.php?acao=excluir&id=<?= $funcionario['id'] ?>" class="btn-action btn-delete"><i class="fas fa-trash-alt"></i></a>
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
            button.addEventListener('click', function(event) {
                event.preventDefault();
                const url = this.href;
                Swal.fire({
                    title: 'Tem certeza?',
                    text: "O funcionário será inativado, mas não apagado do histórico.",
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
    <script src="main.js"></script>
    <script src="notificacoes.js"></script>
    <script src="notificacoes_fornecedor.js"></script>
</body>

</html>