-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Tempo de geração: 15/09/2025 às 17:36
-- Versão do servidor: 10.4.32-MariaDB
-- Versão do PHP: 8.0.30

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
(2, 'Móveis');

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
  `reset_token_expire` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `fornecedores`
--

INSERT INTO `fornecedores` (`id`, `razao_social`, `cnpj`, `email`, `telefone`, `senha`, `reset_token`, `reset_token_expire`) VALUES
(6, 'Goteira', '49447734000102', 'iarafontes@usp.br', '11947010600', '$2y$10$bKNB0Gi0cOiit82IGld38uSbsfP9AghfDOMyxXKd4Wr2/EoYwvWrG', NULL, NULL),
(7, 'Leroy Merlin', '11111111111111111', 'lippealmeida@gmail.com', '1111111222222', NULL, NULL, NULL);

-- --------------------------------------------------------

--
-- Estrutura para tabela `produtos`
--

CREATE TABLE `produtos` (
  `id` int(11) NOT NULL,
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

INSERT INTO `produtos` (`id`, `nome`, `especificacao`, `quantidade_estoque`, `quantidade_minima`, `valor_compra`, `valor_venda`, `categoria_id`, `fornecedor_id`, `data_cadastro`) VALUES
(1, 'Cadeira', 'Cadeira de madeira', 30, 10, 55.00, 125.00, NULL, NULL, '2025-09-08 13:43:05'),
(2, 'Prensado de cinco', '', 100, 5, 2.50, 5.00, 1, 6, '2025-09-08 13:55:29'),
(3, 'Pó de dez', 'Melhor pó da região', 123, 10, 2.45, 10.00, NULL, 6, '2025-09-08 14:11:25');

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
(9, 'Relp!', 'relp123@outlook.com', '11-94031-4679', 'Atacado/Varejo', '1-5', 'LTDA', '49.447.734/0001-02', '12345678', NULL, NULL),
(10, 'gaby', 'gabyhatsunemiku@gmail.com', '11-94031-4567', 'Atacado/Varejo', '11-20', 'LTDA', '49.447.734/0001-22', '123#abc', NULL, NULL),
(11, 'ttt', 'ttt23@gmail.com', '121212121', 'Atacado/Varejo', '6-10', 'LTDA', '49.457.734/0001-02', '455667', NULL, NULL),
(12, 'hihi', 'hihi123@gmail.com', '11-96767-5644', 'Atacado/Varejo', '11-20', 'ltda', '82281119000144', '$2y$10$6ofchys8ByzoWMEBTJXaSOHQtPl0Nw7LcGvCIB7eJCS4Jyu/soXFK', NULL, NULL),
(13, 'paulo', 'paulo123@gmail.com', '11-96767-5655', 'Atacado/Varejo', '51+', 'ltda', '82281119000166', '$2y$10$6ToWHDKfamHSteoIKmBFpe1NnfB4L5TlCnMwP81TygJCC2Qiur/Iq', NULL, NULL),
(14, 'thiago', 'thiago@gmail.com', '11 95675-3245', 'Atacado/Varejo', '21-50', 'LTDA', '49447734000133', '$2y$10$RfMvk/qoGVI1VwJ8WrwVJ.pILFE9wnJ7DLu2xALMsN6I14NsRFWum', '85b719f4ea303aeeb4a9b8515aabd65c7678f5d119bbca8087d86f7e2eb004e35fa025d3e79e5f697116215a7db0087464d6', '2025-09-01 15:00:15'),
(15, 'dedada', 'dedada@gmail.com', '11 95675-3277', 'Higiene/Limpeza', '11-20', 'LTDA', '49447734000188', '$2y$10$LhssPYW4tn.k3EooUG0lHu4qdiY799VZRNaiz0dCG/vSPJOG5LrU6', 'a5bb2cc2e9f2731c4859aa503882cc32ec86948512075dd8122d80c8635ef9ae6fb5aebe74d688e5f8c9d388a05de9cf5e15', '2025-09-01 15:18:41'),
(16, 'vitin', 'vitin@gmail.com', '1111111111', 'Outro', '1-5', 'LTDA', '49447734000102', '$2y$10$yGmmOm1CqBGSxvJBtVkT7uUtdRcJXvEEu/iAdrhdnSdOS6953skUG', NULL, NULL),
(17, 'felipe', 'pepeu2322@gmail.com', '11 94567-4567', 'Beleza/Estética', '6-10', 'MEI', '91852832000191', '$2y$10$88gbW3/XCPLjTe8pcDFCneAJzvbdyrdhzeylAkWI9f.opnFSwR622', NULL, NULL);

--
-- Índices para tabelas despejadas
--

--
-- Índices de tabela `eventos`
--
ALTER TABLE `eventos`
  ADD PRIMARY KEY (`id`);

--
-- Índices de tabela `usuarios`
--
ALTER TABLE `usuarios`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT para tabelas despejadas
--

--
-- AUTO_INCREMENT de tabela `eventos`
--
ALTER TABLE `eventos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=20;

--
-- AUTO_INCREMENT de tabela `usuarios`
--
ALTER TABLE `usuarios`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
