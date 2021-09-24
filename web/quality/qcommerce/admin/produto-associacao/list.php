<?php
$context    = $container->getRequest()->query->get('context');
$reference  = $container->getRequest()->query->get('reference');

$objReference = ProdutoPeer::retrieveByPK($reference);

if ($objReference instanceof Produto && $objReference->getTaxaCadastro()) {
    throw new Exception('Produto taxa não pode conter essa configuração.');
}

$pageTitle = 'Associações criadas';

$_class = AssociacaoProdutoPeer::getOMClass();

$preQuery = AssociacaoProdutoQuery::create()
    ->filterByProdutoOrigemId($reference)
    ->orderByOrdem();

include QCOMMERCE_DIR . '/admin/_2015/load.page.php';
