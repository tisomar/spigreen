
# This is a fix for InnoDB in MySQL >= 4.1.x
# It "suspends judgement" for fkey relationships until are tables are set.
SET FOREIGN_KEY_CHECKS = 0;

-- ---------------------------------------------------------------------
-- qp1_ajuda_pagina_video
-- ---------------------------------------------------------------------

DROP TABLE IF EXISTS `qp1_ajuda_pagina_video`;

CREATE TABLE `qp1_ajuda_pagina_video`
(
    `ID` INTEGER NOT NULL AUTO_INCREMENT,
    `SISTEMA` VARCHAR(255) NOT NULL,
    `PAGINA` VARCHAR(255) NOT NULL,
    `VIDEO` VARCHAR(255),
    `URL_SLUG` VARCHAR(255),
    PRIMARY KEY (`ID`)
) ENGINE=InnoDB;

-- ---------------------------------------------------------------------
-- qp1_arquivo
-- ---------------------------------------------------------------------

DROP TABLE IF EXISTS `qp1_arquivo`;

CREATE TABLE `qp1_arquivo`
(
    `ID` INTEGER NOT NULL AUTO_INCREMENT,
    `NOME` VARCHAR(200) NOT NULL,
    `LEGENDA` VARCHAR(200) NOT NULL,
    PRIMARY KEY (`ID`)
) ENGINE=InnoDB;

-- ---------------------------------------------------------------------
-- qp1_associacao_produto
-- ---------------------------------------------------------------------

DROP TABLE IF EXISTS `qp1_associacao_produto`;

CREATE TABLE `qp1_associacao_produto`
(
    `ID` INTEGER NOT NULL AUTO_INCREMENT,
    `PRODUTO_ORIGEM_ID` INTEGER NOT NULL,
    `TYPE` VARCHAR(100) NOT NULL,
    `NOME` VARCHAR(255) NOT NULL,
    `ORDEM` INTEGER(11) DEFAULT 1 NOT NULL,
    `DISPONIVEL` TINYINT(1) DEFAULT 0 NOT NULL,
    PRIMARY KEY (`ID`),
    INDEX `qp1_associacao_produto_FI_1` (`PRODUTO_ORIGEM_ID`),
    CONSTRAINT `qp1_associacao_produto_FK_1`
        FOREIGN KEY (`PRODUTO_ORIGEM_ID`)
        REFERENCES `qp1_produto` (`ID`)
        ON DELETE CASCADE
) ENGINE=InnoDB;

-- ---------------------------------------------------------------------
-- qp1_alteracao_rede
-- ---------------------------------------------------------------------

DROP TABLE IF EXISTS `qp1_alteracao_rede`;

CREATE TABLE `qp1_alteracao_rede`
(
    `ID` INTEGER NOT NULL AUTO_INCREMENT,
    `CLIENTE_MOVIDO` bigint(20) unsigned NOT NULL,
    `CLIENTE_DESTINO` bigint(20) unsigned NOT NULL,
    `USUARIO_ID` bigint(20) unsigned NOT NULL,
    `UPDATER` VARCHAR(255) NOT NULL,
    `DATA` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `DESCRICAO` TEXT NOT NULL,
    PRIMARY KEY (`ID`),
    INDEX `cliente_movido_FK_1_FI_1` (`CLIENTE_MOVIDO`),
    INDEX `cliente_destino_FI_1` (`CLIENTE_DESTINO`),
    INDEX `usuario_FI_1` (`USUARIO_ID`),
    CONSTRAINT `cliente_movido_FK_1`
        FOREIGN KEY (`CLIENTE_MOVIDO`)
        REFERENCES `qp1_cliente` (`ID`)
        ON DELETE CASCADE,
    CONSTRAINT `cliente_destino_FK_1`
        FOREIGN KEY (`CLIENTE_DESTINO`)
        REFERENCES `qp1_cliente` (`ID`)
        ON DELETE CASCADE,
    CONSTRAINT `usuario_FK_1`
        FOREIGN KEY (`USUARIO_ID`)
        REFERENCES `qp1_usuario` (`ID`)
        ON DELETE CASCADE
) ENGINE=InnoDB;

-- ---------------------------------------------------------------------
-- qp1_associacao_produto_produto
-- ---------------------------------------------------------------------

DROP TABLE IF EXISTS `qp1_associacao_produto_produto`;

CREATE TABLE `qp1_associacao_produto_produto`
(
    `ASSOCIACAO_ID` INTEGER NOT NULL,
    `PRODUTO_ID` INTEGER NOT NULL,
    PRIMARY KEY (`ASSOCIACAO_ID`,`PRODUTO_ID`),
    INDEX `qp1_associacao_produto_produto_FI_2` (`PRODUTO_ID`),
    CONSTRAINT `qp1_associacao_produto_produto_FK_1`
        FOREIGN KEY (`ASSOCIACAO_ID`)
        REFERENCES `qp1_associacao_produto` (`ID`)
        ON DELETE CASCADE,
    CONSTRAINT `qp1_associacao_produto_produto_FK_2`
        FOREIGN KEY (`PRODUTO_ID`)
        REFERENCES `qp1_produto` (`ID`)
        ON DELETE CASCADE
) ENGINE=InnoDB;

-- ---------------------------------------------------------------------
-- qp1_banner
-- ---------------------------------------------------------------------

DROP TABLE IF EXISTS `qp1_banner`;

CREATE TABLE `qp1_banner`
(
    `ID` INTEGER(10) NOT NULL AUTO_INCREMENT,
    `TITULO` VARCHAR(255) NOT NULL COMMENT 'Titulo do Banner',
    `ORDEM` INTEGER(3) DEFAULT 1 COMMENT 'Ordem do banner',
    `MOSTRAR` TINYINT(1) DEFAULT 0 COMMENT 'Mostrar?',
    `LINK` VARCHAR(255) COMMENT 'Link do banner',
    `IMAGEM` VARCHAR(50) NOT NULL COMMENT 'Imagem do banner',
    `TIPO` VARCHAR(50) DEFAULT 'DESTAQUE' NOT NULL COMMENT 'Tipo do banner',
    `TARGET` ENUM('_blank','_self','iframe') DEFAULT '_self' NOT NULL COMMENT 'Target do link ao clicar no banner',
    `COUNT_CLICK` BIGINT(20) UNSIGNED DEFAULT '0',
    `IMAGEM_XS` VARCHAR(50) NOT NULL COMMENT 'Imagem do banner',
    `IMAGEM_SM` VARCHAR(50) NOT NULL COMMENT 'Imagem do banner',
    `IMAGEM_MD` VARCHAR(50) NOT NULL COMMENT 'Imagem do banner',
    `IMAGEM_LG` VARCHAR(50) NOT NULL COMMENT 'Imagem do banner',
    PRIMARY KEY (`ID`)
) ENGINE=MyISAM COMMENT='Banners do sistema';

-- ---------------------------------------------------------------------
-- qp1_banco_cadastro_cliente
-- ---------------------------------------------------------------------

DROP TABLE IF EXISTS `qp1_banco_cadastro_cliente`;

CREATE TABLE `qp1_banco_cadastro_cliente`
(
    `ID` INTEGER(10) NOT NULL AUTO_INCREMENT,
    `BANCO` VARCHAR(255) NOT NULL COMMENT 'Nome do banco',
    `AGENCIA` VARCHAR(255) NOT NULL COMMENT 'Agência do banco',
    `CONTA` VARCHAR(255) NOT NULL COMMENT 'Conta do banco',
    `TIPO_CONTA` VARCHAR(20) NOT NULL COMMENT 'Tipo da conta',
    `CLIENTE_ID` INTEGER NOT NULL,
    `PIS_PASEP` VARCHAR(255) COMMENT 'Pis do cliente',
    `NOME_CORRENTISTA` VARCHAR(255) NOT NULL COMMENT 'Nome do correntista',
    `CPF_CORRENTISTA` VARCHAR(255) NOT NULL COMMENT 'Cpf do correntista',
    `CNPJ_CORRENTISTA` VARCHAR(255) NOT NULL COMMENT 'Cnpj do correntista',
    PRIMARY KEY (`ID`),
    INDEX `banco_cadastro_cliente_FK_1` (`CLIENTE_ID`),
    CONSTRAINT `banco_cadastro_cliente_FK_1`
        FOREIGN KEY (`CLIENTE_ID`)
        REFERENCES `qp1_cliente` (`ID`)
        ON UPDATE CASCADE
        ON DELETE CASCADE
) ENGINE=InnoDB COMMENT='Bancos cadastrados pelo cliente';

-- ---------------------------------------------------------------------
-- qp1_boleto_cielo_dados
-- ---------------------------------------------------------------------

DROP TABLE IF EXISTS `qp1_boleto_cielo_dados`;

CREATE TABLE `qp1_boleto_cielo_dados`
(
    `ID` INTEGER NOT NULL AUTO_INCREMENT,
    `PEDIDO_ID` INTEGER,
    `NUMBER` VARCHAR(255) NOT NULL,
    `DIGITABLE_LINE` VARCHAR(255) NOT NULL,
    `EXPIRATION_DATE` DATETIME NOT NULL,
    `DIAS_VENCIMENTO` VARCHAR(255) NOT NULL,
    `URL` VARCHAR(255) NOT NULL,
    `BAR_CODE_NUMBER` VARCHAR(255) NOT NULL,
    `IDENTIFICATION` VARCHAR(255) NOT NULL,
    `ASSIGNOR` VARCHAR(255) NOT NULL,
    `ADDRESS` VARCHAR(255) NOT NULL,
    `PROVIDER` VARCHAR(255) NOT NULL,
    `STATUS` VARCHAR(255),
    `INSTRUCTIONS` VARCHAR(255) NOT NULL,
    `CIELO_PAYMENT_ID` VARCHAR(255),
    `TIPO` VARCHAR(255) NOT NULL,
    PRIMARY KEY (`ID`),
    INDEX `TCartaoBoletoDados_TPEDI_FI_01` (`PEDIDO_ID`),
    CONSTRAINT `TCartaoBoletoDados_TPEDI_FK_01`
        FOREIGN KEY (`PEDIDO_ID`)
        REFERENCES `qp1_pedido` (`ID`)
        ON UPDATE CASCADE
        ON DELETE CASCADE
) ENGINE=InnoDB;

-- ---------------------------------------------------------------------
-- qp1_bonus_desempenho_cliente
-- ---------------------------------------------------------------------

DROP TABLE IF EXISTS `qp1_bonus_desempenho_cliente`;

CREATE TABLE `qp1_bonus_desempenho_cliente`
(
    `ID` INTEGER NOT NULL AUTO_INCREMENT,
    `CLIENTE_ID` bigint(20) unsigned NOT NULL,
    `DATA_CRIACAO` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `DATA_ATUALIZACAO` DATETIME,
    `DATA_REFERENTE` DATETIME NOT NULL,
    `NOME` VARCHAR(255) NOT NULL,
    `FAIXA_INICIAL` DOUBLE DEFAULT 0 NOT NULL,
    `FAIXA_FINAL` DOUBLE DEFAULT 0 NOT NULL,
    PRIMARY KEY (`ID`),
    INDEX `bonus_desempenho_cliente_FI_1` (`CLIENTE_ID`),
    CONSTRAINT `bonus_desempenho_cliente_FK_1`
        FOREIGN KEY (`CLIENTE_ID`)
        REFERENCES `qp1_cliente` (`ID`)
        ON DELETE CASCADE
) ENGINE=InnoDB;

-- ---------------------------------------------------------------------
-- qp1_buscape_shopping_item
-- ---------------------------------------------------------------------

DROP TABLE IF EXISTS `qp1_buscape_shopping_item`;

CREATE TABLE `qp1_buscape_shopping_item`
(
    `PRODUTO_ID` INTEGER NOT NULL,
    PRIMARY KEY (`PRODUTO_ID`),
    CONSTRAINT `buscape_shopping_item_FK_1`
        FOREIGN KEY (`PRODUTO_ID`)
        REFERENCES `qp1_produto` (`ID`)
        ON DELETE CASCADE
) ENGINE=InnoDB;

-- ---------------------------------------------------------------------
-- qp1_cartao_cielo_dados
-- ---------------------------------------------------------------------

DROP TABLE IF EXISTS `qp1_cartao_cielo_dados`;

CREATE TABLE `qp1_cartao_cielo_dados`
(
    `ID` INTEGER NOT NULL AUTO_INCREMENT,
    `PEDIDO_ID` INTEGER,
    `NUMERO` VARCHAR(255) NOT NULL,
    `NOME` VARCHAR(255) NOT NULL,
    `VALIDADE_MES` VARCHAR(255) NOT NULL,
    `VALIDADE_ANO` VARCHAR(255) NOT NULL,
    `CODIGO` VARCHAR(255) NOT NULL,
    `TIPO` VARCHAR(255) NOT NULL,
    `CPF` VARCHAR(255) NOT NULL,
    `BANDEIRA` VARCHAR(255) NOT NULL,
    `JSON_ANALISIS_RISCO` TEXT,
    `STATUS` VARCHAR(255),
    `CIELO_PAYMENT_ID` VARCHAR(255),
    PRIMARY KEY (`ID`),
    INDEX `TCartaoCieloDados_TPEDI_FI_01` (`PEDIDO_ID`),
    CONSTRAINT `TCartaoCieloDados_TPEDI_FK_01`
        FOREIGN KEY (`PEDIDO_ID`)
        REFERENCES `qp1_pedido` (`ID`)
        ON UPDATE CASCADE
        ON DELETE CASCADE
) ENGINE=InnoDB;

-- ---------------------------------------------------------------------
-- qp1_categoria
-- ---------------------------------------------------------------------

DROP TABLE IF EXISTS `qp1_categoria`;

CREATE TABLE `qp1_categoria`
(
    `ID` INTEGER(10) NOT NULL AUTO_INCREMENT,
    `NOME` VARCHAR(250) NOT NULL,
    `KEY` VARCHAR(250) NOT NULL,
    `URL_CATEGORIA` VARCHAR(250) NOT NULL,
    `ORDEM` INTEGER DEFAULT 0,
    `MOSTRAR_BARRA_MENU` TINYINT(1) DEFAULT 0,
    `MOSTRAR_PAGINA_INICIAL` TINYINT(1) DEFAULT 0,
    `DISPONIVEL` TINYINT(1) DEFAULT 1,
    `COMBO` TINYINT(1) DEFAULT 0,
    `IS_CATEGORY_BY_KIT` TINYINT(1) DEFAULT 0,
    `TOTAL_PRODUTOS` INTEGER,
    `BANNER` VARCHAR(255),
    `NR_LFT` INTEGER,
    `NR_RGT` INTEGER,
    `NR_LVL` INTEGER,
    PRIMARY KEY (`ID`)
) ENGINE=InnoDB;

-- ---------------------------------------------------------------------
-- qp1_centro_distribuicao
-- ---------------------------------------------------------------------

DROP TABLE IF EXISTS `qp1_centro_distribuicao`;

CREATE TABLE `qp1_centro_distribuicao`
(
    `ID` INTEGER(11) NOT NULL AUTO_INCREMENT,
    `CEP` VARCHAR(10) NOT NULL,
    `DESCRICAO` VARCHAR(255) NOT NULL,
    `STATUS` TINYINT(1) DEFAULT 1 NOT NULL,
    PRIMARY KEY (`ID`),
    UNIQUE INDEX `centro_distribuicao_cep_uindex` (`CEP`)
) ENGINE=InnoDB;

-- ---------------------------------------------------------------------
-- qp1_cidade
-- ---------------------------------------------------------------------

DROP TABLE IF EXISTS `qp1_cidade`;

CREATE TABLE `qp1_cidade`
(
    `ID` INTEGER NOT NULL AUTO_INCREMENT,
    `NOME` VARCHAR(255) NOT NULL,
    `ESTADO_ID` INTEGER NOT NULL,
    PRIMARY KEY (`ID`),
    INDEX `cidade_FI_1` (`ESTADO_ID`),
    CONSTRAINT `cidade_FK_1`
        FOREIGN KEY (`ESTADO_ID`)
        REFERENCES `qp1_estado` (`ID`)
        ON UPDATE CASCADE
        ON DELETE CASCADE
) ENGINE=InnoDB;

-- ---------------------------------------------------------------------
-- qp1_classificacao_unilevel
-- ---------------------------------------------------------------------

DROP TABLE IF EXISTS `qp1_classificacao_unilevel`;

CREATE TABLE `qp1_classificacao_unilevel`
(
    `ID` INTEGER(11) NOT NULL AUTO_INCREMENT,
    `DATA_CRIACAO` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `DATA_ATUALIZACAO` DATETIME,
    `NOME` VARCHAR(255) NOT NULL,
    `OBSERVACAO` TEXT NOT NULL,
    `VALOR_COMPRA` DOUBLE DEFAULT 0 NOT NULL,
    `PERCENTUAL_BONUS` DOUBLE DEFAULT 0 NOT NULL,
    `QTD_CLIENTES_ATIVOS` INTEGER DEFAULT 0 NOT NULL,
    `NIVEIS_PAGANTES` INTEGER DEFAULT 0 NOT NULL,
    `ORDEM` INTEGER DEFAULT 0 NOT NULL,
    `LEVEL_BONUS` TINYINT(1) DEFAULT 0 NOT NULL,
    PRIMARY KEY (`ID`)
) ENGINE=InnoDB;

-- ---------------------------------------------------------------------
-- qp1_cliente
-- ---------------------------------------------------------------------

DROP TABLE IF EXISTS `qp1_cliente`;

CREATE TABLE `qp1_cliente`
(
    `ID` BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
    `NOME` VARCHAR(255) NOT NULL,
    `CPF` VARCHAR(14) NOT NULL,
    `TELEFONE` VARCHAR(20) NOT NULL,
    `DATA_NASCIMENTO` DATE NOT NULL,
    `RAZAO_SOCIAL` VARCHAR(255),
    `NOME_FANTASIA` VARCHAR(255),
    `INSCRICAO_ESTADUAL` VARCHAR(25),
    `CNPJ` VARCHAR(18),
    `EMAIL` VARCHAR(255) NOT NULL,
    `SENHA` VARCHAR(50) NOT NULL,
    `RECUPERACAO_SENHA_TOKEN` VARCHAR(50),
    `RECUPERACAO_SENHA_DATA` DATE,
    `STATUS` TINYINT DEFAULT 0 NOT NULL,
    `TABELA_PRECO_ID` BIGINT(20) UNSIGNED,
    `MOTIVO_REPROVACAO` TEXT,
    `PLANO_ID` INTEGER,
    `CHAVE_INDICACAO` VARCHAR(11) NOT NULL COMMENT 'Chave gerada no cadastro do cliente que é usada para indicar novos clientes.',
    `INDICADOR_ID` BIGINT(20) UNSIGNED,
    `INDICADOR_DIRETO_ID` BIGINT(20) UNSIGNED,
    `LADO_INSERCAO_CADASTRADOS` ENUM('AUTOMATICO','ESQUERDO','DIREITO') DEFAULT 'AUTOMATICO' NOT NULL,
    `VENCIMENTO_MENSALIDADE` DATETIME,
    `LIVRE_MENSALIDADE` TINYINT(1) DEFAULT 0 NOT NULL,
    `TAXA_CADASTRO` TINYINT(1) DEFAULT 0 NOT NULL,
    `VAGO` TINYINT(1) DEFAULT 0 NOT NULL,
    `PONTOS_DISTRIBUIDOS_PLANOS` INTEGER DEFAULT 0 NOT NULL,
    `NAO_COMPRA` TINYINT(1) DEFAULT 0 NOT NULL,
    `COMPRA_ULTIMO_MES` DOUBLE,
    `TIPO_CONSUMIDOR` TINYINT DEFAULT 0 NOT NULL,
    `NUMERO_MESES_ATIVO` INTEGER DEFAULT 0 NOT NULL,
    `DATA_ULTIMO_LEAD` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `CREATED_BY_HOTSITE` TINYINT(1) DEFAULT 0,
    `DATA_ATIVACAO` DATETIME,
    `created_at` DATETIME,
    `updated_at` DATETIME,
    `tree_left` INTEGER,
    `tree_right` INTEGER,
    `tree_level` INTEGER,
    PRIMARY KEY (`ID`),
    UNIQUE INDEX `qp1_cliente_U_1` (`CHAVE_INDICACAO`),
    INDEX `qp1_cliente_FI_1` (`TABELA_PRECO_ID`),
    INDEX `qp1_cliente_FI_2` (`PLANO_ID`),
    INDEX `qp1_cliente_FI_3` (`INDICADOR_ID`),
    INDEX `qp1_cliente_FI_4` (`INDICADOR_DIRETO_ID`),
    CONSTRAINT `qp1_cliente_FK_1`
        FOREIGN KEY (`TABELA_PRECO_ID`)
        REFERENCES `qp1_tabela_preco` (`ID`)
        ON DELETE SET NULL,
    CONSTRAINT `qp1_cliente_FK_2`
        FOREIGN KEY (`PLANO_ID`)
        REFERENCES `qp1_plano` (`ID`)
        ON DELETE RESTRICT,
    CONSTRAINT `qp1_cliente_FK_3`
        FOREIGN KEY (`INDICADOR_ID`)
        REFERENCES `qp1_cliente` (`ID`)
        ON DELETE SET NULL,
    CONSTRAINT `qp1_cliente_FK_4`
        FOREIGN KEY (`INDICADOR_DIRETO_ID`)
        REFERENCES `qp1_cliente` (`ID`)
        ON DELETE SET NULL
) ENGINE=InnoDB;

