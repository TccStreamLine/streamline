<?php
    session_start();
    unset($_SESSION["empresa"]);
    unset($_SESSION["cnpj"]);
    unset($_SESSION["senha"]);
    header('Location: home.php');
?>