<?php
use QPress\Template\Widget;
//use QualityPress\QCommerce\Component\Association\Propel;

$collAssociacaoProduto = \AssociacaoProdutoQuery::create()
    ->filterByProduto($objProduto)
    ->filterByDisponivel(true)
    ->orderByOrdem()
    ->find();

foreach ($collAssociacaoProduto as $objAssociacaoProduto) {

    switch ($objAssociacaoProduto->getType()) {

        case new \QPress\Component\Association\Product\Type\ProdutoRelacionadoType():

            Widget::render('produto-detalhe/produtos-relacionados-container', array(
                'objAssociacaoProduto' => $objAssociacaoProduto,
            ));

            break;

        case new \QPress\Component\Association\Product\Type\VendaCruzadaType():

            Widget::render('produto-detalhe/compre-junto-container', array(
                'objAssociacaoProduto' => $objAssociacaoProduto,
            ));

            break;
    }

}