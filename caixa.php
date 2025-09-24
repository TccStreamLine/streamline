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
    <title>Caixa - Streamline</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="css/sistema.css">
    <link rel="stylesheet" href="css/caixa.css">
</head>

<body>
    <nav class="sidebar">
        <div class="sidebar-logo">
            <img class="logo" src="img/relplogo.png" alt="Relp! Logo" style="width: 100px;">
        </div>
        <div class="menu-section">
            <h6>MENU</h6>
            <ul class="menu-list">
                <li><a href="sistema.php"><i class="fas fa-home"></i> Início</a></li>
                <li><a href="estoque.php"><i class="fas fa-box"></i> Estoque</a></li>
                <li><a href="agenda.php"><i class="fas fa-calendar-alt"></i> Agenda</a></li>
                <li><a href="fornecedores.php"><i class="fas fa-truck"></i> Fornecimento</a></li>
                <li><a href="vendas.php"><i class="fas fa-chart-bar"></i> Vendas</a></li>
                <li><a href="caixa.php" class="active"><i class="fas fa-cash-register"></i> Caixa</a></li>
                <li><a href="dashboard.php"><i class="fas fa-tachometer-alt"></i> Dashboard</a></li>
                <li><a href="nota_fiscal.php"><i class="fas fa-file-invoice-dollar"></i> Nota Fiscal</a></li>
                <li><a href="servicos.php"><i class="fas fa-concierge-bell"></i> Serviços</a></li>
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
        <header class="main-header">
            <h2>Caixa Aberto</h2>
            <div class="user-profile"><span><?= htmlspecialchars($nome_empresa) ?></span>
                <div class="avatar"><i class="fas fa-user"></i></div>
            </div>
        </header>

        <div class="message-container">
            <?php if (isset($_SESSION['msg_sucesso_caixa'])): ?>
                <div class="alert alert-success" style="background-color: #10B981; color: white; padding: 1rem; border-radius: 8px;">
                    <?= $_SESSION['msg_sucesso_caixa'];
                    unset($_SESSION['msg_sucesso_caixa']); ?>
                </div>
            <?php endif; ?>
        </div>

        <div class="caixa-container">
            <div class="caixa-painel-venda">
                <img src="img/relplogo.png" alt="Logo" class="caixa-logo">

                <div class="form-group-caixa">
                    <input type="text" id="codigo_barras" placeholder="Código de Barras">
                </div>
                <div class="info-group-caixa">
                    <div class="info-box">
                        <label>Valor Unitário</label>
                        <span id="valor_unitario">R$0,00</span>
                    </div>
                    <div class="info-box">
                        <label>Total. Item</label>
                        <span id="total_item">R$0,00</span>
                    </div>
                </div>
                <div class="form-group-caixa">
                    <input type="number" id="quantidade" value="1" min="1" placeholder="Quant. do Item">
                </div>

                <button class="btn-caixa btn-lancamento">Lançar Produto</button>

                <div class="caixa-acoes-secundarias">
                    <button class="btn-caixa btn-secundario" id="btnExcluirVenda">Limpar Venda</button>
                    <a href="vendas.php" class="btn-caixa btn-secundario">Gerenciar Vendas</a>
                </div>

                <div class="form-group-caixa">
                    <input type="text" id="valor_pago" placeholder="Valor Pago pelo Cliente (R$)">
                </div>

                <button class="btn-caixa btn-finalizar">Finalizar Compra</button>
            </div>

            <div class="caixa-painel-lista">
                <div class="lista-header">
                    <h4>Lista de Produtos</h4>
                </div>
                <div class="lista-produtos-venda" id="lista-produtos">
                    <p class="lista-vazia">Nenhum produto lançado.</p>
                </div>
                <div class="caixa-totais">
                    <div><span>Subtotal:</span> <span id="subtotal">R$ 0,00</span></div>
                    <div><span>Troco:</span> <span id="troco">R$ 0,00</span></div>
                    <div class="total-geral"><span>Total:</span> <span id="total_geral">R$ 0,00</span></div>
                </div>
            </div>
        </div>
    </main>

    <script>
        const inputCodigoBarras = document.getElementById('codigo_barras');
        const displayValorUnitario = document.getElementById('valor_unitario');
        const inputQuantidade = document.getElementById('quantidade');
        const displayTotalItem = document.getElementById('total_item');
        const btnLancarProduto = document.querySelector('.btn-lancamento');
        const btnFinalizarCompra = document.querySelector('.btn-finalizar');
        const btnExcluirVenda = document.getElementById('btnExcluirVenda');
        const listaProdutosDiv = document.getElementById('lista-produtos');
        const subtotalDisplay = document.getElementById('subtotal');
        const totalGeralDisplay = document.getElementById('total_geral');
        const inputValorPago = document.getElementById('valor_pago');
        const trocoDisplay = document.getElementById('troco');
        let produtoAtual = null;
        let totalVendaAtual = 0;

        function formatarMoeda(valor) {
            return parseFloat(valor).toLocaleString('pt-BR', {
                style: 'currency',
                currency: 'BRL'
            });
        }

        function parseMoeda(valorString) {
            if (typeof valorString !== 'string') return 0;
            return parseFloat(valorString.replace('R$', '').trim().replace(/\./g, '').replace(',', '.')) || 0;
        }

        function calcularTotalItem() {
            const valorUnitario = produtoAtual ? parseFloat(produtoAtual.valor_venda) : 0;
            const quantidade = parseInt(inputQuantidade.value);
            if (!isNaN(valorUnitario) && !isNaN(quantidade)) {
                const total = valorUnitario * quantidade;
                displayTotalItem.innerText = formatarMoeda(total);
            }
        }

        async function buscarProduto() {
            const codigo = inputCodigoBarras.value.trim();
            produtoAtual = null;
            displayValorUnitario.innerText = 'R$ 0,00';
            if (codigo) {
                try {
                    const response = await fetch(`buscar_produto.php?codigo_barras=${codigo}`);
                    const data = await response.json();
                    if (data.error) {
                        Swal.fire({
                            icon: 'error',
                            title: 'Oops...',
                            text: data.error
                        });
                    } else {
                        produtoAtual = data;
                        displayValorUnitario.innerText = formatarMoeda(data.valor_venda);
                        inputQuantidade.focus();
                        inputQuantidade.select();
                    }
                } catch (error) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Erro de Conexão',
                        text: 'Não foi possível se comunicar com o servidor.'
                    });
                }
            }
            calcularTotalItem();
        }

        function atualizarCarrinhoNaTela(carrinho) {
            listaProdutosDiv.innerHTML = '';
            let subtotal = 0;
            if (carrinho.length === 0) {
                listaProdutosDiv.innerHTML = '<p class="lista-vazia">Nenhum produto lançado.</p>';
            } else {
                const table = document.createElement('table');
                table.innerHTML = `<thead><tr><th>Produto</th><th>Qtd.</th><th>V. Unit.</th><th>V. Total</th><th>Ações</th></tr></thead>`;
                const tbody = document.createElement('tbody');
                carrinho.forEach(item => {
                    const totalItem = item.quantidade * item.valor_unitario;
                    subtotal += totalItem;
                    const tr = document.createElement('tr');
                    tr.innerHTML = `
                        <td>${item.nome}</td>
                        <td>${item.quantidade}</td>
                        <td>${formatarMoeda(item.valor_unitario)}</td>
                        <td>${formatarMoeda(totalItem)}</td>
                        <td><button class="btn-remover-item" data-id="${item.id}"><i class="fas fa-trash-alt"></i></button></td>
                    `;
                    tbody.appendChild(tr);
                });
                table.appendChild(tbody);
                listaProdutosDiv.appendChild(table);
            }
            totalVendaAtual = subtotal;
            subtotalDisplay.innerText = formatarMoeda(subtotal);
            totalGeralDisplay.innerText = formatarMoeda(subtotal);
            calcularTroco();
        }

        async function lancarProduto() {
            if (!produtoAtual) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Atenção',
                    text: 'Por favor, busque um produto válido primeiro.'
                });
                return;
            }
            const dados = {
                acao: 'adicionar',
                produto_id: produtoAtual.id,
                quantidade: parseInt(inputQuantidade.value)
            };
            const response = await fetch('gerenciar_carrinho.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify(dados)
            });
            const carrinhoAtualizado = await response.json();
            atualizarCarrinhoNaTela(carrinhoAtualizado);
            inputCodigoBarras.value = '';
            displayValorUnitario.innerText = 'R$ 0,00';
            inputQuantidade.value = '1';
            displayTotalItem.innerText = 'R$ 0,00';
            produtoAtual = null;
            inputCodigoBarras.focus();
        }

        async function removerProduto(produtoId) {
            const dados = {
                acao: 'remover',
                produto_id: produtoId
            };
            const response = await fetch('gerenciar_carrinho.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify(dados)
            });
            const carrinhoAtualizado = await response.json();
            atualizarCarrinhoNaTela(carrinhoAtualizado);
        }

        function finalizarVenda() {
            Swal.fire({
                title: 'Finalizar Venda?',
                text: "Esta ação é irreversível.",
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#6D28D9',
                cancelButtonColor: '#6B7280',
                confirmButtonText: 'Sim, finalizar',
                cancelButtonText: 'Cancelar'
            }).then(async (result) => {
                if (result.isConfirmed) {
                    try {
                        const response = await fetch('finalizar_venda.php', {
                            method: 'POST'
                        });
                        const data = await response.json();
                        if (data.error) {
                            Swal.fire({
                                icon: 'error',
                                title: 'Erro!',
                                text: data.error,
                                confirmButtonColor: '#6D28D9'
                            });
                        } else if (data.success) {
                            window.location.reload();
                        }
                    } catch (error) {
                        Swal.fire({
                            icon: 'error',
                            title: 'Erro de Conexão',
                            text: 'Ocorreu um erro ao finalizar a venda.',
                            confirmButtonColor: '#6D28D9'
                        });
                    }
                }
            });
        }

        function calcularTroco() {
            const valorPago = parseFloat(inputValorPago.value.replace(',', '.')) || 0;
            let troco = 0;
            if (valorPago > 0 && valorPago >= totalVendaAtual) {
                troco = valorPago - totalVendaAtual;
            }
            trocoDisplay.innerText = formatarMoeda(troco);
        }

        async function excluirVenda() {
            Swal.fire({
                title: 'Limpar Venda?',
                text: "Todos os itens do carrinho serão removidos.",
                showCancelButton: true,
                icon: 'warning',
                confirmButtonColor: '#6D28D9',
                cancelButtonColor: '#6B7280',
                confirmButtonText: 'Sim',
                cancelButtonText: 'Cancelar'
            }).then(async (result) => {
                if (result.isConfirmed) {
                    const dados = {
                        acao: 'limpar'
                    };
                    const response = await fetch('gerenciar_carrinho.php', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json'
                        },
                        body: JSON.stringify(dados)
                    });
                    const carrinhoVazio = await response.json();
                    atualizarCarrinhoNaTela(carrinhoVazio);
                    inputValorPago.value = '';
                    calcularTroco();
                }
            });
        }

        inputCodigoBarras.addEventListener('keypress', e => {
            if (e.key === 'Enter') {
                e.preventDefault();
                buscarProduto();
            }
        });
        inputQuantidade.addEventListener('input', calcularTotalItem);
        btnLancarProduto.addEventListener('click', lancarProduto);
        btnFinalizarCompra.addEventListener('click', finalizarVenda);
        inputValorPago.addEventListener('input', calcularTroco);
        btnExcluirVenda.addEventListener('click', excluirVenda);

        listaProdutosDiv.addEventListener('click', function(e) {
            const removeButton = e.target.closest('.btn-remover-item');
            if (removeButton) {
                const produtoId = removeButton.dataset.id;
                removerProduto(produtoId);
            }
        });
    </script>
    <style>
        .btn-remover-item {
            background: none;
            border: none;
            color: #EF4444;
            cursor: pointer;
            font-size: 1rem;
        }
    </style>
</body>

</html>