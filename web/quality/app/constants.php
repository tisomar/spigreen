<?php

// Definir environment
$env = (false === strpos(getenv('APPLICATION_ENV'), 'prod')) ? 'dev' : 'prod';
define('ENV', $env);

// Constantes de diretórios
define('DS', DIRECTORY_SEPARATOR);
define('BASE_ROOT', realpath(__DIR__ . DS . '..' . DS . '..'));
define('DIR_ROOT', realpath(__DIR__ . DS . '..'));
define('ROOT_DIR', DIR_ROOT);
define('APP_ROOT', DIR_ROOT . DS . 'app');
define('QCOMMERCE_DIR', DIR_ROOT . DS . 'qcommerce');
define('PROPEL_DIR', QCOMMERCE_DIR . DS . 'propel');