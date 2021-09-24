<?php

$rootDir = realpath(__DIR__ . DIRECTORY_SEPARATOR . '..');
require_once $rootDir . '/includes/include_propel_cron.inc.php';

require_once $rootDir . '/includes/funcoes/array_column.php';
require_once $rootDir . '/includes/helpers/phpQuery-onefile.php';

require_once $rootDir . '/includes/helpers/tag.inc.php';
require_once $rootDir . '/includes/helpers/format.inc.php';
require_once $rootDir . '/includes/helpers/util.inc.php';
require_once $rootDir . '/includes/helpers/atalhos.inc.php';
require_once $rootDir . '/includes/funcoes.inc.php';
//require_once $rootDir . '/includes/assets.inc.php';
//require_once QCOMMERCE_DIR . "/../src/Libs/guzzle-master/vendor/autoload.php";

//require_once $_SERVER['DOCUMENT_ROOT'] . BASE_PATH . '/resize/qualityResize.php';

// Trata as opções de filtros de produtos
//require_once $rootDir . '/produtos/actions/filtro.listagem.actions.php';

// Possibilitando salvar a newsletter em todas as páginas
//require_once $rootDir . '/newsletter/actions/newsletter.actions.php';
