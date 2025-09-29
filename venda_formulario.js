// JavaScript para Formulário de Venda - Streamline

document.addEventListener('DOMContentLoaded', function () {

    // Event listener para cliques dinâmicos
    document.addEventListener('click', function (e) {
        // Adiciona evento change para selects de produto adicionados dinamicamente
        if (e.target && e.target.classList.contains('produto-select')) {
            e.target.addEventListener('change', preencherValor);
        }

        // Remove item da venda quando clica no botão remover
        if (e.target && e.target.classList.contains('btn-remover')) {
            e.target.closest('.item-venda').remove();
        }
    });

    /**
     * Preenche automaticamente o valor do produto quando selecionado
     * @param {Event} event - Evento de mudança do select
     */
    function preencherValor(event) {
        const select = event.target;
        const selectedOption = select.options[select.selectedIndex];
        const valor = selectedOption.getAttribute('data-valor');
        const valorInput = select.closest('.item-venda').querySelector('.valor-input');

        if (valor) {
            // Converte ponto para vírgula (padrão brasileiro)
            valorInput.value = valor.replace('.', ',');
        }
    }

    // Funcionalidade para adicionar novos itens à venda
    const btnAdicionar = document.getElementById('btn-adicionar-item');
    if (btnAdicionar) {
        btnAdicionar.addEventListener('click', function () {
            const container = document.getElementById('itens-container');
            const index = container.children.length;

            // Clona o primeiro item para criar um novo
            const novoItem = container.children[0].cloneNode(true);

            // Atualiza os nomes dos campos para o novo índice
            novoItem.querySelector('select').name = `itens[${index}][produto_id]`;
            novoItem.querySelector('.quantidade-input').name = `itens[${index}][quantidade]`;
            novoItem.querySelector('.valor-input').name = `itens[${index}][valor_venda]`;

            // Reseta os valores dos campos
            novoItem.querySelector('select').value = '';
            novoItem.querySelector('.quantidade-input').value = '1';
            novoItem.querySelector('.valor-input').value = '';

            // Mostra o botão remover (estava oculto no primeiro item)
            const btnRemover = novoItem.querySelector('.btn-remover');
            btnRemover.classList.remove('hidden');

            // Adiciona o novo item ao container
            container.appendChild(novoItem);
        });
    }

    // Adiciona evento change para o primeiro select (já presente na página)
    const firstSelect = document.querySelector('.produto-select');
    if (firstSelect) {
        firstSelect.addEventListener('change', preencherValor);
    }

});