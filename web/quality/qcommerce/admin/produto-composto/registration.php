<?php

$pageTitle = 'Produtos';
$_class = ProdutoPeer::OM_CLASS;

$produto = ProdutoQuery::create()->findOneById($_GET['reference']);

include QCOMMERCE_DIR . '/admin/_2015/load.page.php';
