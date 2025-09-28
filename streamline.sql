-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Tempo de geração: 28/09/2025 às 21:36
-- Versão do servidor: 10.4.32-MariaDB
-- Versão do PHP: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Banco de dados: `streamline`
--

-- --------------------------------------------------------

--
-- Estrutura para tabela `categorias`
--

CREATE TABLE `categorias` (
  `id` int(11) NOT NULL,
  `nome` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `categorias`
--

INSERT INTO `categorias` (`id`, `nome`) VALUES
(10, 'Alimentos'),
(11, 'Eletrônicos'),
(9, 'Movéis');

-- --------------------------------------------------------

--
-- Estrutura para tabela `eventos`
--

CREATE TABLE `eventos` (
  `id` int(11) NOT NULL,
  `titulo` varchar(255) NOT NULL COMMENT 'O título ou nome do evento.',
  `inicio` datetime NOT NULL COMMENT 'Data e hora de início do evento.',
  `horario` time DEFAULT NULL,
  `fim` datetime DEFAULT NULL COMMENT 'Data e hora de término do evento (opcional).',
  `usuario_id` int(11) NOT NULL COMMENT 'Chave estrangeira que liga o evento ao usuário que o criou.',
  `descricao` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `eventos`
--

INSERT INTO `eventos` (`id`, `titulo`, `inicio`, `horario`, `fim`, `usuario_id`, `descricao`) VALUES
(32, 'Apresentação TCC', '2025-09-28 14:00:00', '14:00:00', NULL, 22, '');

-- --------------------------------------------------------

--
-- Estrutura para tabela `fornecedores`
--

CREATE TABLE `fornecedores` (
  `id` int(11) NOT NULL,
  `razao_social` varchar(255) NOT NULL,
  `cnpj` varchar(20) NOT NULL,
  `email` varchar(150) DEFAULT NULL,
  `telefone` varchar(20) DEFAULT NULL,
  `senha` varchar(255) DEFAULT NULL,
  `reset_token` varchar(255) DEFAULT NULL,
  `reset_token_expire` datetime DEFAULT NULL,
  `status` varchar(20) NOT NULL DEFAULT 'ativo'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `fornecedores`
--

INSERT INTO `fornecedores` (`id`, `razao_social`, `cnpj`, `email`, `telefone`, `senha`, `reset_token`, `reset_token_expire`, `status`) VALUES
(14, 'Arthur', '12345678901234', 'lastzrr@gmail.com', '11947010600', '$2y$10$vFNBShy7rn/3foTFdAx6oO5IMFKzuH/jHUKPSQZ.x2fL.7DnzdOwi', NULL, NULL, 'ativo');

-- --------------------------------------------------------

--
-- Estrutura para tabela `produtos`
--

CREATE TABLE `produtos` (
  `id` int(11) NOT NULL,
  `codigo_barras` varchar(255) DEFAULT NULL,
  `nome` varchar(255) NOT NULL,
  `especificacao` text DEFAULT NULL,
  `quantidade_estoque` int(11) NOT NULL DEFAULT 0,
  `quantidade_minima` int(11) NOT NULL DEFAULT 5,
  `valor_compra` decimal(10,2) NOT NULL,
  `valor_venda` decimal(10,2) NOT NULL,
  `categoria_id` int(11) DEFAULT NULL,
  `fornecedor_id` int(11) DEFAULT NULL,
  `data_cadastro` timestamp NOT NULL DEFAULT current_timestamp(),
  `status` varchar(20) NOT NULL DEFAULT 'ativo'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `produtos`
--

INSERT INTO `produtos` (`id`, `codigo_barras`, `nome`, `especificacao`, `quantidade_estoque`, `quantidade_minima`, `valor_compra`, `valor_venda`, `categoria_id`, `fornecedor_id`, `data_cadastro`, `status`) VALUES
(11, '1', 'Cadeira de madeira', '', 97, 5, 20.00, 60.00, 9, 6, '2025-09-28 16:47:11', 'ativo'),
(12, '2', 'Salgadinho Fofura', '', 55, 5, 0.50, 7.50, 10, 14, '2025-09-28 16:47:50', 'ativo'),
(13, '3', 'Mouse Attack Shark X3', '', 69, 5, 60.00, 150.00, 11, 6, '2025-09-28 16:48:28', 'ativo');

-- --------------------------------------------------------

--
-- Estrutura para tabela `servicos_prestados`
--

CREATE TABLE `servicos_prestados` (
  `id` int(11) NOT NULL,
  `usuario_id` int(11) NOT NULL,
  `nome_servico` varchar(255) NOT NULL,
  `especificacao` text DEFAULT NULL,
  `horas_gastas` decimal(10,2) DEFAULT NULL,
  `data_prestacao` datetime DEFAULT NULL,
  `gastos` decimal(10,2) DEFAULT NULL,
  `valor_venda` decimal(10,2) NOT NULL,
  `produtos_usados` text DEFAULT NULL,
  `status` varchar(20) NOT NULL DEFAULT 'ativo'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `servicos_prestados`
--

INSERT INTO `servicos_prestados` (`id`, `usuario_id`, `nome_servico`, `especificacao`, `horas_gastas`, `data_prestacao`, `gastos`, `valor_venda`, `produtos_usados`, `status`) VALUES
(4, 22, 'Corte de cabelo', 'Corte padrão', 1.00, '2025-09-28 13:24:00', 2.50, 40.00, '1 gilette e 1 gola alta', 'ativo');

-- --------------------------------------------------------

--
-- Estrutura para tabela `usuarios`
--

CREATE TABLE `usuarios` (
  `id` int(11) NOT NULL,
  `nome_empresa` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `telefone` varchar(20) NOT NULL,
  `ramo_atuacao` varchar(100) NOT NULL,
  `quantidade_funcionarios` varchar(20) NOT NULL,
  `natureza_juridica` varchar(100) NOT NULL,
  `cnpj` varchar(18) NOT NULL,
  `senha` varchar(255) NOT NULL,
  `senha_funcionarios` varchar(255) DEFAULT NULL,
  `reset_token` varchar(255) DEFAULT NULL,
  `reset_token_expire` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Despejando dados para a tabela `usuarios`
--

INSERT INTO `usuarios` (`id`, `nome_empresa`, `email`, `telefone`, `ramo_atuacao`, `quantidade_funcionarios`, `natureza_juridica`, `cnpj`, `senha`, `senha_funcionarios`, `reset_token`, `reset_token_expire`) VALUES
(22, 'Adati', 'lastzrr@gmail.com', '11947010600', 'Atacado/Varejo', '15', 'LTDA', '12345678901234', '$2y$10$7734Nf939zA9I8OlEFSwEOBwAg0kDWC9XGNXLWbJn8S88PKOZoGoK', '$2y$10$Qb/sWHk7/WRhQOYm8/qOi.l//MHD6CLzEvdPWPjPg9mFPXpLmQ33a', 'a2dbdcc51ab9359257caa7bf189d6a46aa183c2e34cd363d572407c7639b88003d324326d2f6bdaec931e58354c0452c3205', '2025-09-28 15:46:46');

-- --------------------------------------------------------

--
-- Estrutura para tabela `vendas`
--

CREATE TABLE `vendas` (
  `id` int(11) NOT NULL,
  `usuario_id` int(11) NOT NULL,
  `valor_total` decimal(10,2) NOT NULL,
  `descricao` text DEFAULT NULL,
  `data_venda` timestamp NOT NULL DEFAULT current_timestamp(),
  `status` varchar(50) NOT NULL DEFAULT 'finalizada'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `vendas`
--

INSERT INTO `vendas` (`id`, `usuario_id`, `valor_total`, `descricao`, `data_venda`, `status`) VALUES
(32, 22, 40.00, NULL, '2025-09-28 16:24:51', 'finalizada'),
(33, 22, 120.00, '', '2025-09-27 16:48:00', 'finalizada'),
(34, 22, 60.00, '', '2025-09-28 16:48:00', 'finalizada'),
(35, 22, 150.00, '', '2025-09-29 16:48:00', 'finalizada'),
(36, 22, 150.00, '', '2025-09-30 16:49:00', 'finalizada'),
(37, 22, 487.50, NULL, '2025-09-28 17:39:06', 'finalizada');

-- --------------------------------------------------------

--
-- Estrutura para tabela `venda_itens`
--

CREATE TABLE `venda_itens` (
  `id` int(11) NOT NULL,
  `venda_id` int(11) NOT NULL,
  `produto_id` int(11) NOT NULL,
  `quantidade` int(11) NOT NULL,
  `valor_unitario` decimal(10,2) NOT NULL,
  `valor_total` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `venda_itens`
--

INSERT INTO `venda_itens` (`id`, `venda_id`, `produto_id`, `quantidade`, `valor_unitario`, `valor_total`) VALUES
(40, 33, 11, 2, 60.00, 120.00),
(41, 34, 11, 1, 60.00, 60.00),
(42, 35, 13, 1, 150.00, 150.00),
(43, 36, 13, 1, 150.00, 150.00),
(44, 37, 12, 65, 7.50, 487.50);

-- --------------------------------------------------------

--
-- Estrutura para tabela `venda_servicos`
--

CREATE TABLE `venda_servicos` (
  `id` int(11) NOT NULL,
  `venda_id` int(11) NOT NULL,
  `servico_id` int(11) NOT NULL,
  `valor` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `venda_servicos`
--

INSERT INTO `venda_servicos` (`id`, `venda_id`, `servico_id`, `valor`) VALUES
(2, 32, 4, 40.00);

--
-- Índices para tabelas despejadas
--

--
-- Índices de tabela `categorias`
--
ALTER TABLE `categorias`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `nome_unico` (`nome`);

--
-- Índices de tabela `eventos`
--
ALTER TABLE `eventos`
  ADD PRIMARY KEY (`id`);

--
-- Índices de tabela `fornecedores`
--
ALTER TABLE `fornecedores`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `cnpj_unico` (`cnpj`),
  ADD UNIQUE KEY `email_unico` (`email`);

--
-- Índices de tabela `produtos`
--
ALTER TABLE `produtos`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `codigo_barras` (`codigo_barras`);

--
-- Índices de tabela `servicos_prestados`
--
ALTER TABLE `servicos_prestados`
  ADD PRIMARY KEY (`id`),
  ADD KEY `usuario_id` (`usuario_id`);

--
-- Índices de tabela `usuarios`
--
ALTER TABLE `usuarios`
  ADD PRIMARY KEY (`id`);

--
-- Índices de tabela `vendas`
--
ALTER TABLE `vendas`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_vendas_usuarios` (`usuario_id`);

--
-- Índices de tabela `venda_itens`
--
ALTER TABLE `venda_itens`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_venda_itens_vendas` (`venda_id`),
  ADD KEY `fk_venda_itens_produtos` (`produto_id`);

--
-- Índices de tabela `venda_servicos`
--
ALTER TABLE `venda_servicos`
  ADD PRIMARY KEY (`id`),
  ADD KEY `venda_id` (`venda_id`),
  ADD KEY `servico_id` (`servico_id`);

--
-- AUTO_INCREMENT para tabelas despejadas
--

--
-- AUTO_INCREMENT de tabela `categorias`
--
ALTER TABLE `categorias`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT de tabela `eventos`
--
ALTER TABLE `eventos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=33;

--
-- AUTO_INCREMENT de tabela `fornecedores`
--
ALTER TABLE `fornecedores`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT de tabela `produtos`
--
ALTER TABLE `produtos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT de tabela `servicos_prestados`
--
ALTER TABLE `servicos_prestados`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT de tabela `usuarios`
--
ALTER TABLE `usuarios`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=23;

--
-- AUTO_INCREMENT de tabela `vendas`
--
ALTER TABLE `vendas`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=38;

--
-- AUTO_INCREMENT de tabela `venda_itens`
--
ALTER TABLE `venda_itens`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=45;

--
-- AUTO_INCREMENT de tabela `venda_servicos`
--
ALTER TABLE `venda_servicos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- Restrições para tabelas despejadas
--

--
-- Restrições para tabelas `servicos_prestados`
--
ALTER TABLE `servicos_prestados`
  ADD CONSTRAINT `servicos_prestados_ibfk_1` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`id`);

--
-- Restrições para tabelas `vendas`
--
ALTER TABLE `vendas`
  ADD CONSTRAINT `fk_vendas_usuarios` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`id`);

--
-- Restrições para tabelas `venda_itens`
--
ALTER TABLE `venda_itens`
  ADD CONSTRAINT `fk_venda_itens_produtos` FOREIGN KEY (`produto_id`) REFERENCES `produtos` (`id`),
  ADD CONSTRAINT `fk_venda_itens_vendas` FOREIGN KEY (`venda_id`) REFERENCES `vendas` (`id`) ON DELETE CASCADE;

--
-- Restrições para tabelas `venda_servicos`
--
ALTER TABLE `venda_servicos`
  ADD CONSTRAINT `venda_servicos_ibfk_1` FOREIGN KEY (`venda_id`) REFERENCES `vendas` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `venda_servicos_ibfk_2` FOREIGN KEY (`servico_id`) REFERENCES `servicos_prestados` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
