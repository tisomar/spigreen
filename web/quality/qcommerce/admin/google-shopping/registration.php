<?php

$pageTitle = 'Google Shopping';
$_class = ProdutoPeer::OM_CLASS;

$produto = ProdutoQuery::create()->findOneById($_GET['reference']);

if ($produto instanceof Produto && $produto->getTaxaCadastro()) {
    throw new Exception('Produto taxa não pode conter essa configuração.');
}

include QCOMMERCE_DIR . '/admin/_2015/load.page.php';
