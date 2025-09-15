<?php 
session_start(); 
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Login do Fornecedor - Streamline</title>
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <link rel="stylesheet" href="css/stylelogin.css">
    <link rel="stylesheet" href="css/imagem.css">
    <style>
        .success-message {
            color: #10B981;
            font-weight: bold;
            text-align: center;
            margin-bottom: 20px;
        }
    </style>
</head>
<body>
    <div class="main-container">
        <div class="left-panel">
            <div class="header-logo">
                <img class="logo" src="img/relplogo.png" alt="Relp! Logo">
            </div>
            <nav class="nav-links">
                <a href="home.php" class="nav-link">Início</a>
                <a href="login.php" class="nav-link">Login</a>
                <a href="formulario.php" class="nav-link">Cadastro</a>
            </nav>
            <div class="login-content">
                <h1 class="main-login-title">ACESSO DO FORNECEDOR</h1>
                <p class="login-slogan">Bem-vindo! Faça login para continuar.</p>
                <?php
                    if (!empty($_SESSION['msg_login'])) {
                        echo "<p class='success-message'>" . htmlspecialchars($_SESSION['msg_login']) . "</p>";
                        unset($_SESSION['msg_login']);
                    }
                    if (!empty($_SESSION['erro_login'])) {
                        echo "<p class='error-message'>" . htmlspecialchars($_SESSION['erro_login']) . "</p>";
                        unset($_SESSION['erro_login']);
                    }
                ?>
                <form action="testa_login_fornecedor.php" method="POST" class="loginForm">
                    <div class="inputLogin">
                        <div class="input-group">
                            <i class="fas fa-envelope icon"></i>
                            <input type="email" name="email" placeholder="Seu e-mail de cadastro" required>
                        </div>
                        <div class="input-group">
                            <i class="fas fa-lock icon"></i>
                            <input type="password" name="senha" placeholder="Sua senha" required>
                        </div>
                    </div>
                    <input class="inputSubmit" type="submit" name="submit" value="Entrar">
                </form>
            </div>
        </div>
        <div class="right-panel">
            <img src="img/imagemtela.png">
        </div>
    </div>
</body>
</html>