-- ---------------------------------------------------------------------
-- qp1_cliente_ajuda_pagina_view
-- ---------------------------------------------------------------------

DROP TABLE IF EXISTS `qp1_cliente_ajuda_pagina_view`;

CREATE TABLE `qp1_cliente_ajuda_pagina_view`
(
    `ID` INTEGER NOT NULL AUTO_INCREMENT,
    `CLIENTE_ID` bigint(20) unsigned NOT NULL,
    `VIDEO_ID` INTEGER NOT NULL,
    `DATAVISTO` DATETIME NOT NULL,
    PRIMARY KEY (`ID`),
    INDEX `FI_cliente_ajuda_pagina_video` (`CLIENTE_ID`),
    INDEX `FI_ajuda_video_pagina_video_view` (`VIDEO_ID`),
    CONSTRAINT `FK_ajuda_video_pagina_video_view`
        FOREIGN KEY (`VIDEO_ID`)
        REFERENCES `qp1_ajuda_pagina_video` (`ID`),
    CONSTRAINT `FK_cliente_ajuda_pagina_video`
        FOREIGN KEY (`CLIENTE_ID`)
        REFERENCES `qp1_cliente` (`ID`)
) ENGINE=InnoDB;

-- ---------------------------------------------------------------------
-- qp1_cliente_distribuidor
-- ---------------------------------------------------------------------

DROP TABLE IF EXISTS `qp1_cliente_distribuidor`;

CREATE TABLE `qp1_cliente_distribuidor`
(
    `ID` INTEGER NOT NULL AUTO_INCREMENT,
    `CLIENTE_ID` INTEGER NOT NULL,
    `CLIENTE_REDEFACIL_ID` INTEGER,
    `TIPO` CHAR NOT NULL,
    `TIPO_LEAD` CHAR,
    `EMAIL` VARCHAR(200),
    `TELEFONE` VARCHAR(20),
    `TELEFONE_CELULAR` VARCHAR(20),
    `WHATSAPP` VARCHAR(20),
    `NOME_RAZAO_SOCIAL` VARCHAR(200),
    `SOBRENOME_NOME_FANTASIA` VARCHAR(200),
    `CPF_CNPJ` VARCHAR(18),
    `RG_IE` VARCHAR(40),
    `SEXO` CHAR,
    `DATA_NASCIMENTO_DATA_FUNDACAO` DATE,
    `RESPONSAVEL_NOME` VARCHAR(100),
    `RESPONSAVEL_SOBRENOME` VARCHAR(100),
    `RESPONSAVEL_SEXO` CHAR,
    `RESPONSAVEL_CPF` VARCHAR(14),
    `RESPONSAVEL_RG` VARCHAR(45),
    `RESPONSAVEL_DATA_NASCIMENTO` DATE,
    `DATA_CADASTRO` DATETIME NOT NULL,
    `DATA_ATUALIZACAO` DATETIME,
    `STATUS` enum('APROVADO','PENDENTE'),
    `CEP` VARCHAR(9),
    `LEAD` TINYINT,
    `NOTIFICACAO_ALERTA` INTEGER,
    `ALERTA_AVISO_MUDANCA_PATROCINADOR` INTEGER,
    `ESTADO` VARCHAR(100),
    `CIDADE` VARCHAR(100),
    `BAIRRO` VARCHAR(100),
    `COMPLEMENTO` VARCHAR(255),
    `ENDERECO` VARCHAR(255),
    `NUMERO` INTEGER,
    PRIMARY KEY (`ID`),
    INDEX `FI_CLIENTE_DISTRIBUIDOR_CLIENTE` (`CLIENTE_ID`),
    CONSTRAINT `FK_CLIENTE_DISTRIBUIDOR_CLIENTE`
        FOREIGN KEY (`CLIENTE_ID`)
        REFERENCES `qp1_cliente` (`ID`)
        ON DELETE CASCADE
) ENGINE=InnoDB;

-- ---------------------------------------------------------------------
-- qp1_cliente_inativado
-- ---------------------------------------------------------------------

DROP TABLE IF EXISTS `qp1_cliente_inativado`;

CREATE TABLE `qp1_cliente_inativado`
(
    `ID` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
    `CLIENTE_ID` bigint(20) unsigned NOT NULL,
    `NOME` VARCHAR(255) NOT NULL,
    `CPF` VARCHAR(14) NOT NULL,
    `TELEFONE` VARCHAR(20) NOT NULL,
    `DATA_NASCIMENTO` DATE NOT NULL,
    `RAZAO_SOCIAL` VARCHAR(255),
    `NOME_FANTASIA` VARCHAR(255),
    `INSCRICAO_ESTADUAL` VARCHAR(25),
    `CNPJ` VARCHAR(18),
    `EMAIL` VARCHAR(255) NOT NULL,
    `SENHA` VARCHAR(50) NOT NULL,
    `RECUPERACAO_SENHA_TOKEN` VARCHAR(50),
    `RECUPERACAO_SENHA_DATA` DATE,
    `STATUS` TINYINT DEFAULT 0 NOT NULL,
    `TABELA_PRECO_ID` bigint(20) unsigned,
    `MOTIVO_REPROVACAO` TEXT,
    `PLANO_ID` INTEGER,
    `CHAVE_INDICACAO` VARCHAR(11) NOT NULL,
    `INDICADOR_ID` bigint(20) unsigned,
    `INDICADOR_DIRETO_ID` bigint(20) unsigned,
    `LADO_INSERCAO_CADASTRADOS` enum('AUTOMATICO','ESQUERDO','DIREITO') DEFAULT 'AUTOMATICO' NOT NULL,
    `VENCIMENTO_MENSALIDADE` DATETIME,
    `LIVRE_MENSALIDADE` TINYINT(1) DEFAULT 0 NOT NULL,
    `VAGO` TINYINT(1) DEFAULT 0 NOT NULL,
    `PONTOS_DISTRIBUIDOS_PLANOS` INTEGER DEFAULT 0 NOT NULL,
    `created_at` DATETIME,
    `updated_at` DATETIME,
    `tree_left` INTEGER,
    `tree_right` INTEGER,
    `tree_level` INTEGER,
    `DATA_ATIVACAO` DATETIME,
    PRIMARY KEY (`ID`,`CLIENTE_ID`),
    INDEX `cliente_inativado_FI_1` (`TABELA_PRECO_ID`),
    INDEX `cliente_inativado_FI_2` (`PLANO_ID`),
    INDEX `cliente_inativado_FI_3` (`INDICADOR_ID`),
    INDEX `cliente_inativado_FI_4` (`INDICADOR_DIRETO_ID`),
    INDEX `cliente_inativado_FI_5` (`CLIENTE_ID`),
    CONSTRAINT `cliente_inativado_FK_1`
        FOREIGN KEY (`TABELA_PRECO_ID`)
        REFERENCES `qp1_tabela_preco` (`ID`)
        ON DELETE SET NULL,
    CONSTRAINT `cliente_inativado_FK_2`
        FOREIGN KEY (`PLANO_ID`)
        REFERENCES `qp1_plano` (`ID`)
        ON DELETE RESTRICT,
    CONSTRAINT `cliente_inativado_FK_3`
        FOREIGN KEY (`INDICADOR_ID`)
        REFERENCES `qp1_cliente` (`ID`)
        ON DELETE SET NULL,
    CONSTRAINT `cliente_inativado_FK_4`
        FOREIGN KEY (`INDICADOR_DIRETO_ID`)
        REFERENCES `qp1_cliente` (`ID`)
        ON DELETE SET NULL,
    CONSTRAINT `cliente_inativado_FK_5`
        FOREIGN KEY (`CLIENTE_ID`)
        REFERENCES `qp1_cliente` (`ID`)
        ON DELETE RESTRICT
) ENGINE=InnoDB;

-- ---------------------------------------------------------------------
-- qp1_configuracao_pontuacao_mensal
-- ---------------------------------------------------------------------

DROP TABLE IF EXISTS `qp1_configuracao_pontuacao_mensal`;

CREATE TABLE `qp1_configuracao_pontuacao_mensal`
(
    `ID` INTEGER(10) NOT NULL AUTO_INCREMENT,
    `DIA_AVISO_1` INTEGER NOT NULL,
    `TIPO_AVISO_2` INTEGER(8) NOT NULL,
    `DIA_AVISO_2` VARCHAR(255) NOT NULL,
    `ASSUNTO_AVISO_1` VARCHAR(50) NOT NULL,
    `ASSUNTO_AVISO_2` VARCHAR(50) NOT NULL,
    `DESCRICAO_EXTRATO` VARCHAR(150) NOT NULL,
    `DESCRICAO_AVISO_1` TEXT NOT NULL,
    `DESCRICAO_AVISO_2` TEXT NOT NULL,
    `VALOR_COMPRA` DOUBLE DEFAULT 0 NOT NULL,
    `VALOR_PONTOS` DOUBLE DEFAULT 0 NOT NULL,
    `MENSAGEM_RESGATE_PONTOS_DIRETO` VARCHAR(255),
    `MENSAGEM_RESGATE_PONTOS_INDIRETO` VARCHAR(255),
    `MENSAGEM_RESGATE_PONTOS_RECOMPRA` VARCHAR(255),
    PRIMARY KEY (`ID`)
) ENGINE=InnoDB;

-- ---------------------------------------------------------------------
-- qp1_conteudo
-- ---------------------------------------------------------------------

DROP TABLE IF EXISTS `qp1_conteudo`;

CREATE TABLE `qp1_conteudo`
(
    `ID` INTEGER NOT NULL AUTO_INCREMENT,
    `CHAVE` VARCHAR(200) NOT NULL,
    `NOME` VARCHAR(200) NOT NULL,
    `DESCRICAO` TEXT NOT NULL,
    `IMAGEM` VARCHAR(50),
    `GALERIA_ID` INTEGER,
    `POSSUI_IMAGEM` TINYINT(1) DEFAULT 0,
    `POSSUI_GALERIA` TINYINT(1) DEFAULT 0,
    `TIPO_CONTEUDO` enum('PAGINA','BLOCO'),
    PRIMARY KEY (`ID`),
    INDEX `TCONT_TGALE_FK_IDX_01` (`GALERIA_ID`),
    CONSTRAINT `TCONT_TGALE_FK_01`
        FOREIGN KEY (`GALERIA_ID`)
        REFERENCES `qp1_galeria` (`ID`)
        ON UPDATE SET NULL
        ON DELETE SET NULL
) ENGINE=InnoDB;

-- ---------------------------------------------------------------------
-- qp1_cronlog_aviso_compra_mensal
-- ---------------------------------------------------------------------

DROP TABLE IF EXISTS `qp1_cronlog_aviso_compra_mensal`;

CREATE TABLE `qp1_cronlog_aviso_compra_mensal`
(
    `ID` INTEGER NOT NULL AUTO_INCREMENT,
    `DATA` DATETIME NOT NULL,
    `CLIENTE_ID` VARCHAR(255) NOT NULL,
    `AVISO` VARCHAR(255) NOT NULL,
    `MENSAGEM` TEXT NOT NULL,
    `TITULO` TEXT NOT NULL,
    `VISUALIZADO` TINYINT(1) DEFAULT 0 NOT NULL,
    `VALOR_COMPRA` DOUBLE NOT NULL,
    `updated_at` DATETIME,
    PRIMARY KEY (`ID`)
) ENGINE=InnoDB;

-- ---------------------------------------------------------------------
-- qp1_cronlog_bloqueia_compra_mensal
-- ---------------------------------------------------------------------

DROP TABLE IF EXISTS `qp1_cronlog_bloqueia_compra_mensal`;

CREATE TABLE `qp1_cronlog_bloqueia_compra_mensal`
(
    `ID` INTEGER NOT NULL AUTO_INCREMENT,
    `DATA` DATETIME NOT NULL,
    `CLIENTE_ID` VARCHAR(255) NOT NULL,
    `VALOR_COMPRA` DOUBLE NOT NULL,
    `updated_at` DATETIME,
    PRIMARY KEY (`ID`)
) ENGINE=InnoDB;

-- ---------------------------------------------------------------------
-- qp1_cupom
-- ---------------------------------------------------------------------

DROP TABLE IF EXISTS `qp1_cupom`;

CREATE TABLE `qp1_cupom`
(
    `ID` INTEGER NOT NULL AUTO_INCREMENT,
    `CUPOM` VARCHAR(25) NOT NULL,
    `TIPO_DESCONTO` enum('REAIS','PORCENTAGEM') DEFAULT 'PORCENTAGEM' NOT NULL,
    `VALOR_DESCONTO` FLOAT(12,2) NOT NULL,
    `DATA_INICIAL` DATE NOT NULL,
    `DATA_FINAL` DATE,
    `VALOR_MINIMO_CARRINHO` FLOAT(12,2),
    `MODELO_CLIENTE_USO` VARCHAR(30) DEFAULT 'todos' NOT NULL COMMENT 'Modelo dos cientes que irão usar dentre as opções estã todos, consumidor final, distribuidor, nominal (um ou mais clientes que deverão ser escolhidos, rede que funciona similar ao nominal porém o nominal não pode usar e o nominal + rede.',
    `CLIENTES` TEXT,
    PRIMARY KEY (`ID`),
    UNIQUE INDEX `TCUPO_UK_01` (`CUPOM`)
) ENGINE=InnoDB;

-- ---------------------------------------------------------------------
-- qp1_cupom_cliente
-- ---------------------------------------------------------------------

DROP TABLE IF EXISTS `qp1_cupom_cliente`;

CREATE TABLE `qp1_cupom_cliente`
(
    `ID` INTEGER NOT NULL AUTO_INCREMENT,
    `CUPOM_ID` INTEGER NOT NULL,
    `CLIENTE_ID` bigint(20) unsigned NOT NULL,
    `UTILIZADO` TINYINT(1) DEFAULT 0 NOT NULL,
    PRIMARY KEY (`ID`),
    INDEX `TCUCL_TCUPO_FK_IDX_01` (`CUPOM_ID`),
    INDEX `TCUCL_TCLIE_FK_IDX_01` (`CLIENTE_ID`),
    CONSTRAINT `TCUCL_TCLIE_FK_01`
        FOREIGN KEY (`CLIENTE_ID`)
        REFERENCES `qp1_cliente` (`ID`)
        ON UPDATE CASCADE
        ON DELETE CASCADE,
    CONSTRAINT `TCUCL_TCUPO_FK_01`
        FOREIGN KEY (`CUPOM_ID`)
        REFERENCES `qp1_cupom` (`ID`)
        ON UPDATE CASCADE
        ON DELETE CASCADE
) ENGINE=InnoDB;

-- ---------------------------------------------------------------------
-- qp1_desconto_fidelidade
-- ---------------------------------------------------------------------

DROP TABLE IF EXISTS `qp1_desconto_fidelidade`;

CREATE TABLE `qp1_desconto_fidelidade`
(
    `ID` INTEGER NOT NULL AUTO_INCREMENT,
    `DATA_CRIACAO` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `DATA_ATUALIZACAO` DATETIME,
    `NOME` VARCHAR(255) NOT NULL,
    `MES_INICIAL` INTEGER DEFAULT 0 NOT NULL,
    `MES_FINAL` INTEGER DEFAULT 0 NOT NULL,
    `PERCENTUAL_DESCONTO` DOUBLE DEFAULT 0 NOT NULL,
    PRIMARY KEY (`ID`)
) ENGINE=InnoDB;

-- ---------------------------------------------------------------------
-- qp1_desconto_fidelidade_cliente
-- ---------------------------------------------------------------------

DROP TABLE IF EXISTS `qp1_desconto_fidelidade_cliente`;

CREATE TABLE `qp1_desconto_fidelidade_cliente`
(
    `ID` INTEGER NOT NULL AUTO_INCREMENT,
    `CLIENTE_ID` bigint(20) unsigned NOT NULL,
    `DATA_CRIACAO` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `DATA_ATUALIZACAO` DATETIME,
    `MES_ATUAL` INTEGER DEFAULT 0 NOT NULL,
    `MES_INICIAL` INTEGER DEFAULT 0 NOT NULL,
    `MES_FINAL` INTEGER DEFAULT 0 NOT NULL,
    `PERCENTUAL_DESCONTO` DOUBLE DEFAULT 0 NOT NULL,
    PRIMARY KEY (`ID`),
    INDEX `desconto_fidelidade_cliente_FI_1` (`CLIENTE_ID`),
    CONSTRAINT `desconto_fidelidade_cliente_FK_1`
        FOREIGN KEY (`CLIENTE_ID`)
        REFERENCES `qp1_cliente` (`ID`)
        ON DELETE CASCADE
) ENGINE=InnoDB;

-- ---------------------------------------------------------------------
-- qp1_desconto_pagamento_pontos
-- ---------------------------------------------------------------------

DROP TABLE IF EXISTS `qp1_desconto_pagamento_pontos`;

CREATE TABLE `qp1_desconto_pagamento_pontos`
(
    `ID` INTEGER NOT NULL AUTO_INCREMENT,
    `VALOR_DESCONTO` FLOAT(12,2) NOT NULL,
    `PEDIDO_ID` bigint(20) unsigned NOT NULL,
    `PAGAMENTO_BONUS` TINYINT(1) DEFAULT 0 NOT NULL,
    `PAGAMENTO_BONUS_CP` TINYINT(1) DEFAULT 0 NOT NULL,
    `PAGAMENTO_BONUS_FRETE` TINYINT(1) DEFAULT 0 NOT NULL,
    PRIMARY KEY (`ID`),
    INDEX `desconto_pagamento_pontos_FI_1` (`PEDIDO_ID`),
    CONSTRAINT `desconto_pagamento_pontos_FK_1`
        FOREIGN KEY (`PEDIDO_ID`)
        REFERENCES `qp1_pedido` (`ID`)
        ON DELETE CASCADE
) ENGINE=InnoDB;

-- ---------------------------------------------------------------------
-- qp1_distribuicao
-- ---------------------------------------------------------------------

DROP TABLE IF EXISTS `qp1_distribuicao`;

CREATE TABLE `qp1_distribuicao`
(
    `ID` INTEGER NOT NULL AUTO_INCREMENT,
    `DATA` DATETIME NOT NULL,
    `DATA_INICIO` DATETIME NOT NULL,
    `DATA_FINAL` DATETIME NOT NULL,
    `TOTAL_PONTOS` FLOAT NOT NULL,
    `STATUS` enum('AGUARDANDO_PREVIEW','PROCESSANDO_PREVIEW','PREVIEW','AGUARDANDO','PROCESSANDO','DISTRIBUIDO','CANCELADO') DEFAULT 'AGUARDANDO_PREVIEW' NOT NULL,
    PRIMARY KEY (`ID`)
) ENGINE=InnoDB;

-- ---------------------------------------------------------------------
-- qp1_distribuicao_bonus_produtos
-- ---------------------------------------------------------------------

