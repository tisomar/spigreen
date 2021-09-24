<?php

require_once QCOMMERCE_DIR . '/includes/constantes.inc.php';
require_once QCOMMERCE_DIR . '/includes/include_propel.inc.php';
require_once QCOMMERCE_DIR . '/includes/funcoes/array_column.php';
require_once QCOMMERCE_DIR . '/includes/helpers/phpQuery-onefile.php';
require_once QCOMMERCE_DIR . '/includes/helpers/tag.inc.php';
require_once QCOMMERCE_DIR . '/includes/helpers/format.inc.php';
require_once QCOMMERCE_DIR . '/includes/helpers/util.inc.php';
require_once QCOMMERCE_DIR . '/includes/helpers/atalhos.inc.php';
require_once QCOMMERCE_DIR . '/includes/funcoes.inc.php';
require_once QCOMMERCE_DIR . '/includes/assets.inc.php';
//require_once QCOMMERCE_DIR . "/../src/Libs/guzzle-master/vendor/autoload.php";
include_once QCOMMERCE_DIR . '/classes/QPTranslator.php';
require_once $_SERVER['DOCUMENT_ROOT'] . BASE_PATH . '/resize/qualityResize.php';
// Trata as opções de filtros de produtos
require_once QCOMMERCE_DIR . '/produtos/actions/filtro.listagem.actions.php';
// Possibilitando salvar a newsletter em todas as páginas
require_once QCOMMERCE_DIR . '/newsletter/actions/newsletter.actions.php';
