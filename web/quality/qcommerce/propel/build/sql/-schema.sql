
# This is a fix for InnoDB in MySQL >= 4.1.x
# It "suspends judgement" for fkey relationships until are tables are set.
SET FOREIGN_KEY_CHECKS = 0;

-- ---------------------------------------------------------------------
-- qp1_banner
-- ---------------------------------------------------------------------

DROP TABLE IF EXISTS `qp1_banner`;

CREATE TABLE `qp1_banner`
(
    `ID` INTEGER(10) NOT NULL AUTO_INCREMENT,
    `TITULO` VARCHAR(255) NOT NULL COMMENT 'Titulo do Banner',
    `ORDEM` INTEGER(3) DEFAULT 0 COMMENT 'Ordem do banner',
    `MOSTRAR` TINYINT(1) DEFAULT 0 COMMENT 'Mostrar?',
    `LINK` VARCHAR(255) COMMENT 'Link do banner',
    `IMAGEM` VARCHAR(50) NOT NULL COMMENT 'Imagem do banner',
    `TIPO` ENUM('DESTAQUE','LATERAL') DEFAULT 'LATERAL' NOT NULL COMMENT 'Tipo do banner',
    PRIMARY KEY (`ID`)
) ENGINE=MyISAM COMMENT='Banners do sistema';

-- ---------------------------------------------------------------------
-- qp1_cidade
-- ---------------------------------------------------------------------

DROP TABLE IF EXISTS `qp1_cidade`;

CREATE TABLE `qp1_cidade`
(
    `ID` INTEGER(11) NOT NULL AUTO_INCREMENT,
    `NOME` VARCHAR(200) NOT NULL COMMENT 'Nome da cidade',
    `ESTADO_ID` INTEGER NOT NULL COMMENT 'Id do estado ao qual a cidade pertence',
    PRIMARY KEY (`ID`),
    INDEX `TCIDA_TESTA_FK_IDX_01` (`ESTADO_ID`),
    CONSTRAINT `TCIDA_TESTA_FK_01`
        FOREIGN KEY (`ESTADO_ID`)
        REFERENCES `qp1_estado` (`ID`)
        ON UPDATE CASCADE
        ON DELETE CASCADE
) ENGINE=InnoDB COMMENT='Cidades brasileiras';

-- ---------------------------------------------------------------------
-- qp1_estado
-- ---------------------------------------------------------------------

DROP TABLE IF EXISTS `qp1_estado`;

CREATE TABLE `qp1_estado`
(
    `ID` INTEGER(11) NOT NULL AUTO_INCREMENT,
    `SIGLA` CHAR(2) NOT NULL COMMENT 'Sigla do estado',
    `NOME` VARCHAR(45) NOT NULL COMMENT 'Nome do estado',
    `CAPITAL_ID` INTEGER NOT NULL COMMENT 'Id da cidade capital do estado',
    PRIMARY KEY (`ID`),
    UNIQUE INDEX `TESTA_UK_01` (`NOME`),
    UNIQUE INDEX `TESTA_UK_02` (`SIGLA`),
    UNIQUE INDEX `TESTA_UK_03` (`CAPITAL_ID`),
    INDEX `TCIDA_TESTA_FK_IDX_01` (`CAPITAL_ID`)
) ENGINE=InnoDB COMMENT='Estados brasileiros';

-- ---------------------------------------------------------------------
-- qp1_cliente
-- ---------------------------------------------------------------------

DROP TABLE IF EXISTS `qp1_cliente`;

CREATE TABLE `qp1_cliente`
(
    `ID` INTEGER NOT NULL AUTO_INCREMENT,
    `TIPO` CHAR NOT NULL,
    `STATUS` ENUM('PENDENTE','APROVADO','REPROVADO') DEFAULT 'APROVADO' NOT NULL,
    `EMAIL` VARCHAR(200) NOT NULL,
    `SENHA` VARCHAR(100) NOT NULL,
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
    `RECUPERACAO_SENHA_TOKEN` VARCHAR(45) COMMENT 'Token unico utilizado para identificar o usuario que solicitou a nova senha',
    `RECUPERACAO_SENHA_EXPIRACAO` DATETIME COMMENT 'Data maxima em que o usuario podera utilizar o token para recuperar a senha',
    PRIMARY KEY (`ID`),
    UNIQUE INDEX `TCLIE_UK_01` (`EMAIL`),
    UNIQUE INDEX `TCLIE_UK_02` (`CPF_CNPJ`)
) ENGINE=InnoDB;

-- ---------------------------------------------------------------------
-- qp1_configuracao
-- ---------------------------------------------------------------------

DROP TABLE IF EXISTS `qp1_configuracao`;

CREATE TABLE `qp1_configuracao`
(
    `ID` INTEGER NOT NULL AUTO_INCREMENT,
    `VALOR_MIN_PARCELAS` FLOAT(12,2) NOT NULL,
    `NUMERO_MAX_PARCELAS` INTEGER(3) NOT NULL,
    `COBRAR_JUROS` TINYINT(1) DEFAULT 0 NOT NULL,
    `DESCONTO_VISTA` TINYINT(1) DEFAULT 0 NOT NULL,
    `EMAIL_ADMIN` VARCHAR(200) NOT NULL,
    `CEP_ADMIN` VARCHAR(8) NOT NULL,
    `VALOR_INDICACAO` DOUBLE,
    `MANUTENCAO` INTEGER(1) DEFAULT 0,
    `COMENTARIO_APROVACAO` ENUM('APROVADO','REPROVADO','PENDENTE') DEFAULT 'PENDENTE' NOT NULL,
    `LOGO_MANUTENCAO` VARCHAR(50),
    `URL_BLOG` VARCHAR(100),
    `ANALYTICS_LOGIN` VARCHAR(50),
    `ANALYTICS_SENHA` VARCHAR(20),
    `ANALYTICS_ID` VARCHAR(20),
    `PAGSEGURO_LOGIN` VARCHAR(200),
    `PAGSEGURO_TOKEN` VARCHAR(50),
    `GOOGLE_APPS_LOGIN` VARCHAR(200),
    `GOOGLE_APPS_SENHA` VARCHAR(50),
    `LOCAWEB_IDENTIFICACAO` INTEGER(10),
    `LOCAWEB_TOKEN` VARCHAR(50),
    `FRETE_REGIAO` VARCHAR(100) COMMENT 'Texto para apresentar na barra da home. Ex: Sul e Suldeste',
    `VALOR_ACIMA` DOUBLE COMMENT 'Valor para apresentar na barra da home. Frete Grátis acima de Valor \'this\'',
    `MOSTRAR_INDICACAO` TINYINT(1) DEFAULT 0 NOT NULL,
    `MOSTRAR_AVALIACAO` TINYINT(1) DEFAULT 0 NOT NULL,
    `MOSTRAR_COMENTARIOS` TINYINT(1) DEFAULT 0 NOT NULL,
    PRIMARY KEY (`ID`)
) ENGINE=MyISAM;

-- ---------------------------------------------------------------------
-- qp1_conteudo
-- ---------------------------------------------------------------------

DROP TABLE IF EXISTS `qp1_conteudo`;