DROP TABLE IF EXISTS `qp1_distribuicao_bonus_produtos`;

CREATE TABLE `qp1_distribuicao_bonus_produtos`
(
    `ID` INTEGER NOT NULL AUTO_INCREMENT,
    `DATA` DATETIME NOT NULL,
    `DATA_INICIO` DATETIME NOT NULL,
    `DATA_FINAL` DATETIME NOT NULL,
    `TOTAL_CLIENTES` INTEGER,
    `STATUS` enum('AGUARDANDO_PREVIEW','PROCESSANDO_PREVIEW','PREVIEW','AGUARDANDO','PROCESSANDO','DISTRIBUIDO','CANCELADO') DEFAULT 'AGUARDANDO_PREVIEW' NOT NULL,
    PRIMARY KEY (`ID`)
) ENGINE=InnoDB;

-- ---------------------------------------------------------------------
-- qp1_distribuicao_cliente
-- ---------------------------------------------------------------------

DROP TABLE IF EXISTS `qp1_distribuicao_cliente`;

CREATE TABLE `qp1_distribuicao_cliente`
(
    `ID` INTEGER NOT NULL AUTO_INCREMENT,
    `DISTRIBUICAO_ID` INTEGER NOT NULL,
    `CLIENTE_ID` bigint(20) unsigned NOT NULL,
    `TOTAL_PONTOS_PROCESSADOS` FLOAT NOT NULL,
    `TOTAL_PONTOS_USADOS` FLOAT NOT NULL,
    `TOTAL_PONTOS` FLOAT NOT NULL,
    `TOTAL_PONTOS_ADESAO` FLOAT NOT NULL,
    `TOTAL_PONTOS_RECOMPRA` FLOAT NOT NULL,
    `TOTAL_PONTOS_LIDERANCA` FLOAT NOT NULL,
    `DATA` DATETIME NOT NULL,
    PRIMARY KEY (`ID`),
    UNIQUE INDEX `distribuicao_cliente_U_1` (`DISTRIBUICAO_ID`, `CLIENTE_ID`),
    INDEX `distribuicao_cliente_FI_2` (`CLIENTE_ID`),
    CONSTRAINT `distribuicao_cliente_FK_1`
        FOREIGN KEY (`DISTRIBUICAO_ID`)
        REFERENCES `qp1_distribuicao` (`ID`)
        ON DELETE CASCADE,
    CONSTRAINT `distribuicao_cliente_FK_2`
        FOREIGN KEY (`CLIENTE_ID`)
        REFERENCES `qp1_cliente` (`ID`)
        ON DELETE CASCADE
) ENGINE=InnoDB;

-- ---------------------------------------------------------------------
-- qp1_distribuicao_cliente_unilevel
-- ---------------------------------------------------------------------

DROP TABLE IF EXISTS `qp1_distribuicao_cliente_unilevel`;

CREATE TABLE `qp1_distribuicao_cliente_unilevel`
(
    `ID` INTEGER NOT NULL AUTO_INCREMENT,
    `DISTRIBUICAO_ID` INTEGER NOT NULL,
    `CLIENTE_ID` bigint(20) unsigned NOT NULL,
    `DATA` DATETIME NOT NULL,
    `TOTAL_ATIVOS_REDE` INTEGER NOT NULL,
    `CLASSIFICACAO` VARCHAR(255) NOT NULL,
    `NIVEL_CLASSIFICACAO` INTEGER NOT NULL,
    `TOTAL_ATIVOS_REDE_ANTERIOR` INTEGER NOT NULL,
    `CLASSIFICACAO_ANTERIOR` VARCHAR(255) NOT NULL,
    `NIVEL_CLASSIFICACAO_ANTERIOR` INTEGER NOT NULL,
    `PERCENTUAL_BONUS` DOUBLE DEFAULT 0 NOT NULL,
    `TOTAL_BONUS` DOUBLE DEFAULT 0 NOT NULL,
    `LEVEL_BONUS` TINYINT(1) DEFAULT 0 NOT NULL,
    PRIMARY KEY (`ID`),
    UNIQUE INDEX `distribuicao_cliente_unilevel_U_1` (`DISTRIBUICAO_ID`, `CLIENTE_ID`),
    INDEX `distribuicao_cliente_unilevel_FI_2` (`CLIENTE_ID`),
    CONSTRAINT `distribuicao_cliente_unilevel_FK_1`
        FOREIGN KEY (`DISTRIBUICAO_ID`)
        REFERENCES `qp1_distribuicao_unilevel` (`ID`)
        ON DELETE CASCADE,
    CONSTRAINT `distribuicao_cliente_unilevel_FK_2`
        FOREIGN KEY (`CLIENTE_ID`)
        REFERENCES `qp1_cliente` (`ID`)
        ON DELETE CASCADE
) ENGINE=InnoDB;

-- ---------------------------------------------------------------------
-- qp1_distribuicao_unilevel
-- ---------------------------------------------------------------------

DROP TABLE IF EXISTS `qp1_distribuicao_unilevel`;

CREATE TABLE `qp1_distribuicao_unilevel`
(
    `ID` INTEGER NOT NULL AUTO_INCREMENT,
    `DATA` DATETIME NOT NULL,
    `DATA_INICIO` DATETIME NOT NULL,
    `DATA_FINAL` DATETIME NOT NULL,
    `TOTAL_ATIVOS` INTEGER NOT NULL,
    `STATUS` enum('AGUARDANDO_PREVIEW','PROCESSANDO_PREVIEW','PREVIEW','AGUARDANDO','PROCESSANDO','DISTRIBUIDO','CANCELADO') DEFAULT 'AGUARDANDO_PREVIEW' NOT NULL,
    PRIMARY KEY (`ID`)
) ENGINE=InnoDB;

-- ---------------------------------------------------------------------
-- qp1_distribuicao_unilevel_preview
-- ---------------------------------------------------------------------

DROP TABLE IF EXISTS `qp1_distribuicao_unilevel_preview`;

CREATE TABLE `qp1_distribuicao_unilevel_preview`
(
    `ID` INTEGER NOT NULL AUTO_INCREMENT,
    `DISTRIBUICAO_ID` INTEGER NOT NULL,
    `NIVEL` INTEGER,
    `CLIENTE_ID` bigint(20) unsigned,
    `NOME` VARCHAR(200),
    `EMAIL` VARCHAR(150),
    `TELEFONE` VARCHAR(50),
    `CHAVE` VARCHAR(50),
    `INDICADOR_ID` INTEGER,
    `VIP` INTEGER,
    `TOTAL_ATIVOS_REDE` INTEGER NOT NULL,
    `CLASSIFICACAO` VARCHAR(255) NOT NULL,
    `NIVEL_CLASSIFICACAO` INTEGER NOT NULL,
    `TOTAL_ATIVOS_REDE_ANTERIOR` INTEGER NOT NULL,
    `CLASSIFICACAO_ANTERIOR` VARCHAR(255) NOT NULL,
    `NIVEL_CLASSIFICACAO_ANTERIOR` INTEGER NOT NULL,
    `PERCENTUAL_BONUS` DOUBLE DEFAULT 0 NOT NULL,
    `TOTAL_BONUS` DOUBLE DEFAULT 0 NOT NULL,
    `LEVEL_BONUS` TINYINT(1) DEFAULT 0 NOT NULL,
    PRIMARY KEY (`ID`),
    INDEX `TDIPR_DISTUNI_IDX_01` (`DISTRIBUICAO_ID`),
    CONSTRAINT `TDIPR_DISTUNI_FK_01`
        FOREIGN KEY (`DISTRIBUICAO_ID`)
        REFERENCES `qp1_distribuicao_unilevel` (`ID`)
        ON UPDATE CASCADE
        ON DELETE CASCADE
) ENGINE=MyISAM;

-- ---------------------------------------------------------------------
-- qp1_documento_alerta
-- ---------------------------------------------------------------------

DROP TABLE IF EXISTS `qp1_documento_alerta`;

CREATE TABLE `qp1_documento_alerta`
(
    `ID` INTEGER NOT NULL AUTO_INCREMENT,
    `USUARIO_ID` INTEGER,
    `TITULO` VARCHAR(250) NOT NULL,
    `TIPO_MENSAGEM` VARCHAR(255) DEFAULT 'termos_uso' NOT NULL,
    `TIPO_DEST` VARCHAR(255) NOT NULL,
    `DESCRICAO_DEST` TEXT,
    `ID_CLIENTES_STR` TEXT NOT NULL,
    `CORPO` TEXT NOT NULL,
    `SOMENTE_LEITURA` TINYINT(1) DEFAULT 1 NOT NULL,
    `ORDEM` INTEGER NOT NULL,
    `DATA_ENVIO` DATETIME NOT NULL,
    `CANCELADA` TINYINT(1) DEFAULT 0,
    PRIMARY KEY (`ID`),
    INDEX `FI_DOCUMENTO_ALERTA_USUARIO` (`USUARIO_ID`),
    CONSTRAINT `FK_DOCUMENTO_ALERTA_USUARIO`
        FOREIGN KEY (`USUARIO_ID`)
        REFERENCES `qp1_usuario` (`ID`)
) ENGINE=InnoDB;

-- ---------------------------------------------------------------------
-- qp1_documento_alerta_clientes
-- ---------------------------------------------------------------------

DROP TABLE IF EXISTS `qp1_documento_alerta_clientes`;

CREATE TABLE `qp1_documento_alerta_clientes`
(
    `ID` INTEGER NOT NULL AUTO_INCREMENT,
    `CLIENTE_ID` bigint(20) unsigned NOT NULL,
    `DOCUMENTO_ALERTA_ID` INTEGER NOT NULL,
    `DATA_LIDO` DATETIME,
    `DATA_CRIACAO` DATETIME NOT NULL,
    `updated_at` DATETIME,
    PRIMARY KEY (`ID`),
    INDEX `FI_documento_alerta_clientes_cliente` (`CLIENTE_ID`),
    INDEX `FI_documento_alerta_clientes_documento_alerta` (`DOCUMENTO_ALERTA_ID`),
    CONSTRAINT `FK_documento_alerta_clientes_cliente`
        FOREIGN KEY (`CLIENTE_ID`)
        REFERENCES `qp1_cliente` (`ID`),
    CONSTRAINT `FK_documento_alerta_clientes_documento_alerta`
        FOREIGN KEY (`DOCUMENTO_ALERTA_ID`)
        REFERENCES `qp1_documento_alerta` (`ID`)
        ON UPDATE CASCADE
        ON DELETE CASCADE
) ENGINE=InnoDB;

-- ---------------------------------------------------------------------
-- qp1_documento_alerta_pdf
-- ---------------------------------------------------------------------

DROP TABLE IF EXISTS `qp1_documento_alerta_pdf`;

CREATE TABLE `qp1_documento_alerta_pdf`
(
    `ID` INTEGER NOT NULL AUTO_INCREMENT,
    `DOCUMENTO_ALERTA_ID` INTEGER NOT NULL,
    `NOME_ARQUIVO` VARCHAR(250) NOT NULL,
    `NOME_ORIGINAL` VARCHAR(250) NOT NULL,
    PRIMARY KEY (`ID`),
    INDEX `FI_documento_alerta_pdf` (`DOCUMENTO_ALERTA_ID`),
    CONSTRAINT `FK_documento_alerta_pdf`
        FOREIGN KEY (`DOCUMENTO_ALERTA_ID`)
        REFERENCES `qp1_documento_alerta` (`ID`)
        ON UPDATE CASCADE
        ON DELETE CASCADE
) ENGINE=InnoDB;

-- ---------------------------------------------------------------------
-- qp1_email_log
-- ---------------------------------------------------------------------

DROP TABLE IF EXISTS `qp1_email_log`;

CREATE TABLE `qp1_email_log`
(
    `ID` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
    `TIPO` VARCHAR(150) NOT NULL,
    `REMETENTE` VARCHAR(250) NOT NULL,
    `DESTINATARIO` VARCHAR(250) NOT NULL,
    `ASSUNTO` VARCHAR(250) NOT NULL,
    `CONTEUDO` TEXT NOT NULL,
    `STATUS` SMALLINT DEFAULT 1 NOT NULL,
    `DATA_ENVIO` DATETIME NOT NULL,
    PRIMARY KEY (`ID`)
) ENGINE=InnoDB;

-- ---------------------------------------------------------------------
-- qp1_endereco
-- ---------------------------------------------------------------------

DROP TABLE IF EXISTS `qp1_endereco`;

CREATE TABLE `qp1_endereco`
(
    `ID` INTEGER NOT NULL AUTO_INCREMENT,
    `CLIENTE_ID` bigint(20) unsigned NOT NULL,
    `CEP` VARCHAR(10) NOT NULL,
    `LOGRADOURO` VARCHAR(255) NOT NULL,
    `NUMERO` VARCHAR(255) NOT NULL,
    `BAIRRO` VARCHAR(255) NOT NULL,
    `CIDADE_ID` INTEGER NOT NULL,
    `IDENTIFICACAO` VARCHAR(255),
    `NOME_DESTINATARIO` VARCHAR(255),
    `COMPLEMENTO` VARCHAR(255),
    `created_at` DATETIME,
    `updated_at` DATETIME,
    `ENDERECO_PRINCIPAL` TINYINT DEFAULT 0 NOT NULL,
    PRIMARY KEY (`ID`),
    INDEX `endereco_FI_1` (`CLIENTE_ID`),
    INDEX `endereco_FI_2` (`CIDADE_ID`),
    CONSTRAINT `endereco_FK_1`
        FOREIGN KEY (`CLIENTE_ID`)
        REFERENCES `qp1_cliente` (`ID`)
        ON UPDATE CASCADE
        ON DELETE CASCADE,
    CONSTRAINT `endereco_FK_2`
        FOREIGN KEY (`CIDADE_ID`)
        REFERENCES `qp1_cidade` (`ID`)
        ON UPDATE RESTRICT
        ON DELETE RESTRICT
) ENGINE=InnoDB;

-- ---------------------------------------------------------------------
-- qp1_estado
-- ---------------------------------------------------------------------

DROP TABLE IF EXISTS `qp1_estado`;

CREATE TABLE `qp1_estado`
(
    `ID` INTEGER NOT NULL AUTO_INCREMENT,
    `SIGLA` CHAR(2) NOT NULL,
    `NOME` VARCHAR(45) NOT NULL,
    `CAPITAL_ID` INTEGER,
    PRIMARY KEY (`ID`)
) ENGINE=InnoDB;

-- ---------------------------------------------------------------------
-- qp1_estado_centro_distribuicao
-- ---------------------------------------------------------------------

DROP TABLE IF EXISTS `qp1_estado_centro_distribuicao`;

CREATE TABLE `qp1_estado_centro_distribuicao`
(
    `ID` INTEGER(11) NOT NULL AUTO_INCREMENT,
    `ESTADO_ID` INTEGER(11) NOT NULL,
    `CENTRO_DISTRIBUICAO_ID` INTEGER(11),
    PRIMARY KEY (`ID`),
    INDEX `qp1_estado_centro_distribuicao_qp1_estado_ID_fk` (`ESTADO_ID`),
    INDEX `qp1_estado_centro_distribuicao_qp1_centro_distribuicao_ID_fk` (`CENTRO_DISTRIBUICAO_ID`),
    CONSTRAINT `qp1_estado_centro_distribuicao_qp1_estado_ID_fk`
        FOREIGN KEY (`ESTADO_ID`)
        REFERENCES `qp1_estado` (`ID`)
        ON UPDATE CASCADE
        ON DELETE CASCADE,
    CONSTRAINT `qp1_estado_centro_distribuicao_qp1_centro_distribuicao_ID_fk`
        FOREIGN KEY (`CENTRO_DISTRIBUICAO_ID`)
        REFERENCES `qp1_centro_distribuicao` (`ID`)
        ON UPDATE CASCADE
        ON DELETE CASCADE
) ENGINE=InnoDB;

-- ---------------------------------------------------------------------
-- qp1_estatistica_produto_variacao
-- ---------------------------------------------------------------------

DROP TABLE IF EXISTS `qp1_estatistica_produto_variacao`;

CREATE TABLE `qp1_estatistica_produto_variacao`
(
    `PRODUTO_VARIACAO_ID` bigint(20) unsigned NOT NULL,
    `QUANTIDADE_VENDIDA` BIGINT DEFAULT 0,
    PRIMARY KEY (`PRODUTO_VARIACAO_ID`),
    CONSTRAINT `estatistica_produto_variacao_FK_1`
        FOREIGN KEY (`PRODUTO_VARIACAO_ID`)
        REFERENCES `qp1_produto_variacao` (`ID`)
        ON UPDATE CASCADE
        ON DELETE CASCADE
) ENGINE=InnoDB;

-- ---------------------------------------------------------------------
-- qp1_estoque_produto
-- ---------------------------------------------------------------------

DROP TABLE IF EXISTS `qp1_estoque_produto`;

CREATE TABLE `qp1_estoque_produto`
(
    `ID` INTEGER NOT NULL AUTO_INCREMENT,
    `PRODUTO_ID` INTEGER NOT NULL,
    `PRODUTO_VARIACAO_ID` bigint(20) unsigned NOT NULL,
    `DATA` DATETIME NOT NULL,
    `OPERACAO` enum('ENTRADA','SAIDA') NOT NULL,
    `PEDIDO_ID` bigint(20) unsigned,
    `QUANTIDADE` INTEGER NOT NULL,
    `CONFIRMADO` TINYINT(1) DEFAULT 0,
    `OBSERVACAO` VARCHAR(255),
    `CENTRO_DISTRIBUICAO_ID` INTEGER(11),
    PRIMARY KEY (`ID`),
    INDEX `FI_produto` (`PRODUTO_ID`),
    INDEX `FI_pedido` (`PEDIDO_ID`),
    INDEX `FI_produto_variacao` (`PRODUTO_VARIACAO_ID`),
    INDEX `qp1_estoque_produto_qp1_estado_centro_distribuicao_ID_fk` (`CENTRO_DISTRIBUICAO_ID`),
    CONSTRAINT `FK_pedido`
        FOREIGN KEY (`PEDIDO_ID`)
        REFERENCES `qp1_pedido` (`ID`)
        ON UPDATE CASCADE
        ON DELETE CASCADE,
    CONSTRAINT `FK_produto`
        FOREIGN KEY (`PRODUTO_ID`)
        REFERENCES `qp1_produto` (`ID`)
        ON UPDATE CASCADE
        ON DELETE CASCADE,
    CONSTRAINT `FK_produto_variacao`
        FOREIGN KEY (`PRODUTO_VARIACAO_ID`)
        REFERENCES `qp1_produto_variacao` (`ID`)
        ON UPDATE CASCADE
        ON DELETE CASCADE,
    CONSTRAINT `qp1_estoque_produto_qp1_estado_centro_distribuicao_ID_fk`
        FOREIGN KEY (`CENTRO_DISTRIBUICAO_ID`)
        REFERENCES `qp1_estoque_produto` (`ID`)
        ON UPDATE CASCADE
        ON DELETE CASCADE
) ENGINE=InnoDB;

-- ---------------------------------------------------------------------
-- qp1_extrato
-- ---------------------------------------------------------------------

DROP TABLE IF EXISTS `qp1_extrato`;

