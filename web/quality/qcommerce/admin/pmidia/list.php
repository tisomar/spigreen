<?php

$pageTitle = 'Fotos';
$_class = FotoPeer::OM_CLASS;

$preQuery = FotoQuery::create()->filterByProdutoId($_GET['reference'])->orderByCor()->orderById();

include QCOMMERCE_DIR . '/admin/_2015/load.page.php';