CREATE TABLE `qp1_conteudo`
(
    `ID` INTEGER NOT NULL AUTO_INCREMENT,
    `NOME` VARCHAR(200) NOT NULL,
    `DESCRICAO` TEXT NOT NULL,
    `IMAGEM` VARCHAR(50),
    `GALERIA_ID` INTEGER,
    `POSSUI_IMAGEM` TINYINT(1) DEFAULT 0,
    `POSSUI_GALERIA` TINYINT(1) DEFAULT 0,
    `TIPO_CONTEUDO` ENUM('PAGINA','TEXTO_LOCALIZADO'),
    PRIMARY KEY (`ID`),
    INDEX `TCONT_TGALE_FK_IDX_01` (`GALERIA_ID`),
    CONSTRAINT `TCONT_TGALE_FK_01`
        FOREIGN KEY (`GALERIA_ID`)
        REFERENCES `qp1_galeria` (`ID`)
        ON UPDATE SET NULL
        ON DELETE SET NULL
) ENGINE=InnoDB;

-- ---------------------------------------------------------------------
-- qp1_empresa
-- ---------------------------------------------------------------------

DROP TABLE IF EXISTS `qp1_empresa`;

CREATE TABLE `qp1_empresa`
(
    `ID` INTEGER NOT NULL AUTO_INCREMENT,
    `NOME` VARCHAR(200) NOT NULL,
    `ENDERECO` TEXT,
    `NUMERO` VARCHAR(10),
    `COMPLEMENTO` VARCHAR(100),
    `BAIRRO` VARCHAR(100),
    `CIDADE` VARCHAR(100),
    `CEP` VARCHAR(9),
    `ESTADO_ID` INTEGER,
    `TELEFONE` VARCHAR(100),
    `FAX` VARCHAR(100),
    `EMAIL` VARCHAR(200) NOT NULL,
    `MOSTRAR_MAPA` TINYINT(1) DEFAULT 1 NOT NULL,
    `GOOGLE_MAPS` TEXT,
    `HORARIO` TEXT,
    `DESCRICAO_EMPRESA` TEXT,
    `PALAVRAS_CHAVE` TEXT,
    PRIMARY KEY (`ID`),
    INDEX `TEMPR_TESTA_IDX_01` (`ESTADO_ID`),
    CONSTRAINT `TEMPR_TESTA_FK_01`
        FOREIGN KEY (`ESTADO_ID`)
        REFERENCES `qp1_estado` (`ID`)
        ON UPDATE SET NULL
        ON DELETE SET NULL
) ENGINE=InnoDB;

-- ---------------------------------------------------------------------
-- qp1_endereco
-- ---------------------------------------------------------------------

DROP TABLE IF EXISTS `qp1_endereco`;

CREATE TABLE `qp1_endereco`
(
    `ID` INTEGER NOT NULL AUTO_INCREMENT,
    `IDENTIFICACAO` VARCHAR(50) COMMENT 'Nome do endereço',
    `CEP` VARCHAR(10) NOT NULL,
    `ENDERECO` VARCHAR(200) NOT NULL,
    `NUMERO` VARCHAR(20) NOT NULL,
    `BAIRRO` VARCHAR(150) NOT NULL,
    `TELEFONE1` VARCHAR(40) NOT NULL,
    `TELEFONE2` VARCHAR(45),
    `COMPLEMENTO` VARCHAR(200),
    `TIPO` ENUM('PRINCIPAL','ENTREGA') DEFAULT 'PRINCIPAL' NOT NULL,
    `CLIENTE_ID` INTEGER NOT NULL,
    `CIDADE_ID` INTEGER(10) NOT NULL,
    `DATA_EXCLUSAO` DATETIME,
    PRIMARY KEY (`ID`),
    INDEX `TENDE_TCLIE_FK_IDX_01` (`CLIENTE_ID`),
    INDEX `TENDE_TCIDA_FK_IDX_01` (`CIDADE_ID`),
    CONSTRAINT `TENDE_TCLIE_FK_01`
        FOREIGN KEY (`CLIENTE_ID`)
        REFERENCES `qp1_cliente` (`ID`)
        ON UPDATE CASCADE
        ON DELETE CASCADE,
    CONSTRAINT `TENDE_TCIDA_FK_01`
        FOREIGN KEY (`CIDADE_ID`)
        REFERENCES `qp1_cidade` (`ID`)
        ON UPDATE RESTRICT
        ON DELETE RESTRICT
) ENGINE=InnoDB;

-- ---------------------------------------------------------------------
-- qp1_faq
-- ---------------------------------------------------------------------

DROP TABLE IF EXISTS `qp1_faq`;

CREATE TABLE `qp1_faq`
(
    `ID` INTEGER(11) NOT NULL AUTO_INCREMENT,
    `NOME` VARCHAR(200) NOT NULL COMMENT 'Nome do visitante',
    `EMAIL` VARCHAR(200) NOT NULL COMMENT 'Email do visitante',
    `PERGUNTA` VARCHAR(255) NOT NULL COMMENT 'Pergunta',
    `RESPOSTA` TEXT(200) COMMENT 'Resposta',
    `DATA_PERGUNTA` DATETIME NOT NULL,
    `DATA_RESPOSTA` DATETIME,
    `ORDEM` INTEGER(5) COMMENT 'Ordem da pergunta',
    `MOSTRAR` TINYINT(1) DEFAULT 0 NOT NULL COMMENT 'Mostrar no site?',
    `ENVIADO` TINYINT(1) DEFAULT 0 NOT NULL COMMENT 'Já foi enviado email com a resposta ao cliente?',
    PRIMARY KEY (`ID`)
) ENGINE=MyISAM COMMENT='Perguntas Frequentes';

-- ---------------------------------------------------------------------
-- qp1_galeria
-- ---------------------------------------------------------------------

DROP TABLE IF EXISTS `qp1_galeria`;

CREATE TABLE `qp1_galeria`
(
    `ID` INTEGER(11) NOT NULL AUTO_INCREMENT,
    `NOME` VARCHAR(200) NOT NULL COMMENT 'Nome da galeria de imagens',
    PRIMARY KEY (`ID`)
) ENGINE=InnoDB COMMENT='Galerias de imagem';

-- ---------------------------------------------------------------------
-- qp1_grafico
-- ---------------------------------------------------------------------

DROP TABLE IF EXISTS `qp1_grafico`;

CREATE TABLE `qp1_grafico`
(
    `ID` INTEGER NOT NULL AUTO_INCREMENT,
    `TIPO` ENUM('VOLUME','VENDAS', 'ANALYTICS') DEFAULT 'ANALYTICS' NOT NULL,
    `GRAFICO_STRING` TEXT,
    `DATA_CRIACAO` DATETIME NOT NULL,
    `DATA_ATUALIZACAO` DATETIME,
    PRIMARY KEY (`ID`)
) ENGINE=InnoDB;

-- ---------------------------------------------------------------------
-- qp1_historico
-- ---------------------------------------------------------------------

DROP TABLE IF EXISTS `qp1_historico`;

CREATE TABLE `qp1_historico`
(
    `ID` INTEGER NOT NULL AUTO_INCREMENT,
    `DATA` DATETIME NOT NULL,
    `SITUACAO_ID` INTEGER NOT NULL,
    `PEDIDO_ID` INTEGER NOT NULL,
    `ENVIADO` TINYINT(1) DEFAULT 0 NOT NULL,
    PRIMARY KEY (`ID`),
    INDEX `THIST_TPEDI_FK_IDX_01` (`PEDIDO_ID`),
    INDEX `THIST_TSITU_FK_IDX_01` (`SITUACAO_ID`),
    CONSTRAINT `THIST_TPEDI_FK_01`
        FOREIGN KEY (`PEDIDO_ID`)
        REFERENCES `qp1_pedido` (`ID`)
        ON UPDATE CASCADE
        ON DELETE CASCADE,
    CONSTRAINT `THIST_TSITU_FK_01`
        FOREIGN KEY (`SITUACAO_ID`)
        REFERENCES `qp1_situacao` (`ID`)
        ON UPDATE RESTRICT
        ON DELETE RESTRICT
) ENGINE=InnoDB;