CREATE TABLE `qp1_extrato`
(
    `ID` INTEGER NOT NULL AUTO_INCREMENT,
    `CLIENTE_ID` bigint(20) unsigned NOT NULL,
    `TIPO` VARCHAR(200) DEFAULT 'INDICACAO_DIRETA' NOT NULL,
    `PEDIDO_ID` bigint(20) unsigned,
    `RESGATE_ID` INTEGER,
    `DISTRIBUICAO_ID` INTEGER,
    `PARTICIPACAO_RESULTADO_ID` INTEGER,
    `PLANO_CARREIRA_ID` INTEGER,
    `PONTOS` DOUBLE NOT NULL,
    `DATA` DATETIME NOT NULL,
    `DATA_EXPIRACAO` DATETIME,
    `OPERACAO` enum('+','-') NOT NULL,
    `OBSERVACAO` TEXT NOT NULL,
    `BLOQUEADO` TINYINT(1) DEFAULT 0 NOT NULL,
    `IS_DESCONTO_IR` TINYINT DEFAULT 0,
    `TRANSFERENCIA_ID` INTEGER COMMENT 'Codigo da transferencia. Usado apenas quando o tipo for TRANSFERENCIA',
    PRIMARY KEY (`ID`),
    INDEX `extrato_FI_1` (`CLIENTE_ID`),
    INDEX `extrato_FI_2` (`PEDIDO_ID`),
    INDEX `extrato_FI_3` (`RESGATE_ID`),
    INDEX `extrato_FI_4` (`DISTRIBUICAO_ID`),
    INDEX `extrato_FI_5` (`PARTICIPACAO_RESULTADO_ID`),
    INDEX `extrato_FI_6` (`PLANO_CARREIRA_ID`),
    INDEX `qp1_extrato_FI_7` (`TRANSFERENCIA_ID`),
    CONSTRAINT `extrato_FK_1`
        FOREIGN KEY (`CLIENTE_ID`)
        REFERENCES `qp1_cliente` (`ID`)
        ON UPDATE CASCADE
        ON DELETE CASCADE,
    CONSTRAINT `extrato_FK_2`
        FOREIGN KEY (`PEDIDO_ID`)
        REFERENCES `qp1_pedido` (`ID`),
    CONSTRAINT `extrato_FK_3`
        FOREIGN KEY (`RESGATE_ID`)
        REFERENCES `qp1_resgate` (`ID`),
    CONSTRAINT `extrato_FK_4`
        FOREIGN KEY (`DISTRIBUICAO_ID`)
        REFERENCES `qp1_distribuicao` (`ID`),
    CONSTRAINT `extrato_FK_5`
        FOREIGN KEY (`PARTICIPACAO_RESULTADO_ID`)
        REFERENCES `qp1_participacao_resultado` (`ID`),
    CONSTRAINT `extrato_FK_6`
        FOREIGN KEY (`PLANO_CARREIRA_ID`)
        REFERENCES `qp1_plano_carreira` (`ID`),
    CONSTRAINT `qp1_extrato_FK_7`
        FOREIGN KEY (`TRANSFERENCIA_ID`)
        REFERENCES `qp1_transferencia` (`ID`)
) ENGINE=InnoDB;

-- ---------------------------------------------------------------------
-- qp1_extrato_cliente_preferencial
-- ---------------------------------------------------------------------

DROP TABLE IF EXISTS `qp1_extrato_cliente_preferencial`;

CREATE TABLE `qp1_extrato_cliente_preferencial`
(
    `ID` INTEGER NOT NULL AUTO_INCREMENT,
    `CLIENTE_ID` bigint(20) unsigned NOT NULL,
    `PEDIDO_ID` bigint(20) unsigned NOT NULL,
    `DATA` DATETIME NOT NULL,
    `OPERACAO` enum('+','-') NOT NULL,
    `PONTOS` DOUBLE NOT NULL,
    `OBSERVACAO` TEXT NOT NULL,
    PRIMARY KEY (`ID`),
    INDEX `qp1_extrato_cliente_preferencial_qp1_cliente_ID_fk` (`CLIENTE_ID`),
    INDEX `qp1_extrato_cliente_preferencial_qp1_pedido_ID_fk` (`PEDIDO_ID`),
    CONSTRAINT `qp1_extrato_cliente_preferencial_qp1_cliente_ID_fk`
        FOREIGN KEY (`CLIENTE_ID`)
        REFERENCES `qp1_cliente` (`ID`)
        ON UPDATE CASCADE
        ON DELETE CASCADE,
    CONSTRAINT `qp1_extrato_cliente_preferencial_qp1_pedido_ID_fk`
        FOREIGN KEY (`PEDIDO_ID`)
        REFERENCES `qp1_pedido` (`ID`)
        ON UPDATE CASCADE
        ON DELETE CASCADE
) ENGINE=InnoDB;

-- ---------------------------------------------------------------------
-- qp1_extrato_individual
-- ---------------------------------------------------------------------

DROP TABLE IF EXISTS `qp1_extrato_individual`;

CREATE TABLE `qp1_extrato_individual`
(
    `ID` INTEGER NOT NULL AUTO_INCREMENT,
    `CLIENTE_ID` bigint(20) unsigned NOT NULL,
    `TIPO` VARCHAR(200) DEFAULT 'INDICACAO_DIRETA' NOT NULL,
    `PEDIDO_ID` bigint(20) unsigned,
    `RESGATE_ID` INTEGER,
    `DISTRIBUICAO_ID` INTEGER,
    `PARTICIPACAO_RESULTADO_ID` INTEGER,
    `PLANO_CARREIRA_ID` INTEGER,
    `PONTOS` DOUBLE NOT NULL,
    `DATA` DATETIME NOT NULL,
    `DATA_EXPIRACAO` DATETIME,
    `OPERACAO` enum('+','-') NOT NULL,
    `OBSERVACAO` TEXT NOT NULL,
    `BLOQUEADO` TINYINT(1) DEFAULT 0 NOT NULL,
    PRIMARY KEY (`ID`),
    INDEX `extrato_individual_FI_1` (`CLIENTE_ID`),
    INDEX `extrato_individual_FI_2` (`PEDIDO_ID`),
    INDEX `extrato_individual_FI_3` (`RESGATE_ID`),
    INDEX `extrato_individual_FI_4` (`DISTRIBUICAO_ID`),
    INDEX `extrato_individual_FI_5` (`PARTICIPACAO_RESULTADO_ID`),
    INDEX `extrato_individual_FI_6` (`PLANO_CARREIRA_ID`),
    CONSTRAINT `extrato_individual_FK_1`
        FOREIGN KEY (`CLIENTE_ID`)
        REFERENCES `qp1_cliente` (`ID`)
        ON UPDATE CASCADE
        ON DELETE CASCADE,
    CONSTRAINT `extrato_individual_FK_2`
        FOREIGN KEY (`PEDIDO_ID`)
        REFERENCES `qp1_pedido` (`ID`),
    CONSTRAINT `extrato_individual_FK_3`
        FOREIGN KEY (`RESGATE_ID`)
        REFERENCES `qp1_resgate` (`ID`),
    CONSTRAINT `extrato_individual_FK_4`
        FOREIGN KEY (`DISTRIBUICAO_ID`)
        REFERENCES `qp1_distribuicao` (`ID`),
    CONSTRAINT `extrato_individual_FK_5`
        FOREIGN KEY (`PARTICIPACAO_RESULTADO_ID`)
        REFERENCES `qp1_participacao_resultado` (`ID`),
    CONSTRAINT `extrato_individual_FK_6`
        FOREIGN KEY (`PLANO_CARREIRA_ID`)
        REFERENCES `qp1_plano_carreira` (`ID`)
) ENGINE=InnoDB;

-- ---------------------------------------------------------------------
-- qp1_extrato_bonus_produtos
-- ---------------------------------------------------------------------

DROP TABLE IF EXISTS `qp1_extrato_bonus_produtos`;

CREATE TABLE `qp1_extrato_bonus_produtos`
(
    `ID` INTEGER NOT NULL AUTO_INCREMENT,
    `CLIENTE_ID` bigint(20) unsigned NOT NULL,
    `PRODUTOS_BONUS_ID` INTEGER,
    `DISTRIBUICAO_ID` INTEGER,
    `PLANO_CARREIRA_ID` INTEGER,
    `DATA` DATETIME NOT NULL,
    `DATA_RETIRADA` DATETIME,
    `DATA_EXPIRACAO` DATETIME,
    `OPERACAO` enum('+','-') NOT NULL,
    `OBSERVACAO` TEXT NOT NULL,
    `BLOQUEADO` TINYINT(1) DEFAULT 0 NOT NULL,
    PRIMARY KEY (`ID`),
    INDEX `extrato_individual_FI_1` (`CLIENTE_ID`),
    INDEX `extrato_individual_FI_2` (`DISTRIBUICAO_ID`),
    INDEX `extrato_individual_FI_3` (`PLANO_CARREIRA_ID`),
    CONSTRAINT `extrato_bonus_produtos_FK_1`
        FOREIGN KEY (`CLIENTE_ID`)
        REFERENCES `qp1_cliente` (`ID`)
        ON UPDATE CASCADE
        ON DELETE CASCADE,
    CONSTRAINT `extrato_bonus_produtos_FK_2`
        FOREIGN KEY (`DISTRIBUICAO_ID`)
        REFERENCES `qp1_distribuicao_bonus_produtos` (`ID`),
    CONSTRAINT `extrato_bonus_produtos_FK_3`
        FOREIGN KEY (`PLANO_CARREIRA_ID`)
        REFERENCES `qp1_plano_carreira` (`ID`)
) ENGINE=InnoDB;

-- ---------------------------------------------------------------------
-- qp1_faixa_bonus_desempenho
-- ---------------------------------------------------------------------

DROP TABLE IF EXISTS `qp1_faixa_bonus_desempenho`;

CREATE TABLE `qp1_faixa_bonus_desempenho`
(
    `ID` INTEGER NOT NULL AUTO_INCREMENT,
    `DATA_CRIACAO` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `DATA_ATUALIZACAO` DATETIME,
    `NOME` VARCHAR(255) NOT NULL,
    `FAIXA_INICIAL` DOUBLE DEFAULT 0 NOT NULL,
    `FAIXA_FINAL` DOUBLE DEFAULT 0 NOT NULL,
    `GERA_PONTOS` TINYINT(1) DEFAULT 0 NOT NULL,
    `PONTOS` DOUBLE DEFAULT 0 NOT NULL,
    PRIMARY KEY (`ID`)
) ENGINE=InnoDB;

-- ---------------------------------------------------------------------
-- qp1_faixas_distribuicao_binaria
-- ---------------------------------------------------------------------

DROP TABLE IF EXISTS `qp1_faixas_distribuicao_binaria`;

CREATE TABLE `qp1_faixas_distribuicao_binaria`
(
    `ID` INTEGER(10) NOT NULL AUTO_INCREMENT,
    `PLANO_ID` INTEGER DEFAULT 0 NOT NULL,
    `PONTUACAO_INICIAL` INTEGER(8) NOT NULL,
    `PONTUACAO_FINAL` INTEGER(8) NOT NULL,
    `PONTOS_TETO` INTEGER NOT NULL,
    `OBSERVACAO` TEXT,
    PRIMARY KEY (`ID`),
    INDEX `FK_faixas_distribuicao_binaria_plano` (`PLANO_ID`),
    CONSTRAINT `FK_faixas_distribuicao_binaria_plano`
        FOREIGN KEY (`PLANO_ID`)
        REFERENCES `qp1_plano` (`ID`)
        ON UPDATE CASCADE
        ON DELETE RESTRICT
) ENGINE=InnoDB;

-- ---------------------------------------------------------------------
-- qp1_faq
-- ---------------------------------------------------------------------

DROP TABLE IF EXISTS `qp1_faq`;

CREATE TABLE `qp1_faq`
(
    `ID` INTEGER NOT NULL AUTO_INCREMENT,
    `PERGUNTA` VARCHAR(255) NOT NULL,
    `RESPOSTA` TEXT,
    `NOME` VARCHAR(200),
    `EMAIL` VARCHAR(200),
    `DATA_PERGUNTA` DATETIME,
    `DATA_RESPOSTA` DATETIME,
    `ORDEM` INTEGER(5),
    `MOSTRAR` TINYINT(1) DEFAULT 0 NOT NULL,
    `ENVIADO` TINYINT(1) DEFAULT 0 NOT NULL,
    PRIMARY KEY (`ID`)
) ENGINE=InnoDB;

-- ---------------------------------------------------------------------
-- qp1_faturamento_direto
-- ---------------------------------------------------------------------

DROP TABLE IF EXISTS `qp1_faturamento_direto`;

CREATE TABLE `qp1_faturamento_direto`
(
    `ID` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
    `NOME` VARCHAR(200) NOT NULL,
    `NUMERO_PARCELAS` SMALLINT NOT NULL,
    `OBSERVACAO_INTERNA` TEXT,
    `VALOR_MINIMO_COMPRA` FLOAT NOT NULL,
    `PADRAO` TINYINT(1) DEFAULT 1 NOT NULL,
    PRIMARY KEY (`ID`)
) ENGINE=InnoDB;

-- ---------------------------------------------------------------------
-- qp1_faturamento_direto_cliente
-- ---------------------------------------------------------------------

DROP TABLE IF EXISTS `qp1_faturamento_direto_cliente`;

CREATE TABLE `qp1_faturamento_direto_cliente`
(
    `CLIENTE_ID` bigint(20) unsigned NOT NULL,
    `FATURAMENTO_DIRETO_ID` bigint(20) unsigned NOT NULL,
    PRIMARY KEY (`CLIENTE_ID`,`FATURAMENTO_DIRETO_ID`),
    INDEX `faturamento_direto_cliente_FI_2` (`FATURAMENTO_DIRETO_ID`),
    CONSTRAINT `faturamento_direto_cliente_FK_1`
        FOREIGN KEY (`CLIENTE_ID`)
        REFERENCES `qp1_cliente` (`ID`)
        ON DELETE CASCADE,
    CONSTRAINT `faturamento_direto_cliente_FK_2`
        FOREIGN KEY (`FATURAMENTO_DIRETO_ID`)
        REFERENCES `qp1_faturamento_direto` (`ID`)
        ON DELETE CASCADE
) ENGINE=InnoDB;

-- ---------------------------------------------------------------------
-- qp1_fluxo_caixa
-- ---------------------------------------------------------------------

DROP TABLE IF EXISTS `qp1_fluxo_caixa`;

CREATE TABLE `qp1_fluxo_caixa`
(
    `ID` INTEGER NOT NULL AUTO_INCREMENT,
    `DATA` DATETIME NOT NULL,
    `PEDIDO_ID` bigint(20) unsigned NOT NULL,
    `PARCELA` INTEGER NOT NULL,
    `MAX_PARCELA` INTEGER NOT NULL,
    `DIA_SEMANA` INTEGER NOT NULL,
    `DATA_VENCIMENTO` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `VALOR_PARCELA` DOUBLE NOT NULL,
    `VALOR_CONTAS_PAGAR` DOUBLE NOT NULL,
    `TAXA_CONTAS_PAGAR` DOUBLE NOT NULL,
    `updated_at` DATETIME,
    `FORMA_PAGAMENTO` VARCHAR(50) NOT NULL,
    `CODIGO_OCORRENCIA` VARCHAR(50) NOT NULL,
    PRIMARY KEY (`ID`),
    INDEX `FI_pedidoFlux` (`PEDIDO_ID`),
    CONSTRAINT `FK_pedidoFlux`
        FOREIGN KEY (`PEDIDO_ID`)
        REFERENCES `qp1_pedido` (`ID`)
        ON UPDATE CASCADE
        ON DELETE CASCADE
) ENGINE=InnoDB;

-- ---------------------------------------------------------------------
-- qp1_foto
-- ---------------------------------------------------------------------

DROP TABLE IF EXISTS `qp1_foto`;

CREATE TABLE `qp1_foto`
(
    `ID` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
    `LEGENDA` VARCHAR(200),
    `IMAGEM` VARCHAR(50) NOT NULL,
    `COR` VARCHAR(100) NOT NULL,
    `ORDEM` INTEGER DEFAULT 0,
    `PRODUTO_ID` INTEGER NOT NULL,
    PRIMARY KEY (`ID`),
    INDEX `TFOTO_TPROD_FK_IDX_01` (`PRODUTO_ID`),
    CONSTRAINT `TFOTO_TPROD_FK_01`
        FOREIGN KEY (`PRODUTO_ID`)
        REFERENCES `qp1_produto` (`ID`)
) ENGINE=InnoDB;

-- ---------------------------------------------------------------------
-- qp1_galeria
-- ---------------------------------------------------------------------

DROP TABLE IF EXISTS `qp1_galeria`;

CREATE TABLE `qp1_galeria`
(
    `ID` INTEGER NOT NULL AUTO_INCREMENT,
    `NOME` VARCHAR(200) NOT NULL,
    `DESCRICAO` TEXT,
    PRIMARY KEY (`ID`)
) ENGINE=InnoDB;

-- ---------------------------------------------------------------------
-- qp1_galeria_arquivo
-- ---------------------------------------------------------------------

DROP TABLE IF EXISTS `qp1_galeria_arquivo`;

CREATE TABLE `qp1_galeria_arquivo`
(
    `ID` INTEGER NOT NULL AUTO_INCREMENT,
    `GALERIA_ID` INTEGER,
    `ARQUIVO_ID` INTEGER,
    `ORDEM` INTEGER DEFAULT 1,
    PRIMARY KEY (`ID`),
    INDEX `galeria_arquivo_FI_1` (`ARQUIVO_ID`),
    INDEX `galeria_arquivo_FI_2` (`GALERIA_ID`),
    CONSTRAINT `galeria_arquivo_FK_1`
        FOREIGN KEY (`ARQUIVO_ID`)
        REFERENCES `qp1_arquivo` (`ID`)
        ON DELETE CASCADE,
    CONSTRAINT `galeria_arquivo_FK_2`
        FOREIGN KEY (`GALERIA_ID`)
        REFERENCES `qp1_galeria` (`ID`)
        ON DELETE CASCADE
) ENGINE=InnoDB;

-- ---------------------------------------------------------------------
-- qp1_google_shopping_categoria
-- ---------------------------------------------------------------------

DROP TABLE IF EXISTS `qp1_google_shopping_categoria`;

CREATE TABLE `qp1_google_shopping_categoria`
(
    `ID` INTEGER NOT NULL AUTO_INCREMENT,
    `NOME` TEXT NOT NULL,
    PRIMARY KEY (`ID`)
) ENGINE=InnoDB;

-- ---------------------------------------------------------------------
-- qp1_google_shopping_item
-- ---------------------------------------------------------------------

DROP TABLE IF EXISTS `qp1_google_shopping_item`;

CREATE TABLE `qp1_google_shopping_item`
(
    `PRODUTO_ID` INTEGER NOT NULL,
    `ATIVO` TINYINT(1) DEFAULT 1 NOT NULL,
    `ADULTO` enum('SIM','NAO','NAOINFORMADO') DEFAULT 'NAO' NOT NULL,
    `FAIXA_ETARIA` enum('RECEMNASCIDO','3TO12M','1TO5Y','CHILDREN','ADULT','NAOINFORMADO') DEFAULT 'NAOINFORMADO' NOT NULL,
    `GENERO` enum('MASCULINO','FEMININO','UNISEX','NAOINFORMADO') DEFAULT 'NAOINFORMADO' NOT NULL,
    `CONDICAO` enum('NOVO','USADO','RECONDICIONADO') DEFAULT 'NOVO' NOT NULL,
    `GCATEGORY` INTEGER NOT NULL,
    `USAR_IMAGENS` TINYINT(1) DEFAULT 1 NOT NULL,
    PRIMARY KEY (`PRODUTO_ID`),
    INDEX `google_shopping_item_FI_2` (`GCATEGORY`),
    CONSTRAINT `google_shopping_item_FK_1`
        FOREIGN KEY (`PRODUTO_ID`)
        REFERENCES `qp1_produto` (`ID`)
        ON DELETE CASCADE,
    CONSTRAINT `google_shopping_item_FK_2`
        FOREIGN KEY (`GCATEGORY`)
        REFERENCES `qp1_google_shopping_categoria` (`ID`)
        ON DELETE CASCADE
) ENGINE=InnoDB;

-- ---------------------------------------------------------------------
-- qp1_grafico
-- ---------------------------------------------------------------------

DROP TABLE IF EXISTS `qp1_grafico`;

CREATE TABLE `qp1_grafico`
(
    `ID` INTEGER NOT NULL AUTO_INCREMENT,
    `TIPO` enum('VOLUME','VENDAS','ANALYTICS') DEFAULT 'ANALYTICS' NOT NULL,
    `GRAFICO_STRING` TEXT,
    `DATA_CRIACAO` DATETIME NOT NULL,
    `DATA_ATUALIZACAO` DATETIME,
    PRIMARY KEY (`ID`)
) ENGINE=InnoDB;

-- ---------------------------------------------------------------------
-- qp1_hotsite
-- ---------------------------------------------------------------------

