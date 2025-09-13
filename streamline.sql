-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Tempo de geração: 12/09/2025 às 04:06
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
(1, 'Cannabis'),
(4, 'Dry');

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
  `reset_token_expire` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `fornecedores`
--

INSERT INTO `fornecedores` (`id`, `razao_social`, `cnpj`, `email`, `telefone`, `senha`, `reset_token`, `reset_token_expire`) VALUES
(7, 'Leroy Merlin', '11111111111111111', 'lippealmeida@gmail.com', '1111111222222', NULL, NULL, NULL),
(9, 'Goteira', '12345678901234', 'lastzrr@gmail.com', '11111111111', '$2y$10$.Oxsomkppx6gcAVgTMiQgOUkNLBkRl9mg6QUodx.mypJK7R2ne//O', NULL, NULL),
(10, 'mulher mais linda do mundo', 'cu', 'iarafontes@usp.br', '69', NULL, '0b71492a56080f002a9cea093c77f1300d888552185247e5454abfdb057a0dcdb4f326bf81cd62aee4a6a1b4bef5f9c774e6', '2025-09-13 01:49:06');

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
  `data_cadastro` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `produtos`
--

INSERT INTO `produtos` (`id`, `codigo_barras`, `nome`, `especificacao`, `quantidade_estoque`, `quantidade_minima`, `valor_compra`, `valor_venda`, `categoria_id`, `fornecedor_id`, `data_cadastro`) VALUES
(3, NULL, 'Pó de dez', 'Melhor pó da região', 123, 10, 2.45, 10.00, NULL, 6, '2025-09-08 14:11:25'),
(8, '111111111111', 'Prensado de cinco', 'Gostozin', 120, 50, 2.50, 5.00, 1, NULL, '2025-09-12 01:24:16');

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
(12, 'back', 'back@gmail.com', '1111111111111', 'Beleza/Estética', '1-5', 'LTDA', '12345678901234', '$2y$10$WOQBLPKmFSDdrEjtJCS77eo.OAC93mBzZJSFzsn.hcgh6elGD.siK', NULL, NULL),
(13, 'teste', 'teste@gmail.com', '1111111111111', 'Higiene/Limpeza', '51+', 'LTDA', '12345678901234', '$2y$10$D060g11qOVt.miU5qa69ausfy8HlU2R1.47dM8ylF3uYnxxYeArei', NULL, NULL),
(14, 'paulo woods', 'lastzrr@gmail.com', '11947010600', 'Atacado/Varejo', '21-50', 'LTDA', '12345678901234', '$2y$10$vZQ6d0U5lC4.s1Q8OVKZWORbpdpJ2DaABdzLHBXp7jbKykoaPv1c.', '1309d86ed5b6cfa91fbd88ed9591523e84e03f686477b51ba7fb6e0b13066d09cc436e33c4e0a0a61bb4d5e10563a3aa96f1', '2025-09-12 02:55:05');

--
-- Índices para tabelas despejadas
--

--
-- Índices de tabela `categorias`
--
ALTER TABLE `categorias`
  ADD PRIMARY KEY (`id`);

--
-- Índices de tabela `fornecedores`
--
ALTER TABLE `fornecedores`
  ADD PRIMARY KEY (`id`);

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
-- AUTO_INCREMENT para tabelas despejadas
--

--
-- AUTO_INCREMENT de tabela `categorias`
--
ALTER TABLE `categorias`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT de tabela `fornecedores`
--
ALTER TABLE `fornecedores`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT de tabela `produtos`
--
ALTER TABLE `produtos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT de tabela `usuarios`
--
ALTER TABLE `usuarios`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
