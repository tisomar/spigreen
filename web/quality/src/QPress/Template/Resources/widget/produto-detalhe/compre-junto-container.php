<?php
use QPress\Template\Widget;
use QPress\Component\Association\Product\Type\VendaCruzadaType;
use QPress\Component\Association\Product\Manager\AssociationProductManager;

if (!isset($objAssociacaoProduto) || !$objAssociacaoProduto instanceof \QualityPress\QCommerce\Component\Association\Propel\AssociacaoProduto) {
    throw new Exception('você precisa definir a variável $objAssociacaoProduto sendo instancia de \QualityPress\QCommerce\Component\Association\Propel\AssociacaoProduto');
}

if (ClientePeer::isAuthenticad() || Config::get('clientes.ocultar_preco') == 0) {

    $limit = isset($limit) ? $limit : 2;

    // Encontra os produtos associados
    $collProdutos = ProdutoQuery::create()
        ->useProdutoVariacaoQuery()
            ->filterByDisponivel(true)
            ->groupBy('ProdutoVariacao.ProdutoId')
        ->endUse()
        ->useAssociacaoProdutoProdutoQuery()
            ->filterByAssociacaoId($objAssociacaoProduto->getId())
        ->endUse()
        ->filterByDisponivel(true)
        ->having('SUM(ProdutoVariacao.EstoqueAtual) > 0')
        ->limit($limit)
        ->find();

    /** @var $objProduto Produto */
    $objProduto = $objAssociacaoProduto->getProduto();

    // Total do valor a ser pago sem os descontos dos produtos
    $totalSemDesconto = $objProduto->getValorBase();

    // Total do valor a ser pago com os descontos dos produtos
    $totalComDesconto = $objProduto->getValor();

    // Lista com o ID dos produtos relacionados
    $relacionados = array();

    // Indica se é necessário abrir um modal para selecionar a variação de algum produto
    $needSelectVariation = $objProduto->hasVariacoes();

    if (count($collProdutos) > 0) {

        Widget::render('general/box-title', array(
            'title' => $objAssociacaoProduto->getNome()
        ));
        ?>
        <section>
            <div class="container-product-list">
                <div class="product-list cross-selling-container clearfix">
                    <form id="adicionar-carrinho-venda-cruzada" data-modal="false" method="post"
                          action="<?php echo get_url_site() ?>/carrinho/actions/adicionar/">
                        <?php

                        Widget::render('produto/product', array(
                            'objProduto'        => $objProduto,
                            'iconCrossSelling'  => 'plus'
                        ));
                        Widget::render('produto-detalhe/campo-produto-variacao-id', array(
                            'objProduto' => $objProduto
                        ));

                        /** @var Produto $objProdutoRelacionado */
                        foreach ($collProdutos as $i => $objProdutoRelacionado) {
                            Widget::render('produto/product', array(
                                'objProduto'        => $objProdutoRelacionado,
                                'iconCrossSelling'  => ($i != count($collProdutos) - 1) ? 'plus' : 'equals',
                            ));
                            Widget::render('produto-detalhe/campo-produto-variacao-id', array(
                                'objProduto'    => $objProdutoRelacionado,
                            ));

                            $totalSemDesconto += $objProdutoRelacionado->getValorBase();
                            $totalComDesconto += $objProdutoRelacionado->getValor();
                            $relacionados[] = $objProdutoRelacionado->getId();
                            $needSelectVariation = $needSelectVariation || $objProdutoRelacionado->hasVariacoes();
                        }
                        ?>

                        <div class="col-xs-12 col-md-3">
                            <div class="product cross-selling-price-resume">
                                <div class="row">
                                    <div class="box-secondary bg-default">
                                        <?php
                                        Widget::render('produto-comprar-junto/wrapper-total-price-box', array(
                                            'totalSemDesconto' => $totalSemDesconto,
                                            'totalComDesconto' => $totalComDesconto,
                                            'submit' => $needSelectVariation == false
                                        ));
                                        if ($needSelectVariation) {
                                            ?>
                                            <br>
                                            <a href="<?php echo get_url_site() ?>/produtos/modal-comprar-junto/?produto=<?php echo $objProduto->getId() ?>&relacionados=<?php echo json_encode($relacionados) ?>"
                                               class="btn-action-comprar-junto btn btn-success btn-block">
                                                <span class="<?php echo icon('cart-plus') ?>"></span> Comprar junto
                                            </a>
                                            <?php
                                        } else {

                                        }
                                        ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>
                    <div class="clearfix"></div>
                </div>
        </section>
        <?php
    }
}