DROP TABLE IF EXISTS `qp1_hotsite`;

CREATE TABLE `qp1_hotsite`
(
    `CLIENTE_ID` bigint(20) unsigned NOT NULL,
    `URL` VARCHAR(200) NOT NULL,
    `SLUG` VARCHAR(200) NOT NULL,
    `DESCRICAO` TEXT NOT NULL,
    `FOTO` VARCHAR(50) NOT NULL,
    `NOME` VARCHAR(200) NOT NULL,
    `EMAIL` VARCHAR(200) NOT NULL,
    PRIMARY KEY (`CLIENTE_ID`),
    CONSTRAINT `hotsite_FK1`
        FOREIGN KEY (`CLIENTE_ID`)
        REFERENCES `qp1_cliente` (`ID`)
        ON UPDATE CASCADE
        ON DELETE CASCADE
) ENGINE=InnoDB;

-- ---------------------------------------------------------------------
-- qp1_integracao
-- ---------------------------------------------------------------------

DROP TABLE IF EXISTS `qp1_integracao`;

CREATE TABLE `qp1_integracao`
(
    `ID` INTEGER NOT NULL AUTO_INCREMENT,
    `PRODUTO_ID` INTEGER(10) NOT NULL,
    `TIPO` enum('BUSCAPE') NOT NULL,
    PRIMARY KEY (`ID`),
    INDEX `TINTE_TPROD_FK_IDX_01` (`PRODUTO_ID`),
    CONSTRAINT `TINTE_TPROD_FK_01`
        FOREIGN KEY (`PRODUTO_ID`)
        REFERENCES `qp1_produto` (`ID`)
        ON UPDATE CASCADE
        ON DELETE CASCADE
) ENGINE=InnoDB;

-- ---------------------------------------------------------------------
-- qp1_integracao_cliente
-- ---------------------------------------------------------------------

DROP TABLE IF EXISTS `qp1_integracao_cliente`;

CREATE TABLE `qp1_integracao_cliente`
(
    `ID` INTEGER NOT NULL AUTO_INCREMENT,
    `CLIENTE_ID` bigint(20) unsigned NOT NULL,
    `CODIGO_INTEGRACAO` VARCHAR(50),
    `TIPO_INTEGRACAO` VARCHAR(50),
    `CONCLUIDO_COM_SUCESSO` TINYINT(1) DEFAULT 0 NOT NULL,
    `RESULT` TEXT,
    `DATA_CRIACAO` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `DATA_ATUALIZACAO` DATETIME,
    `DATA_EXCLUSAO` DATETIME,
    PRIMARY KEY (`ID`),
    INDEX `TINPRO_TCLIE_FI_01` (`CLIENTE_ID`),
    CONSTRAINT `TINPRO_TCLIE_FK_01`
        FOREIGN KEY (`CLIENTE_ID`)
        REFERENCES `qp1_cliente` (`ID`)
        ON UPDATE CASCADE
        ON DELETE CASCADE
) ENGINE=InnoDB;

-- ---------------------------------------------------------------------
-- qp1_integracao_contas_pagar
-- ---------------------------------------------------------------------

DROP TABLE IF EXISTS `qp1_integracao_contas_pagar`;

CREATE TABLE `qp1_integracao_contas_pagar`
(
    `ID` INTEGER NOT NULL AUTO_INCREMENT,
    `FLUXO_ID` INTEGER NOT NULL,
    `CODIGO_INTEGRACAO` VARCHAR(50),
    `TIPO_INTEGRACAO` VARCHAR(50),
    `CONCLUIDO_COM_SUCESSO` TINYINT(1) DEFAULT 0 NOT NULL,
    `RESULT` TEXT,
    `DATA_CRIACAO` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `DATA_ATUALIZACAO` DATETIME,
    `DATA_EXCLUSAO` DATETIME,
    PRIMARY KEY (`ID`),
    INDEX `TINCONR12_TFLUX_FI_01` (`FLUXO_ID`),
    CONSTRAINT `TINCONR12_TFLUX_FK_01`
        FOREIGN KEY (`FLUXO_ID`)
        REFERENCES `qp1_fluxo_caixa` (`ID`)
        ON UPDATE CASCADE
        ON DELETE CASCADE
) ENGINE=InnoDB;

-- ---------------------------------------------------------------------
-- qp1_integracao_contas_receber
-- ---------------------------------------------------------------------

DROP TABLE IF EXISTS `qp1_integracao_contas_receber`;

CREATE TABLE `qp1_integracao_contas_receber`
(
    `ID` INTEGER NOT NULL AUTO_INCREMENT,
    `FLUXO_ID` INTEGER NOT NULL,
    `CODIGO_INTEGRACAO` VARCHAR(50),
    `TIPO_INTEGRACAO` VARCHAR(50),
    `CONCLUIDO_COM_SUCESSO` TINYINT(1) DEFAULT 0 NOT NULL,
    `RESULT` TEXT,
    `DATA_CRIACAO` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `DATA_ATUALIZACAO` DATETIME,
    `DATA_EXCLUSAO` DATETIME,
    PRIMARY KEY (`ID`),
    INDEX `TINCONR_TFLUX_FI_01` (`FLUXO_ID`),
    CONSTRAINT `TINCONR_TFLUX_FK_01`
        FOREIGN KEY (`FLUXO_ID`)
        REFERENCES `qp1_fluxo_caixa` (`ID`)
        ON UPDATE CASCADE
        ON DELETE CASCADE
) ENGINE=InnoDB;

-- ---------------------------------------------------------------------
-- qp1_integracao_pedido
-- ---------------------------------------------------------------------

DROP TABLE IF EXISTS `qp1_integracao_pedido`;

CREATE TABLE `qp1_integracao_pedido`
(
    `ID` INTEGER NOT NULL AUTO_INCREMENT,
    `PEDIDO_ID` bigint(20) unsigned NOT NULL,
    `CODIGO_INTEGRACAO` VARCHAR(50),
    `TIPO_INTEGRACAO` VARCHAR(50),
    `CONCLUIDO_COM_SUCESSO` TINYINT(1) DEFAULT 0 NOT NULL,
    `RESULT` TEXT,
    `DATA_CRIACAO` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `DATA_ATUALIZACAO` DATETIME,
    `DATA_EXCLUSAO` DATETIME,
    PRIMARY KEY (`ID`),
    INDEX `TINPRO_TPEDI_FI_01` (`PEDIDO_ID`),
    CONSTRAINT `TINPRO_TPEDI_FK_01`
        FOREIGN KEY (`PEDIDO_ID`)
        REFERENCES `qp1_pedido` (`ID`)
        ON UPDATE CASCADE
        ON DELETE CASCADE
) ENGINE=InnoDB;

-- ---------------------------------------------------------------------
-- qp1_integracao_produto
-- ---------------------------------------------------------------------

DROP TABLE IF EXISTS `qp1_integracao_produto`;

CREATE TABLE `qp1_integracao_produto`
(
    `ID` INTEGER NOT NULL AUTO_INCREMENT,
    `PRODUTO_ID` INTEGER NOT NULL,
    `CODIGO_INTEGRACAO` VARCHAR(50),
    `TIPO_INTEGRACAO` VARCHAR(50),
    `CONCLUIDO_COM_SUCESSO` TINYINT(1) DEFAULT 0 NOT NULL,
    `RESULT` TEXT,
    `DATA_CRIACAO` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `DATA_ATUALIZACAO` DATETIME,
    `DATA_EXCLUSAO` DATETIME,
    PRIMARY KEY (`ID`),
    INDEX `TINPRO_TPROD_FI_01` (`PRODUTO_ID`),
    CONSTRAINT `TINPRO_TPROD_FK_01`
        FOREIGN KEY (`PRODUTO_ID`)
        REFERENCES `qp1_produto` (`ID`)
        ON UPDATE CASCADE
        ON DELETE CASCADE
) ENGINE=InnoDB;

-- ---------------------------------------------------------------------
-- qp1_log_admin
-- ---------------------------------------------------------------------

DROP TABLE IF EXISTS `qp1_log_admin`;

CREATE TABLE `qp1_log_admin`
(
    `ID` INTEGER NOT NULL AUTO_INCREMENT,
    `USUARIO_ID` INTEGER NOT NULL,
    `DATA` DATETIME NOT NULL,
    `URL` VARCHAR(250) NOT NULL,
    `MODULO` VARCHAR(250) NOT NULL,
    `SQL` TEXT NOT NULL,
    `updated_at` DATETIME,
    PRIMARY KEY (`ID`),
    INDEX `IDX_LOG_USER` (`USUARIO_ID`),
    CONSTRAINT `FK_LOG_USUARIO`
        FOREIGN KEY (`USUARIO_ID`)
        REFERENCES `qp1_usuario` (`ID`)
) ENGINE=InnoDB;

-- ---------------------------------------------------------------------
-- qp1_marca
-- ---------------------------------------------------------------------

DROP TABLE IF EXISTS `qp1_marca`;

CREATE TABLE `qp1_marca`
(
    `ID` INTEGER NOT NULL AUTO_INCREMENT,
    `NOME` VARCHAR(50) NOT NULL,
    `IMAGEM` VARCHAR(50),
    `MEDIDA` VARCHAR(50),
    `KEY` VARCHAR(255),
    PRIMARY KEY (`ID`),
    UNIQUE INDEX `marca_slug` (`KEY`)
) ENGINE=InnoDB;

-- ---------------------------------------------------------------------
-- qp1_newsletter
-- ---------------------------------------------------------------------

DROP TABLE IF EXISTS `qp1_newsletter`;

CREATE TABLE `qp1_newsletter`
(
    `ID` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
    `NOME` VARCHAR(200) NOT NULL,
    `EMAIL` VARCHAR(200) NOT NULL,
    `DATA_CADASTRO` DATETIME NOT NULL,
    `DATA_ATUALIZACAO` DATETIME NOT NULL,
    PRIMARY KEY (`ID`),
    UNIQUE INDEX `TNEWS_UK_01` (`EMAIL`)
) ENGINE=MyISAM;

-- ---------------------------------------------------------------------
-- qp1_noticia
-- ---------------------------------------------------------------------

DROP TABLE IF EXISTS `qp1_noticia`;

CREATE TABLE `qp1_noticia`
(
    `ID` INTEGER(10) NOT NULL AUTO_INCREMENT,
    `TITULO` VARCHAR(100) NOT NULL,
    `CONTEUDO` TEXT NOT NULL,
    `IS_ATIVO` TINYINT DEFAULT 1 NOT NULL,
    `DATA_PUBLICACAO` DATE NOT NULL,
    `IMAGEM` VARCHAR(100) NOT NULL,
    PRIMARY KEY (`ID`)
) ENGINE=InnoDB;

-- ---------------------------------------------------------------------
-- qp1_parametro
-- ---------------------------------------------------------------------

DROP TABLE IF EXISTS `qp1_parametro`;

CREATE TABLE `qp1_parametro`
(
    `ID` int(10) unsigned NOT NULL AUTO_INCREMENT,
    `NOME_AMIGAVEL` VARCHAR(150) NOT NULL,
    `ALIAS` VARCHAR(60) NOT NULL,
    `VALOR` TEXT NOT NULL,
    `IS_AUTOLOAD` TINYINT(1) DEFAULT 0 NOT NULL,
    `DICA` TEXT,
    `ORDEM` int(4) unsigned,
    `PARAMETRO_GRUPO_ID` int(10) unsigned NOT NULL,
    `IS_CONFIGURACAO_SISTEMA` SMALLINT NOT NULL,
    `TYPE` enum('TEXT','CHECKBOX','RADIOBOX','SELECT','TEXTAREA','EDITOR','BOOLEAN','MONEY','IMAGE') NOT NULL,
    `TYPE_OPTIONS` TEXT,
    PRIMARY KEY (`ID`),
    UNIQUE INDEX `ALIAS_UNIQUE` (`ALIAS`),
    INDEX `fk_parametro_parametro_grupo1` (`PARAMETRO_GRUPO_ID`),
    CONSTRAINT `fk_parametro_parametro_grupo1`
        FOREIGN KEY (`PARAMETRO_GRUPO_ID`)
        REFERENCES `qp1_parametro_grupo` (`ID`)
) ENGINE=InnoDB;

-- ---------------------------------------------------------------------
-- qp1_parametro_grupo
-- ---------------------------------------------------------------------

DROP TABLE IF EXISTS `qp1_parametro_grupo`;

CREATE TABLE `qp1_parametro_grupo`
(
    `ID` int(10) unsigned NOT NULL AUTO_INCREMENT,
    `NOME` VARCHAR(150) NOT NULL,
    `ALIAS` VARCHAR(60) NOT NULL,
    `DICA` VARCHAR(255),
    `ORDEM` int(4) unsigned,
    `IS_MASTER` SMALLINT DEFAULT 1 NOT NULL,
    PRIMARY KEY (`ID`),
    UNIQUE INDEX `ALIAS_UNIQUE` (`ALIAS`)
) ENGINE=InnoDB;

-- ---------------------------------------------------------------------
-- qp1_participacao_resultado
-- ---------------------------------------------------------------------

DROP TABLE IF EXISTS `qp1_participacao_resultado`;

CREATE TABLE `qp1_participacao_resultado`
(
    `ID` INTEGER NOT NULL AUTO_INCREMENT,
    `TIPO` ENUM('DESTAQUE','DESEMPENHO', 'ACELERACAO') NOT NULL,
    `DATA` DATETIME NOT NULL,
    `TOTAL_PONTOS` FLOAT NOT NULL,
    `TOTAL_PONTOS_PROCESSADOS` FLOAT NOT NULL,
    `TOTAL_PONTOS_RESTANTES` FLOAT NOT NULL,
    `STATUS` enum('AGUARDANDO_PREVIEW','PROCESSANDO_PREVIEW','PREVIEW','AGUARDANDO','PROCESSANDO','DISTRIBUIDO','CANCELADO') DEFAULT 'AGUARDANDO_PREVIEW' NOT NULL,
    PRIMARY KEY (`ID`)
) ENGINE=InnoDB COMMENT='Tabela onde serão gravados as participações nos resultados';

-- ---------------------------------------------------------------------
-- qp1_participacao_resultado_cliente
-- ---------------------------------------------------------------------

DROP TABLE IF EXISTS `qp1_participacao_resultado_cliente`;

CREATE TABLE `qp1_participacao_resultado_cliente`
(
    `ID` INTEGER NOT NULL AUTO_INCREMENT,
    `PARTICIPACAO_RESULTADO_ID` INTEGER NOT NULL,
    `CLIENTE_ID` bigint(20) unsigned NOT NULL,
    `TOTAL_PONTOS` FLOAT NOT NULL,
    `DATA` DATETIME NOT NULL,
    `PERCENTUAL` FLOAT(5) NOT NULL,
    `GRADUACAO` VARCHAR(255),
    `OBSERVACAO` VARCHAR(255),
    PRIMARY KEY (`ID`),
    UNIQUE INDEX `participacao_resultado_cliente_U_1` (`PARTICIPACAO_RESULTADO_ID`, `CLIENTE_ID`),
    INDEX `participacao_resultado_cliente_FI_2` (`CLIENTE_ID`),
    CONSTRAINT `participacao_resultado_cliente_FK_1`
        FOREIGN KEY (`PARTICIPACAO_RESULTADO_ID`)
        REFERENCES `qp1_participacao_resultado` (`ID`)
        ON DELETE CASCADE,
    CONSTRAINT `participacao_resultado_cliente_FK_2`
        FOREIGN KEY (`CLIENTE_ID`)
        REFERENCES `qp1_cliente` (`ID`)
        ON DELETE CASCADE
) ENGINE=InnoDB;

-- ---------------------------------------------------------------------
-- qp1_pedido
-- ---------------------------------------------------------------------

DROP TABLE IF EXISTS `qp1_pedido`;

CREATE TABLE `qp1_pedido`
(
    `ID` BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
    `CREATED_AT` DATETIME,
    `UPDATED_AT` DATETIME,
    `CLIENTE_ID` BIGINT(20) UNSIGNED,
    `VALOR_ITENS` FLOAT(12,2),
    `VALOR_PONTOS` DOUBLE,
    `VALOR_ENTREGA` FLOAT(12,2),
    `VALOR_CUPOM_DESCONTO` FLOAT(12,2) DEFAULT 0 NOT NULL,
    `NUMERO_NOTA_FISCAL` VARCHAR(255),
    `CODIGO_RASTREIO` VARCHAR(45),
    `LINK_RASTREIO` TEXT,
    `FRETE` VARCHAR(100),
    `TRANSPORTADORA_NOME` VARCHAR(100),
    `FRETE_PRAZO` VARCHAR(100),
    `STATUS` ENUM('ANDAMENTO','FINALIZADO','CANCELADO') DEFAULT 'ANDAMENTO' NOT NULL,
    `ENDERECO` TEXT NOT NULL,
    `CUPOM` TEXT,
    `DESCONTO_PONTOS` TEXT,
    `DATA_AVISO_ABANDONO` DATETIME,
    `CIDADE_ID` INTEGER,
    `INTEGROU_CLEAR_SALE` TINYINT(1) DEFAULT 0 COMMENT 'Indica se os dados do pedido ja foram enviados a Clear Sale',
    `SITUACAO_CLEAR_SALE` VARCHAR(20) COMMENT 'Status do pedido na Clear Sale [APROVADO, REPROVADO]',
    `HOTSITE_CLIENTE_ID` BIGINT(20) UNSIGNED,
    `PONTOS_REDE_BINARIA` FLOAT DEFAULT 0 NOT NULL,
    `TAXA_CADASTRO` TINYINT(1) DEFAULT 0 NOT NULL,
    `CLASS_KEY` INTEGER DEFAULT 2,
    `COMPRA_CLIENTE_PREFERENCIAL` TINYINT(1) DEFAULT 0 NOT NULL,
    `CENTRO_DISTRIBUICAO_ID` INTEGER(11),
    `UPDATER_USER_ID` INTEGER(11),
    PRIMARY KEY (`ID`),
    INDEX `pedido_FI_1` (`CLIENTE_ID`),
    INDEX `pedido_FI_2` (`CIDADE_ID`),
    INDEX `qp1_pedido_qp1_centro_distribuicao_ID_fk` (`CENTRO_DISTRIBUICAO_ID`),
    CONSTRAINT `pedido_FK_1`
        FOREIGN KEY (`CLIENTE_ID`)
        REFERENCES `qp1_cliente` (`ID`)
        ON DELETE RESTRICT,
    CONSTRAINT `pedido_FK_2`
        FOREIGN KEY (`CIDADE_ID`)
        REFERENCES `qp1_cidade` (`ID`)
        ON DELETE SET NULL,
    CONSTRAINT `qp1_pedido_qp1_centro_distribuicao_ID_fk`
        FOREIGN KEY (`CENTRO_DISTRIBUICAO_ID`)
        REFERENCES `qp1_centro_distribuicao` (`ID`)
        ON DELETE RESTRICT
) ENGINE=InnoDB;

-- ---------------------------------------------------------------------
-- qp1_pedido_forma_pagamento
-- ---------------------------------------------------------------------

DROP TABLE IF EXISTS `qp1_pedido_forma_pagamento`;

CREATE TABLE `qp1_pedido_forma_pagamento`
(
    `ID` BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
    `PEDIDO_ID` BIGINT(20) UNSIGNED NOT NULL,
    `FORMA_PAGAMENTO` VARCHAR(50) NOT NULL,
    `BANDEIRA` VARCHAR(50),
    `VALOR_DESCONTO` FLOAT(10,2),
    `STATUS` ENUM('PENDENTE','APROVADO','NEGADO','CANCELADO') NOT NULL,
    `NUMERO_PARCELAS` INTEGER,
    `TRANSACAO_ID` VARCHAR(255),
    `COD_AUTORIZACAO` VARCHAR(255),
    `URL_ACESSO` VARCHAR(255),
    `FATURAMENTO_DIRETO_OPCAO` VARCHAR(255),
    `DATA_VENCIMENTO` DATE,
    `CREATED_AT` DATETIME,
    `UPDATED_AT` DATETIME,
    `OBSERVACAO` TEXT,
    `VALOR_PAGAMENTO` FLOAT(12,2),
    `DATA_APROVACAO` DATETIME,
    `CIELO_PAYMENT_ID` VARCHAR(255),
    `COMPROVANTE` VARCHAR(100),
    PRIMARY KEY (`ID`),
    INDEX `pedido_forma_pagamento_FI_1` (`PEDIDO_ID`),
    CONSTRAINT `pedido_forma_pagamento_FK_1`
        FOREIGN KEY (`PEDIDO_ID`)
        REFERENCES `qp1_pedido` (`ID`)
        ON DELETE CASCADE
) ENGINE=InnoDB;

