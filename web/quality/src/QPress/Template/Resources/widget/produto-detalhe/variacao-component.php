<?php
use QPress\Template\Widget;

$produtoVariacaoLayout = isset($produtoVariacaoLayout) ? $produtoVariacaoLayout : Config::PRODUTO_LAYOUT_VARIACAO_CAIXA;

if (Config::get('produto_variacao.selecao_automatica') != 0 && $objProdutoDetalhe->getVariacaoPadrao() != null) :
    $objProdutoVariacaoPadrao = $objProdutoDetalhe->getVariacaoPadrao();
else :
    $objProdutoVariacaoPadrao = $objProdutoDetalhe->getProdutoVariacao();
endif;
?>
<?php if ($objProdutoDetalhe->isDisponivel()): ?>

    <?php if (ClientePeer::isAuthenticad() || Config::get('clientes.ocultar_preco') == 0): ?>

        <?php
        Widget::render('produto-detalhe/variacao-block', array(
            'objProdutoDetalhe' => $objProdutoDetalhe,
            'produtoVariacaoLayout' => $produtoVariacaoLayout,
        ));
        ?>

        <?php if ($produtoVariacaoLayout != Config::PRODUTO_LAYOUT_VARIACAO_GRADE): ?>
            <div data-content-id="produto_price_<?php echo $objProdutoDetalhe->getId() ?>" class="price-container">
                <?php
                Widget::render('produto/price', array(
                    'objProdutoVariacao'    => $objProdutoVariacaoPadrao,
                    'parcelDescription'     => $objProdutoDetalhe->getDescricaoParcelado()
                ));
                ?>
            </div>

            <div data-content-id="produto_detalhe_box_quantity_<?php echo $objProdutoDetalhe->getId() ?>" class="row">
                <?php
                Widget::render("produto-detalhe/box_quantity", array(
                    'objProdutoVariacao' => $objProdutoVariacaoPadrao,
                ));
                ?>
            </div>
        <?php endif; ?>

        <div data-content-id="produto_detalhe_btn_comprar_<?php echo $objProdutoDetalhe->getId() ?>">
            <?php
            Widget::render("produto-detalhe/btn_comprar", array(
                'objProduto' => $objProdutoDetalhe,
            ));
            ?>
        </div>


    <?php else: ?>
        <div class="row">
            <div class="col-xs-12">
                <?php
                Widget::render('components/alert', array(
                    'type' => 'warning',
                    'title' => 'Atenção!',
                    'message' => '<p>Para visualizar os preços dos produtos, você deve efetuar o login no sistema.</p>',
                ));
                ?>
            </div>
        </div>
    <?php endif; ?>

<?php
else:
    Widget::render("produto-detalhe/indisponivel");
    Widget::render("produto-detalhe/btn_aviseme", array('variacaoId' => $objProdutoVariacaoPadrao->getId()));
endif;
?>