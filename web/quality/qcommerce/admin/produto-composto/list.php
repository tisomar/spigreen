<?php

$pageTitle = 'Atributos';
$_class = ProdutoAtributoPeer::OM_CLASS;

// Obtém o contexto
$context = $_GET['context'];

$produto = ProdutoQuery::create()->findOneById($_GET['reference']);

$preQuery = ProdutoAtributoQuery::create()->filterByProdutoId($_GET['reference']);

include QCOMMERCE_DIR . '/admin/_2015/load.page.php';
