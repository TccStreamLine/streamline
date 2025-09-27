<?php
session_start();
include_once('config.php');

if (empty($_SESSION['id'])) {
    header('Location: login.php');
    exit;
}

$usuario_id = $_SESSION['id'];
$nome_empresa = $_SESSION['nome_empresa'] ?? 'Empresa';

// --- QUERIES PARA KPIs E GRÁFICOS (CÓDIGO ORIGINAL RESTAURADO) ---
$faturamento_mes = $pdo->prepare("SELECT SUM(valor_total) as total FROM vendas WHERE usuario_id = ? AND MONTH(data_venda) = MONTH(CURDATE()) AND YEAR(data_venda) = YEAR(CURDATE()) AND status = 'finalizada'");
$faturamento_mes->execute([$usuario_id]);
$faturamento_mes = $faturamento_mes->fetchColumn();

$vendas_hoje = $pdo->prepare("SELECT COUNT(id) as total FROM vendas WHERE usuario_id = ? AND DATE(data_venda) = CURDATE() AND status = 'finalizada'");
$vendas_hoje->execute([$usuario_id]);
$vendas_hoje = $vendas_hoje->fetchColumn();

$estoque_baixo = $pdo->prepare("SELECT COUNT(id) as total FROM produtos WHERE quantidade_estoque <= quantidade_minima");
$estoque_baixo->execute();
$estoque_baixo = $estoque_baixo->fetchColumn();

$sql_faturamento_diario = "SELECT DATE(data_venda) as dia, SUM(valor_total) as total FROM vendas WHERE usuario_id = ? AND data_venda >= CURDATE() - INTERVAL 7 DAY AND status = 'finalizada' GROUP BY DATE(data_venda) ORDER BY DATE(data_venda) ASC";
$stmt_faturamento_diario = $pdo->prepare($sql_faturamento_diario);
$stmt_faturamento_diario->execute([$usuario_id]);
$faturamento_diario = $stmt_faturamento_diario->fetchAll(PDO::FETCH_ASSOC);

$labels_faturamento = [];
$data_faturamento = [];
foreach ($faturamento_diario as $row) {
    $labels_faturamento[] = date('d/m', strtotime($row['dia']));
    $data_faturamento[] = $row['total'];
}

$sql_vendas_categoria = "SELECT c.nome, COUNT(vi.id) as total_vendas FROM venda_itens vi JOIN produtos p ON vi.produto_id = p.id JOIN categorias c ON p.categoria_id = c.id JOIN vendas v ON vi.venda_id = v.id WHERE v.usuario_id = ? AND v.status = 'finalizada' GROUP BY c.nome ORDER BY total_vendas DESC";
$stmt_vendas_categoria = $pdo->prepare($sql_vendas_categoria);
$stmt_vendas_categoria->execute([$usuario_id]);
$vendas_categoria = $stmt_vendas_categoria->fetchAll(PDO::FETCH_ASSOC);

$labels_categoria = [];
$data_categoria = [];
foreach ($vendas_categoria as $row) {
    $labels_categoria[] = $row['nome'];
    $data_categoria[] = $row['total_vendas'];
}

$sql_top_produtos = "SELECT p.nome, SUM(vi.quantidade) as total_vendido FROM venda_itens vi JOIN produtos p ON vi.produto_id = p.id JOIN vendas v ON vi.venda_id = v.id WHERE v.usuario_id = ? AND v.status = 'finalizada' GROUP BY p.nome ORDER BY total_vendido DESC LIMIT 5";
$stmt_top_produtos = $pdo->prepare($sql_top_produtos);
$stmt_top_produtos->execute([$usuario_id]);
$top_produtos = $stmt_top_produtos->fetchAll(PDO::FETCH_ASSOC);


$insight_melhor_dia = $pdo->prepare("SELECT DAYNAME(data_venda) as dia, SUM(valor_total) as total FROM vendas WHERE usuario_id = ? AND data_venda >= CURDATE() - INTERVAL 7 DAY AND status = 'finalizada' GROUP BY DAYNAME(data_venda) ORDER BY total DESC LIMIT 1");
$insight_melhor_dia->execute([$usuario_id]);
$melhor_dia = $insight_melhor_dia->fetch(PDO::FETCH_ASSOC);

