<?php

require_once __DIR__ . '/../vendor/autoload.php';

use Symfony\Component\Dotenv\Dotenv;

$dotenv = new Dotenv(true);
$dotenv->load(__DIR__ . '/.env');
Propel::init(QCOMMERCE_DIR . '/propel/build/conf/qcommerce-conf.php');
set_include_path(QCOMMERCE_DIR . '/propel/build/classes' . PATH_SEPARATOR . get_include_path());
require_once QCOMMERCE_DIR . '/propel/build/classes/qpress/util/QPropelPager.php';
