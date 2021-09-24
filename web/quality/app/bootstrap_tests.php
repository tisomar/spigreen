<?php

require __DIR__.'/../vendor/autoload.php';

$request  = Symfony\Component\HttpFoundation\Request::createFromGlobals();

require_once QCOMMERCE_DIR . '/includes/constantes.inc.php';
require_once QCOMMERCE_DIR . '/includes/include_propel_tests.inc.php';

require_once QCOMMERCE_DIR . '/includes/funcoes/array_column.php';
require_once QCOMMERCE_DIR . '/includes/helpers/phpQuery-onefile.php';

require_once QCOMMERCE_DIR . '/includes/helpers/tag.inc.php';
require_once QCOMMERCE_DIR . '/includes/helpers/format.inc.php';
require_once QCOMMERCE_DIR . '/includes/helpers/util.inc.php';
require_once QCOMMERCE_DIR . '/includes/helpers/atalhos.inc.php';
require_once QCOMMERCE_DIR . '/includes/funcoes.inc.php';
require_once QCOMMERCE_DIR . '/includes/assets.inc.php';
