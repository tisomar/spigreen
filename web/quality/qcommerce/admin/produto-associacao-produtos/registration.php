<?php
use QualityPress\QCommerce\Component\Association\Propel\AssociacaoProdutoProdutoPeer;
use QualityPress\QCommerce\Component\Association\Propel\AssociacaoProdutoPeer;

$context        = $container->getRequest()->query->get('context');
$reference      = $container->getRequest()->query->get('reference');
$associacao_id  = $container->getRequest()->query->get('associacao_id');

$objReference = ProdutoPeer::retrieveByPK($reference);
$objAssociacao = AssociacaoProdutoPeer::retrieveByPK($associacao_id);

$_class = AssociacaoProdutoProdutoPeer::getOMClass();

$pageTitle = 'Novos produtos';

include QCOMMERCE_DIR . '/admin/_2015/load.page.php';
