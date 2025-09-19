<?php
session_start();
// Inclui seu arquivo de configuração que estabelece a conexão $pdo
include_once('config.php');

// 1. VERIFICAÇÃO DE SEGURANÇA: Garante que o usuário está logado
if (empty($_SESSION['id'])) {
    // Se não estiver logado, retorna um erro de "Não Autorizado" e para a execução
    http_response_code(403); 
    exit('Não autorizado');
}

// 2. COLETA E LIMPEZA DOS DADOS DO FORMULÁRIO
// Usamos o operador '??' para evitar erros caso a variável não exista
$usuario_id = $_SESSION['id'];
$data = $_POST['data'] ?? '';       // Ex: '2025-09-17'
$titulo = $_POST['titulo'] ?? '';
$horario = $_POST['horario'] ?? '';   // Ex: '16:30'
$descricao = $_POST['descricao'] ?? '';

// 3. VALIDAÇÃO DOS DADOS ESSENCIAIS
// Garante que os campos mais importantes não estão vazios
if (!empty($data) && !empty($titulo) && !empty($horario)) {
    
    // --- A CORREÇÃO PRINCIPAL ESTÁ AQUI ---
    // 4. COMBINAÇÃO DA DATA E HORA EM UM FORMATO DATETIME VÁLIDO
    // Junta a string da data, um espaço, e a string da hora. Ex: '2025-09-17 16:30'
    $data_inicio_completa = $data . ' ' . $horario;

    try {
        // 5. PREPARAÇÃO E EXECUÇÃO DO COMANDO SQL
        // O SQL agora insere dados apenas nas colunas necessárias.
        // Note que a coluna `horario` foi removida daqui, pois é redundante.
        $stmt = $pdo->prepare("INSERT INTO eventos (usuario_id, titulo, inicio, descricao) VALUES (?, ?, ?, ?)");
        
        // Executa a query passando os valores na ordem correta
        // Usamos a nova variável $data_inicio_completa para a coluna `inicio`
        $stmt->execute([$usuario_id, $titulo, $data_inicio_completa, $descricao]);
        
        // 6. RESPOSTA DE SUCESSO
        // Envia "ok" de volta para o JavaScript, que entende como sucesso
        echo "ok";

    } catch (PDOException $e) {
        // Em caso de erro no banco de dados, retorna uma mensagem clara
        http_response_code(500); // Erro interno do servidor
        // Para depuração: exit('Erro no banco de dados: ' . $e->getMessage());
        exit('Ocorreu um erro ao salvar o evento.'); // Mensagem para o usuário
    }

} else {
    // Se os dados essenciais estiverem faltando, retorna um erro de "Requisição Inválida"
    http_response_code(400);
    echo "Dados inválidos. Por favor, preencha todos os campos obrigatórios.";
}
?>