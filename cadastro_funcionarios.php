<?php
session_start();
include_once('config.php');

// Protege a página, apenas usuários logados (CEO/Admin) podem acessar
if (empty($_SESSION['id'])) {
    header('Location: login.php');
    exit;
}

$nome_empresa = $_SESSION['nome_empresa'] ?? 'Sua empresa';
?>
<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cadastro de Funcionários - Streamline</title>
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
                <a href="funcionarios.php" class="nav-link active">Funcionários</a>
                <a href="sair.php" class="nav-link">Sair</a>
            </nav>
            <div class="login-content">
                <form action="processa_cadastro_funcionario.php" method="POST">
                    <fieldset>
                        <legend><b>CADASTRO DE FUNCIONÁRIOS</b></legend>
                        <p class="subtitle">Olá, <?= htmlspecialchars($nome_empresa) ?>! Cadastre seus usuários aqui.</p>
                        
                        <?php if (isset($_SESSION['msg_erro'])): ?>
                            <p style="color: red; text-align: center; margin-bottom: 15px;"><?= $_SESSION['msg_erro']; unset($_SESSION['msg_erro']); ?></p>
                        <?php endif; ?>

                        <div class="form-row-group">
                            <div class="form-row">
                                <div class="form-group inputBox">
                                    <i class="fa fa-user icon"></i>
                                    <input type="text" name="nome" class="inputUser" placeholder="Nome completo do funcionário" required>
                                </div>
                                <div class="form-group inputBox">
                                    <i class="fa fa-envelope icon"></i>
                                    <input type="email" name="email" class="inputUser" placeholder="E-mail do funcionário" required>
                                </div>
                            </div>
                            <div class="form-row">
                                <div class="form-group inputBox">
                                    <i class="fa fa-briefcase icon"></i>
                                    <input type="text" name="cargo" class="inputUser" placeholder="Cargo (Ex: Vendedor)">
                                </div>
                                <div class="form-group inputBox">
                                    <i class="fa fa-phone icon"></i>
                                    <input type="tel" name="telefone" class="inputUser" placeholder="Telefone">
                                </div>
                            </div>
                            <div class="form-row">
                                <div class="form-group inputBox">
                                    <i class="fa fa-lock icon"></i>
                                    <input type="password" name="senha" class="inputUser" placeholder="Crie uma senha para o funcionário" required>
                                </div>
                            </div>
                        </div>
                        <input type="submit" name="submit" id="submit" value="CADASTRAR AQUI">
                         <a href="funcionarios.php" class="makelogin">Voltar para a lista</a>
                    </fieldset>
                </form>
            </div>
        </div>
        <div class="right-panel">
            <img src="img/imagemtela.png" alt="Imagem ilustrativa">
        </div>
    </div>
</body>
</html>