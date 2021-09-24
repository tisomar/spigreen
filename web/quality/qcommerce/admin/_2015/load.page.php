<?php

require_once QCOMMERCE_DIR . '/admin/includes/security.inc.php';
require_once QCOMMERCE_DIR . '/admin/includes/config.inc.php';

/**
 * Carrega o arquivo com as configurações do módulo
 */
if (file_exists(QCOMMERCE_DIR . '/admin/' . $router->getModule() . '/config/routes.php')) :
    require_once QCOMMERCE_DIR . '/admin/' . $router->getModule() . '/config/routes.php';
elseif (file_exists(QCOMMERCE_DIR . '/admin/' . basename(__DIR__) . '/config/routes.php')) :
    require_once QCOMMERCE_DIR . '/admin/' . basename(__DIR__) . '/config/routes.php';
endif;

/**
 * Carrega o arquivo com as ações do módulo
 */
if (file_exists(QCOMMERCE_DIR . '/admin/' . $router->getModule() .
    '/actions/' . $router->getAction() . '/action.php')) :
    require_once QCOMMERCE_DIR . '/admin/' . $router->getModule() . '/actions/' . $router->getAction() . '/action.php';
elseif (file_exists(
    QCOMMERCE_DIR . '/admin/' .
    basename(__DIR__) . '/actions/' . $router->getAction() . '/action.php'
)) :
    require_once QCOMMERCE_DIR . '/admin/' . basename(__DIR__) . '/actions/' . $router->getAction() . '/action.php';
endif;


/**
 * Carrega a view
 */
if (file_exists(QCOMMERCE_DIR . '/admin/' . $router->getModule() . '/views/' . $router->getAction() . '/view.php')) :
    require_once QCOMMERCE_DIR . '/admin/' . $router->getModule() . '/views/' . $router->getAction() . '/view.php';
elseif (file_exists(QCOMMERCE_DIR . '/admin/' . basename(__DIR__) . '/views/' . $router->getAction() . '/view.php')) :
    require_once QCOMMERCE_DIR . '/admin/' . basename(__DIR__) . '/views/' . $router->getAction() . '/view.php';
else :
    require_once QCOMMERCE_DIR . '/admin/' . basename(__DIR__) . '/views/default/view.php';
endif;
