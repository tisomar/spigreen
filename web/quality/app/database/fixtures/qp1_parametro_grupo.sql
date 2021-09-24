-- --------------------------------------------------------
-- Servidor:                     10.8.10.3
-- Versão do servidor:           5.6.10-log - MySQL Community Server (GPL)
-- OS do Servidor:               Win64
-- HeidiSQL Versão:              9.3.0.4984
-- --------------------------------------------------------

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET NAMES utf8mb4 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
-- Copiando dados para a tabela padrao-qcommerce-checkout.qp1_parametro_grupo: ~36 rows (aproximadamente)
DELETE FROM `qp1_parametro_grupo`;
/*!40000 ALTER TABLE `qp1_parametro_grupo` DISABLE KEYS */;
INSERT INTO `qp1_parametro_grupo` (`ID`, `NOME`, `ALIAS`, `DICA`, `ORDEM`, `IS_MASTER`) VALUES
	(1, 'Administração', 'administracao', NULL, 1, 0),
	(2, 'E-mail (SMTP)', 'servidor_email', NULL, 2, 1),
	(3, 'Dados da Empresa', 'dados_empresa', NULL, 3, 0),
	(5, 'Gateway :: SuperPay', 'gateway_superpay', NULL, 4, 1),
	(6, 'Configuração Boleto', 'configuracao_boleto', NULL, 6, 0),
	(7, 'SSL - Comodo', 'comodo', NULL, 9, 1),
	(8, 'Google', 'google', NULL, 8, 1),
	(9, 'Correios', 'correios', NULL, 5, 0),
	(10, 'E-bit', 'ebit', NULL, 9, 1),
	(11, 'PagSeguro :: Transparente', 'pagseguro_transparente', NULL, 9, 0),
	(14, 'Catálogo', 'produto_configuracoes', NULL, 9, 0),
	(15, 'Perguntas frequentes', 'perguntas_frequentes', NULL, 9, 0),
	(17, 'Chat :: brTalk', 'modulo_brtalk', NULL, 9, 1),
	(21, 'Banners', 'banners', NULL, 9, 0),
	(24, 'Chat :: Zopim Live', 'chat_zopim', NULL, 9, 1),
	(26, 'Popup', 'popup', NULL, 9, 0),
	(27, 'Clientes', 'clientes', NULL, 9, 0),
	(29, 'Cartão de Crédito (SuperPay)', 'superpay_cartao', NULL, 9, 0),
	(31, 'Contato', 'contato', NULL, 9, 0),
	(32, 'Logotipo', 'logotipo', NULL, 9, 1),
	(33, 'Módulo - Avaliação', 'avaliacao_produto', NULL, 9, 0),
	(34, 'PayPal', 'gateway_paypal', NULL, 9, 0),
	(35, 'Comparador de Preços', 'comparador_precos', NULL, 9, 1),
	(36, 'Faturamento Direto', 'faturamento_direto', NULL, 9, 0),
	(37, 'SSL - Positive', 'positive', NULL, 0, 1),
	(38, 'ClearSale - Start', 'clear_sale', NULL, 9, 1),
	(39, 'Cloud Flare', 'cloud_flare', NULL, 9, 1),
	(40, 'Boleto PHP', 'boleto_php', NULL, 9, 1),
	(41, 'Facebook Tracking', 'facebook_tracking', NULL, 10, 1),
	(43, 'Gateway :: PagSeguro', 'configuracao_pagseguro', NULL, NULL, 1),
	(44, 'PagSeguro :: Redirecionamento', 'pagseguro_padrao', NULL, 9, 0),
	(45, 'Itaú Shopline', 'configuracao_itau_shopline', NULL, NULL, 1),
	(46, 'Itaú Shopline', 'itau_shopline', NULL, NULL, 0),
	(48, 'Gateway :: Paypal', 'configuracao_paypal', NULL, 0, 1),
	(49, 'Boleto (Com registro)', 'boleto', NULL, 9, 0),
	(50, 'Javascripts', 'javascripts', NULL, 10, 1);
/*!40000 ALTER TABLE `qp1_parametro_grupo` ENABLE KEYS */;
/*!40101 SET SQL_MODE=IFNULL(@OLD_SQL_MODE, '') */;
/*!40014 SET FOREIGN_KEY_CHECKS=IF(@OLD_FOREIGN_KEY_CHECKS IS NULL, 1, @OLD_FOREIGN_KEY_CHECKS) */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