$insight_produto_campeao = $pdo->prepare("SELECT p.nome FROM venda_itens vi JOIN produtos p ON vi.produto_id = p.id JOIN vendas v ON vi.venda_id = v.id WHERE v.usuario_id = ? AND v.status = 'finalizada' GROUP BY p.nome ORDER BY SUM(vi.quantidade) DESC LIMIT 1");
$insight_produto_campeao->execute([$usuario_id]);
$produto_campeao = $insight_produto_campeao->fetchColumn();
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Streamline</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="css/sistema.css">
    <link rel="stylesheet" href="css/dashboard.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
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
                <li><a href="agenda.php"><i class="fas fa-calendar-alt"></i> Agenda</a></li>
                <li><a href="fornecedores.php"><i class="fas fa-truck"></i> Fornecimento</a></li>
                <li><a href="vendas.php"><i class="fas fa-chart-bar"></i> Vendas</a></li>
                <li><a href="caixa.php"><i class="fas fa-cash-register"></i> Caixa</a></li>
                <li><a href="dashboard.php" class="active"><i class="fas fa-tachometer-alt"></i> Dashboard</a></li>
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
        <header class="main-header">
            <h2>Dashboard</h2>
            <div class="user-profile"><span><?= htmlspecialchars($nome_empresa) ?></span>
                <div class="avatar"><i class="fas fa-user"></i></div>
            </div>
        </header>
        
        <div class="dashboard-grid">
            <div class="kpi-card">
                <i class="fas fa-dollar-sign icon"></i>
                <div class="kpi-info">
                    <span class="kpi-value">R$ <?= number_format($faturamento_mes ?: 0, 2, ',', '.') ?></span>
                    <span class="kpi-label">Faturamento este mês</span>
                </div>
            </div>
            <div class="kpi-card">
                <i class="fas fa-shopping-cart icon"></i>
                <div class="kpi-info">
                    <span class="kpi-value"><?= $vendas_hoje ?: 0 ?></span>
                    <span class="kpi-label">Vendas hoje</span>
                </div>
            </div>
            <div class="kpi-card">
                <i class="fas fa-exclamation-triangle icon danger"></i>
                <div class="kpi-info">
                    <span class="kpi-value"><?= $estoque_baixo ?: 0 ?></span>
                    <span class="kpi-label">Produtos com estoque baixo</span>
                </div>
            </div>

            <div class="chart-card large">
                <h3>Faturamento Diário (Últimos 7 dias)</h3>
                <div class="chart-container"><canvas id="faturamentoDiarioChart"></canvas></div>
            </div>

            <div class="chart-card">
                <h3>Vendas por Categoria</h3>
                <div class="chart-container"><canvas id="vendasCategoriaChart"></canvas></div>
            </div>

            <div class="list-card">
                <h3>Produtos Mais Vendidos</h3>
                <ul>
                    <?php if (empty($top_produtos)): ?>
                        <li>Nenhuma venda registrada ainda.</li>
                    <?php else: ?>
                        <?php foreach ($top_produtos as $produto): ?>
                            <li>
                                <span class="produto-nome"><?= htmlspecialchars($produto['nome']) ?></span>
                                <span class="produto-qtd"><?= $produto['total_vendido'] ?> vendidos</span>
                            </li>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </ul>
            </div>

            <div class="list-card" style="grid-column: span 2;">
                <h3><i class="fas fa-lightbulb" style="color: #F59E0B;"></i> Insights da Semana</h3>
                <ul>
                    <?php if ($melhor_dia): ?>
                        <li class="insight-item">O dia de maior faturamento nos últimos 7 dias foi <strong><?= htmlspecialchars($melhor_dia['dia']) ?></strong>. Bom trabalho!</li>
                    <?php endif; ?>

                    <?php if ($produto_campeao): ?>
                        <li class="insight-item">O produto campeão de vendas é o <strong><?= htmlspecialchars($produto_campeao) ?></strong>. Considere repor o estoque.</li>
                    <?php endif; ?>

                    <?php if ($estoque_baixo > 0): ?>
                        <li class="insight-item" style="color: #EF4444;"><strong>Atenção:</strong> Você tem <strong><?= $estoque_baixo ?></strong> produto(s) com nível de estoque baixo ou zerado.</li>
                    <?php else: ?>
                        <li class="insight-item" style="color: #10B981;">Seu controle de estoque está em dia! Nenhum produto com estoque baixo.</li>
                    <?php endif; ?>
                </ul>
            </div>
        </div>
        
        <div id="chat-launcher">
            <i class="fas fa-robot"></i>
        </div>
        <div id="chat-container" class="hidden">
            <div class="chat-header">
                <h3>Converse com Relp! IA</h3>
                <button id="close-chat"><i class="fas fa-times"></i></button>
            </div>
            <div class="chat-messages" id="chat-messages">
                <div class="message ai">
                    Olá! Eu sou Relp!, sua assistente de IA. Pergunte-me sobre seus dados, como "Qual o faturamento deste mês?" ou "Qual o produto mais vendido?".
                </div>
            </div>
            <div class="chat-input-form">
                <div id="typing-indicator" class="hidden">
                    <div class="dot"></div>
                    <div class="dot"></div>
                    <div class="dot"></div>
                </div>
                <form id="ai-chat-form">
                    <input type="text" id="chat-input" placeholder="Faça uma pergunta..." autocomplete="off">
                    <button type="submit"><i class="fas fa-paper-plane"></i></button>
                </form>
            </div>
        </div>
    </main>
    
    <script>
        const faturamentoCtx = document.getElementById('faturamentoDiarioChart').getContext('2d');
        new Chart(faturamentoCtx, {
            type: 'bar',
            data: {
                labels: <?= json_encode($labels_faturamento) ?>,
                datasets: [{
                    label: 'Faturamento (R$)',
                    data: <?= json_encode($data_faturamento) ?>,
                    backgroundColor: '#6D28D9',
                    borderRadius: 5
                }]
            },
            options: {
                scales: {
                    y: {
                        beginAtZero: true
                    }
                },
                responsive: true,
                maintainAspectRatio: false
            }
        });
        
        const categoriaCtx = document.getElementById('vendasCategoriaChart').getContext('2d');
        new Chart(categoriaCtx, {
            type: 'doughnut',
            data: {
                labels: <?= json_encode($labels_categoria) ?>,
                datasets: [{
                    label: 'Vendas',
                    data: <?= json_encode($data_categoria) ?>,
                    backgroundColor: ['#6D28D9', '#D946EF', '#3B82F6', '#14B8A6', '#F97316', '#A78BFA'],
                    hoverOffset: 4
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'bottom',
                    }
                }
            }
        });
    </script>

    <script src="dashboard_chat.js"></script>
</body>

</html>
