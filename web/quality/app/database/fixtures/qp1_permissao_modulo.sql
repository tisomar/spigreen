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
-- Copiando dados para a tabela padrao-qcommerce.qp1_permissao_modulo: ~59 rows (aproximadamente)
DELETE FROM `qp1_permissao_modulo`;
/*!40000 ALTER TABLE `qp1_permissao_modulo` DISABLE KEYS */;
INSERT INTO `qp1_permissao_modulo` (`ID`, `NOME`, `URL`, `ICON`, `ORDEM`, `MOSTRAR`, `tree_left`, `tree_right`, `tree_level`, `slug`) VALUES
	(1, 'Módulos', '', NULL, 1, 1, 1, 116, 0, 'modulos'),
	(3, 'Vendas', '#', 'icon-dollar', 1, 1, 110, 115, 1, 'vendas'),
	(4, 'Pedidos', '/pedidos/list', NULL, 1, 1, 113, 114, 2, 'pedidos'),
	(5, 'Carrinhos Abandonados', '/carrinhos-abandonados/list', NULL, 1, 1, 111, 112, 2, 'carrinhos-abandonados'),
	(6, 'Catálogo', '#', 'icon-book', 1, 1, 2, 13, 1, 'catalogo'),
	(7, 'Produtos', '/produtos/list/', NULL, 1, 1, 11, 12, 2, 'produtos'),
	(8, 'Categorias', '/categorias/list', NULL, 1, 1, 5, 6, 2, 'categorias'),
	(9, 'Comentários', '/comentarios/list', NULL, 1, 1, 7, 8, 2, 'comentarios'),
	(10, 'Marcas', '/marcas/list', NULL, 1, 1, 9, 10, 2, 'marcas'),
	(11, 'Promoções', '#', 'icon-tag', 1, 1, 86, 91, 1, 'promocoes'),
	(12, 'Cupom de Desconto', '/cupom-desconto/list', NULL, 1, 1, 87, 88, 2, 'cupom-de-desconto'),
	(13, 'Conteúdos', '#', 'icon-th-list', 1, 1, 40, 51, 1, 'conteudos'),
	(14, 'Banners', '/banners/list', NULL, 1, 1, 41, 42, 2, 'banners'),
	(15, 'Galeria de Imagens', '/galerias/list', NULL, 1, 1, 45, 46, 2, 'galeria-de-imagens'),
	(16, 'Páginas e Blocos', '/conteudos/list', NULL, 1, 1, 49, 50, 2, 'paginas-e-blocos'),
	(17, 'Perguntas Frequentes', '/faq/list', NULL, 1, 1, 47, 48, 2, 'perguntas-frequentes'),
	(18, 'Marketing', '#', 'icon-globe', 1, 1, 76, 85, 1, 'marketing'),
	(19, 'Mídias Sociais', '/midias-sociais/list', NULL, 1, 1, 77, 78, 2, 'midias-sociais'),
	(20, 'Newsletter', '/newsletter/list', NULL, 1, 1, 79, 80, 2, 'newsletter'),
	(21, 'SEO', '/seo/registration/', NULL, 1, 1, 83, 84, 2, 'seo'),
	(22, 'Clientes', '#', 'icon-user', 1, 1, 14, 19, 1, 'clientes'),
	(23, 'Usuários', '#', 'icon-user', 1, 0, 104, 109, 1, 'usuarios'),
	(24, 'Usuários', '/usuarios/list', NULL, 1, 0, 107, 108, 2, 'usuarios-1'),
	(25, 'Grupos', '/grupo-usuario/list', NULL, 1, 0, 105, 106, 2, 'grupos'),
	(26, 'Configurações', '#', 'icon-cog', 1, 1, 20, 39, 1, 'configuracoes'),
	(27, 'Administração', '/configuracoes/list/administracao', NULL, 1, 1, 21, 22, 2, 'administracao'),
	(28, 'Dados da Empresa', '/configuracoes/list/dados_empresa', NULL, 1, 1, 33, 34, 2, 'dados-da-empresa'),
	(30, 'Biblioteca de Cores', '/produto-cor/list/', NULL, 1, 1, 3, 4, 2, 'biblioteca-de-cores'),
	(31, 'Clientes', '/clientes/list', NULL, 1, 1, 15, 16, 2, 'clientes-1'),
	(33, 'Transportadora', '/transportadora-regiao/list', NULL, 1, 1, 57, 58, 2, 'transportadora'),
	(34, 'Frete Grátis por Região', '/frete-gratis-regiao/list', NULL, 1, 1, 89, 90, 2, 'frete-gratis-por-regiao'),
	(38, 'Popup', '/configuracoes/list/popup', NULL, 1, 1, 81, 82, 2, 'popup'),
	(42, 'Correios', '/configuracoes/list/correios', NULL, 1, 1, 53, 54, 2, 'correios'),
	(43, 'Clientes', '/configuracoes/list/clientes', NULL, 1, 1, 31, 32, 2, 'clientes-2'),
	(44, 'Contato', '/configuracoes/list/contato', NULL, 1, 1, 43, 44, 2, 'contato'),
	(46, 'Catálogo', '/configuracoes/list/produto_configuracoes', NULL, 1, 1, 29, 30, 2, 'catalogo-1'),
	(47, 'Formas de Entrega', '#', 'icon-truck', 1, 1, 52, 59, 1, 'formas-de-entrega'),
	(48, 'Formas de Pagamento', '#', 'icon-dollar', 1, 0, 60, 75, 1, 'formas-de-pagamento'),
	(49, 'PagSeguro - Padrão', '/configuracoes/list/pagseguro_padrao', NULL, 1, 0, 69, 70, 2, 'pagseguro-padrao'),
	(50, 'Boleto (#nome-do-banco#)', '/configuracoes/list/boleto', NULL, 1, 0, 61, 62, 2, 'boleto-nome-do-banco'),
	(51, 'Cartão de Crédito (SuperPay)', '/configuracoes/list/superpay_cartao', NULL, 1, 0, 63, 64, 2, 'cartao-de-credito-superpay'),
	(52, 'Relatórios', '#', 'icon-list-alt', 1, 1, 92, 103, 1, 'relatorios'),
	(53, 'Volume de Venda', '/relatorio/volume-venda/', NULL, 1, 1, 101, 102, 2, 'volume-de-venda'),
	(54, 'Formas de Pagamento', '/relatorio/formas-pagamento', NULL, 1, 1, 95, 96, 2, 'formas-de-pagamento-1'),
	(56, 'Estoque', '/relatorio/estoque', NULL, 1, 1, 93, 94, 2, 'estoque'),
	(57, 'Produtos mais vendidos', '/relatorio/mais-vendidos', NULL, 1, 1, 99, 100, 2, 'produtos-mais-vendidos'),
	(58, 'Novos clientes', '/relatorio/novos-clientes/', NULL, 1, 1, 97, 98, 2, 'novos-clientes'),
	(59, 'Avaliação de Produtos', '/configuracoes/list/avaliacao_produto', NULL, 1, 1, 23, 24, 2, 'avaliacao-de-produtos'),
	(60, 'PayPal', '/configuracoes/list/gateway_paypal', NULL, 1, 0, 73, 74, 2, 'paypal'),
	(63, 'Tabela de Preços', '/tabela-preco/list', NULL, 1, 1, 17, 18, 2, 'tabela-de-precos'),
	(64, 'Faturamento Direto', '/faturamento-direto/list', NULL, 1, 0, 65, 66, 2, 'faturamento-direto'),
	(65, 'Faturamento Direto', '/configuracoes/list/faturamento_direto', NULL, 1, 1, 35, 36, 2, 'faturamento-direto-1'),
	(67, 'Banners', '/configuracoes/list/banners', NULL, 1, 1, 25, 26, 2, 'banners-1'),
	(68, 'Perguntas Frequentes', '/configuracoes/list/perguntas_frequentes', NULL, 1, 1, 37, 38, 2, 'perguntas-frequentes-1'),
	(69, 'PagSeguro - Transparente', '/configuracoes/list/pagseguro_transparente', NULL, 1, 0, 71, 72, 2, 'pagseguro-transparente'),
	(70, 'Itaú Shopline', '/configuracoes/list/itau_shopline', NULL, 1, 0, 67, 68, 2, 'itau-shopline'),
	(71, 'Boleto', '/configuracoes/list/configuracao_boleto', NULL, 1, 0, 27, 28, 2, 'boleto'),
	(72, 'Retirada na Loja', '/retirada-na-loja/list', NULL, 1, 0, 55, 56, 2, 'retirada-na-loja');
/*!40000 ALTER TABLE `qp1_permissao_modulo` ENABLE KEYS */;
/*!40101 SET SQL_MODE=IFNULL(@OLD_SQL_MODE, '') */;
/*!40014 SET FOREIGN_KEY_CHECKS=IF(@OLD_FOREIGN_KEY_CHECKS IS NULL, 1, @OLD_FOREIGN_KEY_CHECKS) */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
