<!DOCTYPE html>
<html lang="pt-br">
<head>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/stylelogin.css">
    <link rel="stylesheet" href="css/imagem.css">
    <title>Tela de login</title>
</head>
<body>
    <div class="main-container">
        <div class="left-panel">
            <div class="header-logo">
                <img class= "logo" src="img/relplogo.png" alt="Relp! Logo">
            </div>
            <nav class="nav-links">
                <a href="home.php" class="nav-link">Inicio</a>
                <a href="login.php" class="nav-link active">Login</a>
                <a href="formulario.php" class="nav-link">Cadastro</a>
            </nav>
            <div class="login-content">
                <h1 class="main-login-title">LOGIN</h1>
                <p class="login-slogan">Está pronto para começar?</p>
                <form action="testLogin.php" method="POST" class="loginForm">
                    <div class="inputLogin">
                        <div class="input-group">
                            <i class="fas fa-user icon"></i>
                            <input type="text" name="acesso" id="acesso" placeholder="Quem está acessando?">
                        </div>
                        <div class="input-group">
                            <i class="fas fa-building icon"></i>
                            <input type="text" name="cnpj" id="cnpj" placeholder="CNPJ" maxlength="14">
                        </div>
                        <div class="input-group">
                            <i class="fas fa-lock icon"></i>
                            <input type="password" name="senha" id="senha" placeholder="Senha">
                        </div>
                    </div>
                    <div class="login-links-container">
                        <a href="recuperar_senha.php" class="forgot">Esqueceu a senha?</a>
                    </div>
                    <input class="inputSubmit" type="submit" name="submit" value="Login">
                    <a href="formulario.php" class="forgot cadastro-link">Ou cadastre-se</a>
                
                </form>
                </form>
                <?php
                    session_start();
                    if (!empty($_SESSION['erro_login'])) {
                        echo "<script>console.log('" . $_SESSION['erro_login'] . "')</script>";
                        echo "<p class='error-message'>" . $_SESSION['erro_login'] . "</p>";
                        unset($_SESSION['erro_login']);
                    }
                ?>

            </div>
        </div>

        
        <div class="right-panel">
            <img src=img/imagemtela.png>
            
        </div>
    </div>
</body>
</html>
