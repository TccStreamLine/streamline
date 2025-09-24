-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Tempo de geração: 24/09/2025 às 05:54
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
(5, 'Arma'),
(1, 'Cannabis sativa'),
(4, 'Munição'),
(2, 'Sintéticos');

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
(17, 'tcc', '2025-09-15 00:00:00', NULL, NULL, 17, 'tcc porra'),
(18, 'tcc', '2025-09-16 00:00:00', '00:26:00', NULL, 17, 'gfgf'),
(19, 'tcc', '2025-09-15 00:00:00', '12:27:00', NULL, 17, 'fgfgf');

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
(1, 'brunao', '111111111111111111', '689241bruno@gmail.com', '123456789', NULL, '310a774f5f5349cc17e0dfd09d555a1ee6b8766623418b657d7e3107f2e896e036dde1c0da855aa2649e2682a3e95e00f79a', '2025-09-17 14:22:06', 'inativo'),
(3, 'arthur', '12345678901234', 'lastzrr@gmail.com', '11 94567-4567', NULL, '87d368c440ad8f0a046001a4ad139f764e5eac6ec161fe680fc4afcf99994fd77c09755d41185db40b36e8ff2aa3512bef45', '2025-09-17 14:23:44', 'ativo');

-- --------------------------------------------------------

--
-- Estrutura para tabela `funcionarios`
--