-- ---------------------------------------------------------------------
-- qp1_pedido_item
-- ---------------------------------------------------------------------

DROP TABLE IF EXISTS `qp1_pedido_item`;

CREATE TABLE `qp1_pedido_item`
(
    `ID` BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
    `PEDIDO_ID` BIGINT(20) UNSIGNED NOT NULL,
    `PRODUTO_VARIACAO_ID` BIGINT(20) UNSIGNED,
    `QUANTIDADE` INTEGER DEFAULT 1 NOT NULL,
    `PESO` INTEGER NOT NULL,
    `VALOR_UNITARIO` FLOAT(10,2) NOT NULL,
    `VALOR_PONTOS_UNITARIO` DOUBLE,
    `VALOR_CUSTO` FLOAT(12,2) DEFAULT 0.00 NOT NULL,
    `OBSERVACOES` TEXT,
    `ESTATISTICA_PRODUTO_VARIACAO` TINYINT DEFAULT 0 COMMENT 'Marca o item se sua quantidade estiver sido somada nas estatísticas de venda do produto variação',
    `PLANO_ID` INTEGER,
    PRIMARY KEY (`ID`),
    INDEX `pedido_item_FI_1` (`PEDIDO_ID`),
    INDEX `pedido_item_FI_2` (`PRODUTO_VARIACAO_ID`),
    INDEX `pedido_item_FI_3` (`PLANO_ID`),
    CONSTRAINT `pedido_item_FK_1`
        FOREIGN KEY (`PEDIDO_ID`)
        REFERENCES `qp1_pedido` (`ID`)
        ON UPDATE CASCADE
        ON DELETE CASCADE,
    CONSTRAINT `pedido_item_FK_2`
        FOREIGN KEY (`PRODUTO_VARIACAO_ID`)
        REFERENCES `qp1_produto_variacao` (`ID`)
        ON UPDATE RESTRICT
        ON DELETE RESTRICT,
    CONSTRAINT `pedido_item_FK_3`
        FOREIGN KEY (`PLANO_ID`)
        REFERENCES `qp1_plano` (`ID`)
        ON DELETE RESTRICT
) ENGINE=InnoDB;

-- ---------------------------------------------------------------------
-- qp1_pedido_nota_fiscal_bling
-- ---------------------------------------------------------------------

DROP TABLE IF EXISTS `qp1_pedido_nota_fiscal_bling`;

CREATE TABLE `qp1_pedido_nota_fiscal_bling`
(
    `PEDIDO_ID` bigint(20) unsigned NOT NULL,
    `SERIE` VARCHAR(255) NOT NULL,
    `NUMERO` VARCHAR(255) NOT NULL,
    `DATA_EMISSAO` DATETIME NOT NULL,
    `SITUACAO` VARCHAR(255) NOT NULL,
    `CHAVE_ACESSO` VARCHAR(255) NOT NULL,
    `SERVICO` TINYINT(1) DEFAULT 0 NOT NULL,
    `VALOR_NOTA` VARCHAR(255) NOT NULL,
    `DATA_CRIACAO` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `DATA_ATUALIZACAO` DATETIME,
    `DATA_EXCLUSAO` DATETIME,
    `RESULT` TEXT,
    PRIMARY KEY (`PEDIDO_ID`),
    CONSTRAINT `pedido_nota_fiscal_bling_FK_1`
        FOREIGN KEY (`PEDIDO_ID`)
        REFERENCES `qp1_pedido` (`ID`)
        ON DELETE CASCADE
) ENGINE=InnoDB;

-- ---------------------------------------------------------------------
-- qp1_pedido_retirada_loja
-- ---------------------------------------------------------------------

DROP TABLE IF EXISTS `qp1_pedido_retirada_loja`;

CREATE TABLE `qp1_pedido_retirada_loja`
(
    `PEDIDO_ID` bigint(20) unsigned NOT NULL,
    `LOJA_ID` bigint(20) unsigned,
    `NOME` VARCHAR(255) NOT NULL,
    `KEY` VARCHAR(255) NOT NULL,
    `ENDERECO` VARCHAR(255) NOT NULL,
    `TELEFONE` VARCHAR(20) NOT NULL,
    `VALOR` FLOAT NOT NULL,
    `PRAZO` SMALLINT NOT NULL,
    PRIMARY KEY (`PEDIDO_ID`),
    INDEX `pedido_retirada_loja_FI_2` (`LOJA_ID`),
    CONSTRAINT `pedido_retirada_loja_FK_1`
        FOREIGN KEY (`PEDIDO_ID`)
        REFERENCES `qp1_pedido` (`ID`)
        ON DELETE CASCADE,
    CONSTRAINT `pedido_retirada_loja_FK_2`
        FOREIGN KEY (`LOJA_ID`)
        REFERENCES `qp1_retirada_loja` (`ID`)
        ON DELETE SET NULL
) ENGINE=InnoDB;

-- ---------------------------------------------------------------------
-- qp1_pedido_status
-- ---------------------------------------------------------------------

DROP TABLE IF EXISTS `qp1_pedido_status`;

CREATE TABLE `qp1_pedido_status`
(
    `ID` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
    `MENSAGEM` TEXT,
    `LABEL_PRE_CONFIRMACAO` VARCHAR(255),
    `LABEL_CONFIRMACAO` VARCHAR(255),
    `LABEL_POS_CONFIRMACAO` VARCHAR(255),
    `STATUS` enum('ANDAMENTO','FINALIZADO','CANCELADO') NOT NULL,
    `METODO` enum('OBRIGATORIO','ENTREGA','RETIRADA'),
    `ORDEM` TINYINT,
    PRIMARY KEY (`ID`)
) ENGINE=InnoDB;

-- ---------------------------------------------------------------------
-- qp1_pedido_status_historico
-- ---------------------------------------------------------------------

DROP TABLE IF EXISTS `qp1_pedido_status_historico`;

CREATE TABLE `qp1_pedido_status_historico`
(
    `PEDIDO_ID` BIGINT(20) UNSIGNED NOT NULL,
    `PEDIDO_STATUS_ID` BIGINT(20) UNSIGNED NOT NULL,
    `CREATED_AT` DATETIME,
    `UPDATED_AT` DATETIME,
    `IS_CONCLUIDO` TINYINT DEFAULT 0,
    PRIMARY KEY (`PEDIDO_ID`,`PEDIDO_STATUS_ID`),
    INDEX `pedido_status_historico_FI_2` (`PEDIDO_STATUS_ID`),
    CONSTRAINT `pedido_status_historico_FK_1`
        FOREIGN KEY (`PEDIDO_ID`)
        REFERENCES `qp1_pedido` (`ID`)
        ON DELETE CASCADE,
    CONSTRAINT `pedido_status_historico_FK_2`
        FOREIGN KEY (`PEDIDO_STATUS_ID`)
        REFERENCES `qp1_pedido_status` (`ID`)
        ON DELETE RESTRICT
) ENGINE=InnoDB;

-- ---------------------------------------------------------------------
-- qp1_permissao_grupo
-- ---------------------------------------------------------------------

DROP TABLE IF EXISTS `qp1_permissao_grupo`;

CREATE TABLE `qp1_permissao_grupo`
(
    `ID` INTEGER(10) NOT NULL AUTO_INCREMENT,
    `NOME` VARCHAR(50) NOT NULL,
    PRIMARY KEY (`ID`)
) ENGINE=InnoDB;

-- ---------------------------------------------------------------------
-- qp1_permissao_grupo_modulo
-- ---------------------------------------------------------------------

DROP TABLE IF EXISTS `qp1_permissao_grupo_modulo`;

CREATE TABLE `qp1_permissao_grupo_modulo`
(
    `ID` INTEGER(10) NOT NULL AUTO_INCREMENT,
    `MODULO_ID` INTEGER NOT NULL,
    `GRUPO_ID` INTEGER NOT NULL,
    PRIMARY KEY (`ID`),
    INDEX `permissao_grupo_modulo_FI_1` (`MODULO_ID`),
    INDEX `permissao_grupo_modulo_FI_2` (`GRUPO_ID`),
    CONSTRAINT `permissao_grupo_modulo_FK_1`
        FOREIGN KEY (`MODULO_ID`)
        REFERENCES `qp1_permissao_modulo` (`ID`)
        ON DELETE CASCADE,
    CONSTRAINT `permissao_grupo_modulo_FK_2`
        FOREIGN KEY (`GRUPO_ID`)
        REFERENCES `qp1_permissao_grupo` (`ID`)
        ON DELETE CASCADE
) ENGINE=InnoDB;

-- ---------------------------------------------------------------------
-- qp1_permissao_grupo_usuario
-- ---------------------------------------------------------------------

DROP TABLE IF EXISTS `qp1_permissao_grupo_usuario`;

CREATE TABLE `qp1_permissao_grupo_usuario`
(
    `ID` INTEGER(10) NOT NULL AUTO_INCREMENT,
    `USUARIO_ID` INTEGER NOT NULL,
    `GRUPO_ID` INTEGER NOT NULL,
    PRIMARY KEY (`ID`),
    INDEX `permissao_grupo_usuario_FI_1` (`USUARIO_ID`),
    INDEX `permissao_grupo_usuario_FI_2` (`GRUPO_ID`),
    CONSTRAINT `permissao_grupo_usuario_FK_1`
        FOREIGN KEY (`USUARIO_ID`)
        REFERENCES `qp1_usuario` (`ID`)
        ON DELETE CASCADE,
    CONSTRAINT `permissao_grupo_usuario_FK_2`
        FOREIGN KEY (`GRUPO_ID`)
        REFERENCES `qp1_permissao_grupo` (`ID`)
        ON DELETE CASCADE
) ENGINE=InnoDB;

-- ---------------------------------------------------------------------
-- qp1_permissao_modulo
-- ---------------------------------------------------------------------

DROP TABLE IF EXISTS `qp1_permissao_modulo`;

CREATE TABLE `qp1_permissao_modulo`
(
    `ID` INTEGER(10) NOT NULL AUTO_INCREMENT,
    `NOME` VARCHAR(50) NOT NULL,
    `URL` VARCHAR(50) NOT NULL,
    `ICON` VARCHAR(50),
    `ORDEM` INTEGER DEFAULT 1,
    `MOSTRAR` TINYINT(1) DEFAULT 0,
    `tree_left` INTEGER,
    `tree_right` INTEGER,
    `tree_level` INTEGER,
    `slug` VARCHAR(255),
    PRIMARY KEY (`ID`),
    UNIQUE INDEX `permissao_modulo_slug` (`slug`)
) ENGINE=InnoDB;

-- ---------------------------------------------------------------------
-- qp1_pontos_acumulados
-- ---------------------------------------------------------------------

DROP TABLE IF EXISTS `qp1_pontos_acumulados`;

CREATE TABLE `qp1_pontos_acumulados`
(
    `ID` INTEGER NOT NULL AUTO_INCREMENT,
    `CLIENTE_ID` bigint(20) unsigned NOT NULL,
    `PONTUACAO_ACUMULADA_TOTAL` bigint(20) NOT NULL,
    `PONTUACAO_UTILIZADA_TOTAL` bigint(20) NOT NULL,
    `PONTUACAO_RETIRADA_ADMIN` bigint(20) NOT NULL,
    `UPDATED_AT` DATETIME,
    PRIMARY KEY (`ID`),
    INDEX `pontos_acumulados_FI_1` (`CLIENTE_ID`),
    CONSTRAINT `pontos_acumulados_FK_1`
        FOREIGN KEY (`CLIENTE_ID`)
        REFERENCES `qp1_cliente` (`ID`)
        ON UPDATE CASCADE
        ON DELETE CASCADE
) ENGINE=InnoDB;

-- ---------------------------------------------------------------------
-- qp1_plano
-- ---------------------------------------------------------------------

DROP TABLE IF EXISTS `qp1_plano`;

CREATE TABLE `qp1_plano`
(
    `ID` INTEGER NOT NULL AUTO_INCREMENT,
    `NOME` VARCHAR(200) NOT NULL,
    `DESCRICAO` TEXT,
    `PARTICIPA_EXPANSAO` TINYINT(1) DEFAULT 1 NOT NULL,
    `PARTICIPA_PRODUTIVIDADE` TINYINT(1) DEFAULT 1 NOT NULL,
    `PARTICIPA_FIDELIDADE` TINYINT(1) DEFAULT 1 NOT NULL,
    `PARTICIPA_PARTICIPACAO_LUCROS` TINYINT(1) DEFAULT 1 NOT NULL,
    `PARTICIPA_PLANO_CARREIRA` TINYINT(1) DEFAULT 1 NOT NULL,
    `GRADUACAO_MAXIMA` INTEGER(10),
    `PARTICIPA_LIDERANCA` TINYINT(1) DEFAULT 1 NOT NULL,
    `PARTICIPA_DESEMPENHO` TINYINT(1) DEFAULT 1 NOT NULL,
    `PARTICIPA_DESTAQUE` TINYINT(1) DEFAULT 1 NOT NULL,
    `PARTICIPA_INCENTIVO` TINYINT(1) DEFAULT 1 NOT NULL,
    `PLANO_CLIENTE_PREFERENCIAL` TINYINT(1) DEFAULT 0 NOT NULL,
    `PRODUTO_ID` INTEGER,
    `PERC_DESCONTO_HOTSITE` TINYINT(3) NOT NULL,
    `NIVEL` TINYINT(1) DEFAULT 0 NOT NULL,
    `PARTICIPA_CLIENTE_PREFERENCIAL` TINYINT(1) DEFAULT 1 NOT NULL,
    PRIMARY KEY (`ID`),
    INDEX `plano_FI_1` (`PRODUTO_ID`),
    INDEX `plano_FI_2` (`GRADUACAO_MAXIMA`),
    CONSTRAINT `plano_FK_1`
        FOREIGN KEY (`PRODUTO_ID`)
        REFERENCES `qp1_produto` (`ID`)
        ON DELETE RESTRICT,
    CONSTRAINT `plano_FK_2`
        FOREIGN KEY (`GRADUACAO_MAXIMA`)
        REFERENCES `qp1_plano_carreira` (`ID`)
        ON UPDATE SET NULL
        ON DELETE SET NULL
) ENGINE=InnoDB;

-- ---------------------------------------------------------------------
-- qp1_plano_percentual_bonus
-- ---------------------------------------------------------------------

DROP TABLE IF EXISTS `qp1_plano_percentual_bonus`;

CREATE TABLE `qp1_plano_percentual_bonus`
(
    `ID` INTEGER(11) NOT NULL AUTO_INCREMENT,
    `PLANO_ID` INTEGER(11) NOT NULL,
    `TIPO` ENUM('EXPANSAO','RECOMPRA') NOT NULL,
    `GERACAO` INTEGER NOT NULL,
    `PERCENTUAL` INTEGER NOT NULL,
    PRIMARY KEY (`ID`),
    INDEX `qp1_plano_percentual_bonus_FI_1` (`PLANO_ID`),
    CONSTRAINT `qp1_plano_percentual_bonus_FK_1`
        FOREIGN KEY (`PLANO_ID`)
        REFERENCES `qp1_plano` (`ID`)
        ON UPDATE CASCADE
        ON DELETE CASCADE
) ENGINE=InnoDB;

-- ---------------------------------------------------------------------
-- qp1_plano_desconto_fidelidade
-- ---------------------------------------------------------------------

DROP TABLE IF EXISTS `qp1_plano_desconto_fidelidade`;

CREATE TABLE `qp1_plano_desconto_fidelidade`
(
    `ID` INTEGER(11) NOT NULL AUTO_INCREMENT,
    `PLANO_ID` INTEGER(11) NOT NULL,
    `MES_INICIAL` TINYINT(3) NOT NULL,
    `MES_FINAL` TINYINT(3),
    `PERCENTUAL` INTEGER NOT NULL,
    PRIMARY KEY (`ID`),
    INDEX `qp1_plano_desconto_fidelidade_FI_1` (`PLANO_ID`),
    CONSTRAINT `qp1_plano_desconto_fidelidade_FK_1`
        FOREIGN KEY (`PLANO_ID`)
        REFERENCES `qp1_plano` (`ID`)
        ON UPDATE CASCADE
        ON DELETE CASCADE
) ENGINE=InnoDB;

-- ---------------------------------------------------------------------
-- qp1_plano_desconto_fidelidade_graduacao
-- ---------------------------------------------------------------------

DROP TABLE IF EXISTS `qp1_plano_desconto_fidelidade_graduacao`;

CREATE TABLE `qp1_plano_desconto_fidelidade_graduacao`
(
    `ID` INTEGER(11) NOT NULL AUTO_INCREMENT,
    `PLANO_ID` INTEGER(11) NOT NULL,
    `PLANO_CARREIRA_ID` INTEGER(11) NOT NULL,
    `PERCENTUAL` INTEGER NOT NULL,
    PRIMARY KEY (`ID`),
    INDEX `qp1_plano_desconto_fidelidade_graduacao_FI_1` (`PLANO_ID`),
    INDEX `qp1_plano_desconto_fidelidade_graduacao_FI_2` (`PLANO_CARREIRA_ID`),
    CONSTRAINT `qp1_plano_desconto_fidelidade_graduacao_FK_1`
        FOREIGN KEY (`PLANO_ID`)
        REFERENCES `qp1_plano` (`ID`)
        ON UPDATE CASCADE
        ON DELETE CASCADE,
    CONSTRAINT `qp1_plano_desconto_fidelidade_graduacao_FK_2`
        FOREIGN KEY (`PLANO_CARREIRA_ID`)
        REFERENCES `qp1_plano_carreira` (`ID`)
        ON UPDATE CASCADE
        ON DELETE CASCADE
) ENGINE=InnoDB;

-- ---------------------------------------------------------------------
-- qp1_plano_carreira
-- ---------------------------------------------------------------------

DROP TABLE IF EXISTS `qp1_plano_carreira`;

CREATE TABLE `qp1_plano_carreira`
(
    `ID` INTEGER(10) NOT NULL AUTO_INCREMENT,
    `GRADUACAO` VARCHAR(100) NOT NULL,
    `IMAGEM` VARCHAR(255),
    `BANNER_GRADUACAO` VARCHAR(255),
    `NIVEL` INTEGER NOT NULL,
    `PONTOS` INTEGER NOT NULL,
    `APROVEITAMENTO_LINHA` INTEGER NOT NULL,
    `REQU_QUANTIDADE` INTEGER NOT NULL,
    `REQU_GRADUACAO` INTEGER NOT NULL,
    `REQU_DIRETO` TINYINT(1) NOT NULL,
    `PERC_BONUS_LIDERANCA` TINYINT(3),
    `PERC_BONUS_DESEMPENHO` TINYINT(3),
    `QUANTIDADE_GEN_RECEBE_RECOMPRA` INTEGER DEFAULT 0 NOT NULL,
    `VALOR_PREMIO` FLOAT NOT NULL,
    `VALOR_BONUS_ACELERACAO_PRIMEIRO_PERIODO` FLOAT,
    `VALOR_BONUS_ACELERACAO_SEGUNDO_PERIODO` FLOAT,
    PRIMARY KEY (`ID`)
) ENGINE=InnoDB;

-- ---------------------------------------------------------------------
-- qp1_plano_carreira_cliente
-- ---------------------------------------------------------------------

DROP TABLE IF EXISTS `qp1_plano_carreira_cliente`;

