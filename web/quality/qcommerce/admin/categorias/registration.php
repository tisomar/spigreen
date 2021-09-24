<?php

set_time_limit(0);
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);

ini_set('memory_limit', '1024M');
error_reporting(E_ALL);

$pageTitle = 'Categorias';
$_class = CategoriaPeer::OM_CLASS;
$_classQuery = 'CategoriaQuery';
$_classPeer = 'CategoriaPeer';

include QCOMMERCE_DIR . '/admin/_2015/load.page.php';
