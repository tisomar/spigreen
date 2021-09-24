<?php

$pageTitle = 'Atributos';
$_class = ProdutoAtributoPeer::OM_CLASS;

$context = $_GET['context'];

$relation_local = $context . 'Id';
$context_field = ProdutoAtributoPeer::translateFieldName($relation_local, BasePeer::TYPE_PHPNAME, BasePeer::TYPE_FIELDNAME);

$produto = ProdutoQuery::create()->findOneById($_GET['reference']);

if ($produto instanceof Produto && $produto->getTaxaCadastro()) {
    throw new Exception('Produto taxa não pode conter essa configuração.');
}

include QCOMMERCE_DIR . '/admin/_2015/load.page.php';
