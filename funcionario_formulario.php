<?php
session_start();
include_once('config.php');

if (empty($_SESSION['id']) || $_SESSION['role'] !== 'ceo') {
    header('Location: sistema.php');
    exit;
}

$modo_edicao = false;
$funcionario = [];
$titulo_pagina = "Cadastro de Funcionário";
$titulo_formulario = "CADASTRO DE FUNCIONÁRIOS";
$nome_botao = "CADASTRAR AQUI";
$pagina_ativa = 'funcionarios';
$titulo_header = 'Funcionários > ' . $titulo_pagina;

if (isset($_GET['id']) && !empty($_GET['id'])) {
    $id = filter_var($_GET['id'], FILTER_VALIDATE_INT);
    if ($id) {
        $modo_edicao = true;
        $titulo_pagina = "Editar Funcionário";
        $titulo_formulario = "EDIÇÃO DE FUNCIONÁRIOS";
        $nome_botao = "SALVAR ALTERAÇÕES";

        $stmt = $pdo->prepare("SELECT * FROM funcionarios WHERE id = :id AND usuario_id = :usuario_id");
        $stmt->execute([':id' => $id, ':usuario_id' => $_SESSION['id']]);
        $funcionario = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$funcionario) {
            $_SESSION['msg_erro'] = "Funcionário não encontrado.";
            header('Location: funcionarios.php');
            exit;
        }
    }
}

$nome_empresa = $_SESSION['nome_empresa'] ?? 'Sua empresa';
?>
<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $titulo_pagina ?> - Streamline</title>
    <link rel="stylesheet" href="css/stylecadastro.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
</head>

<body>
    <div class="main-container">
        <div class="left-panel">
            <header class="header-logo">
                <img src="img/relplogo.png" alt="Logo" class="logo">
            </header>
            <nav class="nav-links">
                <a href="sistema.php" class="nav-link">Início</a>
                <a href="funcionario_formulario.php" class="nav-link active">Funcionários</a>
                <a href="sair.php" class="nav-link">Sair</a>
            </nav>
            <div class="login-content">
                <form action="processa_funcionario.php" method="POST">
                    <input type="hidden" name="acao" value="<?= $modo_edicao ? 'editar' : 'cadastrar' ?>">
                    <input type="hidden" name="id" value="<?= $funcionario['id'] ?? '' ?>">
                    <fieldset>
                        <legend><b><?= $titulo_formulario ?></b></legend>
                        <p class="subtitle">Olá, <?= htmlspecialchars($nome_empresa) ?>! Gerencie seus usuários aqui.</p>

                        <?php if (isset($_SESSION['msg_erro_funcionario'])): ?>
                            <p style="color: red; text-align: center; margin-bottom: 15px;"><?= $_SESSION['msg_erro_funcionario'];
                                                                                            unset($_SESSION['msg_erro_funcionario']); ?></p>
                        <?php endif; ?>

                        <div class="form-row-group">
                            <div class="form-row">
                                <div class="form-group inputBox">
                                    <i class="fa fa-user icon"></i>
                                    <input type="text" name="nome" class="inputUser" placeholder="Nome do funcionário" required value="<?= htmlspecialchars($funcionario['nome'] ?? '') ?>">
                                </div>
                                <div class="form-group inputBox">
                                    <i class="fa fa-envelope icon"></i>
                                    <input type="email" name="email" class="inputUser" placeholder="E-mail" required value="<?= htmlspecialchars($funcionario['email'] ?? '') ?>">
                                </div>
                            </div>
                            <div class="form-row">
                                <div class="form-group inputBox">
                                    <i class="fa fa-briefcase icon"></i>
                                    <input type="text" name="cargo" class="inputUser" placeholder="Cargo (Ex: Vendedor)" value="<?= htmlspecialchars($funcionario['cargo'] ?? '') ?>">
                                </div>
                                <div class="form-group inputBox">
                                    <i class="fa fa-phone icon"></i>
                                    <input type="tel" name="telefone" class="inputUser" placeholder="Telefone" value="<?= htmlspecialchars($funcionario['telefone'] ?? '') ?>">
                                </div>
                            </div>
                            <div class="form-row">
                                <div class="form-group inputBox">
                                    <i class="fa fa-lock icon"></i>
                                    <input type="password" name="senha" class="inputUser" placeholder="<?= $modo_edicao ? 'Nova senha (deixe em branco para manter)' : 'Senha do funcionário' ?>" <?= $modo_edicao ? '' : 'required' ?>>
                                </div>
                            </div>
                        </div>
                        <input type="submit" name="submit" id="submit" value="<?= $nome_botao ?>">
                    </fieldset>
                </form>
            </div>
        </div>
        <div class="right-panel">
            <img src="img/imagemtela.png" alt="Imagem ilustrativa">
        </div>
    </div>
    <script src="main.js"></script>
    <script src="notificacoes.js"></script>
    <script src="notificacoes_fornecedor.js"></script>
</body>

</html>