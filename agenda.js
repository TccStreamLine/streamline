document.addEventListener('DOMContentLoaded', function () {
    // --- ELEMENTOS DO DOM ---
    const calendarioCorpo = document.getElementById('calendario-corpo');
    const mesAno = document.getElementById('mes-ano');
    const btnAnterior = document.getElementById('mes-anterior');
    const btnSeguinte = document.getElementById('mes-seguinte');
    const btnNovoEvento = document.querySelector('.btn-primary'); // Botão principal
    const modal = document.getElementById('modal-evento');
    const dataDisplay = document.getElementById('data-selecionada-display');
    const dataInput = document.getElementById('data-selecionada-input');
    const formEvento = document.getElementById('form-evento');

    // --- ESTADO DO CALENDÁRIO ---
    let dataAtual = new Date();
    let dataSelecionada = new Date(); // Guarda a data que está selecionada

    // --- FUNÇÕES ---

    /**
     * Capitaliza a primeira letra de uma string.
     * @param {string} str A string para capitalizar.
     * @returns {string} A string capitalizada.
     */
    function capitalize(str) {
        return str.charAt(0).toUpperCase() + str.slice(1);
    }

    /**
     * Renderiza o grid do calendário para o mês e ano em 'dataAtual'.
     */
    function renderizarCalendario() {
        calendarioCorpo.innerHTML = '';
        const ano = dataAtual.getFullYear();
        const mes = dataAtual.getMonth();

        const nomeMes = dataAtual.toLocaleString('pt-BR', { month: 'long' });
        mesAno.textContent = `${capitalize(nomeMes)} de ${ano}`;

        const primeiroDia = new Date(ano, mes, 1);
        const ultimoDia = new Date(ano, mes + 1, 0);
        const diaSemanaInicio = primeiroDia.getDay();
        const totalDias = ultimoDia.getDate();

        const hoje = new Date();
        const hojeAno = hoje.getFullYear();
        const hojeMes = hoje.getMonth();
        const hojeDia = hoje.getDate();

        // Cria células vazias para os dias antes do início do mês
        for (let i = 0; i < diaSemanaInicio; i++) {
            const div = document.createElement('div');
            div.classList.add('dia-vazio');
            calendarioCorpo.appendChild(div);
        }

        // Cria as células para cada dia do mês
        for (let dia = 1; dia <= totalDias; dia++) {
            const div = document.createElement('div');
            div.classList.add('dia-mes');
            div.textContent = dia;

            // Marca o dia de hoje
            if (ano === hoje.getFullYear() && mes === hoje.getMonth() && dia === hoje.getDate()) {
                div.classList.add('dia-hoje');
            }

            // Marca o dia selecionado
            if (ano === dataSelecionada.getFullYear() && mes === dataSelecionada.getMonth() && dia === dataSelecionada.getDate()) {
                div.classList.add('dia-selecionado');
            }

            // Adiciona o evento de clique para selecionar o dia
            div.onclick = () => {
                dataSelecionada = new Date(ano, mes, dia);
                const dataStr = `${ano}-${String(mes + 1).padStart(2, '0')}-${String(dia).padStart(2, '0')}`;
                carregarEventosDoDia(dataStr);
                renderizarCalendario(); // Re-renderiza para atualizar a classe 'dia-selecionado'
            };
            
            calendarioCorpo.appendChild(div);
        }
    }
    
    /**
     * Busca e exibe os eventos para uma data específica.
     * @param {string} dataStr A data no formato 'YYYY-MM-DD'.
     */
    function carregarEventosDoDia(dataStr) {
        fetch(`buscar_eventos.php?data=${dataStr}`)
            .then(res => res.json())
            .then(eventos => {
                const tbody = document.querySelector('#eventos-dia-tabela tbody');
                tbody.innerHTML = ''; // Limpa a tabela
                if (eventos.length === 0) {
                    tbody.innerHTML = '<tr><td colspan="3" style="text-align:center;">Nenhum evento para este dia.</td></tr>';
                } else {
                    eventos.forEach(ev => {
                        const tr = document.createElement('tr');
                        // Ajuste para incluir a coluna de Ações
                        tr.innerHTML = `
                            <td>${ev.titulo}</td>
                            <td>${ev.horario ? ev.horario.substring(0, 5) : ''}</td>
                            <td><button class="btn-acao-excluir" data-id="${ev.id}">Excluir</button></td>
                        `;
                        tbody.appendChild(tr);
                    });
                }
            });
    }

    /**
     * Abre o modal para adicionar um novo evento na data atualmente selecionada.
     */
    function abrirModal() {
        const ano = dataSelecionada.getFullYear();
        const mes = dataSelecionada.getMonth() + 1;
        const dia = dataSelecionada.getDate();
        
        const dataFormatada = `${String(dia).padStart(2, '0')}/${String(mes).padStart(2, '0')}/${ano}`;
        const dataValue = `${ano}-${String(mes).padStart(2, '0')}-${String(dia).padStart(2, '0')}`;
        
        dataDisplay.textContent = dataFormatada;
        dataInput.value = dataValue;
        formEvento.reset(); // Limpa o formulário
        modal.style.display = 'block';
    }

    /**
     * Fecha o modal de eventos.
     */
    window.fecharModal = function () {
        modal.style.display = 'none';
    };

    // --- EVENT LISTENERS ---

    btnAnterior.onclick = () => {
        dataAtual.setMonth(dataAtual.getMonth() - 1);
        renderizarCalendario();
    };

    btnSeguinte.onclick = () => {
        dataAtual.setMonth(dataAtual.getMonth() + 1);
        renderizarCalendario();
    };
    
    // O botão "Novo Evento" agora é responsável por abrir o modal
    btnNovoEvento.onclick = abrirModal;

    // Lida com o envio do formulário para salvar um evento
    formEvento.onsubmit = function(e) {
        e.preventDefault();
        const formData = new URLSearchParams(new FormData(formEvento)).toString();

        fetch('salvar_evento.php', {
            method: 'POST',
            headers: {'Content-Type': 'application/x-www-form-urlencoded'},
            body: formData
        })
        .then(res => res.text())
        .then(res => {
            if (res === "ok") {
                fecharModal();
                carregarEventosDoDia(dataInput.value); // Atualiza a lista de eventos do dia
                
                const popup = document.getElementById('notification-popup');
                popup.textContent = "Evento salvo com sucesso!";
                popup.style.display = 'block'; // Usar display ao invés de class para simplificar
                setTimeout(() => popup.style.display = 'none', 4000);
            } else {
                alert("Erro ao salvar o evento: " + res);
            }
        });
    };

    // Fecha o modal se o usuário clicar fora dele
    window.onclick = function (event) {
        if (event.target === modal) {
            fecharModal();
        }
    };
    
    // --- INICIALIZAÇÃO ---
    function init() {
        renderizarCalendario();
        const dataHojeStr = `${dataSelecionada.getFullYear()}-${String(dataSelecionada.getMonth() + 1).padStart(2, '0')}-${String(dataSelecionada.getDate()).padStart(2, '0')}`;
        carregarEventosDoDia(dataHojeStr);
    }
    
    init();
});