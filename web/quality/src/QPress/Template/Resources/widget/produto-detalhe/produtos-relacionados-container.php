<?php
use QPress\Template\Widget;
use QPress\ProdutoAssociacao\ProdutoRelacionadoType;

if (!isset($objAssociacaoProduto) || !$objAssociacaoProduto instanceof \QualityPress\QCommerce\Component\Association\Propel\AssociacaoProduto) {
    throw new Exception('você precisa definir a variável $objAssociacaoProduto sendo instancia de \QualityPress\QCommerce\Component\Association\Propel\AssociacaoProduto');
}

$limit = isset($limit) ? $limit : 8;

$collProdutos = ProdutoQuery::create()
    ->useAssociacaoProdutoProdutoQuery()
        ->filterByAssociacaoId($objAssociacaoProduto->getId())
    ->endUse()
    ->filterByDisponivel(true)
    ->limit($limit)
    ->find();

if (count($collProdutos) > 0) {

    Widget::render('general/box-title', array(
        'title' => $objAssociacaoProduto->getNome()
    ));

    Widget::render('produto/product-list', array(
        'collProdutos' => $collProdutos,
        'addBuyButton' => true,
    ));
}