-- ---------------------------------------------------------------------
-- qp1_imagem
-- ---------------------------------------------------------------------

DROP TABLE IF EXISTS `qp1_imagem`;

CREATE TABLE `qp1_imagem`
(
    `ID` INTEGER(11) NOT NULL AUTO_INCREMENT,
    `NOME` VARCHAR(50) COMMENT 'Nome da imagem',
    `IMAGEM` VARCHAR(50) NOT NULL COMMENT 'Arquivo da imagem',
    `GALERIA_ID` INTEGER(11) NOT NULL,
    PRIMARY KEY (`ID`),
    INDEX `TIMAG_TGALE_FK_IDX_01` (`GALERIA_ID`),
    CONSTRAINT `TIMAG_TGALE_FK_01`
        FOREIGN KEY (`GALERIA_ID`)
        REFERENCES `qp1_galeria` (`ID`)
        ON UPDATE CASCADE
        ON DELETE CASCADE
) ENGINE=InnoDB COMMENT='Tabela que grava as imagens da galeria';

-- ---------------------------------------------------------------------
-- qp1_newsletter
-- ---------------------------------------------------------------------

DROP TABLE IF EXISTS `qp1_newsletter`;

CREATE TABLE `qp1_newsletter`
(
    `ID` BIGINT(20) NOT NULL AUTO_INCREMENT,
    `NOME` VARCHAR(200) NOT NULL COMMENT 'Nome da pessoa',
    `EMAIL` VARCHAR(200) NOT NULL COMMENT 'Email cadastrado',
    `DATA_CADASTRO` DATETIME NOT NULL COMMENT 'Data do cadastro',
    `DATA_ATUALIZACAO` DATETIME NOT NULL COMMENT 'Data de atualização',
    PRIMARY KEY (`ID`),
    UNIQUE INDEX `TNEWS_UK_01` (`EMAIL`)
) ENGINE=MyISAM;

-- ---------------------------------------------------------------------
-- qp1_noticia
-- ---------------------------------------------------------------------

DROP TABLE IF EXISTS `qp1_noticia`;

CREATE TABLE `qp1_noticia`
(
    `ID` INTEGER(11) NOT NULL AUTO_INCREMENT,
    `TITULO` VARCHAR(255) NOT NULL COMMENT 'Titulo da Noticia',
    `KEY` VARCHAR(255) NOT NULL COMMENT 'Slug da Noticia',
    `DATA` DATE NOT NULL COMMENT 'Data da Noticia',
    `DESCRICAO` TEXT NOT NULL COMMENT 'Texto da Noticia',
    `IMAGEM` VARCHAR(50) NOT NULL COMMENT 'Imagem da Noticia',
    `GALERIA_ID` INTEGER(11),
    PRIMARY KEY (`ID`),
    INDEX `TNOTI_TGALE_FK_IDX_01` (`GALERIA_ID`),
    INDEX `TNOTI_UK_01` (`KEY`),
    CONSTRAINT `TNOTI_TGALE_FK_01`
        FOREIGN KEY (`GALERIA_ID`)
        REFERENCES `qp1_galeria` (`ID`)
        ON UPDATE SET NULL
        ON DELETE SET NULL
) ENGINE=InnoDB COMMENT='Tabela de Noticias';

-- ---------------------------------------------------------------------
-- qp1_produto_categoria
-- ---------------------------------------------------------------------

DROP TABLE IF EXISTS `qp1_produto_categoria`;

CREATE TABLE `qp1_produto_categoria`
(
    `ID` INTEGER(10) NOT NULL AUTO_INCREMENT,
    `NOME` VARCHAR(250) NOT NULL COMMENT 'Nome da categoria',
    `KEY` VARCHAR(250) NOT NULL COMMENT 'Slug da categoria',
    `ORDEM` INTEGER DEFAULT 0 COMMENT 'Ordem da Categoria',
    `DESTAQUE` TINYINT(1) DEFAULT 0 COMMENT 'Categoria em destaque?',
    `DATA_EXCLUSAO` DATETIME COMMENT 'Data de exclução',
    `NR_LFT` INTEGER,
    `NR_RGT` INTEGER,
    `NR_LVL` INTEGER,
    PRIMARY KEY (`ID`)
) ENGINE=InnoDB COMMENT='Categoria de Produtos';

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
    `DESCRICAO` TEXT NOT NULL,
    `IP` VARCHAR(100),
    `STATUS` ENUM('APROVADO','REPROVADO','PENDENTE') DEFAULT 'PENDENTE' NOT NULL,
    `CLIENTE_ID` INTEGER,
    `NOTA` TINYINT(1),
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
-- qp1_produto_visitado
-- ---------------------------------------------------------------------

DROP TABLE IF EXISTS `qp1_produto_visitado`;

