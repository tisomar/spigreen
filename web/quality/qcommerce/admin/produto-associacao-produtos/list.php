<?php
//use QualityPress\QCommerce\Component\Association\Propel\AssociacaoProdutoProdutoQuery;
//use QualityPress\QCommerce\Component\Association\Propel\AssociacaoProdutoPeer;
//use QualityPress\QCommerce\Component\Association\Propel\AssociacaoProdutoProdutoPeer;

$context        = $container->getRequest()->query->get('context');
$reference      = $container->getRequest()->query->get('reference');
$associacao_id  = $container->getRequest()->query->get('associacao_id');

$pageTitle = 'Produtos associados';

$_class = AssociacaoProdutoProdutoPeer::getOMClass();

$objReference = ProdutoPeer::retrieveByPK($reference);
$objAssociacao = AssociacaoProdutoPeer::retrieveByPK($associacao_id);

$preQuery = AssociacaoProdutoProdutoQuery::create()
    ->filterByAssociacaoId($container->getRequest()->query->get('associacao_id'));

include QCOMMERCE_DIR . '/admin/_2015/load.page.php';
