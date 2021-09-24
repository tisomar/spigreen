<?php

$pageTitle = 'Atributos';
$_class = ProdutoAtributoPeer::OM_CLASS;

// Obtém o contexto
$context = $_GET['context'];

$produto = ProdutoQuery::create()->findOneById($_GET['reference']);
if ($produto instanceof Produto && $produto->getTaxaCadastro()) {
    throw new Exception('Produto taxa não pode conter essa configuração.');
}

$preQuery = ProdutoAtributoQuery::create()->filterByProdutoId($_GET['reference']);

include QCOMMERCE_DIR . '/admin/_2015/load.page.php';
