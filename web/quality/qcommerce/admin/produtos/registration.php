<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
$pageTitle = 'Produtos';
$_class = ProdutoPeer::OM_CLASS;
$_classPeer = $_class::PEER;

include QCOMMERCE_DIR . '/admin/_2015/load.page.php';
