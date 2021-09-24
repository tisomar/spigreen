<?php
$context    = $container->getRequest()->query->get('context');
$reference  = $container->getRequest()->query->get('reference');

$objReference = ProdutoPeer::retrieveByPK($reference);

if ($objReference instanceof Produto && $objReference->getTaxaCadastro()) {
    throw new Exception('Produto taxa não pode conter essa configuração.');
}

$pageTitle = 'Nova associação de produto';

$_class = AssociacaoProdutoPeer::getOMClass();

include QCOMMERCE_DIR . '/admin/_2015/load.page.php';