CREATE TABLE `funcionarios` (
  `id` int(11) NOT NULL,
  `usuario_id` int(11) NOT NULL,
  `nome` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `cargo` varchar(100) DEFAULT NULL,
  `telefone` varchar(20) DEFAULT NULL,
  `senha` varchar(255) NOT NULL,
  `status` varchar(20) NOT NULL DEFAULT 'ativo'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `funcionarios`
--

INSERT INTO `funcionarios` (`id`, `usuario_id`, `nome`, `email`, `cargo`, `telefone`, `senha`, `status`) VALUES
(4, 18, 'Felipe', 'lastzrr@gmail.com', 'lindo', '11947010600', '$2y$10$r0RIKwiSKH3fJyDQtsIcBOcpnnOOX3tAmkSneaWQ4KCINZBslxFsu', 'ativo');

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
(1, '123', 'Prensado do jaca', 'do jaca', 151, 5, 2.50, 5.00, 1, NULL, '2025-09-16 12:05:18', 'ativo'),
(2, '321', 'Md do bart simpson', 'NBOMB', 123, 4, 12.50, 25.50, 2, NULL, '2025-09-16 12:06:22', 'ativo'),
(3, '111', 'Lança do bico verde', 'Lança do bom', 89, 5, 15.00, 35.00, 2, NULL, '2025-09-16 12:06:51', 'ativo'),
(4, '1234', 'canela seca', 'Mata bem', 11, 5, 3000.00, 5000.00, 5, NULL, '2025-09-16 12:17:10', 'ativo');

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
  `reset_token` varchar(255) DEFAULT NULL,
  `reset_token_expire` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Despejando dados para a tabela `usuarios`
--

INSERT INTO `usuarios` (`id`, `nome_empresa`, `email`, `telefone`, `ramo_atuacao`, `quantidade_funcionarios`, `natureza_juridica`, `cnpj`, `senha`, `reset_token`, `reset_token_expire`) VALUES
(12, 'hihi', 'hihi123@gmail.com', '11-96767-5644', 'Atacado/Varejo', '11-20', 'ltda', '82281119000144', '$2y$10$6ofchys8ByzoWMEBTJXaSOHQtPl0Nw7LcGvCIB7eJCS4Jyu/soXFK', NULL, NULL),
(13, 'paulo', 'paulo123@gmail.com', '11-96767-5655', 'Atacado/Varejo', '51+', 'ltda', '82281119000166', '$2y$10$6ToWHDKfamHSteoIKmBFpe1NnfB4L5TlCnMwP81TygJCC2Qiur/Iq', NULL, NULL),
(14, 'thiago', 'thiago@gmail.com', '11 95675-3245', 'Atacado/Varejo', '21-50', 'LTDA', '49447734000133', '$2y$10$RfMvk/qoGVI1VwJ8WrwVJ.pILFE9wnJ7DLu2xALMsN6I14NsRFWum', '85b719f4ea303aeeb4a9b8515aabd65c7678f5d119bbca8087d86f7e2eb004e35fa025d3e79e5f697116215a7db0087464d6', '2025-09-01 15:00:15'),
(15, 'dedada', 'dedada@gmail.com', '11 95675-3277', 'Higiene/Limpeza', '11-20', 'LTDA', '49447734000188', '$2y$10$LhssPYW4tn.k3EooUG0lHu4qdiY799VZRNaiz0dCG/vSPJOG5LrU6', 'a5bb2cc2e9f2731c4859aa503882cc32ec86948512075dd8122d80c8635ef9ae6fb5aebe74d688e5f8c9d388a05de9cf5e15', '2025-09-01 15:18:41'),
(16, 'vitin', 'vitin@gmail.com', '1111111111', 'Outro', '1-5', 'LTDA', '49447734000102', '$2y$10$yGmmOm1CqBGSxvJBtVkT7uUtdRcJXvEEu/iAdrhdnSdOS6953skUG', NULL, NULL),
(17, 'felipe', 'pepeu2322@gmail.com', '11 94567-4567', 'Beleza/Estética', '6-10', 'MEI', '91852832000191', '$2y$10$88gbW3/XCPLjTe8pcDFCneAJzvbdyrdhzeylAkWI9f.opnFSwR622', NULL, NULL),
(18, 'teste', 'teste@gmail.com', '11111111111', 'Atacado/Varejo', '11-20', 'LTDA', '12345678901234', '$2y$10$M0Lz8QygNYc4Tea7uVj0G.zCPS5Ow/O.tdy/YNG.yP81fPFLzTteS', NULL, NULL),
(19, 'back', 'lastzrr@gmail.com', '11111111111', 'Outro', '21-50', 'LTDA', '11111111111111', '$2y$10$0Msz21FicQf4bkJEa0A2NOWDVyDAa/QOtE7eEfBCbsNGil/2xdDlC', NULL, NULL);

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
(11, 18, 2525.50, 'desconto de 50% no 3Oitão', '2025-09-24 02:59:00', 'inativo'),
(12, 18, 5000.00, '', '2025-09-24 08:02:00', 'inativo'),
(13, 18, 10116.50, NULL, '2025-09-24 03:03:35', 'inativo'),
(14, 18, 5.00, NULL, '2025-09-24 03:11:55', 'finalizada'),
(15, 18, 245.00, '', '2025-09-25 03:50:00', 'finalizada'),
(16, 18, 15.00, '', '2025-09-25 03:50:00', 'finalizada'),
(17, 18, 382.50, '', '2025-09-26 03:50:00', 'finalizada'),
(18, 18, 25.00, '', '2025-09-24 03:51:00', 'finalizada'),
(19, 18, 76.50, '', '2025-09-24 03:51:00', 'finalizada'),
(20, 18, 76.50, '', '2025-09-24 03:51:00', 'finalizada');

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
(16, 11, 4, 1, 2500.00, 2500.00),
(17, 11, 2, 1, 25.50, 25.50),
(18, 12, 4, 1, 5000.00, 5000.00),
(19, 13, 1, 8, 5.00, 40.00),
(20, 13, 4, 2, 5000.00, 10000.00),
(21, 13, 2, 3, 25.50, 76.50),
(22, 14, 1, 1, 5.00, 5.00),
(23, 15, 3, 7, 35.00, 245.00),
(24, 16, 1, 3, 5.00, 15.00),
(25, 17, 2, 15, 25.50, 382.50),
(26, 18, 1, 5, 5.00, 25.00),
(27, 19, 2, 3, 25.50, 76.50),
(28, 20, 2, 3, 25.50, 76.50);

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
-- Índices de tabela `funcionarios`
--
ALTER TABLE `funcionarios`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email_unico` (`email`),
  ADD KEY `fk_funcionarios_usuarios` (`usuario_id`);

--
-- Índices de tabela `produtos`
--
ALTER TABLE `produtos`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `codigo_barras` (`codigo_barras`);

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
-- AUTO_INCREMENT para tabelas despejadas
--

--
-- AUTO_INCREMENT de tabela `categorias`
--
ALTER TABLE `categorias`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT de tabela `eventos`
--
ALTER TABLE `eventos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=20;

--
-- AUTO_INCREMENT de tabela `fornecedores`
--
ALTER TABLE `fornecedores`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT de tabela `funcionarios`
--
ALTER TABLE `funcionarios`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT de tabela `produtos`
--
ALTER TABLE `produtos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT de tabela `usuarios`
--
ALTER TABLE `usuarios`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=20;

--
-- AUTO_INCREMENT de tabela `vendas`
--
ALTER TABLE `vendas`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- AUTO_INCREMENT de tabela `venda_itens`
--
ALTER TABLE `venda_itens`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=29;

--
-- Restrições para tabelas despejadas
--

--
-- Restrições para tabelas `funcionarios`
--
ALTER TABLE `funcionarios`
  ADD CONSTRAINT `fk_funcionarios_usuarios` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`id`);

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
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
