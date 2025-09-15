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
<?php if (isset($_GET['msg']) && $_GET['msg'] === 'notificacao'): ?>
    <div class="alerta" style="background:#ffeeba; color:#856404; padding:10px; margin-bottom:10px; border-radius:5px;">
        Você tem eventos importantes, verifique sua agenda.
    </div>
<?php endif; ?>

    <div class="container">  
        <div class="notificacao-container" title="Nenhuma notificação hoje">
            <i class="fa fa-bell"></i>
            <span id="notificacao-contador" class="notificacao-contador" style="display:none;">0</span>
        </div>
        <hr>
        <h2>Agenda de Compromissos</h2>
        <div id="calendario">
            <div class="calendario-header">
                <button id="mes-anterior">&lt;</button>
                <h3 id="mes-ano"></h3>
                <button id="mes-seguinte">&gt;</button>
            </div>
            <div class="calendario-grid">
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
    </div>

    <div id="modal-evento" class="modal">
        <div class="modal-content">
            <h3>Adicionar Compromisso</h3>
            <p><strong>Data:</strong> <span id="data-selecionada-display"></span></p>
            <form id="form-evento">
                <input type="hidden" id="data-selecionada-input" name="data">
                <label for="titulo">Título:</label><br>
                <input type="text" id="titulo-evento" name="titulo" required style="width: 95%; margin-bottom: 10px;"><br>
                <button type="submit">Salvar</button>
                <button type="button" onclick="fecharModal()">Cancelar</button>
            </form>
        </div>
    </div>
    <div id="eventos-dia-container">
        <h3>Eventos do dia selecionado</h3>
        <table id="eventos-dia-tabela" style="width:100%; border-collapse:collapse;">
            <thead>
                <tr>
                    <th>Título</th>
                    <th>Início</th>
                    <th>Ações</th>
                </tr>
            </thead>
            <tbody>
                <!-- Eventos serão inseridos aqui via JS -->
            </tbody>
        </table>
    </div>

</body>
</html>