CREATE TABLE `qp1_plano_carreira_cliente`
(
    `ID` INTEGER(10) NOT NULL AUTO_INCREMENT,
    `PLANO_CARREIRA_ID` INTEGER(10) NOT NULL,
    `CLIENTE_ID` bigint(20) unsigned NOT NULL,
    `DATA_DISTRIBUICAO` DATETIME NOT NULL,
    `PONTOS` DOUBLE NOT NULL,
    PRIMARY KEY (`ID`),
    UNIQUE INDEX `plano_carreira_cliente_U_1` (`PLANO_CARREIRA_ID`, `CLIENTE_ID`),
    INDEX `plano_carreira_cliente_FI_2` (`CLIENTE_ID`),
    CONSTRAINT `plano_carreira_cliente_FK_1`
        FOREIGN KEY (`PLANO_CARREIRA_ID`)
        REFERENCES `qp1_plano_carreira` (`ID`)
        ON DELETE RESTRICT,
    CONSTRAINT `plano_carreira_cliente_FK_2`
        FOREIGN KEY (`CLIENTE_ID`)
        REFERENCES `qp1_cliente` (`ID`)
        ON DELETE CASCADE
) ENGINE=InnoDB;

-- ---------------------------------------------------------------------
-- qp1_plano_carreira_historico
-- ---------------------------------------------------------------------

DROP TABLE IF EXISTS `qp1_plano_carreira_historico`;

CREATE TABLE `qp1_plano_carreira_historico`
(
    `ID` INTEGER(11) NOT NULL AUTO_INCREMENT,
    `CLIENTE_ID` bigint(20) unsigned NOT NULL,
    `PLANO_CARREIRA_ID` INTEGER(10) NOT NULL,
    `MES` VARCHAR(2) NOT NULL,
    `ANO` VARCHAR(4) NOT NULL,
    `TOTAL_PONTOS_PESSOAIS` DOUBLE DEFAULT 0 NOT NULL,
    `TOTAL_PONTOS_ADESAO` DOUBLE DEFAULT 0 NOT NULL,
    `TOTAL_PONTOS_RECOMPRA` DOUBLE DEFAULT 0 NOT NULL,
    PRIMARY KEY (`ID`),
    INDEX `qp1_plano_carreira_historico_qp1_cliente_ID_fk` (`CLIENTE_ID`),
    INDEX `qp1_plano_carreira_historico_qp1_plano_carreira_ID_fk` (`PLANO_CARREIRA_ID`),
    CONSTRAINT `qp1_plano_carreira_historico_qp1_cliente_ID_fk`
        FOREIGN KEY (`CLIENTE_ID`)
        REFERENCES `qp1_cliente` (`ID`)
        ON DELETE CASCADE,
    CONSTRAINT `qp1_plano_carreira_historico_qp1_plano_carreira_ID_fk`
        FOREIGN KEY (`PLANO_CARREIRA_ID`)
        REFERENCES `qp1_plano_carreira` (`ID`)
        ON DELETE CASCADE
) ENGINE=InnoDB;

-- ---------------------------------------------------------------------
-- qp1_pre_cadastro_cliente
-- ---------------------------------------------------------------------

DROP TABLE IF EXISTS `qp1_pre_cadastro_cliente`;

CREATE TABLE `qp1_pre_cadastro_cliente`
(
    `ID` INTEGER(10) NOT NULL AUTO_INCREMENT,
    `CLIENTE_ID` bigint(20) unsigned NOT NULL,
    `TIPO` VARCHAR(50) NOT NULL,
    `LADO_INSERCAO` VARCHAR(100) NOT NULL,
    `DATA_INICIO` DATETIME NOT NULL,
    `DATA_FINALIZACAO` DATETIME NOT NULL,
    `CONCLUIDO` INTEGER DEFAULT 0 NOT NULL,
    PRIMARY KEY (`ID`),
    UNIQUE INDEX `pre_cadastro_cliente_U_1` (`CLIENTE_ID`),
    CONSTRAINT `pre_cadastro_cliente_FK_1`
        FOREIGN KEY (`CLIENTE_ID`)
        REFERENCES `qp1_cliente` (`ID`)
        ON DELETE CASCADE
) ENGINE=InnoDB;

-- ---------------------------------------------------------------------
-- qp1_premios_acumulados
-- ---------------------------------------------------------------------

DROP TABLE IF EXISTS `qp1_premios_acumulados`;

CREATE TABLE `qp1_premios_acumulados`
(
    `ID` INTEGER(10) NOT NULL AUTO_INCREMENT,
    `PONTOS_RESGATE` bigint(20) NOT NULL,
    `PRIMEIRO_PREMIO` VARCHAR(255) NOT NULL,
    `SEGUNDO_PREMIO` VARCHAR(255) NOT NULL,
    `PERCENTUAL_VME` FLOAT DEFAULT 0 NOT NULL,
    `UPDATED_AT` DATETIME NOT NULL,
    `USER_ID` bigint(20) NOT NULL,
    PRIMARY KEY (`ID`)
) ENGINE=InnoDB;

-- ---------------------------------------------------------------------
-- qp1_produto
-- ---------------------------------------------------------------------

DROP TABLE IF EXISTS `qp1_produto`;

CREATE TABLE `qp1_produto`
(
    `ID` INTEGER NOT NULL AUTO_INCREMENT,
    `NOME` VARCHAR(200) NOT NULL,
    `KEY` VARCHAR(255) NOT NULL,
    `MARCA_ID` INTEGER,
    `DESCRICAO` TEXT NOT NULL,
    `CARACTERISTICAS` TEXT,
    `DESTAQUE` TINYINT(1) DEFAULT 0 NOT NULL,
    `PARTICIPACAO_RESULTADOS` TINYINT(1) DEFAULT 1 NOT NULL,
    `ALTURA` INTEGER,
    `LARGURA` INTEGER,
    `COMPRIMENTO` INTEGER,
    `PESO` INTEGER NOT NULL,
    `DATA_CRIACAO` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `DATA_ATUALIZACAO` DATETIME,
    `DATA_EXCLUSAO` DATETIME,
    `NOTA_AVALIACAO` FLOAT DEFAULT 0 NOT NULL,
    `QUANTIDADE_AVALIACAO` INTEGER DEFAULT 0 NOT NULL,
    `TAGS` TEXT,
    `VALOR_PONTOS` INTEGER(11) DEFAULT 0,
    `VALOR_SERVICO` FLOAT(12,2) NOT NULL,
    `VALOR_CUSTO` FLOAT(12,2) DEFAULT 0.00 NOT NULL,
    `PLANO_ID` INTEGER,
    `MENSALIDADE` TINYINT(1) DEFAULT 0 NOT NULL,
    `TAXA_CADASTRO` TINYINT(1) DEFAULT 0 NOT NULL,
    `TIPO_CLIENTE_VISUALIZACAO` VARCHAR(10) DEFAULT 'AMBOS' NOT NULL,
    `TIPO_PRODUTO` VARCHAR(10) DEFAULT 'SIMPLES' NOT NULL,
    `NOME_INTEGRACAO` VARCHAR(120) COMMENT 'Nome que vai na integração',
    `APLICA_DESCONTO_PLANO` TINYINT(1) DEFAULT 1 NOT NULL,
    `PARCELAMENTO_INDIVIDUAL` INTEGER,
    `FRETE_GRATIS` TINYINT(1) DEFAULT 0 NOT NULL,
    PRIMARY KEY (`ID`),
    INDEX `TPROD_TMARC_FK_IDX_01` (`MARCA_ID`),
    INDEX `TPROD_TPLAN_FK_IDX_01` (`PLANO_ID`),
    CONSTRAINT `qp1_produto_FK_1`
        FOREIGN KEY (`MARCA_ID`)
        REFERENCES `qp1_marca` (`ID`)
        ON UPDATE SET NULL
        ON DELETE SET NULL,
    CONSTRAINT `qp1_produto_FK_2`
        FOREIGN KEY (`PLANO_ID`)
        REFERENCES `qp1_plano` (`ID`)
        ON DELETE RESTRICT
) ENGINE=InnoDB;

-- ---------------------------------------------------------------------
-- qp1_produto_atributo
-- ---------------------------------------------------------------------

DROP TABLE IF EXISTS `qp1_produto_atributo`;

CREATE TABLE `qp1_produto_atributo`
(
    `ID` INTEGER NOT NULL AUTO_INCREMENT,
    `PRODUTO_ID` INTEGER NOT NULL,
    `ORDEM` INTEGER NOT NULL,
    `TYPE` SMALLINT DEFAULT 0 NOT NULL,
    `DESCRICAO` VARCHAR(200) NOT NULL,
    `DATA_EXCLUSAO` DATETIME,
    PRIMARY KEY (`ID`),
    INDEX `TPROD_TPRAT_FI_01` (`PRODUTO_ID`),
    CONSTRAINT `TPROD_TPRAT_FK_01`
        FOREIGN KEY (`PRODUTO_ID`)
        REFERENCES `qp1_produto` (`ID`)
        ON UPDATE CASCADE
        ON DELETE CASCADE
) ENGINE=InnoDB;

-- ---------------------------------------------------------------------
-- qp1_produto_categoria
-- ---------------------------------------------------------------------

DROP TABLE IF EXISTS `qp1_produto_categoria`;

CREATE TABLE `qp1_produto_categoria`
(
    `CATEGORIA_ID` INTEGER NOT NULL,
    `PRODUTO_ID` INTEGER NOT NULL,
    PRIMARY KEY (`CATEGORIA_ID`,`PRODUTO_ID`),
    INDEX `qp1_produto_categoria_FI_2` (`PRODUTO_ID`),
    CONSTRAINT `qp1_produto_categoria_FK_1`
        FOREIGN KEY (`CATEGORIA_ID`)
        REFERENCES `qp1_categoria` (`ID`)
        ON UPDATE CASCADE
        ON DELETE CASCADE,
    CONSTRAINT `qp1_produto_categoria_FK_2`
        FOREIGN KEY (`PRODUTO_ID`)
        REFERENCES `qp1_produto` (`ID`)
        ON UPDATE CASCADE
        ON DELETE CASCADE
) ENGINE=InnoDB;

-- ---------------------------------------------------------------------
-- qp1_produto_comentario
-- ---------------------------------------------------------------------

DROP TABLE IF EXISTS `qp1_produto_comentario`;

CREATE TABLE `qp1_produto_comentario`
(
    `ID` INTEGER NOT NULL AUTO_INCREMENT,
    `DATA` DATETIME NOT NULL,
    `NOME` VARCHAR(200) NOT NULL,
    `EMAIL` VARCHAR(200) NOT NULL,
    `TITULO` VARCHAR(255) NOT NULL,
    `DESCRICAO` TEXT NOT NULL,
    `IP` VARCHAR(100),
    `STATUS` enum('APROVADO','REPROVADO','PENDENTE') DEFAULT 'PENDENTE' NOT NULL,
    `CLIENTE_ID` bigint(20) unsigned,
    `NOTA` DOUBLE,
    `PRODUTO_ID` INTEGER NOT NULL,
    PRIMARY KEY (`ID`),
    INDEX `TPRCO_TCLIE_FK_IDX_01` (`CLIENTE_ID`),
    INDEX `TPRCO_TPROD_FK_IDX_01` (`PRODUTO_ID`),
    CONSTRAINT `TPRCO_TCLIE_FK_01`
        FOREIGN KEY (`CLIENTE_ID`)
        REFERENCES `qp1_cliente` (`ID`)
        ON UPDATE CASCADE
        ON DELETE CASCADE,
    CONSTRAINT `TPRCO_TPROD_FK_01`
        FOREIGN KEY (`PRODUTO_ID`)
        REFERENCES `qp1_produto` (`ID`)
        ON UPDATE CASCADE
        ON DELETE CASCADE
) ENGINE=InnoDB;

-- ---------------------------------------------------------------------
-- qp1_produto_composto
-- ---------------------------------------------------------------------

DROP TABLE IF EXISTS `qp1_produto_composto`;

CREATE TABLE `qp1_produto_composto`
(
    `ID` INTEGER NOT NULL AUTO_INCREMENT,
    `PRODUTO_ID` INTEGER NOT NULL,
    `PRODUTO_COMPOSTO_ID` INTEGER NOT NULL,
    `PRODUTO_COMPOSTO_VARIACAO_ID` bigint(20) unsigned NOT NULL,
    `ESTOQUE_QUANTIDADE` INTEGER DEFAULT 0,
    `VALOR_INTEGRACAO` FLOAT(12,2),
    PRIMARY KEY (`ID`),
    INDEX `TPRCOMP_TPROD_FI_01` (`PRODUTO_ID`),
    INDEX `TPRCOMP_TPROD_FI_02` (`PRODUTO_COMPOSTO_ID`),
    INDEX `TPRCOMP_TPRODV_FI_03` (`PRODUTO_COMPOSTO_VARIACAO_ID`),
    CONSTRAINT `TPRCOMP_TPRODV_FK_03`
        FOREIGN KEY (`PRODUTO_COMPOSTO_VARIACAO_ID`)
        REFERENCES `qp1_produto_variacao` (`ID`)
        ON UPDATE CASCADE
        ON DELETE CASCADE,
    CONSTRAINT `TPRCOMP_TPROD_FK_01`
        FOREIGN KEY (`PRODUTO_ID`)
        REFERENCES `qp1_produto` (`ID`)
        ON UPDATE CASCADE
        ON DELETE CASCADE,
    CONSTRAINT `TPRCOMP_TPROD_FK_02`
        FOREIGN KEY (`PRODUTO_COMPOSTO_ID`)
        REFERENCES `qp1_produto` (`ID`)
        ON UPDATE CASCADE
        ON DELETE CASCADE
) ENGINE=InnoDB;

-- ---------------------------------------------------------------------
-- qp1_produto_cor
-- ---------------------------------------------------------------------

DROP TABLE IF EXISTS `qp1_produto_cor`;

CREATE TABLE `qp1_produto_cor`
(
    `ID` INTEGER(10) NOT NULL AUTO_INCREMENT,
    `NOME` VARCHAR(100) NOT NULL,
    `RGB` VARCHAR(7),
    `IMAGEM` VARCHAR(50),
    `CODIGO` VARCHAR(32),
    PRIMARY KEY (`ID`)
) ENGINE=InnoDB;

-- ---------------------------------------------------------------------
-- qp1_produto_interesse
-- ---------------------------------------------------------------------

DROP TABLE IF EXISTS `qp1_produto_interesse`;

CREATE TABLE `qp1_produto_interesse`
(
    `ID` INTEGER NOT NULL AUTO_INCREMENT,
    `PRODUTO_VARIACAO_ID` BIGINT(20) UNSIGNED NOT NULL,
    `CLIENTE_NOME` VARCHAR(255) NOT NULL,
    `CLIENTE_EMAIL` VARCHAR(255) NOT NULL,
    `CLIENTE_TELEFONE` VARCHAR(255),
    `DATA_CRIACAO` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `ENVIAR_AVISO` TINYINT(1) DEFAULT 0 NOT NULL,
    PRIMARY KEY (`ID`),
    INDEX `produto_interesse_FI_1` (`PRODUTO_VARIACAO_ID`),
    CONSTRAINT `produto_interesse_FK_1`
        FOREIGN KEY (`PRODUTO_VARIACAO_ID`)
        REFERENCES `qp1_produto_variacao` (`ID`)
        ON UPDATE CASCADE
        ON DELETE CASCADE
) ENGINE=InnoDB;

-- ---------------------------------------------------------------------
-- qp1_produto_variacao
-- ---------------------------------------------------------------------

DROP TABLE IF EXISTS `qp1_produto_variacao`;

CREATE TABLE `qp1_produto_variacao`
(
    `ID` BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
    `PRODUTO_ID` INTEGER NOT NULL,
    `SKU` VARCHAR(50),
    `PESO` INTEGER,
    `VALOR_BASE` FLOAT(12,2) NOT NULL,
    `VALOR_INTEGRACAO_ADMIN` FLOAT(12,2),
    `FATOR_CORRECAO_GRUPO` VARCHAR(10),
    `VALOR_PROMOCIONAL` FLOAT(12,2) NOT NULL,
    `VALOR_DISTRIBUIDOR` FLOAT(12,2) NOT NULL,
    `ESTOQUE_ATUAL` INTEGER NOT NULL,
    `ESTOQUE_MINIMO` INTEGER NOT NULL,
    `DISPONIVEL` TINYINT(1) NOT NULL,
    `IS_MASTER` TINYINT(1) NOT NULL,
    `IS_REQUIRED_PRODUCT` TINYINT(1) DEFAULT 0 NOT NULL,
    `DATA_CRIACAO` DATETIME,
    `DATA_ATUALIZACAO` DATETIME,
    `DATA_EXCLUSAO` DATETIME,
    `IS_PADRAO` TINYINT(1),
    PRIMARY KEY (`ID`),
    INDEX `FI_duto_variacao_produto` (`PRODUTO_ID`),
    CONSTRAINT `produto_variacao_produto`
        FOREIGN KEY (`PRODUTO_ID`)
        REFERENCES `qp1_produto` (`ID`)
        ON UPDATE CASCADE
        ON DELETE CASCADE
) ENGINE=InnoDB;

-- ---------------------------------------------------------------------
-- qp1_produto_variacao_atributo
-- ---------------------------------------------------------------------

DROP TABLE IF EXISTS `qp1_produto_variacao_atributo`;

CREATE TABLE `qp1_produto_variacao_atributo`
(
    `PRODUTO_VARIACAO_ID` BIGINT(20) UNSIGNED NOT NULL,
    `PRODUTO_ATRIBUTO_ID` INTEGER NOT NULL,
    `DESCRICAO` VARCHAR(200) NOT NULL,
    `PROPRIEDADE` TEXT,
    `DATA_EXCLUSAO` DATETIME,
    PRIMARY KEY (`PRODUTO_VARIACAO_ID`,`PRODUTO_ATRIBUTO_ID`),
    INDEX `TPRAT_TPRVA_FI_02` (`PRODUTO_ATRIBUTO_ID`),
    CONSTRAINT `TPRAT_TPRVA_FK_02`
        FOREIGN KEY (`PRODUTO_ATRIBUTO_ID`)
        REFERENCES `qp1_produto_atributo` (`ID`)
        ON UPDATE CASCADE
        ON DELETE CASCADE,
    CONSTRAINT `TPRVA_TPRVA_FK_01`
        FOREIGN KEY (`PRODUTO_VARIACAO_ID`)
        REFERENCES `qp1_produto_variacao` (`ID`)
        ON UPDATE CASCADE
        ON DELETE CASCADE
) ENGINE=InnoDB;

-- ---------------------------------------------------------------------
-- qp1_produto_venda_casada
-- ---------------------------------------------------------------------

DROP TABLE IF EXISTS `qp1_produto_venda_casada`;

CREATE TABLE `qp1_produto_venda_casada`
(
    `ID` INTEGER NOT NULL AUTO_INCREMENT,
    `VENDA_CASADA_ID` INTEGER NOT NULL,
    `PRODUTO_ID` INTEGER NOT NULL,
    PRIMARY KEY (`ID`),
    UNIQUE INDEX `produto_venda_casada_U_1` (`VENDA_CASADA_ID`, `PRODUTO_ID`),
    INDEX `TPRVC_TVECA_FK_IDX_01` (`VENDA_CASADA_ID`),
    INDEX `TPRVC_TPROD_FK_IDX_01` (`PRODUTO_ID`),
    CONSTRAINT `TPRVC_TPROD_FK_01`
        FOREIGN KEY (`PRODUTO_ID`)
        REFERENCES `qp1_produto` (`ID`)
        ON UPDATE CASCADE
        ON DELETE CASCADE,
    CONSTRAINT `TPRVC_TVECA_FK_01`
        FOREIGN KEY (`VENDA_CASADA_ID`)
        REFERENCES `qp1_venda_casada` (`ID`)
        ON UPDATE CASCADE
        ON DELETE CASCADE
) ENGINE=InnoDB;

-- ---------------------------------------------------------------------
-- qp1_produto_venda_estatistica
-- ---------------------------------------------------------------------

DROP TABLE IF EXISTS `qp1_produto_venda_estatistica`;

CREATE TABLE `qp1_produto_venda_estatistica`
(
    `PRODUTO_ID` INTEGER NOT NULL,
    `QUANTIDADE_VENDIDA` bigint(20) unsigned DEFAULT 0 NOT NULL,
    `UPDATED_AT` DATETIME NOT NULL,
    PRIMARY KEY (`PRODUTO_ID`),
    CONSTRAINT `produto_venda_estatistica_FK_1`
        FOREIGN KEY (`PRODUTO_ID`)
        REFERENCES `qp1_produto` (`ID`)
        ON UPDATE CASCADE
        ON DELETE CASCADE
) ENGINE=InnoDB;

