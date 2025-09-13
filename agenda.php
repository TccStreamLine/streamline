<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Minha Agenda</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        /* Estilos básicos para o layout */
        body { font-family: sans-serif; }
        .container { max-width: 900px; margin: 20px auto; padding: 20px; }
        
        /* Estilo para o sino de notificação (adapte ao seu design) */
        .notificacao-container {
            position: relative;
            font-size: 24px;
            cursor: pointer;
            width: 30px; /* Largura para o container */
        }
        .notificacao-contador {
            position: absolute;
            top: -5px;
            right: -10px;
            background-color: red;
            color: white;
            border-radius: 50%;
            padding: 2px 6px;
            font-size: 12px;
            font-weight: bold;
        }

        /* Estilos do Calendário */
        .calendario-header { display: flex; justify-content: space-between; align-items: center; }
        #mes-ano { font-size: 24px; font-weight: bold; }
        .calendario-grid { display: grid; grid-template-columns: repeat(7, 1fr); gap: 5px; }
        .dia-semana, .dia { border: 1px solid #ccc; padding: 10px; text-align: center; }
        .dia-semana { background-color: #f2f2f2; font-weight: bold; }
        .dia { cursor: pointer; min-height: 80px; position: relative; }
        .dia.outro-mes { background-color: #f9f9f9; color: #aaa; }
        .dia.hoje { background-color: #eaf6ff; border: 2px solid #007bff; }
        .dia:hover { background-color: #e9e9e9; }
        .evento-marcador {
            width: 8px; height: 8px;
            background-color: #28a745;
            border-radius: 50%;
            position: absolute;
            bottom: 5px;
            left: 50%;
            transform: translateX(-50%);
        }

        /* Estilos do Modal (janela para adicionar evento) */
        .modal {
            display: none; position: fixed; z-index: 1000;
            left: 0; top: 0; width: 100%; height: 100%;
            background-color: rgba(0,0,0,0.5);
            justify-content: center; align-items: center;
        }
        .modal-content {
            background-color: white; padding: 20px;
            border-radius: 5px; width: 300px;
        }
    </style>
</head>
<body>

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

    <script>
    document.addEventListener('DOMContentLoaded', function() {
        // Elementos do DOM
        const calendarioCorpo = document.getElementById('calendario-corpo');
        const mesAnoEl = document.getElementById('mes-ano');
        const modal = document.getElementById('modal-evento');
        const dataSelecionadaDisplay = document.getElementById('data-selecionada-display');
        const dataSelecionadaInput = document.getElementById('data-selecionada-input');
        
        let dataAtual = new Date();
        let eventosDoUsuario = [];

        // Função para buscar eventos do usuário
        async function buscarEventos() {
            try {
                const response = await fetch('api/buscar_eventos.php');
                eventosDoUsuario = await response.json();
                gerarCalendario(dataAtual.getFullYear(), dataAtual.getMonth());
            } catch (error) {
                console.error("Erro ao buscar eventos:", error);
            }
        }

        // Função principal para gerar o calendário
        function gerarCalendario(ano, mes) {
            calendarioCorpo.innerHTML = ''; // Limpa o calendário anterior
            const primeiroDia = new Date(ano, mes, 1);
            const ultimoDia = new Date(ano, mes + 1, 0);
            
            mesAnoEl.textContent = `${primeiroDia.toLocaleString('pt-br', { month: 'long' })} de ${ano}`;

            let data = new Date(primeiroDia);
            // Preenche os dias vazios no início do mês
            for (let i = 0; i < primeiroDia.getDay(); i++) {
                calendarioCorpo.innerHTML += `<div class="dia outro-mes"></div>`;
            }

            // Preenche os dias do mês
            while (data <= ultimoDia) {
                const diaEl = document.createElement('div');
                diaEl.classList.add('dia');
                diaEl.textContent = data.getDate();
                
                const dataISO = `${ano}-${String(mes + 1).padStart(2, '0')}-${String(data.getDate()).padStart(2, '0')}`;
                diaEl.dataset.date = dataISO;

                // Marca o dia de hoje
                const hoje = new Date();
                if (data.getDate() === hoje.getDate() && data.getMonth() === hoje.getMonth() && data.getFullYear() === hoje.getFullYear()) {
                    diaEl.classList.add('hoje');
                }

                // Marca dias com eventos
                if (eventosDoUsuario.some(e => e.data_evento === dataISO)) {
                    const marcador = document.createElement('div');
                    marcador.classList.add('evento-marcador');
                    diaEl.appendChild(marcador);
                }

                calendarioCorpo.appendChild(diaEl);
                data.setDate(data.getDate() + 1);
            }
        }

        // Funções para controlar o modal
        function abrirModal(data) {
            const [ano, mes, dia] = data.split('-');
            dataSelecionadaDisplay.textContent = `${dia}/${mes}/${ano}`;
            dataSelecionadaInput.value = data;
            modal.style.display = 'flex';
        }
        
        window.fecharModal = function() {
            modal.style.display = 'none';
            document.getElementById('form-evento').reset();
        }

        // Função para checar notificações
        async function checarNotificacoes() {
            try {
                const response = await fetch('api/verificar_notificacoes.php');
                const data = await response.json();
                const contadorEl = document.getElementById('notificacao-contador');
                const containerEl = document.querySelector('.notificacao-container');

                if (data.total > 0) {
                    contadorEl.textContent = data.total;
                    contadorEl.style.display = 'block';
                    containerEl.setAttribute('title', `Hoje: ${data.titulos}`);
                } else {
                    contadorEl.style.display = 'none';
                    containerEl.setAttribute('title', 'Nenhuma notificação hoje');
                }
            } catch (error) {
                console.error("Erro ao verificar notificações:", error);
            }
        }

        // ----- EVENT LISTENERS (Ações do Usuário) -----

        // Botões de navegação do mês
        document.getElementById('mes-anterior').addEventListener('click', () => {
            dataAtual.setMonth(dataAtual.getMonth() - 1);
            gerarCalendario(dataAtual.getFullYear(), dataAtual.getMonth());
        });

        document.getElementById('mes-seguinte').addEventListener('click', () => {
            dataAtual.setMonth(dataAtual.getMonth() + 1);
            gerarCalendario(dataAtual.getFullYear(), dataAtual.getMonth());
        });

        // Abrir modal ao clicar em um dia
        calendarioCorpo.addEventListener('click', (e) => {
            if (e.target.classList.contains('dia') && !e.target.classList.contains('outro-mes')) {
                abrirModal(e.target.dataset.date);
            }
        });

        // Salvar formulário do evento
        document.getElementById('form-evento').addEventListener('submit', async function(e) {
            e.preventDefault();
            const formData = new FormData(this);
            try {
                const response = await fetch('api/salvar_evento.php', {
                    method: 'POST',
                    body: formData
                });
                const result = await response.json();
                if (result.status === 'success') {
                    alert(result.message);
                    fecharModal();
                    buscarEventos(); // Atualiza o calendário com o novo evento
                } else {
                    alert('Erro: ' + result.message);
                }
            } catch (error) {
                console.error("Erro ao salvar evento:", error);
                alert("Ocorreu um erro de comunicação. Tente novamente.");
            }
        });

        // ----- INICIALIZAÇÃO -----
        buscarEventos();
        checarNotificacoes();
        setInterval(checarNotificacoes, 60000); // Verifica notificações a cada 1 minuto
    });
    </script>

</body>
</html>