CREATE TABLE `qp1_produto_visitado`
(
    `ID` INTEGER(11) NOT NULL AUTO_INCREMENT,
    `DATA_VISITADO` DATETIME NOT NULL COMMENT 'Última data que o produto foi visitado',
    `PRODUTO_ID` INTEGER(11) NOT NULL,
    `CLIENTE_ID` INTEGER(11) NOT NULL,
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
-- qp1_produto_favorito
-- ---------------------------------------------------------------------

DROP TABLE IF EXISTS `qp1_produto_favorito`;

CREATE TABLE `qp1_produto_favorito`
(
    `ID` INTEGER(11) NOT NULL AUTO_INCREMENT,
    `DATA` DATE NOT NULL COMMENT 'Data que foi adicionado aos favoritos',
    `PRODUTO_ID` INTEGER(11) NOT NULL,
    `CLIENTE_ID` INTEGER(11) NOT NULL,
    PRIMARY KEY (`ID`),
    INDEX `TPRFA_TCLIE_FK_IDX_01` (`CLIENTE_ID`),
    INDEX `TPRFA_TPROD_FK_IDX_01` (`PRODUTO_ID`),
    CONSTRAINT `TPRFA_TCLIE_FK_01`
        FOREIGN KEY (`CLIENTE_ID`)
        REFERENCES `qp1_cliente` (`ID`)
        ON UPDATE CASCADE
        ON DELETE CASCADE,
    CONSTRAINT `TPRFA_TPROD_FK_01`
        FOREIGN KEY (`PRODUTO_ID`)
        REFERENCES `qp1_produto` (`ID`)
        ON UPDATE CASCADE
        ON DELETE CASCADE
) ENGINE=InnoDB;

-- ---------------------------------------------------------------------
-- qp1_modelo
-- ---------------------------------------------------------------------

DROP TABLE IF EXISTS `qp1_modelo`;

CREATE TABLE `qp1_modelo`
(
    `ID` INTEGER NOT NULL AUTO_INCREMENT,
    `NOME` VARCHAR(50) NOT NULL,
    `LAYOUT_ID` INTEGER NOT NULL,
    `DATA_CRIACAO` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `DATA_ATUALIZACAO` DATETIME,
    PRIMARY KEY (`ID`),
    INDEX `TMODE_TLAYO_FK_IDX_01` (`LAYOUT_ID`),
    CONSTRAINT `TMODE_TLAYO_FK_01`
        FOREIGN KEY (`LAYOUT_ID`)
        REFERENCES `qp1_layout` (`ID`)
        ON UPDATE RESTRICT
        ON DELETE RESTRICT
) ENGINE=InnoDB;

-- ---------------------------------------------------------------------
-- qp1_opcao
-- ---------------------------------------------------------------------

DROP TABLE IF EXISTS `qp1_opcao`;

CREATE TABLE `qp1_opcao`
(
    `ID` INTEGER NOT NULL AUTO_INCREMENT,
    `NOME` VARCHAR(50) NOT NULL,
    `DATA_CRIACAO` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `DATA_ATUALIZACAO` DATETIME,
    `MODELO_ID` INTEGER NOT NULL,
    `IS_COR` TINYINT(1) DEFAULT 0 NOT NULL,
    `POSSUI_DESCRICAO` TINYINT(1) DEFAULT 0 NOT NULL,
    PRIMARY KEY (`ID`),
    INDEX `TOPCA_TMODE_FK_IDX_01` (`MODELO_ID`),
    CONSTRAINT `TOPCA_TMODE_FK_01`
        FOREIGN KEY (`MODELO_ID`)
        REFERENCES `qp1_modelo` (`ID`)
        ON UPDATE CASCADE
        ON DELETE CASCADE
) ENGINE=InnoDB;

-- ---------------------------------------------------------------------
-- qp1_opcao_valor
-- ---------------------------------------------------------------------

DROP TABLE IF EXISTS `qp1_opcao_valor`;

CREATE TABLE `qp1_opcao_valor`
(
    `ID` INTEGER NOT NULL AUTO_INCREMENT,
    `NOME` VARCHAR(50) NOT NULL,
    `NOME_EXIBICAO` VARCHAR(200) NOT NULL,
    `DESCRICAO` TEXT,
    `OPCAO_ID` INTEGER NOT NULL,
    `ORDEM` INTEGER DEFAULT 0 NOT NULL,
    `BIBLIOTECA_COR_ID` INTEGER,
    PRIMARY KEY (`ID`),
    INDEX `TOPVL_TOPCA_FK_IDX_01` (`OPCAO_ID`),
    INDEX `TOPVL_TBICO_FK_01` (`BIBLIOTECA_COR_ID`),
    CONSTRAINT `TOPVL_TOPCA_FK_01`
        FOREIGN KEY (`OPCAO_ID`)
        REFERENCES `qp1_opcao` (`ID`)
        ON UPDATE CASCADE
        ON DELETE CASCADE,
    CONSTRAINT `TOPVL_TBICO_FK_01`
        FOREIGN KEY (`BIBLIOTECA_COR_ID`)
        REFERENCES `qp1_biblioteca_cor` (`ID`)
        ON UPDATE CASCADE
        ON DELETE CASCADE
) ENGINE=InnoDB;

-- ---------------------------------------------------------------------
-- qp1_propriedade
-- ---------------------------------------------------------------------

DROP TABLE IF EXISTS `qp1_propriedade`;

CREATE TABLE `qp1_propriedade`
(
    `ID` INTEGER NOT NULL AUTO_INCREMENT,
    `NOME` VARCHAR(50) NOT NULL,
    `NOME_EXIBICAO` VARCHAR(200) NOT NULL,
    `DATA_CRIACAO` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `DATA_ATUALIZACAO` DATETIME,
    PRIMARY KEY (`ID`)
) ENGINE=InnoDB;

-- ---------------------------------------------------------------------
-- qp1_modelo_propriedade
-- ---------------------------------------------------------------------

DROP TABLE IF EXISTS `qp1_modelo_propriedade`;

CREATE TABLE `qp1_modelo_propriedade`
(
    `ID` INTEGER NOT NULL AUTO_INCREMENT,
    `MODELO_ID` INTEGER NOT NULL,
    `PROPRIEDADE_ID` INTEGER NOT NULL,
    PRIMARY KEY (`ID`),
    INDEX `TMOPR_TPROP_FK_IDX_01` (`PROPRIEDADE_ID`),
    INDEX `TMOPR_TMODE_FK_IDX_01` (`MODELO_ID`),
    CONSTRAINT `TMOPR_TPROP_FK_01`
        FOREIGN KEY (`PROPRIEDADE_ID`)
        REFERENCES `qp1_propriedade` (`ID`)
        ON UPDATE CASCADE
        ON DELETE CASCADE,
    CONSTRAINT `TMOPR_TMODE_FK_01`
        FOREIGN KEY (`MODELO_ID`)
        REFERENCES `qp1_modelo` (`ID`)
        ON UPDATE CASCADE
        ON DELETE CASCADE
) ENGINE=InnoDB;

-- ---------------------------------------------------------------------
-- qp1_produto_modelo_combinacao
-- ---------------------------------------------------------------------

DROP TABLE IF EXISTS `qp1_produto_modelo_combinacao`;

CREATE TABLE `qp1_produto_modelo_combinacao`
(
    `ID` INTEGER NOT NULL AUTO_INCREMENT,
    `MODELO_ID` INTEGER NOT NULL,
    `PRODUTO_ID` INTEGER NOT NULL,
    `REFERENCIA` VARCHAR(50),
    `VALOR` FLOAT(12,2),
    `VALOR_DESCONTO_UNITARIO` FLOAT(12,2),
    `PESO` INTEGER(11),
    `QTD_ESTOQUE` INTEGER(11),
    `QTD_MIN_ESTOQUE` INTEGER(11),
    `ATIVO` TINYINT(1) DEFAULT 0 NOT NULL,
    `DATA_EXCLUSAO` DATETIME,
    PRIMARY KEY (`ID`),
    INDEX `TPMCO_TPROD_FK_IDX_01` (`PRODUTO_ID`),
    INDEX `TPMCO_TMODE_FK_IDX_01` (`MODELO_ID`),
    CONSTRAINT `TPMCO_TPROD_FK_01`
        FOREIGN KEY (`PRODUTO_ID`)
        REFERENCES `qp1_produto` (`ID`)
        ON UPDATE CASCADE
        ON DELETE CASCADE,
    CONSTRAINT `TPMCO_TMODE_FK_01`
        FOREIGN KEY (`MODELO_ID`)
        REFERENCES `qp1_modelo` (`ID`)
        ON UPDATE CASCADE
        ON DELETE CASCADE
) ENGINE=InnoDB;

-- ---------------------------------------------------------------------
-- qp1_pmc_opcao_valor
-- ---------------------------------------------------------------------

DROP TABLE IF EXISTS `qp1_pmc_opcao_valor`;

CREATE TABLE `qp1_pmc_opcao_valor`
(
    `ID` INTEGER NOT NULL AUTO_INCREMENT,
    `PRODUTO_MODELO_COMBINACAO_ID` INTEGER NOT NULL,
    `OPCAO_VALOR_ID` INTEGER NOT NULL,
    `DESCRICAO` TEXT,
    `DATA_EXCLUSAO` DATETIME,
    PRIMARY KEY (`ID`),
    INDEX `TPMCO_TPMC_FK_IDX_01` (`PRODUTO_MODELO_COMBINACAO_ID`),
    INDEX `TPMCO_TOPVL_FK_IDX_01` (`OPCAO_VALOR_ID`),
    CONSTRAINT `TPMCO_TPMC_FK_01`
        FOREIGN KEY (`PRODUTO_MODELO_COMBINACAO_ID`)
        REFERENCES `qp1_produto_modelo_combinacao` (`ID`)
        ON UPDATE CASCADE
        ON DELETE CASCADE,
    CONSTRAINT `TPMCO_TOPVL_FK_01`
        FOREIGN KEY (`OPCAO_VALOR_ID`)
        REFERENCES `qp1_opcao_valor` (`ID`)
        ON UPDATE RESTRICT
        ON DELETE RESTRICT
) ENGINE=InnoDB;

-- ---------------------------------------------------------------------
-- qp1_produto
-- ---------------------------------------------------------------------

DROP TABLE IF EXISTS `qp1_produto`;

CREATE TABLE `qp1_produto`
(
    `ID` INTEGER NOT NULL AUTO_INCREMENT,
    `REFERENCIA` VARCHAR(50),
    `NOME` VARCHAR(200) NOT NULL,
    `IMAGEM` VARCHAR(50),
    `KEY` VARCHAR(255) NOT NULL,
    `DESCRICAO` TEXT NOT NULL,
    `CARACTERISTICAS` TEXT,
    `ATIVO` TINYINT(1) DEFAULT 0 NOT NULL,
    `DESTAQUE` TINYINT(1) DEFAULT 0 NOT NULL,
    `VALOR` FLOAT(12,2) NOT NULL,
    `VALOR_DESCONTO_UNITARIO` FLOAT(12,2) DEFAULT 0,
    `PESO` INTEGER(11) DEFAULT 0 NOT NULL,
    `QTD_ESTOQUE` INTEGER(11) DEFAULT 1 NOT NULL,
    `QTD_MIN_ESTOQUE` INTEGER(11) DEFAULT 0 NOT NULL,
    `DATA_CRIACAO` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `DATA_ATUALIZACAO` DATETIME,
    `DATA_EXCLUSAO` DATETIME,
    `CATEGORIA_ID` INTEGER NOT NULL,
    `MARCA_ID` INTEGER NOT NULL,
    `MODELO_ID` INTEGER,
    PRIMARY KEY (`ID`),
    UNIQUE INDEX `TPROD_UK_01` (`REFERENCIA`),
    INDEX `TPROD_TPRCA_FK_IDX_01` (`CATEGORIA_ID`),
    INDEX `TPROD_TMARC_FK_IDX_01` (`MARCA_ID`),
    INDEX `TPROD_TMODE_FK_IDX_01` (`MODELO_ID`),
    CONSTRAINT `TPROD_TPRCA_FK_01`
        FOREIGN KEY (`CATEGORIA_ID`)
        REFERENCES `qp1_produto_categoria` (`ID`)
        ON UPDATE RESTRICT
        ON DELETE RESTRICT,
    CONSTRAINT `TPROD_TMARC_FK_01`
        FOREIGN KEY (`MARCA_ID`)
        REFERENCES `qp1_marca` (`ID`),
    CONSTRAINT `TPROD_TMODE_FK_01`
        FOREIGN KEY (`MODELO_ID`)
        REFERENCES `qp1_modelo` (`ID`)
        ON UPDATE SET NULL
        ON DELETE SET NULL
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
    `DATA_EXCLUSAO` DATETIME,
    PRIMARY KEY (`ID`)
) ENGINE=InnoDB;

-- ---------------------------------------------------------------------
-- qp1_pedido
-- ---------------------------------------------------------------------

DROP TABLE IF EXISTS `qp1_pedido`;

CREATE TABLE `qp1_pedido`
(
    `ID` INTEGER NOT NULL AUTO_INCREMENT,
    `DATA` DATE NOT NULL,
    `VALOR` FLOAT(12,2) NOT NULL,
    `VALOR_TOTAL` FLOAT(12,2) NOT NULL,
    `VALOR_FRETE` FLOAT(12,2) NOT NULL,
    `VALOR_DESCONTO` FLOAT(12,2) NOT NULL,
    `FORMA_PAGAMENTO` ENUM('BOLETO','PAGSEGURO','VISA','MASTER','DEBITO') DEFAULT 'BOLETO' NOT NULL,
    `CLIENTE_ID` INTEGER NOT NULL,
    `SITUACAO` ENUM('ANDAMENTO','ALTERACAO','FINALIZADO','CANCELADO') DEFAULT 'ANDAMENTO' NOT NULL,
    `FRETE` ENUM('PAC','SEDEX','LOJA') DEFAULT 'PAC' NOT NULL,
    `ENDERECO_ID` INTEGER NOT NULL,
    `TID` VARCHAR(150),
    `QTD_PARCELA` INTEGER NOT NULL,
    `CODIGO_RASTREIO` VARCHAR(50),
    `NOVO` TINYINT(1) DEFAULT 1 NOT NULL,
    `CUPOM_ID` INTEGER(11),
    `CARRINHO_ID` INTEGER(11) NOT NULL,
    PRIMARY KEY (`ID`),
    INDEX `TPEDI_TCLIE_FK_IDX_01` (`CLIENTE_ID`),
    INDEX `TPEDI_TENDE_FK_IDX_01` (`ENDERECO_ID`),
    INDEX `TPEDI_TCUPO_FK_IDX_01` (`CUPOM_ID`),
    INDEX `TPEDI_TCARRI_FI_01` (`CARRINHO_ID`),
    CONSTRAINT `TPEDI_TCLIE_FK_01`
        FOREIGN KEY (`CLIENTE_ID`)
        REFERENCES `qp1_cliente` (`ID`)
        ON UPDATE CASCADE
        ON DELETE CASCADE,
    CONSTRAINT `TPEDI_TENDE_FK_01`
        FOREIGN KEY (`ENDERECO_ID`)
        REFERENCES `qp1_endereco` (`ID`)
        ON UPDATE RESTRICT
        ON DELETE RESTRICT,
    CONSTRAINT `TPEDI_TCUPO_FK_01`
        FOREIGN KEY (`CUPOM_ID`)
        REFERENCES `qp1_cupom` (`ID`)
        ON UPDATE RESTRICT
        ON DELETE RESTRICT,
    CONSTRAINT `TPEDI_TCARRI_FK_01`
        FOREIGN KEY (`CARRINHO_ID`)
        REFERENCES `qp1_carrinho` (`ID`)
        ON UPDATE RESTRICT
        ON DELETE RESTRICT
) ENGINE=InnoDB;

-- ---------------------------------------------------------------------
-- qp1_foto
-- ---------------------------------------------------------------------

DROP TABLE IF EXISTS `qp1_foto`;

CREATE TABLE `qp1_foto`
(
    `ID` INTEGER NOT NULL AUTO_INCREMENT,
    `LEGENDA` VARCHAR(200),
    `IMAGEM` VARCHAR(50) NOT NULL,
    `PRODUTO_ID` INTEGER NOT NULL,
    PRIMARY KEY (`ID`),
    INDEX `TFOTO_TPROD_FK_IDX_01` (`PRODUTO_ID`),
    CONSTRAINT `TFOTO_TPROD_FK_01`
        FOREIGN KEY (`PRODUTO_ID`)
        REFERENCES `qp1_produto` (`ID`)
) ENGINE=InnoDB;

-- ---------------------------------------------------------------------
-- qp1_vaga
-- ---------------------------------------------------------------------

DROP TABLE IF EXISTS `qp1_vaga`;

CREATE TABLE `qp1_vaga`
(
    `ID` INTEGER NOT NULL AUTO_INCREMENT,
    `NOME` VARCHAR(255) NOT NULL,
    `DESCRICAO` TEXT NOT NULL,
    `DATA` DATETIME NOT NULL,
    `MOSTRAR_SITE` TINYINT(1) DEFAULT 0 NOT NULL,
    PRIMARY KEY (`ID`)
) ENGINE=InnoDB;

-- ---------------------------------------------------------------------
-- qp1_rede
-- ---------------------------------------------------------------------

DROP TABLE IF EXISTS `qp1_rede`;

CREATE TABLE `qp1_rede`
(
    `ID` INTEGER(11) NOT NULL AUTO_INCREMENT,
    `NOME` VARCHAR(255) NOT NULL COMMENT 'Nome da rede',
    `IMAGEM` VARCHAR(50) NOT NULL COMMENT 'Arquivo de imagem da rede',
    `LINK` VARCHAR(255) NOT NULL COMMENT 'URL do profile do site na rede',
    `ORDEM` INTEGER(5) DEFAULT 0 NOT NULL COMMENT 'Ordem de exibição da rede',
    PRIMARY KEY (`ID`)
) ENGINE=MyISAM COMMENT='Redes sociais pertencentes ao site';

-- ---------------------------------------------------------------------
-- qp1_seo
-- ---------------------------------------------------------------------

DROP TABLE IF EXISTS `qp1_seo`;

CREATE TABLE `qp1_seo`
(
    `ID` INTEGER(11) NOT NULL AUTO_INCREMENT,
    `TIPO` ENUM('HOME','EMPRESA','PRODUTO','PROMOCAO','FAQ','CADASTRO','LOGIN','CARRINHO','NOTICIA','CENTRAL','CONTATO') NOT NULL COMMENT 'Tipo cadastro do SEO',
    `REGISTRO_ID` INTEGER(11) COMMENT 'Codigo de relacionamento do tipo com o cadastro do tipo',
    `TITULO` VARCHAR(65) COMMENT 'Titulo da pagina',
    `DESCRICAO` VARCHAR(150) COMMENT 'Descricao da pagina',
    `PALAVRAS_CHAVE` VARCHAR(255) COMMENT 'Palavras chave da pagina',
    PRIMARY KEY (`ID`)
) ENGINE=MyISAM COMMENT='Informações para os motores de busca';

-- ---------------------------------------------------------------------
-- qp1_situacao
-- ---------------------------------------------------------------------

DROP TABLE IF EXISTS `qp1_situacao`;

CREATE TABLE `qp1_situacao`
(
    `ID` INTEGER NOT NULL AUTO_INCREMENT,
    `ORDEM` INTEGER DEFAULT 0 NOT NULL,
    `NOME` VARCHAR(200) NOT NULL,
    `MENSAGEM` TEXT NOT NULL,
    `CODIGO_RASTREIO` INTEGER(1) NOT NULL,
    PRIMARY KEY (`ID`)
) ENGINE=InnoDB;

-- ---------------------------------------------------------------------
-- qp1_cupom
-- ---------------------------------------------------------------------

DROP TABLE IF EXISTS `qp1_cupom`;

CREATE TABLE `qp1_cupom`
(
    `ID` INTEGER(11) NOT NULL AUTO_INCREMENT,
    `CUPOM` VARCHAR(25) NOT NULL,
    `TIPO_DESCONTO` ENUM('REAIS','PORCENTAGEM') NOT NULL,
    `VALOR_DESCONTO` FLOAT(12,2) NOT NULL,
    `DATA_INICIAL` DATE NOT NULL,
    `DATA_FINAL` DATE,
    `VALOR_MINIMO_CARRINHO` FLOAT(12,2),
    `NUMERO_CLIENTES` INTEGER(6),
    `VALOR_MINIMO_COMPRA` FLOAT(12,2),
    `TIPO_CLIENTES` ENUM('ATIVOS','INATIVOS','TODOS'),
    `NUMERO_MESES_CADASTRO_CLIENTES` INTEGER(4),
    `NUMERO_MESES_ULTIMA_COMPRA` INTEGER(4),
    `ANIVERSARIANTES` TINYINT(1),
    `SEGUNDA_COMPRA` BOOLEAN,
    PRIMARY KEY (`ID`),
    UNIQUE INDEX `TCUPO_UK_01` (`CUPOM`)
) ENGINE=InnoDB;

-- ---------------------------------------------------------------------
-- qp1_cupom_cliente
-- ---------------------------------------------------------------------

DROP TABLE IF EXISTS `qp1_cupom_cliente`;

CREATE TABLE `qp1_cupom_cliente`
(
    `ID` INTEGER(11) NOT NULL AUTO_INCREMENT,
    `CUPOM_ID` INTEGER(11) NOT NULL,
    `CLIENTE_ID` INTEGER(11) NOT NULL,
    `UTILIZADO` TINYINT(1) DEFAULT 0 NOT NULL,
    PRIMARY KEY (`ID`),
    INDEX `TCUCL_TCUPO_FK_IDX_01` (`CUPOM_ID`),
    INDEX `TCUCL_TCLIE_FK_IDX_01` (`CLIENTE_ID`),
    CONSTRAINT `TCUCL_TCUPO_FK_01`
        FOREIGN KEY (`CUPOM_ID`)
        REFERENCES `qp1_cupom` (`ID`)
        ON UPDATE CASCADE
        ON DELETE CASCADE,
    CONSTRAINT `TCUCL_TCLIE_FK_01`
        FOREIGN KEY (`CLIENTE_ID`)
        REFERENCES `qp1_cliente` (`ID`)
        ON UPDATE CASCADE
        ON DELETE CASCADE
) ENGINE=InnoDB;

-- ---------------------------------------------------------------------
-- qp1_curriculo
-- ---------------------------------------------------------------------

DROP TABLE IF EXISTS `qp1_curriculo`;

CREATE TABLE `qp1_curriculo`
(
    `ID` INTEGER NOT NULL AUTO_INCREMENT,
    `NOME` VARCHAR(255) NOT NULL,
    `CPF` VARCHAR(30),
    `RG` VARCHAR(30),
    `EMAIL` VARCHAR(150) NOT NULL,
    `TELEFONE` VARCHAR(30),
    `CEP` VARCHAR(10) NOT NULL,
    `ENDERECO` VARCHAR(200),
    `BAIRRO` VARCHAR(200),
    `NUMERO` VARCHAR(10),
    `CIDADE_ID` INTEGER(10) NOT NULL,
    `AREA_INTERESSE` VARCHAR(200),
    `ARQUIVO_CURRICULO` VARCHAR(50) NOT NULL,
    `DATA_CADASTRO` DATETIME NOT NULL,
    `updated_at` DATETIME,
    PRIMARY KEY (`ID`),
    INDEX `TCURR_TCIDA_FK_IDX_01` (`CIDADE_ID`),
    CONSTRAINT `TCURR_TCIDA_FK_01`
        FOREIGN KEY (`CIDADE_ID`)
        REFERENCES `qp1_cidade` (`ID`)
        ON UPDATE RESTRICT
        ON DELETE RESTRICT
) ENGINE=InnoDB;

-- ---------------------------------------------------------------------
-- qp1_usuario
-- ---------------------------------------------------------------------

DROP TABLE IF EXISTS `qp1_usuario`;

CREATE TABLE `qp1_usuario`
(
    `ID` INTEGER(11) NOT NULL AUTO_INCREMENT,
    `NOME` VARCHAR(200) NOT NULL COMMENT 'Nome do usuário',
    `EMAIL` VARCHAR(200) NOT NULL COMMENT 'Email do usuário',
    `LOGIN` VARCHAR(50) NOT NULL COMMENT 'Login do usuário',
    `SENHA` VARCHAR(50) NOT NULL COMMENT 'Hash MD5 da senha do usuário',
    `ERROS_LOGIN` INTEGER COMMENT 'Quantidade erros login',
    PRIMARY KEY (`ID`),
    UNIQUE INDEX `TUSUA_UK_01` (`LOGIN`),
    UNIQUE INDEX `TUSUA_UK_02` (`EMAIL`)
) ENGINE=InnoDB COMMENT='Usuários do admin';

-- ---------------------------------------------------------------------
-- qp1_permissao
-- ---------------------------------------------------------------------

DROP TABLE IF EXISTS `qp1_permissao`;

CREATE TABLE `qp1_permissao`
(
    `USUARIO_ID` INTEGER NOT NULL,
    `AREA` INTEGER NOT NULL,
    PRIMARY KEY (`USUARIO_ID`,`AREA`),
    INDEX `TPERM_TUSUA_FK_IDX_01` (`USUARIO_ID`),
    CONSTRAINT `TPERM_TUSUA_FK_01`
        FOREIGN KEY (`USUARIO_ID`)
        REFERENCES `qp1_usuario` (`ID`)
        ON UPDATE CASCADE
        ON DELETE CASCADE
) ENGINE=InnoDB;

-- ---------------------------------------------------------------------
-- qp1_integracao
-- ---------------------------------------------------------------------

DROP TABLE IF EXISTS `qp1_integracao`;

CREATE TABLE `qp1_integracao`
(
    `ID` INTEGER(11) NOT NULL AUTO_INCREMENT,
    `PRODUTO_ID` INTEGER(10) NOT NULL,
    `TIPO` ENUM('UOL', 'GOOGLE', 'BUSCAPE') NOT NULL,
    PRIMARY KEY (`ID`),
    INDEX `TINTE_TPROD_FK_IDX_01` (`PRODUTO_ID`),
    CONSTRAINT `TINTE_TPROD_FK_01`
        FOREIGN KEY (`PRODUTO_ID`)
        REFERENCES `qp1_produto` (`ID`)
        ON UPDATE CASCADE
        ON DELETE CASCADE
) ENGINE=InnoDB;

-- ---------------------------------------------------------------------
-- qp1_frete
-- ---------------------------------------------------------------------

DROP TABLE IF EXISTS `qp1_frete`;

CREATE TABLE `qp1_frete`
(
    `ID` INTEGER(11) NOT NULL AUTO_INCREMENT,
    `GRATIS` TINYINT(1) DEFAULT 0 NOT NULL,
    `VALOR` FLOAT(12,2) DEFAULT 0 NOT NULL,
    PRIMARY KEY (`ID`)
) ENGINE=MyISAM COMMENT='Tabela de configurações de frete';

-- ---------------------------------------------------------------------
-- qp1_frete_cep
-- ---------------------------------------------------------------------

DROP TABLE IF EXISTS `qp1_frete_cep`;

CREATE TABLE `qp1_frete_cep`
(
    `ID` INTEGER(11) NOT NULL AUTO_INCREMENT,
    `TIPO_DESCONTO` ENUM('REAL','PORCENTAGEM') NOT NULL,
    `VALOR_DESCONTO_CAPITAL` FLOAT(12,2) NOT NULL,
    `VALOR_DESCONTO_INTERIOR` FLOAT(12,2) NOT NULL,
    `VALOR_MINIMO_COMPRA` FLOAT(12,2) NOT NULL,
    `FAIXA_INICIAL_CEP` INTEGER(8) NOT NULL,
    `FAIXA_FINAL_CEP` INTEGER(8) NOT NULL,
    `DATA_INICIAL` DATE NOT NULL,
    `DATA_FINAL` DATE,
    `ATIVO` TINYINT(1) NOT NULL,
    `CAPITAL` TINYINT(1) NOT NULL,
    PRIMARY KEY (`ID`)
) ENGINE=MyISAM COMMENT='Tabela dos descontos de frete para capital e interior com base em faixas de CEP';

-- ---------------------------------------------------------------------
-- qp1_faixa_cep
-- ---------------------------------------------------------------------

DROP TABLE IF EXISTS `qp1_faixa_cep`;

CREATE TABLE `qp1_faixa_cep`
(
    `ID` INTEGER(11) NOT NULL AUTO_INCREMENT,
    `NOME` VARCHAR(100) NOT NULL COMMENT 'Nome descritivo da faixa de CEPs',
    `FAIXA_INICIAL_CEP` INTEGER(8) NOT NULL,
    `FAIXA_FINAL_CEP` INTEGER(8) NOT NULL,
    PRIMARY KEY (`ID`)
) ENGINE=MyISAM COMMENT='Tabela de Faixas de CEP';

-- ---------------------------------------------------------------------
-- qp1_carrinho
-- ---------------------------------------------------------------------

DROP TABLE IF EXISTS `qp1_carrinho`;

CREATE TABLE `qp1_carrinho`
(
    `ID` INTEGER NOT NULL AUTO_INCREMENT,
    `BLOQUEADO` TINYINT(1) DEFAULT 0 NOT NULL,
    `CLIENTE_ID` INTEGER,
    `DATA_CRIACAO` DATETIME NOT NULL,
    `DATA_VALIDADE` DATETIME,
    PRIMARY KEY (`ID`),
    INDEX `TCARR_TCLIE_FK_IDX_01` (`CLIENTE_ID`),
    CONSTRAINT `TCARR_TCLIE_FK_01`
        FOREIGN KEY (`CLIENTE_ID`)
        REFERENCES `qp1_cliente` (`ID`)
        ON UPDATE CASCADE
        ON DELETE CASCADE
) ENGINE=InnoDB COMMENT='Carrinho';

-- ---------------------------------------------------------------------
-- qp1_item_carrinho
-- ---------------------------------------------------------------------

DROP TABLE IF EXISTS `qp1_item_carrinho`;

CREATE TABLE `qp1_item_carrinho`
(
    `ID` INTEGER NOT NULL AUTO_INCREMENT,
    `CARRINHO_ID` INTEGER NOT NULL,
    `PRODUTO_ID` INTEGER NOT NULL,
    `VARIACAO_ID` INTEGER,
    `QUANTIDADE_REQUISITADA` INTEGER DEFAULT 1 NOT NULL,
    `VALOR` FLOAT(10,2) DEFAULT 0 NOT NULL,
    `OBSERVACOES` TEXT,
    `DATA_CRIACAO` DATETIME NOT NULL,
    PRIMARY KEY (`ID`),
    INDEX `TCAIT_TCARR_FK_IDX_01` (`CARRINHO_ID`),
    INDEX `TCAIT_TPROD_FK_IDX_01` (`PRODUTO_ID`),
    INDEX `TCAIT_TPMC_FK_IDX_01` (`VARIACAO_ID`),
    CONSTRAINT `TCAIT_TCARR_FK_01`
        FOREIGN KEY (`CARRINHO_ID`)
        REFERENCES `qp1_carrinho` (`ID`)
        ON DELETE CASCADE,
    CONSTRAINT `TCAIT_TPROD_FK_01`
        FOREIGN KEY (`PRODUTO_ID`)
        REFERENCES `qp1_produto` (`ID`),
    CONSTRAINT `TCAIT_TPMC_FK_01`
        FOREIGN KEY (`VARIACAO_ID`)
        REFERENCES `qp1_produto_modelo_combinacao` (`ID`)
) ENGINE=InnoDB COMMENT='Listagem de itens do carrinho';

-- ---------------------------------------------------------------------
-- qp1_item_adicional_carrinho
-- ---------------------------------------------------------------------

DROP TABLE IF EXISTS `qp1_item_adicional_carrinho`;

CREATE TABLE `qp1_item_adicional_carrinho`
(
    `ID` INTEGER NOT NULL AUTO_INCREMENT,
    `ITEM_CARRINHO_ID` INTEGER NOT NULL,
    `ITEM_ADICIONAL_ID` INTEGER NOT NULL,
    `STATUS` ENUM('APROVADO','REPROVADO','PENDENTE'),
    PRIMARY KEY (`ID`),
    INDEX `TCAIA_TITCA_FK_IDX_01` (`ITEM_CARRINHO_ID`),
    INDEX `TCAIA_TITAD_FK_IDX_01` (`ITEM_ADICIONAL_ID`),
    CONSTRAINT `TCAIA_TITCA_FK_01`
        FOREIGN KEY (`ITEM_CARRINHO_ID`)
        REFERENCES `qp1_item_carrinho` (`ID`)
        ON UPDATE CASCADE
        ON DELETE CASCADE,
    CONSTRAINT `TCAIA_TITAD_FK_01`
        FOREIGN KEY (`ITEM_ADICIONAL_ID`)
        REFERENCES `qp1_item_adicional` (`ID`)
        ON UPDATE CASCADE
        ON DELETE CASCADE
) ENGINE=InnoDB COMMENT='Listagem de itens adicionais do carrinho';

-- ---------------------------------------------------------------------
-- qp1_item_adicional
-- ---------------------------------------------------------------------

DROP TABLE IF EXISTS `qp1_item_adicional`;

CREATE TABLE `qp1_item_adicional`
(
    `ID` INTEGER NOT NULL AUTO_INCREMENT,
    `NOME` VARCHAR(100),
    `VALOR` FLOAT(12,2) DEFAULT 0 NOT NULL,
    `CLASS_KEY` VARCHAR(50) NOT NULL,
    PRIMARY KEY (`ID`)
) ENGINE=InnoDB COMMENT='Itens Adicionais relacionados aos produtos';

-- ---------------------------------------------------------------------
-- qp1_embalagem
-- ---------------------------------------------------------------------

DROP TABLE IF EXISTS `qp1_embalagem`;

CREATE TABLE `qp1_embalagem`
(
    `DESCRICAO` TEXT NOT NULL,
    `ID` INTEGER NOT NULL,
    PRIMARY KEY (`ID`),
    CONSTRAINT `qp1_embalagem_FK_1`
        FOREIGN KEY (`ID`)
        REFERENCES `qp1_item_adicional` (`ID`)
        ON DELETE CASCADE
) ENGINE=InnoDB COMMENT='Tabela de emabalagens para produtos';

-- ---------------------------------------------------------------------
-- qp1_cartao
-- ---------------------------------------------------------------------

DROP TABLE IF EXISTS `qp1_cartao`;

CREATE TABLE `qp1_cartao`
(
    `DESCRICAO` TEXT NOT NULL,
    `ID` INTEGER NOT NULL,
    PRIMARY KEY (`ID`),
    CONSTRAINT `qp1_cartao_FK_1`
        FOREIGN KEY (`ID`)
        REFERENCES `qp1_item_adicional` (`ID`)
        ON DELETE CASCADE
) ENGINE=InnoDB COMMENT='Tabela de cartões para produtos';

-- ---------------------------------------------------------------------
-- qp1_faixa_desconto
-- ---------------------------------------------------------------------

DROP TABLE IF EXISTS `qp1_faixa_desconto`;

CREATE TABLE `qp1_faixa_desconto`
(
    `ID` INTEGER NOT NULL AUTO_INCREMENT,
    `NOME` VARCHAR(255) NOT NULL COMMENT 'Nome para identificação da faixa.',
    `QUANTIDADE_MINIMA` INTEGER NOT NULL COMMENT 'Quantidade mínima de produtos.',
    `QUANTIDADE_MAXIMA` INTEGER NOT NULL COMMENT 'Quantidade máxima de produtos.',
    `DESCONTO` FLOAT(5,2) NOT NULL COMMENT 'Porcentagem de desconto para a faixa.',
    PRIMARY KEY (`ID`)
) ENGINE=InnoDB;

-- ---------------------------------------------------------------------
-- qp1_produto_faixa_desconto
-- ---------------------------------------------------------------------

DROP TABLE IF EXISTS `qp1_produto_faixa_desconto`;

CREATE TABLE `qp1_produto_faixa_desconto`
(
    `ID` INTEGER NOT NULL AUTO_INCREMENT,
    `FAIXA_DESCONTO_ID` INTEGER NOT NULL,
    `PRODUTO_ID` INTEGER NOT NULL,
    `DESCONTO` FLOAT(5,2) COMMENT 'Porcentagem de desconto para a faixa.',
    PRIMARY KEY (`ID`),
    INDEX `TPRFD_TFADC_FK_IDX_01` (`FAIXA_DESCONTO_ID`),
    INDEX `TPRFD_TPROD_FK_IDX_01` (`PRODUTO_ID`),
    CONSTRAINT `TPRFD_TFADC_FK_01`
        FOREIGN KEY (`FAIXA_DESCONTO_ID`)
        REFERENCES `qp1_faixa_desconto` (`ID`)
        ON UPDATE CASCADE
        ON DELETE CASCADE,
    CONSTRAINT `TPRFD_TPROD_FK_01`
        FOREIGN KEY (`PRODUTO_ID`)
        REFERENCES `qp1_produto` (`ID`)
        ON UPDATE CASCADE
        ON DELETE CASCADE
) ENGINE=InnoDB;

-- ---------------------------------------------------------------------
-- qp1_layout
-- ---------------------------------------------------------------------

DROP TABLE IF EXISTS `qp1_layout`;

CREATE TABLE `qp1_layout`
(
    `ID` INTEGER NOT NULL AUTO_INCREMENT,
    `NOME` VARCHAR(200) NOT NULL,
    `ARQUIVO` VARCHAR(50) NOT NULL,
    PRIMARY KEY (`ID`)
) ENGINE=InnoDB;

-- ---------------------------------------------------------------------
-- qp1_biblioteca_cor
-- ---------------------------------------------------------------------

DROP TABLE IF EXISTS `qp1_biblioteca_cor`;

CREATE TABLE `qp1_biblioteca_cor`
(
    `ID` INTEGER NOT NULL AUTO_INCREMENT,
    `NOME` VARCHAR(200) NOT NULL,
    `RGB` VARCHAR(6),
    `IMAGEM` VARCHAR(50),
    PRIMARY KEY (`ID`)
) ENGINE=InnoDB;

-- ---------------------------------------------------------------------
-- qp1_venda_casada
-- ---------------------------------------------------------------------

DROP TABLE IF EXISTS `qp1_venda_casada`;

CREATE TABLE `qp1_venda_casada`
(
    `ID` INTEGER NOT NULL AUTO_INCREMENT,
    `NOME` VARCHAR(255) NOT NULL COMMENT 'Nome para identificação da venda casada.',
    `VALOR` FLOAT(12,2) NOT NULL COMMENT 'Valor para venda.',
    `VALOR_DESCONTO` FLOAT(12,2) NOT NULL COMMENT 'Valor do desconto',
    `ATIVO` TINYINT(1) DEFAULT 1 NOT NULL COMMENT 'Identifica se a promoção está ativa ou não. 1 - Ativo; 2 - Inativo;',
    `DATA_CADASTRO` DATETIME,
    PRIMARY KEY (`ID`)
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
    UNIQUE INDEX `qp1_produto_venda_casada_U_1` (`VENDA_CASADA_ID`, `PRODUTO_ID`),
    INDEX `TPRVC_TVECA_FK_IDX_01` (`VENDA_CASADA_ID`),
    INDEX `TPRVC_TPROD_FK_IDX_01` (`PRODUTO_ID`),
    CONSTRAINT `TPRVC_TVECA_FK_01`
        FOREIGN KEY (`VENDA_CASADA_ID`)
        REFERENCES `qp1_venda_casada` (`ID`)
        ON UPDATE CASCADE
        ON DELETE CASCADE,
    CONSTRAINT `TPRVC_TPROD_FK_01`
        FOREIGN KEY (`PRODUTO_ID`)
        REFERENCES `qp1_produto` (`ID`)
        ON UPDATE CASCADE
        ON DELETE CASCADE
) ENGINE=InnoDB;

# This restores the fkey checks, after having unset them earlier
SET FOREIGN_KEY_CHECKS = 1;
