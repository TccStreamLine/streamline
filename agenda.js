document.addEventListener('DOMContentLoaded', function() {
    // Elementos do DOM
    const calendarioCorpo = document.getElementById('calendario-corpo');
    const mesAnoEl = document.getElementById('mes-ano');
    const modal = document.getElementById('modal-evento');
    const dataSelecionadaDisplay = document.getElementById('data-selecionada-display');
    const dataSelecionadaInput = document.getElementById('data-selecionada-input');
    const eventosDiaContainer = document.getElementById('eventos-dia-container');
    const eventosDiaTabela = document.getElementById('eventos-dia-tabela').querySelector('tbody');

    let dataAtual = new Date();
    let eventosDoUsuario = [];

    // Buscar todos os eventos do usuário
    async function buscarEventos() {
        try {
            const response = await fetch('buscar_eventos.php');
            eventosDoUsuario = await response.json();
            gerarCalendario(dataAtual.getFullYear(), dataAtual.getMonth());
        } catch (error) {
            console.error("Erro ao buscar eventos:", error);
        }
    }

    // Gerar calendário
    function gerarCalendario(ano, mes) {
        calendarioCorpo.innerHTML = '';
        const primeiroDia = new Date(ano, mes, 1);
        const ultimoDia = new Date(ano, mes + 1, 0);

        mesAnoEl.textContent = `${primeiroDia.toLocaleString('pt-br', { month: 'long' })} de ${ano}`;

        let data = new Date(primeiroDia);
        for (let i = 0; i < primeiroDia.getDay(); i++) {
            calendarioCorpo.innerHTML += `<div class="dia outro-mes"></div>`;
        }

        while (data <= ultimoDia) {
            const diaEl = document.createElement('div');
            diaEl.classList.add('dia');
            diaEl.textContent = data.getDate();

            const dataISO = `${ano}-${String(mes + 1).padStart(2, '0')}-${String(data.getDate()).padStart(2, '0')}`;
            diaEl.dataset.date = dataISO;

            const hoje = new Date();
            if (data.getDate() === hoje.getDate() && data.getMonth() === hoje.getMonth() && data.getFullYear() === hoje.getFullYear()) {
                diaEl.classList.add('hoje');
            }

            if (eventosDoUsuario.some(e => e.data_evento === dataISO)) {
                const marcador = document.createElement('div');
                marcador.classList.add('evento-marcador');
                diaEl.appendChild(marcador);
            }

            calendarioCorpo.appendChild(diaEl);
            data.setDate(data.getDate() + 1);
        }
    }

    // Buscar eventos do dia
    function eventosNoDia(data) {
        return eventosDoUsuario.filter(ev => ev.data_evento === data);
    }

    // Mostrar eventos do dia na tabela
    function mostrarEventosDoDia(data) {
        const eventos = eventosNoDia(data);
        eventosDiaTabela.innerHTML = '';
        if (eventos.length === 0) {
            eventosDiaTabela.innerHTML = `<tr><td colspan="3">Nenhum evento para este dia.</td></tr>`;
        } else {
            eventos.forEach(ev => {
                const tr = document.createElement('tr');
                tr.innerHTML = `
                    <td>${ev.titulo}</td>
                    <td>${ev.data_evento}</td>
                    <td>
                        <button onclick="excluirEvento(${ev.id}, '${data}')">Excluir</button>
                    </td>
                `;
                eventosDiaTabela.appendChild(tr);
            });
        }
        // Botão "Adicionar Evento"
        let addBtn = document.getElementById('btn-adicionar-evento');
        if (addBtn) addBtn.remove();
        addBtn = document.createElement('button');
        addBtn.id = 'btn-adicionar-evento';
        addBtn.textContent = 'Adicionar Evento';
        addBtn.style.marginBottom = '10px';
        addBtn.onclick = () => abrirModal(data);
        eventosDiaContainer.insertBefore(addBtn, eventosDiaContainer.children[1]);
    }

    // Abrir modal
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

    window.excluirEvento = async function(id, data) {
        if (confirm('Deseja realmente excluir este evento?')) {
            await fetch('excluir_evento.php', {
                method: 'POST',
                body: new URLSearchParams({ id })
            });
            await buscarEventos();
            mostrarEventosDoDia(data);
        }
    }

    // Navegação do mês
    document.getElementById('mes-anterior').addEventListener('click', () => {
        dataAtual.setMonth(dataAtual.getMonth() - 1);
        gerarCalendario(dataAtual.getFullYear(), dataAtual.getMonth());
    });

    document.getElementById('mes-seguinte').addEventListener('click', () => {
        dataAtual.setMonth(dataAtual.getMonth() + 1);
        gerarCalendario(dataAtual.getFullYear(), dataAtual.getMonth());
    });

    // Clique em um dia do calendário
    calendarioCorpo.addEventListener('click', (e) => {
        if (e.target.classList.contains('dia') && !e.target.classList.contains('outro-mes')) {
            const data = e.target.dataset.date;
            mostrarEventosDoDia(data);
            // Só abre o modal se não houver eventos
            if (eventosNoDia(data).length === 0) {
                abrirModal(data);
            }
        }
    });

    // Salvar evento
    document.getElementById('form-evento').addEventListener('submit', async function(e) {
        e.preventDefault();
        const formData = new FormData(this);
        try {
            const response = await fetch('salvar_evento.php', {
                method: 'POST',
                body: formData
            });
            const result = await response.json();
            if (result.status === 'success') {
                alert(result.message);
                fecharModal();
                await buscarEventos();
                mostrarEventosDoDia(formData.get('data'));
            } else {
                alert('Erro: ' + result.message);
            }
        } catch (error) {
            console.error("Erro ao salvar evento:", error);
            alert("Ocorreu um erro de comunicação. Tente novamente.");
        }
    });

    // Inicialização
    buscarEventos();
});