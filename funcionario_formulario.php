<?php
session_start();
include_once('config.php');

if (empty($_SESSION['id'])) {
    header('Location: login.php');
    exit;
}

$modo_edicao = false;
$funcionario_para_editar = [];
$titulo_pagina = "Cadastrar Novo Funcionário";

if (isset($_GET['id']) && !empty($_GET['id'])) {
    $modo_edicao = true;
    $id = filter_var($_GET['id'], FILTER_VALIDATE_INT);
    $titulo_pagina = "Editar Funcionário";

    $stmt = $pdo->prepare("SELECT * FROM funcionarios WHERE id = :id");
    $stmt->execute([':id' => $id]);
    $funcionario_para_editar = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$funcionario_para_editar) {
        $_SESSION['msg_erro'] = "Funcionário não encontrado.";
        header('Location: funcionarios.php');
        exit;
    }
}

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
    <link rel="stylesheet" href="css/formulario_fornecedor.css"> 
</head>
<body>
    <nav class="sidebar">
    </nav>

    <main class="main-content">
        <header class="main-header">
            <h2>Início > <?= $titulo_pagina ?></h2>
            <div class="user-profile"><span><?= htmlspecialchars($nome_empresa) ?></span><div class="avatar"><i class="fas fa-user"></i></div></div>
        </header>

        <div class="form-container-figma">
            <h3 class="form-title-figma"><?= $modo_edicao ? 'EDITAR DADOS DO FUNCIONÁRIO' : 'CADASTRAR NOVO FUNCIONÁRIO' ?></h3>
            <form action="processa_funcionario.php" method="POST">
                <input type="hidden" name="acao" value="<?= $modo_edicao ? 'editar' : 'cadastrar' ?>">
                <input type="hidden" name="funcionario_id" value="<?= $funcionario_para_editar['id'] ?? '' ?>">
                
                <div class="input-group-figma">
                    <i class="fas fa-user"></i>
                    <input type="text" name="nome" placeholder="Nome Completo*" required value="<?= htmlspecialchars($funcionario_para_editar['nome'] ?? '') ?>">
                </div>
                 <div class="input-group-figma">
                    <i class="fas fa-envelope"></i>
                    <input type="email" name="email" placeholder="E-mail*" required value="<?= htmlspecialchars($funcionario_para_editar['email'] ?? '') ?>">
                </div>
                 <div class="input-group-figma">
                    <i class="fas fa-briefcase"></i>
                    <input type="text" name="cargo" placeholder="Cargo" value="<?= htmlspecialchars($funcionario_para_editar['cargo'] ?? '') ?>">
                </div>
                 <div class="input-group-figma">
                    <i class="fas fa-phone"></i>
                    <input type="text" name="telefone" placeholder="Telefone" value="<?= htmlspecialchars($funcionario_para_editar['telefone'] ?? '') ?>">
                </div>
                
                <div class="form-actions-figma">
                    <button type="submit" class="btn-figma-primary">
                        <?= $modo_edicao ? 'SALVAR ALTERAÇÕES' : 'CADASTRAR AQUI' ?>
                    </button>
                </div>
            </form>
        </div>
    </main>
</body>
</html>