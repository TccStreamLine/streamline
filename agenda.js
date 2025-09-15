document.addEventListener('DOMContentLoaded', function () {
    const calendarioCorpo = document.getElementById('calendario-corpo');
    const mesAno = document.getElementById('mes-ano');
    const btnAnterior = document.getElementById('mes-anterior');
    const btnSeguinte = document.getElementById('mes-seguinte');
    const modal = document.getElementById('modal-evento');
    const dataDisplay = document.getElementById('data-selecionada-display');
    const dataInput = document.getElementById('data-selecionada-input');
    let dataAtual = new Date();

    function renderizarCalendario() {
        calendarioCorpo.innerHTML = '';
        const ano = dataAtual.getFullYear();
        const mes = dataAtual.getMonth();
        mesAno.textContent = `${dataAtual.toLocaleString('pt-BR', { month: 'long' })} ${ano}`;

        const primeiroDia = new Date(ano, mes, 1);
        const ultimoDia = new Date(ano, mes + 1, 0);
        const diaSemanaInicio = primeiroDia.getDay();
        const totalDias = ultimoDia.getDate();

        const hoje = new Date();
        const hojeAno = hoje.getFullYear();
        const hojeMes = hoje.getMonth();
        const hojeDia = hoje.getDate();

        for (let i = 0; i < diaSemanaInicio; i++) {
            const div = document.createElement('div');
            div.classList.add('dia-vazio');
            calendarioCorpo.appendChild(div);
        }

        for (let dia = 1; dia <= totalDias; dia++) {
            const div = document.createElement('div');
            div.classList.add('dia-mes');
            div.textContent = dia;
            div.style.cursor = 'pointer';
            div.onclick = function () {
                const dataStr = `${ano}-${String(mes + 1).padStart(2, '0')}-${String(dia).padStart(2, '0')}`;
                // if tiver eventos
                // mostrar eventos ali embaicxo 
                carregarEventosDoDia(dataStr);
                abrirModal(ano, mes + 1, dia);
            };
            if (ano === hojeAno && mes === hojeMes && dia === hojeDia) {
                div.classList.add('dia-hoje');
            }
            calendarioCorpo.appendChild(div);
        }
    }

    btnAnterior.onclick = function () {
        dataAtual.setMonth(dataAtual.getMonth() - 1);
        renderizarCalendario();
    };
    btnSeguinte.onclick = function () {
        dataAtual.setMonth(dataAtual.getMonth() + 1);
        renderizarCalendario();
    };

    function abrirModal(ano, mes, dia) {
        const dataStr = `${ano}-${String(mes).padStart(2, '0')}-${String(dia).padStart(2, '0')}`;
        dataDisplay.textContent = dataStr;
        dataInput.value = dataStr;
        modal.style.display = 'block';
    }

    function carregarEventosDoDia(dataStr) {
        fetch(`buscar_eventos.php?data=${dataStr}`)
            .then(res => res.json())
            .then(eventos => {
                let tbody = document.querySelector('#eventos-dia-tabela tbody');
                if (!tbody) {
                    // Cria tbody se não existir
                    tbody = document.createElement('tbody');
                    document.getElementById('eventos-dia-tabela').appendChild(tbody);
                }
                tbody.innerHTML = '';
                if (eventos.length === 0) {
                    tbody.innerHTML = '<tr><td colspan="3" style="text-align:center;">Nenhum evento para este dia.</td></tr>';
                } else {
                    eventos.forEach(ev => {
                        const tr = document.createElement('tr');
                        tr.innerHTML = `
                            <td>${ev.titulo}</td>
                            <td>${ev.horario || ''}</td>
                            <td>${ev.descricao ? ev.descricao : ''}</td>
                        `;
                        tbody.appendChild(tr);
                    });
                }
            });
    }

    // Ao abrir a página, mostra os eventos de hoje
    renderizarCalendario();
    const hoje = new Date();
    const dataHoje = `${hoje.getFullYear()}-${String(hoje.getMonth() + 1).padStart(2, '0')}-${String(hoje.getDate()).padStart(2, '0')}`;
    carregarEventosDoDia(dataHoje);

    // Envio do formulário do modal
    document.getElementById('form-evento').onsubmit = function(e) {
        e.preventDefault();
        const form = e.target;
        const data = form.data.value;
        const titulo = form.titulo.value;
        const horario = form.horario.value;
        const descricao = form.descricao.value;
        fetch('salvar_evento.php', {
            method: 'POST',
            headers: {'Content-Type': 'application/x-www-form-urlencoded'},
            body: `data=${encodeURIComponent(data)}&titulo=${encodeURIComponent(titulo)}&horario=${encodeURIComponent(horario)}&descricao=${encodeURIComponent(descricao)}`
        })
        .then(res => res.text())
        .then(res => {
            if (res === "ok") {
                fecharModal();
                renderizarCalendario();
                carregarEventosDoDia(data); // Atualiza eventos do dia
                const popup = document.getElementById('notification-popup');
                popup.textContent = "Evento salvo com sucesso!";
                popup.classList.add('show');
                setTimeout(() => popup.classList.remove('show'), 4000);
            } else {
                alert("Erro ao salvar: " + res);
            }
        });
    };

    window.fecharModal = function () {
        modal.style.display = 'none';
    };

    // Fecha modal ao clicar fora do conteúdo
    window.onclick = function (event) {
        if (event.target === modal) {
            fecharModal();
        }
    };
});