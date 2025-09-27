<?php
session_start();
include_once('config.php');

if (empty($_SESSION['id'])) {
    header('Location: login.php');
    exit;
}
$nome_empresa = $_SESSION['nome_empresa'] ?? 'Empresa';
?>
<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <title>Minha agenda</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="css/sistema.css">
    <link rel="stylesheet" href="css/agenda.css">
    <script src="agenda.js"></script>
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
                <li><a href="agenda.php" class="active"><i class="fas fa-calendar-alt"></i> Agenda</a></li>
                <li><a href="fornecedores.php"><i class="fas fa-truck"></i> Fornecimento</a></li>
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
        <div class="main-header">
            <div class="header-left">
                <h2><b>Calendário</b></h2>
            </div>

            <div class="navigation-buttons">
                <button id="mes-anterior" class="btn btn-icon"><i class="fas fa-chevron-left"></i></button>
                <h3 id="mes-ano"></h3>
                <button id="mes-seguinte" class="btn btn-icon"><i class="fas fa-chevron-right"></i></button>
            </div>

            <div class="header-right">
                <button class="btn btn-primary"><i class="fas fa-plus"></i> Novo evento</button>
            </div>
        </div>

        <div id="calendar-container">
            <div id="calendario">
                <div class="calendario-grid dias-semana-grid">
                    <div class="dia-semana">Dom</div>
                    <div class="dia-semana">Seg</div>
                    <div class="dia-semana">Ter</div>
                    <div class="dia-semana">Qua</div>
                    <div class="dia-semana">Qui</div>
                    <div class="dia-semana">Sex</div>
                    <div class="dia-semana">Sáb</div>
                </div>
                <div class="calendario-grid" id="calendario-corpo">
                </div>
            </div>

            <div id="eventos-dia-container">
                <h3>Eventos do dia selecionado</h3>
                <div id="lista-eventos">
                    </div>
            </div>
        </div>
    </main>

    <div id="notification-popup"></div>
    <div id="modal-evento" class="modal">
        <div class="modal-content">
            <h3>Adicionar Compromisso</h3>
            <p><strong>Data:</strong> <span id="data-selecionada-display"></span></p>
            <form id="form-evento">
            <input type="hidden" id="evento-id" name="id">

            <input type="hidden" id="data-selecionada-input" name="data">
            
            <label for="titulo-evento">Título:</label><br>
                <input type="text" id="titulo-evento" name="titulo" required style="width: 95%; margin-bottom: 10px;"><br>
                
                <label for="horario-evento">Horário:</label><br>
                <input type="time" id="horario-evento" name="horario" required style="width: 95%; margin-bottom: 10px;"><br>
                
                <label for="descricao-evento">Descrição:</label><br>
                <textarea id="descricao-evento" name="descricao" rows="3" style="width: 95%; margin-bottom: 10px;"></textarea><br>
                
                <button type="submit">Salvar</button>
                <button type="button" onclick="fecharModal()">Cancelar</button>
            </form>
        </div>
    </div>
</body>

</html>
