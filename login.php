<?php 
session_start(); 
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Streamline</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <link rel="stylesheet" href="css/stylelogin.css">
    <link rel="stylesheet" href="css/imagem.css">
    <style>
        .hidden-field {
            display: none;
        }

        /* ESTILO FINAL E DEFINITIVO PARA ALINHAR TUDO */
        .loginForm .inputLogin .input-group input, 
        .loginForm .inputLogin .input-group select {
            padding: 16px 16px 16px 50px;
            font-size: 1.1rem;
            border-radius: 12px;
            width: 100%;
            background: #f3e8ff;
            border: none;
            color: #4a0f81;
            font-family: 'Poppins', 'Segoe UI', sans-serif;
            transition: all 0.3s ease;
            outline: none;
            box-sizing: border-box;
        }
        
        .loginForm .inputLogin .input-group select {
            -webkit-appearance: none;
            -moz-appearance: none;
            appearance: none;
            cursor: pointer;
            background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 16 16'%3e%3cpath fill='none' stroke='%238e24aa' stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='M2 5l6 6 6-6'/%3e%3c/svg%3e");
            background-repeat: no-repeat;
            background-position: right 1rem center;
            background-size: 1em;
        }

        .loginForm .inputLogin .input-group input:focus,
        .loginForm .inputLogin .input-group select:focus {
            background: #fff;
            box-shadow: 0 0 8px rgba(142, 12, 202, 0.5);
            border: 2px solid #8e24aa;
        }

        .loginForm .inputLogin .input-group select:required:invalid {
            color: #8e24aa;
        }

        /* AJUSTE FINAL DO BOTÃO LOGIN */
        .loginForm .inputSubmit {
            width: 100%;
            margin-right: 0;
            justify-content: center;
        }
    </style>
</head>
<body>
    <div class="main-container">
        <div class="left-panel">
            <div class="header-logo">
                <img class= "logo" src="img/relplogo.png" alt="Relp! Logo">
            </div>
            <nav class="nav-links">
                <a href="home.php" class="nav-link">Início</a>
                <a href="login.php" class="nav-link active">Login</a>
                <a href="formulario.php" class="nav-link">Cadastro</a>
            </nav>
            <div class="login-content">
                <h1 class="main-login-title">LOGIN</h1>
                <p class="login-slogan">Está pronto para começar?</p>

                <form action="testLogin.php" method="POST" class="loginForm">
                    <input type="hidden" name="tipo_acesso" id="tipo_acesso" value="ceo">
                    <div class="inputLogin">
                        
                        <div class="input-group">
                             <i class="fas fa-user-tie icon"></i>
                             <select id="tipo_acesso_select" required>
                                <option value="" disabled selected>Quem está acessando?</option>
                                <option value="ceo">CEO</option>
                                <option value="funcionario">Funcionário</option>
                             </select>
                        </div>

                        <div id="campo-cnpj" class="input-group">
                            <i class="fas fa-building icon"></i>
                            <input type="text" name="cnpj" placeholder="CNPJ" maxlength="14">
                        </div>

                        <div id="campo-email" class="input-group hidden-field">
                            <i class="fas fa-envelope icon"></i>
                            <input type="email" name="email" placeholder="Seu e-mail de funcionário">
                        </div>
                        
                        <div class="input-group">
                            <i class="fas fa-lock icon"></i>
                            <input type="password" name="senha" placeholder="Senha" required>
                        </div>
                    </div>
                    <div class="login-links-container">
                        <a href="recuperar_senha.php" class="forgot">Esqueceu a senha?</a>
                    </div>
                    <input class="inputSubmit" type="submit" name="submit" value="Login">
                    <a href="formulario.php" class="forgot cadastro-link">Ou cadastre-se</a>
                </form>
                <?php
                    if (!empty($_SESSION['erro_login'])) {
                        echo "<p class='error-message'>" . $_SESSION['erro_login'] . "</p>";
                        unset($_SESSION['erro_login']);
                    }
                ?>
            </div>
        </div>
        <div class="right-panel">
            <img src="img/imagemtela.png">
        </div>
    </div>

    <script>
        const tipoAcessoSelect = document.getElementById('tipo_acesso_select');
        const tipoAcessoHiddenInput = document.getElementById('tipo_acesso');
        const campoCnpj = document.getElementById('campo-cnpj');
        const campoEmail = document.getElementById('campo-email');
        const inputCnpj = campoCnpj.querySelector('input');
        const inputEmail = campoEmail.querySelector('input');

        function toggleFields() {
            const selectedValue = tipoAcessoSelect.value;
            tipoAcessoHiddenInput.value = selectedValue || 'ceo';

            if (selectedValue === 'funcionario') {
                campoCnpj.classList.add('hidden-field');
                campoEmail.classList.remove('hidden-field');
                inputCnpj.required = false;
                inputEmail.required = true;
            } else {
                campoCnpj.classList.remove('hidden-field');
                campoEmail.classList.add('hidden-field');
                inputCnpj.required = true;
                inputEmail.required = false;
            }
        }
        tipoAcessoSelect.addEventListener('change', toggleFields);
        document.addEventListener('DOMContentLoaded', toggleFields);
    </script>
</body>
</html>