-- ---------------------------------------------------------------------
-- qp1_produto_visitado
-- ---------------------------------------------------------------------

DROP TABLE IF EXISTS `qp1_produto_visitado`;

CREATE TABLE `qp1_produto_visitado`
(
    `ID` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
    `DATA_VISITADO` DATETIME NOT NULL,
    `PRODUTO_ID` INTEGER NOT NULL,
    `CLIENTE_ID` bigint(20) unsigned NOT NULL,
    PRIMARY KEY (`ID`),
    INDEX `TPRVI_TCLIE_FK_IDX_01` (`CLIENTE_ID`),
    INDEX `TPRVI_TPROD_FK_IDX_01` (`PRODUTO_ID`),
    CONSTRAINT `TPRVI_TCLIE_FK_01`
        FOREIGN KEY (`CLIENTE_ID`)
        REFERENCES `qp1_cliente` (`ID`)
        ON UPDATE CASCADE
        ON DELETE CASCADE,
    CONSTRAINT `TPRVI_TPROD_FK_01`
        FOREIGN KEY (`PRODUTO_ID`)
        REFERENCES `qp1_produto` (`ID`)
        ON UPDATE CASCADE
        ON DELETE CASCADE
) ENGINE=InnoDB;

-- ---------------------------------------------------------------------
-- qp1_rede
-- ---------------------------------------------------------------------

DROP TABLE IF EXISTS `qp1_rede`;

CREATE TABLE `qp1_rede`
(
    `ID` INTEGER NOT NULL AUTO_INCREMENT,
    `NOME` VARCHAR(255) NOT NULL,
    `IMAGEM` VARCHAR(50) NOT NULL,
    `ICON` VARCHAR(50) NOT NULL,
    `ATIVO` SMALLINT DEFAULT 0 NOT NULL,
    `LINK` VARCHAR(255) NOT NULL,
    `ORDEM` INTEGER(5) DEFAULT 0 NOT NULL,
    PRIMARY KEY (`ID`)
) ENGINE=MyISAM;

-- ---------------------------------------------------------------------
-- qp1_regiao_frete_gratis
-- ---------------------------------------------------------------------

DROP TABLE IF EXISTS `qp1_regiao_frete_gratis`;

CREATE TABLE `qp1_regiao_frete_gratis`
(
    `ID` INTEGER(10) NOT NULL AUTO_INCREMENT,
    `NOME` VARCHAR(100) NOT NULL,
    `OBSERVACAO` TEXT,
    `CEP_INICIAL` INTEGER(8) NOT NULL,
    `CEP_FINAL` INTEGER(8) NOT NULL,
    `VALOR_MINIMO` FLOAT NOT NULL,
    `PRAZO_ENTREGA` INTEGER NOT NULL,
    `IS_ATIVO` TINYINT DEFAULT 1 NOT NULL,
    PRIMARY KEY (`ID`)
) ENGINE=InnoDB;

-- ---------------------------------------------------------------------
-- qp1_resgate
-- ---------------------------------------------------------------------

DROP TABLE IF EXISTS `qp1_resgate`;

CREATE TABLE `qp1_resgate`
(
    `ID` INTEGER NOT NULL AUTO_INCREMENT,
    `CLIENTE_ID` bigint(20) unsigned NOT NULL,
    `DATA` DATETIME NOT NULL,
    `VALOR` FLOAT(12,2) NOT NULL,
    `VALOR_TAXA` FLOAT(12,2) DEFAULT 0 NOT NULL,
    `VALOR_DEPOSITAR` FLOAT(12,2) DEFAULT 0 NOT NULL,
    `BANCO` VARCHAR(200) NOT NULL,
    `PIS_PASEP` VARCHAR(200),
    `AGENCIA` VARCHAR(50) NOT NULL,
    `CONTA` VARCHAR(50) NOT NULL,
    `TIPO_CONTA` enum('CORRENTE','POUPANCA') DEFAULT 'CORRENTE',
    `NOME_CORRENTISTA` VARCHAR(200) NOT NULL,
    `CPF_CORRENTISTA` VARCHAR(20),
    `CNPJ_CORRENTISTA` VARCHAR(20),
    `SITUACAO` enum('PENDENTE','EFETUADO','NAOEFETUADO') DEFAULT 'PENDENTE',
    PRIMARY KEY (`ID`),
    INDEX `resgate_FI_1` (`CLIENTE_ID`),
    CONSTRAINT `resgate_FK_1`
        FOREIGN KEY (`CLIENTE_ID`)
        REFERENCES `qp1_cliente` (`ID`)
        ON UPDATE CASCADE
        ON DELETE CASCADE
) ENGINE=InnoDB;

-- ---------------------------------------------------------------------
-- qp1_resgate_premios_acumulados
-- ---------------------------------------------------------------------

DROP TABLE IF EXISTS `qp1_resgate_premios_acumulados`;

CREATE TABLE `qp1_resgate_premios_acumulados`
(
    `ID` INTEGER NOT NULL AUTO_INCREMENT,
    `CLIENTE_ID` bigint(20) unsigned NOT NULL,
    `PONTOS_RESGATE` bigint(20) NOT NULL,
    `DATA` DATETIME NOT NULL,
    `PREMIO` VARCHAR(255) NOT NULL,
    `SELECIONADO` enum('PREMIO','DINHEIRO') DEFAULT 'PREMIO',
    `SITUACAO` enum('PENDENTE','EFETUADO','NAOEFETUADO', 'EXTORNADO') DEFAULT 'PENDENTE',
    `UPDATED_AT` DATETIME,
    `USER_ID` bigint(20),
    PRIMARY KEY (`ID`),
    INDEX `resgate_premiacao_FI_1` (`CLIENTE_ID`),
    CONSTRAINT `resgate_premiacao_FK_1`
        FOREIGN KEY (`CLIENTE_ID`)
        REFERENCES `qp1_cliente` (`ID`)
        ON UPDATE CASCADE
        ON DELETE CASCADE
) ENGINE=InnoDB;

-- ---------------------------------------------------------------------
-- qp1_retirada_loja
-- ---------------------------------------------------------------------

DROP TABLE IF EXISTS `qp1_retirada_loja`;

CREATE TABLE `qp1_retirada_loja`
(
    `ID` BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
    `NOME` VARCHAR(255) NOT NULL,
    `KEY` VARCHAR(255) NOT NULL,
    `ENDERECO` VARCHAR(255) NOT NULL,
    `TELEFONE` VARCHAR(20) NOT NULL,
    `VALOR` FLOAT NOT NULL,
    `PRAZO` SMALLINT NOT NULL,
    `HABILITADO` TINYINT(1) DEFAULT 1 NOT NULL,
    `CIDADE_ID` INTEGER(11) NOT NULL,
    `CENTRO_DISTRIBUICAO_ID` INTEGER(11) NOT NULL,
    PRIMARY KEY (`ID`),
    INDEX `FI__retirada_loja_FK1` (`CIDADE_ID`),
    CONSTRAINT `qp1_retirada_loja_FK1`
        FOREIGN KEY (`CIDADE_ID`)
        REFERENCES `qp1_cidade` (`ID`)
        ON UPDATE RESTRICT
        ON DELETE RESTRICT
) ENGINE=InnoDB;

-- ---------------------------------------------------------------------
-- qp1_seo
-- ---------------------------------------------------------------------

DROP TABLE IF EXISTS `qp1_seo`;

CREATE TABLE `qp1_seo`
(
    `ID` INTEGER NOT NULL AUTO_INCREMENT,
    `PAGINA` enum('HOME','EMPRESA','CATEGORIA','PRODUTO','PROMOCAO','FAQ','CADASTRO','LOGIN','CARRINHO','CONTATO') NOT NULL,
    `REGISTRO_ID` INTEGER,
    `META_TITLE` VARCHAR(65),
    `META_DESCRIPTION` VARCHAR(150),
    `META_KEYWORDS` VARCHAR(255),
    PRIMARY KEY (`ID`)
) ENGINE=MyISAM;

-- ---------------------------------------------------------------------
-- qp1_suporte
-- ---------------------------------------------------------------------

DROP TABLE IF EXISTS `qp1_suporte`;

CREATE TABLE `qp1_suporte`
(
    `ID` INTEGER NOT NULL AUTO_INCREMENT,
    `TIPO` enum('TEXTO','VIDEO','ARQUIVO','VIDEO_AULA') DEFAULT 'TEXTO' NOT NULL,
    `TITULO` VARCHAR(255) NOT NULL,
    `DESCRICAO_RESUMIDA` VARCHAR(500) NOT NULL,
    `DESCRICAO` TEXT,
    `VIDEO` TEXT,
    `ARQUIVO` VARCHAR(50),
    `LINK_ARQUIVO_S3` VARCHAR(200),
    `MOSTRAR` TINYINT(1) DEFAULT 0 NOT NULL,
    `ORDEM` INTEGER DEFAULT 0,
    PRIMARY KEY (`ID`)
) ENGINE=InnoDB;

-- ---------------------------------------------------------------------
-- qp1_tabela_preco
-- ---------------------------------------------------------------------

DROP TABLE IF EXISTS `qp1_tabela_preco`;

CREATE TABLE `qp1_tabela_preco`
(
    `ID` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
    `NOME` VARCHAR(250) NOT NULL,
    `PORCENTAGEM` FLOAT NOT NULL,
    `TIPO_OPERACAO` TINYINT NOT NULL,
    `ATUALIZAR_AUTOMATICAMENTE` TINYINT(1) NOT NULL,
    `OBSERVACAO` TEXT,
    PRIMARY KEY (`ID`)
) ENGINE=InnoDB;

-- ---------------------------------------------------------------------
-- qp1_tabela_preco_variacao
-- ---------------------------------------------------------------------

DROP TABLE IF EXISTS `qp1_tabela_preco_variacao`;

CREATE TABLE `qp1_tabela_preco_variacao`
(
    `ID` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
    `PRODUTO_VARIACAO_ID` bigint(20) unsigned NOT NULL,
    `TABELA_PRECO_ID` bigint(20) unsigned NOT NULL,
    `VALOR_BASE` FLOAT(12,2) NOT NULL,
    `VALOR_PROMOCIONAL` FLOAT(12,2) NOT NULL,
    PRIMARY KEY (`ID`),
    INDEX `tabela_preco_variacao_FI_1` (`PRODUTO_VARIACAO_ID`),
    INDEX `tabela_preco_variacao_FI_2` (`TABELA_PRECO_ID`),
    CONSTRAINT `tabela_preco_variacao_FK_1`
        FOREIGN KEY (`PRODUTO_VARIACAO_ID`)
        REFERENCES `qp1_produto_variacao` (`ID`)
        ON DELETE CASCADE,
    CONSTRAINT `tabela_preco_variacao_FK_2`
        FOREIGN KEY (`TABELA_PRECO_ID`)
        REFERENCES `qp1_tabela_preco` (`ID`)
        ON DELETE CASCADE
) ENGINE=InnoDB;

-- ---------------------------------------------------------------------
-- qp1_ticket
-- ---------------------------------------------------------------------

DROP TABLE IF EXISTS `qp1_ticket`;

CREATE TABLE `qp1_ticket`
(
    `ID` INTEGER(10) NOT NULL AUTO_INCREMENT,
    `CLIENTE_ID` bigint(20) unsigned NOT NULL,
    `CATEGORIA` VARCHAR(50) NOT NULL,
    `GRUPO_ID` bigint(20) unsigned NOT NULL,
    `ASSUNTO` VARCHAR(150) NOT NULL,
    `EMAIL_DESTINO` VARCHAR(150) NOT NULL,
    `DESCRICAO` TEXT NOT NULL,
    `DATA` DATETIME NOT NULL,
    `STATUS` enum('PENDENTE','EMANDAMENTO','FINALIZADO') NOT NULL,
    PRIMARY KEY (`ID`),
    INDEX `ticket_FI_1` (`CLIENTE_ID`),
    CONSTRAINT `ticket_FK_1`
        FOREIGN KEY (`CLIENTE_ID`)
        REFERENCES `qp1_cliente` (`ID`)
        ON UPDATE CASCADE
        ON DELETE CASCADE
) ENGINE=InnoDB;

-- ---------------------------------------------------------------------
-- qp1_ticket_messages
-- ---------------------------------------------------------------------

DROP TABLE IF EXISTS `qp1_ticket_messages`;

CREATE TABLE `qp1_ticket_messages`
(
    `ID` INTEGER(10) NOT NULL AUTO_INCREMENT,
    `TICKET_ID` bigint(20) unsigned NOT NULL,
    `REMETENTE` enum('ADMIN','CLIENTE') NOT NULL,
    `REMETENTE_NOME` VARCHAR(150) NOT NULL,
    `MENSAGEM` TEXT NOT NULL,
    `DATA` DATETIME NOT NULL,
    PRIMARY KEY (`ID`),
    INDEX `ticket_FI_1` (`TICKET_ID`),
    CONSTRAINT `ticket_FK_1`
        FOREIGN KEY (`TICKET_ID`)
        REFERENCES `qp1_ticket` (`ID`)
        ON UPDATE CASCADE
        ON DELETE CASCADE
) ENGINE=InnoDB;

-- ---------------------------------------------------------------------
-- qp1_transportadora_faixa_peso
-- ---------------------------------------------------------------------

DROP TABLE IF EXISTS `qp1_transportadora_faixa_peso`;

CREATE TABLE `qp1_transportadora_faixa_peso`
(
    `ID` INTEGER(10) NOT NULL AUTO_INCREMENT,
    `TRANSPORTADORA_REGIAO_ID` INTEGER NOT NULL,
    `PESO` INTEGER NOT NULL,
    `TIPO` enum('PRECO_FIXO','PRECO_KG','PORCENTAGEM') NOT NULL,
    `VALOR` FLOAT NOT NULL,
    `PRAZO_ENTREGA` INTEGER NOT NULL,
    PRIMARY KEY (`ID`,`TRANSPORTADORA_REGIAO_ID`),
    INDEX `transportadora_faixa_peso_FI_1` (`TRANSPORTADORA_REGIAO_ID`),
    CONSTRAINT `transportadora_faixa_peso_FK_1`
        FOREIGN KEY (`TRANSPORTADORA_REGIAO_ID`)
        REFERENCES `qp1_transportadora_regiao` (`ID`)
        ON UPDATE CASCADE
        ON DELETE CASCADE
) ENGINE=InnoDB;

-- ---------------------------------------------------------------------
-- qp1_transportadora_regiao
-- ---------------------------------------------------------------------

DROP TABLE IF EXISTS `qp1_transportadora_regiao`;

CREATE TABLE `qp1_transportadora_regiao`
(
    `ID` INTEGER(10) NOT NULL AUTO_INCREMENT,
    `NOME` VARCHAR(100) NOT NULL,
    `OBSERVACAO` TEXT,
    `CEP_INICIAL` INTEGER(8) NOT NULL,
    `CEP_FINAL` INTEGER(8) NOT NULL,
    `IS_ATIVO` TINYINT DEFAULT 1 NOT NULL,
    PRIMARY KEY (`ID`)
) ENGINE=InnoDB;

-- ---------------------------------------------------------------------
-- qp1_uol_shopping_item
-- ---------------------------------------------------------------------

DROP TABLE IF EXISTS `qp1_uol_shopping_item`;

CREATE TABLE `qp1_uol_shopping_item`
(
    `PRODUTO_ID` INTEGER NOT NULL,
    PRIMARY KEY (`PRODUTO_ID`),
    CONSTRAINT `uol_shopping_item_FK_1`
        FOREIGN KEY (`PRODUTO_ID`)
        REFERENCES `qp1_produto` (`ID`)
        ON DELETE CASCADE
) ENGINE=InnoDB;

-- ---------------------------------------------------------------------
-- qp1_usuario
-- ---------------------------------------------------------------------

DROP TABLE IF EXISTS `qp1_usuario`;

CREATE TABLE `qp1_usuario`
(
    `ID` INTEGER NOT NULL AUTO_INCREMENT,
    `NOME` VARCHAR(200) NOT NULL,
    `EMAIL` VARCHAR(200) NOT NULL,
    `LOGIN` VARCHAR(50) NOT NULL,
    `SENHA` VARCHAR(50) NOT NULL,
    `MASTER` SMALLINT DEFAULT 0,
    `ERROS_LOGIN` INTEGER,
    `TOKEN` VARCHAR(128),
    PRIMARY KEY (`ID`),
    UNIQUE INDEX `TUSUA_UK_01` (`LOGIN`),
    UNIQUE INDEX `TUSUA_UK_02` (`EMAIL`)
) ENGINE=InnoDB;

-- ---------------------------------------------------------------------
-- qp1_venda_casada
-- ---------------------------------------------------------------------

DROP TABLE IF EXISTS `qp1_venda_casada`;

CREATE TABLE `qp1_venda_casada`
(
    `ID` INTEGER NOT NULL AUTO_INCREMENT,
    `NOME` VARCHAR(255) NOT NULL,
    `VALOR` FLOAT(12,2) NOT NULL,
    `VALOR_DESCONTO` FLOAT(12,2) NOT NULL,
    `ATIVO` TINYINT(1) DEFAULT 1 NOT NULL,
    `DATA_CADASTRO` DATETIME,
    PRIMARY KEY (`ID`)
) ENGINE=InnoDB;

-- ---------------------------------------------------------------------
-- qp1_transferencia
-- ---------------------------------------------------------------------

DROP TABLE IF EXISTS `qp1_transferencia`;

CREATE TABLE `qp1_transferencia`
(
    `ID` INTEGER NOT NULL AUTO_INCREMENT,
    `CLIENTE_REMETENTE_ID` INTEGER NOT NULL,
    `CLIENTE_DESTINATARIO_ID` INTEGER NOT NULL,
    `QUANTIDADE_PONTOS` DOUBLE NOT NULL,
    `DATA` DATETIME NOT NULL,
    PRIMARY KEY (`ID`),
    INDEX `TTRAN_TCLIE_FI_01` (`CLIENTE_REMETENTE_ID`),
    INDEX `TTRAN_TCLIE_FI_02` (`CLIENTE_DESTINATARIO_ID`),
    CONSTRAINT `TTRAN_TCLIE_FK_01`
        FOREIGN KEY (`CLIENTE_REMETENTE_ID`)
        REFERENCES `qp1_cliente` (`ID`)
        ON UPDATE CASCADE
        ON DELETE CASCADE,
    CONSTRAINT `TTRAN_TCLIE_FK_02`
        FOREIGN KEY (`CLIENTE_DESTINATARIO_ID`)
        REFERENCES `qp1_cliente` (`ID`)
        ON UPDATE CASCADE
        ON DELETE CASCADE
) ENGINE=InnoDB;

-- ---------------------------------------------------------------------
-- qp1_controle_pontuacao_cliente
-- ---------------------------------------------------------------------

DROP TABLE IF EXISTS `qp1_controle_pontuacao_cliente`;

CREATE TABLE `qp1_controle_pontuacao_cliente`
(
    `ID` BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
    `CLIENTE_ID` BIGINT(20) UNSIGNED NOT NULL,
    `MES` TINYINT(2) NOT NULL,
    `ANO` SMALLINT(4) NOT NULL,
    `PONTOS_PESSOAIS` INTEGER(11) NOT NULL,
    `PONTOS_ADESAO` INTEGER(11) NOT NULL,
    `PONTOS_RECOMPRA` INTEGER(11) NOT NULL,
    PRIMARY KEY (`ID`),
    INDEX `qp1_controle_pontuacao_cliente_FI_1` (`CLIENTE_ID`),
    CONSTRAINT `qp1_controle_pontuacao_cliente_FK_1`
        FOREIGN KEY (`CLIENTE_ID`)
        REFERENCES `qp1_cliente` (`ID`)
        ON DELETE CASCADE
) ENGINE=InnoDB;

# This restores the fkey checks, after having unset them earlier
SET FOREIGN_KEY_CHECKS = 1;
