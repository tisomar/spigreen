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
-- Copiando dados para a tabela padrao-qcommerce.qp1_parametro: ~139 rows (aproximadamente)
DELETE FROM `qp1_parametro`;
/*!40000 ALTER TABLE `qp1_parametro` DISABLE KEYS */;
INSERT INTO `qp1_parametro` (`ID`, `PARAMETRO_GRUPO_ID`, `NOME_AMIGAVEL`, `ALIAS`, `VALOR`, `IS_AUTOLOAD`, `DICA`, `ORDEM`, `IS_CONFIGURACAO_SISTEMA`, `TYPE`, `TYPE_OPTIONS`) VALUES
	(208, 1, 'E-mail do Administrador', 'email_administrador', 'qcommerce.admin@qualitypress.com.br', 1, 'Define o e-mail principal do sistema. Este e-mail receberá as notificações enviadas pelo sistema, tais como contatos, dúvidas e novos pedidos.', 1, 0, 'TEXT', ''),
	(209, 1, 'CEP de Origem', 'cep_origem', '89036250', 1, 'Define o CEP de origem da mercadoria para o cálculo do frete.', 2, 0, 'TEXT', ''),
	(210, 29, 'Número máximo de parcelas', 'numero_maximo_parcelas', '5', 1, 'Define o número máximo de parcelas para pagamentos com cartão de crédito. <br>\r\n<span class="text-danger">Deve ser menor ou igual à configuração definida na operadora de crédito.</span>', NULL, 0, 'TEXT', ''),
	(211, 29, 'Valor mínimo por parcela', 'valor_minimo_parcela', '5', 1, 'Define o valor minimo por parcela aceitável pela loja.', NULL, 0, 'MONEY', ''),
	(212, 2, 'SMTP - Host', 'smtp_host', '', 1, 'Define o SMTP para o envio dos e-mails. Ex: smtp.mandrillapp.com, smtp.gmail.com.', 5, 1, 'TEXT', ''),
	(213, 2, 'SMTP - Usuário/E-mail', 'smtp_usuario', '', 1, 'Define o e-mail de autenticação de saída.', 6, 1, 'TEXT', ''),
	(214, 2, 'SMTP - Senha', 'smtp_senha', '', 1, 'Define a senha de autenticação de saída.', 7, 1, 'TEXT', ''),
	(215, 44, 'Habilitar pagamento via pagseguro?', 'meio_pagamento.pagseguro', '0', 1, 'Define se será disponibilizada a forma de pagamento através do pagseguro.', NULL, 0, 'BOOLEAN', '{"1": "Sim", "0": "Não"}'),
	(217, 3, 'Razão Social', 'empresa_razao_social', 'QualityPress Soluções', 1, 'Define a razão social da empresa. Dado obrigatório no rodapé do Q.COMMERCE conforme novas normas e leis para comércio eletrônico.', NULL, 0, 'TEXT', ''),
	(218, 3, 'CNPJ', 'empresa_cnpj', '78.047.561/0001-33', 1, 'Define o CNPJ da empresa. Dado obrigatório no rodapé do Q.COMMERCE conforme novas normas e leis para comércio eletrônico.', NULL, 0, 'TEXT', ''),
	(219, 3, 'Endereço', 'empresa_endereco_completo', 'Rua Jacó Brueckheimer, 60, Velha - Blumenau/SC - CEP: 89036-250', 1, 'Define o endereço da empresa. Dado obrigatório no rodapé do Q.COMMERCE conforme novas normas e leis para comércio eletrônico.', NULL, 0, 'TEXT', ''),
	(220, 3, 'Nome Fantasia', 'empresa_nome_fantasia', 'Quality Press', 1, 'Define o nome fantasia da empresa.', NULL, 0, 'TEXT', ''),
	(221, 5, 'Ambiente (Produção ou Homologação)', 'superpay.ambiente', '', 1, 'Define o ambiente (homologação ou produção) do gateway de pagamentos. Lembrar de atualizar o código de estabelecimento quando mudar este campo.', 1, 1, 'SELECT', '{"sandbox":"Homologação", "production": "Produção"}'),
	(222, 5, 'Estabelecimento de <b> Homologação</b>', 'superpay.codigo_estabelecimento_sandbox', '', 1, 'Define o código de estabelecimento no gateway do SuperPay. Lembrando que o código de estabelecimento do ambiente de homologação no superpay.', 2, 1, 'TEXT', ''),
	(223, 5, 'Estabelecimento de <b>Produção</b>', 'superpay.codigo_estabelecimento_production', '', 1, 'Define o código de estabelecimento no gateway do superpay do ambiente de produção.', 3, 1, 'TEXT', ''),
	(225, 27, 'Permitir o cadastro de...', 'clientes.tipo_cadastro', '1', 1, 'Define os tipos de cadastros que o sistema permitirá cadastrar-se', NULL, 0, 'SELECT', '{"1": "Pessoa Física e Jurídica", "2": "Somente Pessoa Física", "3": "Somente Pessoa Jurídica"}'),
	(226, 27, 'Liberação de aprovação automática', 'cliente_aprovacao_direta', '1', 1, 'Define se os clientes que se cadastrarem receberão a aprovação do cadastro automaticamente.', NULL, 0, 'SELECT', '{"1": "Pessoa Física e Jurídica", "2": "Somente Pessoa Física", "3": "Somente Pessoa Jurídica", "4": "Nenhum"}'),
	(227, 27, 'Valor mínimo de compra para Pessoa Física', 'clientes.valor_minimo_pf', '0.00', 1, 'Define o valor mínimo para compras para cadastros do tipo pessoa física', NULL, 0, 'MONEY', ''),
	(228, 27, 'Valor mínimo de compra para Pessoa Jurídica', 'clientes.valor_minimo_pj', '0.00', 1, 'Define o valor mínimo para compras para cadastros do tipo pessoa jurídica', NULL, 0, 'MONEY', ''),
	(229, 27, 'Quem pode visualizar os preços dos produtos?', 'clientes.ocultar_preco', '0', 1, 'Define como será a permissão de visualização de preços dos produtos.', NULL, 0, 'SELECT', '{"0": "Todos", "1": "Somente pessoas autenticadas"}'),
	(230, 6, 'Porcentagem de desconto para pagamento via boleto', 'boleto.desconto_pagamento_avista', '10', 1, 'Define a porcentagem de desconto para pagamento à vista com boleto bancário.', 2, 0, 'TEXT', ''),
	(231, 49, 'Número de dias p/ vencimento após a conclusão da compra', 'boleto.quantidade_dias_vencimento', '2', 1, 'Define o número de dias para vencimento do boleto. <br>\r\n<span class="text-danger">Atenção! Este número deve ser menor ou igual ao numero utilizado no gateway.</span>', 9, 0, 'TEXT', ''),
	(232, 7, 'Mostrar o selo de certificação SSL Comodo no site?', 'comodo_habilitado', '', 1, 'Define se será disponibilizado o selo de certificação SSL Comodo no site. É importante que o SSL esteja devidamente instalado no servidor.', NULL, 1, 'BOOLEAN', '{"1": Sim, "0": "Não"}'),
	(233, 7, 'Código a ser inserido imeditamente ANTES da tag </HEAD>', 'comodo_head', '', 1, 'Define o código do comodo que será incluido antes da tag </head>.', NULL, 1, 'TEXTAREA', ''),
	(234, 7, 'Código a ser inserido aonde quer que o selo apareça', 'comodo_body', '', 1, 'Define o código do selo comodo. O selo é obtido no próprio site da comodo.', NULL, 1, 'TEXTAREA', ''),
	(235, 8, 'Código JavaScript do Google Analytics', 'google_analytics', '', 1, 'Define o código javascript completo do Google Analytics.', 1, 1, 'TEXTAREA', ''),
	(236, 31, 'E-mail de Contato', 'email_contato', 'comercial@qualitypress.com.br', 1, 'Define o e-mail que será mostrado na página de contato.', NULL, 0, 'TEXT', ''),
	(237, 31, 'Telefone de Contato', 'empresa_telefone_contato', '(47) 3234-0775', 1, 'Define o número do telefone para os cliente que desejarem entrar em contato via telefonema.', NULL, 0, 'TEXT', ''),
	(238, 31, 'Google Maps - Descrição', 'googlemaps_descricao', 'Rua Jacó Brueckheimer, 60 - Velha - Blumenau - SC, 89036-250', 1, 'Define o conteúdo que aparecerá no box do mapa do google maps na página de contato.', NULL, 0, 'TEXT', ''),
	(239, 31, 'Google Maps - Latitude', 'googlemaps_latitude', '-26.917696', 1, 'Define a latitude para o google maps.', NULL, 0, 'TEXT', ''),
	(240, 31, 'Google Maps - Longitude', 'googlemaps_longitude', '-49.086609', 1, 'Define a longitude para o google maps.', NULL, 0, 'TEXT', ''),
	(241, 14, 'Mostrar página de marcas?', 'mostrar_marcas', '1', 1, 'Mostra ou oculta o link para a página de marcas. Caso o cliente possua marca própria, não é necessário ter um link para listar a marca do site.', NULL, 0, 'BOOLEAN', '{"1": "Sim", "0": "Não"}'),
	(242, 6, 'Valor mínimo para compras com boleto', 'valor_minimo_boleto', '0', 1, 'Define o valor mínimo de compra para pagamentos por boleto bancário.', 9, 0, 'MONEY', ''),
	(243, 9, 'Habilitar PAC', 'has_correios_pac', '1', 1, 'Define se o PAC será habilitado como meio de entrega.', NULL, 0, 'BOOLEAN', '{"1": "Sim", "0": "Não"}'),
	(244, 9, 'Habilitar Sedex', 'has_correios_sedex', '1', 1, 'Define se o SEDEX será habilitado como meio de entrega.', NULL, 0, 'BOOLEAN', '{"1": "Sim", "0": "Não"}'),
	(245, 9, 'Habilitar E-Sedex', 'has_correios_esedex', '0', 1, 'Define se o E-SEDEX será habilitado como meio de entrega.<br>\r\n<span class="text-danger">Para ter o e-sedex habilitado, é necessário ter o código e a senha do contrato com os correios devidamente preenchidos.</span>', NULL, 0, 'BOOLEAN', '{"1": "Sim", "0": "Não"}'),
	(246, 9, 'Código do Contrato', 'correios_codigo_contrato', '', 1, 'Define o código de contrato junto aos correios.', NULL, 0, 'TEXT', ''),
	(247, 9, 'Senha do Contrato', 'correios_senha_contrato', '', 1, 'Define a senha do contrato junto aos correios.', NULL, 0, 'TEXT', ''),
	(248, 10, 'Banner E-Bit', 'ebit_banner_finalizacao', '', 1, 'Define o código do banner do ebit que aparecerá na finalização do pedido.', NULL, 1, 'TEXTAREA', ''),
	(249, 10, 'Selo E-Bit', 'ebit_selo_rodape', '', 1, 'Define o código do selo do ebit que aparecerá no rodapé.', NULL, 1, 'TEXTAREA', ''),
	(250, 43, 'E-mail', 'pagseguro_email', '', 1, 'Define o e-mail da conta do pagseguro.', NULL, 1, 'TEXT', ''),
	(251, 43, 'Token', 'pagseguro_token', '', 1, 'Define o token de acesso gerado no ambiente do pagseguro.', NULL, 1, 'TEXT', ''),
	(252, 14, 'Habilitar avise-me quando disponível?', 'has_aviseme', '1', 1, 'Define se o haverá esta opção para o consumidor para produtos que não estiverem disponíveis.', NULL, 0, 'BOOLEAN', '{"1": "Sim", "0": "Não"}'),
	(253, 15, 'Disponibilizar link para o módulo de perguntas frequentes?', 'has_faq', '1', 1, 'Define se os links de acesso ao módulo de perguntas frequentes (ajuda) estarão disponíveis para o cliente.', NULL, 0, 'BOOLEAN', '{"1": "Sim", "0": "Não"}'),
	(254, 14, 'Mostrar todas as categorias ou somente as que tiverem produtos vinculados?', 'mostrar_todas_categorias', '1', 1, 'Define se todas as categorias serão disponibilizadas no site, ou se somente categorias com produtos.', NULL, 1, 'SELECT', '{"1": "todas as categorias", "0": "somente categorias com produtos vinculados"}'),
	(255, 17, 'Habilitar brTalk', 'has_brtalk', '', 1, 'Define se o cliente possui módulo de atendimento online (brTalk).', NULL, 1, 'BOOLEAN', '{"1": "Sim", "0": "Não"}'),
	(256, 14, 'Habilitar Biblioteca de Cores', 'has_produto_cor', '1', 1, 'Define se o cliente possui o módulo de biblioteca de cores para variação de produtos. Este módulo deve ser desabilitado do "Menu - Admin" também caso o cliente não tenha o contratado.', NULL, 0, 'BOOLEAN', '{"1": "Sim", "0": "Não"}'),
	(257, 14, 'Mostrar variação por grade (somente para produtos com 2 variações)', 'produto_layout_variacao', '3', 1, 'Define o tipo de layout para a variação nos detalhes do produto.', NULL, 0, 'SELECT', '{"1": "por grade", "2": "por caixa de seleção", "3": "ambos"}'),
	(258, 1, 'Habilitar modo "Em manutenção".', 'modo_manutencao', '0', 1, 'Define se o site ficará em modo de manutenção e não ficará disponível para os clientes.', 3, 0, 'BOOLEAN', '{"1": "Sim", "0": "Não"}'),
	(259, 14, 'Limite de imagens por produto', 'produto_limite_imagens', '8', 1, 'Define a quantidade de imagens que o cliente pode adicionar por produto. A quantidade 5 é o padrão com base no layout.', NULL, 1, 'TEXT', ''),
	(260, 21, 'Habilitar banner full-width', 'banner_full', '', 1, 'Define se o banner principal do site ocupará a largura do monitor ou se terá tamanho limitado na tela (Ver orientações de dimensões no cadastro de banners para obter a melhor visualização dos banners)', NULL, 0, 'BOOLEAN', '{"1": "Sim", "0": "Não"}'),
	(261, 14, 'Habilitar recurso de fotos por cor', 'has_foto_por_cor', '1', 1, 'Define se o cliente poderá associar fotos por cores. Quando o cliente selecionar uma cor na página de detalhes do produto, o sistema irá alterar a galeria de imagens para as que foram cadastradas para a cor selecionada.<br>OBS.: Necessita que o módulo de "Biblioteca de Cores" esteja ativo.', NULL, 1, 'BOOLEAN', '{"1": "Sim", "0": "Não"}'),
	(266, 2, 'SMTP - Porta', 'smtp_port', '', 1, 'Define a porta que deverá se conectar no servidor de e-mails. Normalmente: 587. Se necessário utilizar SSL, alterar para 443.', 8, 1, 'TEXT', ''),
	(267, 2, 'SMTP - Criptografia', 'smtp_security', '', 1, 'Define o tipo de conexão segurança que deverá utilizar', 9, 1, 'SELECT', '{"ssl": "SSL", "tls": "TLS", "": "Nenhum"}'),
	(268, 2, 'Envio de e-mails em tempo real', 'email.tempo_real', '0', 1, 'Define se os e-mails deverão ser enviados em tempo real. Se não forem em tempo real, os e-mails serão enviados de 3 em 3 minutos. Recomendamos fortemente que os e-mails não sejam enviados em tempo real.', 4, 1, 'BOOLEAN', '{"0": "Não", "1": "Sim"}'),
	(269, 2, 'E-mail de desenvolvimento', 'email.desenvolvimento', 'suporte@qualitypress.com.br', 1, 'Caso o sistema estiver em modo de desenvolvedor (configuração no .htaccess), o sistema enviará todos os e-mails disparados pelo e-commerce para o e-mail configurado neste campo. Esta é uma preventiva para não enviar e-mails erroneamente à consumidores e a', 10, 1, 'TEXT', ''),
	(270, 49, 'Aplicação de desconto (itens com frete ou sem frete)', 'boleto_desconto_tipo', '1', 1, 'Define como será aplicado o desconto do Boleto. Desconto somente para os itens ou para o valor total da compra.', 9, 0, 'SELECT', '{"1": "Somente nos itens", "2": "Itens e frete"}'),
	(272, 24, 'Habilitar Zopim Chat', 'has_zopim_chat', '', 1, 'Define se utilizará o chat Zopim ou não. Caso esteja ativo o chat brTalk é desativado automaticamente.', NULL, 1, 'BOOLEAN', '{"1":"Sim","0":"Não"}'),
	(273, 24, 'Script Zopim Chat', 'zopim_chat_script', '', 1, 'Script que é gerado no site do Zopim para integrar o chat. Não é necessário colocar o inicio e fim da tag script.', NULL, 1, 'TEXTAREA', ''),
	(274, 8, 'Código da Propriedade no Google Analytics', 'google_analytics_ua', '', 1, 'Define o código da propriedade no Google Analytics. Ex: UA-00000000-0', 2, 1, 'TEXT', ''),
	(275, 8, 'Ativar Tracking de e-commerce do Google?', 'google_track_ecommerce', '0', 1, 'Define se deve enviar os pedidos feito ao tracker do Google', 3, 1, 'BOOLEAN', '{"1": "Sim", "0" : "Não"}'),
	(278, 29, 'Operadora', 'operadora_cartao_credito', 'cielo', 1, 'Define a operadora de cartão de crédito utilizada pelo gateway de pagamentos.', 5, 1, 'SELECT', '{"cielo":"Operações via cartão pela Cielo","redecard_web":"Operações via cartão pela Redecard Komerci Integrada","redecard_ws":"Operações via cartão pela Redecard Komerci Webservice"}'),
	(279, 29, 'Tipo de Operação', 'tipo_operacao', 'credito_a_vista', 1, 'Define o tipo de operação (padrão: Crédito. Ao alterar, o site poderá não funcionar mais, pois necessita de integração).', 6, 1, 'SELECT', '{"credito_a_vista":"Crédito"}'),
	(280, 29, 'Capturar Automaticamente', 'captura_automatica', 'false', 1, 'Define se haverá a captura automática para pagamentos com cartão de crédito. Esta configuração deve estar conforme configurado no painel do Gateway de Pagamentos', 4, 1, 'SELECT', '{"true":"Sim","false":"Não"}'),
	(282, 26, 'Título da janela', 'popup.title', 'Aproveite nossas promoções e novidades exclusivas!', 1, 'Define o título do popup.', NULL, 0, 'TEXT', ''),
	(283, 26, 'Conteúdo do popup', 'popup.content', 'Deixe seu e-mail e seja o primeiro a receber nossas novidades e promoções.', 1, 'Define o conteúdo do popup.', NULL, 0, 'TEXT', ''),
	(284, 26, 'Habilitar popup ao entrar no site', 'popup.show', '1', 1, 'Define se o site abrirá o popup quando o cliente entrar a primeira vez.', NULL, 0, 'BOOLEAN', '{"1": "Sim", "0": "Não"}'),
	(288, 14, 'Modo de ordenação das categorias', 'categorias.modo_ordenacao', 'getOrdem', 1, 'Define o modo de ordenação das categorias. Quando selecionado ordem, após salvar uma categoria, o sistema reorganizará suas categorias por ordem e depois por nome. Se selecionado nome, o sistema organiza apenas alfabéticamente.', NULL, 0, 'SELECT', '{"getOrdem": "Ordem", "getNome": "Nome (Alfabética)"}'),
	(289, 2, 'Enviar em nome de  (E-mail)', 'mail_from', 'contato@qcommerce.com.br', 1, 'Define em nome de qual e-mail quem o e-mail será enviado. Esta informação aparecerá para o consumidor.', 2, 0, 'TEXT', ''),
	(290, 2, 'Enviar em nome de (Nome)', 'mail_name', 'Q.Commerce Express ', 1, 'Define em nome de quem o e-mail será enviado. Esta informação aparecerá para o consumidor.', 1, 0, 'TEXT', ''),
	(291, 14, 'Proporção das imagens de produtos', 'produto.proporcao', '1:1', 1, 'Define a proporção das dimensões das imagens dos produtos. 3x4 recomenda-se o cadastro de imagens 768x1024. 4x3 recomenda-se o cadastro de imagens 1024x768 e 1x1 recomenda-se o cadastro de imagens quadradas 1000x1000.', NULL, 0, 'SELECT', '{"1:1": "1:1", "3:4": "3:4", "4:3": "4:3"}'),
	(292, 2, 'Cor (RGB: #999999) do topo e rodapé do e-mail', 'mail_rgb', '#FF8B69', 1, 'Define a cor para o topo e os links dos e-mails enviados pelo Q.Commerce', 3, 1, 'TEXT', ''),
	(293, 32, 'Logo (Desktop)', 'sistema.logo', 'logo.png', 0, 'Define a logo que será utilizada no site para as dimensões maiores, utilizadas por desktop ou notebooks', NULL, 1, 'IMAGE', ''),
	(294, 32, 'Logo (Mobile)', 'sistema.logo_mobile', 'logo_mobile.png', 0, 'Define a logo que será utilizada no site para as dimensões maiores, utilizadas por tablets ou smartphones', NULL, 1, 'IMAGE', ''),
	(295, 32, 'Favicon', 'sistema.favicon', 'favicon.png', 0, 'Favicon', NULL, 1, 'IMAGE', ''),
	(296, 14, 'Habilitar aviso quando o estoque atingir o mínimo configurado?', 'aviso_estoque_minimo', '1', 1, 'O estoque mínimo serve apenas como um aviso ao administrador. Ao ser definido no cadastro de produtos, assim que algum produto atingir o valor do estoque mínimo, o sistema enviará um e-mail ao administrador informando-o que o estoque do produto atingiu o estoque mínimo configurado. Cada produto (ou variação) possui a sua configuração.', NULL, 0, 'BOOLEAN', '{"1":"Sim","0":"Não"}'),
	(297, 14, 'Habilitar menu lateral de categorias', 'menu_lateral_categorias', '0', 1, 'Define se a categoria mostrará o menu lateral com suas subcategorias.', NULL, 1, 'SELECT', '{"1":"Sim","0":"Não"}'),
	(298, 14, 'Número máximo de subcategorias a ser exibido no menu de "Todas as Categorias"', 'limite_exibicao_subcategorias', '5', 1, 'Define o número máximo de subcategorias que serão exibidas antes de adicionar o link "veja mais"', NULL, 0, 'TEXT', ''),
	(299, 33, 'Enviar e-mail de avaliação na finalização do pedido?', 'avaliacao.is_habilitado', '', 1, 'Define se o sistema enviará um e-mail convidando o cliente a efetuar avaliações referentes ao produto e no e-bit', NULL, 0, 'BOOLEAN', '{"1": "Sim", "0" : "Nao"}'),
	(300, 33, 'Conteúdo padrão do e-mail de avaliação', 'avaliacao.conteudo_padrao', '<p>Ol&aacute;!</p>\r\n<p>Avalie os produtos comprados.</p>', 1, 'Define um conteúdo padrão que irá em todos os e-mails  enviados ao cliente relacionados ao convite de avaliação. Este conteúdo poderá ser utilizado para o envio de promoções por exemplo.', NULL, 0, 'EDITOR', ''),
	(301, 33, 'Assunto padrão do e-mail de avaliação', 'avaliacao.assunto_padrao', 'Avaliação dos produtos comprados', 1, 'Define o assunto do e-mail que será enviado aos clientes', NULL, 0, 'TEXT', ''),
	(302, 34, 'Habilitar PayPal', 'meio_pagamento.paypal', '1', 1, 'Define se o pagamento via paypal está habilitado ou não.', NULL, 0, 'BOOLEAN', '{"1": "Sim", "0": "Não"}'),
	(303, 48, 'Ambiente', 'paypal.ambiente', 'sandbox', 1, 'Define o ambiente (homologação ou produção) do gateway de pagamentos. Lembrar de atualizar o código de estabelecimento quando mudar este campo.', NULL, 1, 'SELECT', '{"sandbox":"Homologação", "production": "Produção"}'),
	(304, 48, 'Username', 'paypal.username', '', 1, 'Usuário da api', NULL, 1, 'TEXT', ''),
	(305, 48, 'Password', 'paypal.password', '', 1, 'Senha da api', NULL, 1, 'TEXT', ''),
	(306, 48, 'Signature', 'paypal.signature', '', 1, 'Assinatura da api', NULL, 1, 'TEXT', ''),
	(307, 27, 'Habilitar tabela de preços por cliente', 'cliente.has_tabela_preco', '0', 1, 'Define se o sistema terá o módulo de tabela de preços', NULL, 1, 'BOOLEAN', '{"1": "Sim", "0": "Não"}'),
	(308, 35, 'Habilitar integração com Google Shopping', 'has_google_shopping', '1', 1, 'Define se o sistema terá integração com o comparador de preços Google Shopping', NULL, 1, 'BOOLEAN', '{"1": "Sim", "0": "Não"}'),
	(309, 35, 'Habilitar integração com Buscapé Company', 'has_buscape', '1', 1, 'Define se o sistema terá integração com o comparador de preços do Buscapé', NULL, 1, 'BOOLEAN', '{"1": "Sim", "0": "Não"}'),
	(310, 27, 'Mensagem do e-mail para clientes aprovados', 'cliente.email_aprovado', 'Você poderá acessar a central do cliente e conferir suas informações e seus pedidos utilizando o dados de e-mail e senha informados no formulário do seu cadastro.', 1, 'Define a mensagem que o cliente receberá de boas vindas caso seu cadastro esteja aprovado.', NULL, 0, 'TEXTAREA', ''),
	(311, 27, 'Mensagem do e-mail para clientes pendente de aprovação', 'cliente.email_pendente', 'Seu cadastro será avaliado por nossa central. Aguarde o contato de um de nossos atendentes. Após a conclusão da avaliação você poderá acessar a central do cliente e conferir suas informações e seus pedidos utilizando o dados de e-mail e senha informados no formulário do seu cadastro. ', 1, 'Define a mensagem que o cliente receberá de boas vindas caso seu cadastro tiver que passar por uma avaliação.', NULL, 0, 'TEXTAREA', ''),
	(312, 27, 'Mensagem do e-mail para clientes em caso de reprovação de cadastro', 'cliente.email_reprovado', 'Seu cadastro foi reprovado por nossa central.', 1, 'Define a mensagem que o cliente receberá caso seu cadastro seja reprovado pela central. O motivo preenchido no momento da reprovação irá abaixo deste texto.', NULL, 0, 'TEXTAREA', ''),
	(313, 27, 'Enviar a descrição do motivo em caso de reprovação de cadastro ao cliente?', 'cliente.enviar_motivo', '1', 1, 'Define se o cliente receberá no e-mail de reprovação o motivo na qual foi definido para a reprovação do seu cadastro.', NULL, 0, 'BOOLEAN', '{"1": "Sim", "0": "Não"}'),
	(314, 36, 'Habilitar Opção de Faturamento Direto', 'meio_pagamento.faturamento_direto', '1', 1, 'Define se o pagamento via faturamento direto está habilitado ou não.', NULL, 0, 'BOOLEAN', '{"1": "Sim", "0": "Não"}'),
	(315, 36, 'Mensagem da tela de pagamento', 'faturamento_direto.mensagem_tela_pagamento', 'Escolha uma das opções', 1, 'Define a mensagem que aparecerá na aba de faturamento direto na tela de pagamento do Q.Commerce', NULL, 0, 'TEXT', ''),
	(316, 37, 'Mostrar selo SSL Positive no site', 'positive_habilitado', '', 1, 'Define se o selo SSL da Positive será mostrado no site. É importante que o SSL positive esteja cadastrado e instalado no servidor para não causar falsa impressão.', NULL, 1, 'BOOLEAN', '{"1": "Sim", "0": "Não"}'),
	(317, 38, 'ClearSale: Habilitar', 'clearsale.habilitado', '', 1, 'Define se o módulo ClearSale será utilizado.', NULL, 1, 'BOOLEAN', '{"1": "Sim", "0": "Não"}'),
	(318, 38, 'ClearSale: Ambiente', 'clearsale.ambiente', 'sandbox', 0, 'Define o ambiente (homologação ou produção) da ClearSale.', NULL, 1, 'SELECT', '{"sandbox":"Homologação", "production": "Produção"}'),
	(319, 38, 'ClearSale: Código Integração para Homologação', 'clearsale.codigo_integracao_sandbox', '', 1, 'Define o código de integração para o ambiente de homologação na ClearSale.', NULL, 1, 'TEXT', ''),
	(320, 38, 'ClearSale: Código Integração para Produção', 'clearsale.codigo_integracao_production', '', 1, 'Define o código de integração para o ambiente de produção na ClearSale.', NULL, 1, 'TEXT', ''),
	(321, 31, 'Whatsapp', 'contato.whatsapp', '(47) 9234-0775', 1, 'Define o número do whatsapp', NULL, 0, 'TEXT', ''),
	(322, 39, 'Habilitar Cloud Flare', 'cloudflare.habilitado', '', 1, 'Define se deve ser usado o cloud flare para os conteúdos estáticos (imagens, javascript, css, etc.)', NULL, 0, 'BOOLEAN', '{"1":"Sim", "0":"Não"}'),
	(323, 39, 'Subdomínio do Cloud Flare', 'cloudflare.subdomain', 'static', 1, 'Informe o subdominio utilizado para os conteúdos estáticos. Informar apenas o alias, sem o dominio principal.', NULL, 0, 'TEXT', ''),
	(324, 49, 'Habilitar Boleto PHP', 'boletophp.enabled', '1', 1, 'Deseja habilitar o recurso de Boleto PHP?', 1, 0, 'BOOLEAN', '{"0":"Não", "1":"Sim"}'),
	(325, 40, 'Banco', 'boletophp.banco', '', 1, 'Informar qual o banco a ser utilizado.', 2, 1, 'SELECT', '{"": "Não Configurado", "BANCO_DO_BRASIL":"Banco do Brasil", "BRADESCO":"Bradesco", "CAIXA_ECONOMICA_FEDERAL":"Caixa", "ITAU": "Itaú", "SANTANDER": "Santander", "CECRED": "Cecred"}'),
	(326, 40, 'Agência', 'boletophp.agencia', '', 1, 'Agência do banco', 3, 1, 'TEXT', ''),
	(327, 40, 'Agência DV', 'boletophp.agencia_dv', '', 1, 'Dígito Verificador da agência.', 4, 1, 'TEXT', ''),
	(328, 40, 'Conta Corrente', 'boletophp.conta', '', 1, 'Conta corrente a ser utilizada', 5, 1, 'TEXT', ''),
	(329, 40, 'Conta Corrente DV', 'boletophp.conta_dv', '', 1, 'Dígito verificador da conta corrente.', 5, 1, 'TEXT', ''),
	(330, 40, 'Carteira', 'boletophp.carteira', '', 1, 'Número da carteira (SR ou RG no caso do banco CEF)', 6, 1, 'TEXT', ''),
	(331, 40, 'Convênio', 'boletophp.convenio', '', 1, 'Informar qual o banco a ser utilizado.', 7, 1, 'TEXT', ''),
	(359, 41, 'Facebook Tracking', 'facebook_tracking.enabled', '1', 1, 'Habilitar ou não o tracking do facebook.', 1, 1, 'BOOLEAN', '{"0": "Não", "1": "Sim"}'),
	(360, 41, 'Facebook Tracking ID', 'facebook_tracking.id', '', 1, 'ID de integração com o facebook.', 2, 1, 'TEXT', 'XXXX'),
	(361, 41, 'Todas as páginas', 'facebook_tracking.todas_as_paginas.events', '["AddToWishlist","InitiateCheckout","Search","ViewContent"]', 1, 'Lista de eventos para executar em todas as páginas, exceto demais abaixo.', 3, 1, 'CHECKBOX', '[{"ViewContent": "Visualizar conteúdo"}, {"Search": "Resultados de Busca"}, {"AddToCart": "Produto adicionado no carrinho"}, {"AddToWishlist": "Adicionado a lista de desejos"}, {"InitiateCheckout": "Inicializar checkout"}, {"AddPaymentInfo": "Informações de pagamento"}, {"Purchase": "Confirmação de compra/pagamento"}, {"Lead": "Prospecção"}, {"CompleteRegistration": "Registro de cliente completo"}]'),
	(362, 41, 'Página inicial', 'facebook_tracking.homepage.events', '', 1, 'Lista de eventos para executar na página inicial', 4, 1, 'CHECKBOX', '[{"ViewContent": "Visualizar conteúdo"}, {"Search": "Resultados de Busca"}, {"AddToCart": "Produto adicionado no carrinho"}, {"AddToWishlist": "Adicionado a lista de desejos"}, {"InitiateCheckout": "Inicializar checkout"}, {"AddPaymentInfo": "Informações de pagamento"}, {"Purchase": "Confirmação de compra/pagamento"}, {"Lead": "Prospecção"}, {"CompleteRegistration": "Registro de cliente completo"}]'),
	(363, 41, 'Busca', 'facebook_tracking.busca.events', '', 1, 'Eventos de tracking do facebook para adicionar na página interna de pesquisa', 4, 1, 'CHECKBOX', '[{"ViewContent": "Visualizar conteúdo"}, {"Search": "Resultados de Busca"}, {"AddToCart": "Produto adicionado no carrinho"}, {"AddToWishlist": "Adicionado a lista de desejos"}, {"InitiateCheckout": "Inicializar checkout"}, {"AddPaymentInfo": "Informações de pagamento"}, {"Purchase": "Confirmação de compra/pagamento"}, {"Lead": "Prospecção"}, {"CompleteRegistration": "Registro de cliente completo"}]'),
	(364, 41, 'Carrinho de Compras', 'facebook_tracking.carrinho_compras.events', '["AddPaymentInfo","AddToCart"]', 1, 'Evento executado após adicionar produto no carrinho', 5, 1, 'CHECKBOX', '[{"ViewContent": "Visualizar conteúdo"}, {"Search": "Resultados de Busca"}, {"AddToCart": "Produto adicionado no carrinho"}, {"AddToWishlist": "Adicionado a lista de desejos"}, {"InitiateCheckout": "Inicializar checkout"}, {"AddPaymentInfo": "Informações de pagamento"}, {"Purchase": "Confirmação de compra/pagamento"}, {"Lead": "Prospecção"}, {"CompleteRegistration": "Registro de cliente completo"}]'),
	(365, 41, 'Lista de Desejos', 'facebook_tracking.list_desejos.events', '', 1, 'Evento executado após adicionar um item na lista de desejos', 6, 1, 'CHECKBOX', '[{"ViewContent": "Visualizar conteúdo"}, {"Search": "Resultados de Busca"}, {"AddToCart": "Produto adicionado no carrinho"}, {"AddToWishlist": "Adicionado a lista de desejos"}, {"InitiateCheckout": "Inicializar checkout"}, {"AddPaymentInfo": "Informações de pagamento"}, {"Purchase": "Confirmação de compra/pagamento"}, {"Lead": "Prospecção"}, {"CompleteRegistration": "Registro de cliente completo"}]'),
	(366, 41, 'Processo de Pagamento', 'facebook_tracking.inicio_processo_pagamento.events', '', 1, 'Evento executado ao iniciar o processo de pagamento', 7, 1, 'CHECKBOX', '[{"ViewContent": "Visualizar conteúdo"}, {"Search": "Resultados de Busca"}, {"AddToCart": "Produto adicionado no carrinho"}, {"AddToWishlist": "Adicionado a lista de desejos"}, {"InitiateCheckout": "Inicializar checkout"}, {"AddPaymentInfo": "Informações de pagamento"}, {"Purchase": "Confirmação de compra/pagamento"}, {"Lead": "Prospecção"}, {"CompleteRegistration": "Registro de cliente completo"}]'),
	(367, 41, 'Pagamento', 'facebook_tracking.pagamento.events', '', 1, 'Evento executado onde as informações de pagamento são inseridas', 8, 1, 'CHECKBOX', '[{"ViewContent": "Visualizar conteúdo"}, {"Search": "Resultados de Busca"}, {"AddToCart": "Produto adicionado no carrinho"}, {"AddToWishlist": "Adicionado a lista de desejos"}, {"InitiateCheckout": "Inicializar checkout"}, {"AddPaymentInfo": "Informações de pagamento"}, {"Purchase": "Confirmação de compra/pagamento"}, {"Lead": "Prospecção"}, {"CompleteRegistration": "Registro de cliente completo"}]'),
	(368, 41, 'Confirmação de Pagamento', 'facebook_tracking.confirmacao_pagamento.events', '', 1, 'Evento executado na tela de confirmação de compras', 9, 1, 'CHECKBOX', '[{"ViewContent": "Visualizar conteúdo"}, {"Search": "Resultados de Busca"}, {"AddToCart": "Produto adicionado no carrinho"}, {"AddToWishlist": "Adicionado a lista de desejos"}, {"InitiateCheckout": "Inicializar checkout"}, {"AddPaymentInfo": "Informações de pagamento"}, {"Purchase": "Confirmação de compra/pagamento"}, {"Lead": "Prospecção"}, {"CompleteRegistration": "Registro de cliente completo"}]'),
	(369, 41, 'Prospecção', 'facebook_tracking.lead.events', '', 1, 'Eventos executados em páginas de possibilidade de prospecção, como detalhe de produto, etc...', 10, 1, 'CHECKBOX', '[{"ViewContent": "Visualizar conteúdo"}, {"Search": "Resultados de Busca"}, {"AddToCart": "Produto adicionado no carrinho"}, {"AddToWishlist": "Adicionado a lista de desejos"}, {"InitiateCheckout": "Inicializar checkout"}, {"AddPaymentInfo": "Informações de pagamento"}, {"Purchase": "Confirmação de compra/pagamento"}, {"Lead": "Prospecção"}, {"CompleteRegistration": "Registro de cliente completo"}]'),
	(370, 41, 'Cadastro de cliente', 'facebook_tracking.confirmacao_cliente.events', '', 1, 'Tela de confirmação do cadastro de cliente', 11, 1, 'CHECKBOX', '[{"ViewContent": "Visualizar conteúdo"}, {"Search": "Resultados de Busca"}, {"AddToCart": "Produto adicionado no carrinho"}, {"AddToWishlist": "Adicionado a lista de desejos"}, {"InitiateCheckout": "Inicializar checkout"}, {"AddPaymentInfo": "Informações de pagamento"}, {"Purchase": "Confirmação de compra/pagamento"}, {"Lead": "Prospecção"}, {"CompleteRegistration": "Registro de cliente completo"}]'),
	(379, 26, 'Inserir o valor do atributo "action" da tag form do formulário de inscrição criado na conta do cliente no mailforweb ', 'mailforweb.form.action', '', 1, 'No formulário de inscrição no mailforweb, redirecionar para: http://dominio.com.br/mailforweb/lead/sucesso', 1, 1, 'TEXT', NULL),
	(380, 45, 'ItauShopline: Ambiente', 'itau_shopline.environment', 'production', 1, 'Define o ambiente para qual o boleto será gerado.', 12, 1, 'SELECT', '{"sandbox":"Homologação", "production": "Produção"}'),
	(381, 45, 'ItauShopline: Código da Empresa', 'itau_shopline.codigo_empresa', '', 1, 'Define o código da empresa fornecido pelo Itaú. Código de 26 dígitos.', 12, 1, 'TEXT', ''),
	(382, 45, 'ItauShopline: Token da empresa', 'itau_shopline.token_empresa', '', 1, 'Define o token fornecido pelo Itaú. Código de 16 dígitos.', 12, 1, 'TEXT', ''),
	(383, 46, 'ItauShopline: Habilitado', 'itau_shopline.enabled', '0', 1, 'Define se o pagamento via boleto bancário será via Itaú Shopline.', 12, 0, 'BOOLEAN', '{"0":"Não", "1": "Sim"}'),
	(384, 14, 'Habilitar descrição abaixo do nome na tela de detalhes do produto.', 'produto.mostrar_descricao_resumida', '1', 1, 'Define se será ou não disponibilizado a descrição resumida do produto logo abaixo do nome na tela de detalhes do produto.', 10, 0, 'BOOLEAN', '{"0": "Não", "1": "Sim"}'),
	(385, 43, 'Opção de pagamento (Nenhum, Padrão ou Checkout Transparente)', 'pagseguro.opcao_pagamento', '', 1, 'Define a opção de pagamento através do pagseguro.\r\n<br><b>Não habilitar esta forma de pagamento:</b> Com esta opção, não será disponibilizado nenhum tipo de pagamento via pagseguro no e-commerce.\r\n<br><b>Padrão:</b> Com esta opção ativa, o cliente é redirecionado para o ambiente do pagseguro para efetuar o pagamento e no final do proceso o cliente será redirecionado ao site novamente.\r\n<br><b>Checkout Transparente:</b> Disponibiliza as formas de pagamento habilitadas no próprio e-commerce sem a necessidade de redirecionar o cliente (necessária contratação deste serviço junto ao pagseguro. <a target="_blank" href="https://pagseguro.uol.com.br/receba-pagamentos.jhtml#checkout-transparent">Saiba mais</a>).', NULL, 0, 'SELECT', '{"": "Não habilitar esta forma de pagamento", "padrao": "Padrão (Redirecionamento)", "transparente": "Checkout Transparente"}'),
	(386, 11, 'Habilitar pagamento por cartão de crédito no checkout transparente?', 'pagseguro.cartao_credito', '0', 1, 'Define se será disponibilizada a forma de pagamento por cartão de crédito através do checkout transparente.', NULL, 0, 'BOOLEAN', '{"1": "Sim", "0": "Não"}'),
	(387, 11, 'Habilitar pagamento por boleto bancário no checkout transparente?', 'pagseguro.boleto_bancario', '0', 1, 'Define se será disponibilizada a forma de pagamento por boleto bancário através do checkout transparente.', NULL, 0, 'BOOLEAN', '{"1": "Sim", "0": "Não"}'),
	(388, 11, 'Habilitar pagamento por débito online no checkout transparente?', 'pagseguro.debito_online', '0', 1, 'Define se será disponibilizada a forma de pagamento por débito online através do checkout transparente.', NULL, 0, 'BOOLEAN', '{"1": "Sim", "0": "Não"}'),
	(391, 29, 'Habilitar forma de pagamento por cartão de crédito via SuperPay?', 'superpay.cartao_credito', '0', 1, 'Define se o sistema disponibilizará a forma de pagamento por cartão de crédito junto à SuperPay.', NULL, 0, 'BOOLEAN', '{"0": "Não", "1": "Sim"}'),
	(392, 1, 'É a versão demonstrativa?', 'sistema.versao_demo', '', 1, 'Define se o sistema trata-se de uma versão demonstrativa. Com esta opção, os pagamentos não utilizarão gateway e haverá avisos no site informando ao visitando que o sistema trata-se de uma versão demonstrativa.', 1, 1, 'BOOLEAN', '{"0": "Não", "1": "Sim"}'),
	(393, 14, 'Selecionar variação automaticamente', 'produto_variacao.selecao_automatica', '2', 1, 'Define como o e-commerce se comportará com relação à seleção automática de variação na página de detalhes do produto.', NULL, 0, 'SELECT', '{"0": "Não selecionar nenhuma opção automaticamente", "1": "Selecionar a primeira variação disponível", "2": "Selecionar a variação mais vendida", "3": "Selecionar a variação padrão configurada"}'),
	(394, 3, 'Inscrição Estadual', 'empresa_ie', '123123', 1, 'Define a inscrição estadual da empresa. Dado obrigatório no rodapé do Q.Commerce conforme normas e leis para comércio eletrônico.', NULL, 0, 'TEXT', NULL),
	(395, 14, 'Disponibilizar o botão comprar na listagem de produtos?', 'mostrar_botao_comprar_listagem', '', 1, 'Define se será ou não disponibilizado o botão comprar na listagem dos produtos.', 20, 0, 'BOOLEAN', '{"0": "Não", "1": "Sim"}'),
	(396, 14, 'Disponibilizar o campo "Quantidade" ao lado do botão comprar na listagem de produtos?', 'mostrar_campo_quantidade_listagem', '', 1, 'Define se será disponibilizado o campo "Quantidade" ao lado do botão comprar na listagem de produtos. Obs.: A opção de mostrar o botão comprar na listagem deve estar habilitado.', 21, 0, 'BOOLEAN', '{"0": "Não", "1": "Sim"}'),
	(397, 50, 'Scripts inclusos dentro da tag <code><head></code>', 'javascript_head', '', 1, 'Este campo é utilizado para scripts que tem por obrigação serem adicionados dentro da tag  <code>&lt;head&gt;</code> conforme a orientação da documentação.', 1, 1, 'TEXTAREA', ''),
	(398, 50, 'Scripts inclusos imediatamente após a tag <code><body></code>', 'javascript_body_inicio', '', 1, 'Este campo é utilizado para scripts que tem por obrigação serem adicionados imediatamente após a abertura da tag <code>&lt;body&gt;</code> conforme a orientação da documentação.', 2, 1, 'TEXTAREA', ''),
	(399, 50, 'Scripts inclusos antes do fechamento da tag <code><body></code>', 'javascript_body_final', '', 1, 'Este campo é utilizado para scripts que tem podem ser adicionados ao final do documento sendo antes do fechamento da tag <code>&lt;body&gt;</code> conforme a orientação da documentação.', 3, 1, 'TEXTAREA', ''),
	(400, 14, 'Disposição dos produtos na página inicial', 'home_layout', 'carousel', 1, 'Define o comportamento do layout dos produtos na página inicial do e-commerce. É possível agrupar através de um carousel ou através de uma lista corrida.', 1, 0, 'SELECT', '{"carousel": "Carrossel", "list": "Listagem contínua"}'),
	(401, 14, 'Limite de produtos por categoria na página inicial (Recomenda-se 8 para não sobrecarregar o site)', 'home_limite_quantidade_produtos', 8, 1, 'Define a quantidade limite de produtos por categoria.', 1, 0, 'TEXT', NULL);
/*!40000 ALTER TABLE `qp1_parametro` ENABLE KEYS */;
/*!40101 SET SQL_MODE=IFNULL(@OLD_SQL_MODE, '') */;
/*!40014 SET FOREIGN_KEY_CHECKS=IF(@OLD_FOREIGN_KEY_CHECKS IS NULL, 1, @OLD_FOREIGN_KEY_CHECKS) */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;