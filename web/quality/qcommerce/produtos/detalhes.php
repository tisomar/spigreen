<?php
/* @var $objProdutoDetalhe Produto */
use QPress\Template\Widget;

$strIncludesKey = 'produto-detalhes';

include_once __DIR__ . '/actions/detalhes_produto/detalhes.actions.php';
include_once __DIR__ . '/actions/avalie.actions.php';

ClearSaleMapper\Manager::set('page', 'product');
ClearSaleMapper\Manager::set('description', 'name=' . escape($objProdutoDetalhe->getNome()) . ', sku=' . escape($objProdutoDetalhe->getSku()));

include_once __DIR__ . '/../includes/head.php';

?>
<body itemscope itemtype="http://schema.org/WebPage" data-page="<?php echo $strIncludesKey; ?>">
<?php include_once QCOMMERCE_DIR . '/includes/javascript_body_inicio.php'; ?>

<?php include_once QCOMMERCE_DIR . '/includes/facebook_tracking/fb.general.tracking.php'; ?>
<script type="text/javascript">
    window.starRate = jQuery.parseJSON( '<?php echo json_encode(ProdutoComentarioPeer::getNotasDescricao()) ?>' );
</script>

<?php Widget::render('general/header'); ?>

<main role="main">
    <?php
    Widget::render('components/breadcrumb', array('links' => $breadcrumb));
    Widget::render('components/flash-messages');

    $colClasses = !$objProdutoDetalhe->getIsPlanoSelecaoItensByClientes() ? 'col-xs-12 col-sm-6' : 'col-xs-12';
    ?>
    <div class="container">

        <div class="row">

            <div class="<?= $colClasses ?>">

                <div data-content-id="produto_detalhe_galeria_fotos_<?php echo $objProdutoDetalhe->getId() ?>">
                    <?php
                    Widget::render('produto-detalhe/galeria-fotos', array(
                        'objProduto' => $objProdutoDetalhe
                    ));
                    ?>
                </div>

                <?php
                Widget::render("produto-detalhe/social");
                ?>

            </div>

            <div class="<?= $colClasses ?>">

                <div data-content-id="produto_detalhe_box_title_<?php echo $objProdutoDetalhe->getId() ?>">
                    <?php
                    Widget::render('produto-detalhe/box_title', array(
                        'objProdutoVariacao' => $objProdutoDetalhe->getProdutoVariacao()
                    ));
                    ?>
                </div>

                <hr>

                <div class="clearfix">
                    
                    <?php if (Config::get('produto.mostrar_descricao_resumida')) : ?>
                        <p data-content-id="produto_detalhe_short_description_<?php echo $objProdutoDetalhe->getId() ?>">
                            <?php
                            Widget::render('produto-detalhe/box_short_description', array(
                                'objProduto' => $objProdutoDetalhe,
                            ));
                            ?>
                        </p>
                    <?php endif; ?>

                    <p class="pull-left brand">
                        <?php if ($objProdutoDetalhe->getMarca()) : ?>
                            Marca:
                            <a class="small" href="<?php echo $objProdutoDetalhe->getMarca()->getUrlListagem() ?>">
                                <?php echo $objProdutoDetalhe->getMarca()->getNome(); ?>
                            </a>
                        <?php endif; ?>
                    </p>

                    <div class="pull-right">
                        <?php if ($objProdutoDetalhe->getQuantidadeAvaliacao() == 0) : ?>
                            <a class="rating-link small pull-left hidden-xs hidden-sm" title="Avaliar este produto" data-lightbox="iframe" href="<?php echo get_url_site(); ?>/produtos/avalie/<?php echo $objProdutoDetalhe->getSlug() ?>">(Seja o primeiro a avaliar)</a>
                        <?php else : ?>
                            <span class="rating-link small pull-left hidden-xs hidden-sm">
                                (<?php echo $objProdutoDetalhe->getQuantidadeAvaliacao(); ?> avaliações)
                            </span>
                        <?php endif; ?>
                        <?php
                        Widget::render('components/rating', array(
                            'size'      =>  'sm',
                            'value'     =>  $objProdutoDetalhe->getNotaAvaliacao(),
                            'disabled'  =>  true
                        ));
                        ?>
                    </div>
                </div>

                <?php
                    Widget::render('produto-detalhe/kit_ouro', array(
                        'objProdutoDetalhe' => $objProdutoDetalhe,
                    ));

                    Widget::render('produto-detalhe/kit_preferencial', array(
                        'objProdutoDetalhe' => $objProdutoDetalhe,
                    ));

                    Widget::render('produto-detalhe/kit_platinum', array(
                        'objProdutoDetalhe' => $objProdutoDetalhe,
                    ));
                ?>

                <form role="form" name="form-produto" class="form-disabled-on-load" id="form-add-produto" action="<?php echo get_url_site(); ?>/carrinho/actions/adicionar" method="post">
                    <div class="box-secondary">
                        <?php
                        Widget::render('produto-detalhe/variacao-component', array(
                            'objProdutoDetalhe' => $objProdutoDetalhe,
                            'produtoVariacaoLayout' => Config::get('produto_layout_variacao'),
                        ));
                        ?>
                    </div>
                    <?php
                    Widget::render('produto-detalhe/campo-produto-variacao-id', array(
                        'objProduto' => $objProdutoDetalhe
                    ));
                    ?>
                </form>

                <?php if ($objProdutoDetalhe->isDisponivel()) :
                    /**
                     * A div #update-box-frete serve para aplicar um js que esconde o box do frete
                     * quando a variação estiver indisponivel.
                     * Ver arquivo: /produtos/actions/variacao.php
                     */
                    ?>
                    <div class="row">
                        <div class="col-xs-12 col-sm-6">
                            <div data-content-id="update-box-frete"></div>

                            <?php
                            if (ClientePeer::isAuthenticad() || Config::get('clientes.ocultar_preco') == 0) :
                                ?>
                                <div id="box-frete" class="tab">
                                    <?php
                                    if (is_null($frete)) {
                                        Widget::render('general/panel-heading', array(
                                            'dataTarget'        => '#shipping-desktop',
                                            'panelTitleMobile'  => 'Calcular Frete',
                                            'panelTitleDesktop' => 'Calcular Frete',
                                            'icon'              => 'truck'
                                        ));
                                    }

                                    Widget::render('produto-detalhe/consulta-frete-completo', array(
                                        'frete' =>  $frete,
                                        'address' =>  $address,
                                        'id' =>  'shipping-desktop',
                                    ));
                                    ?>
                                </div>
                                <?php
                            endif;
                            ?>
                        </div>
                    </div>
                    <?php
                endif;
                ?>

            </div>
        </div>

        <br>

        <div class="box-wrapper">
            <?php
            /**
             * produtos associados, venda cruzada...
             */
            Widget::render('produto-detalhe/box_associacao', array(
                'objProduto' => $objProdutoDetalhe
            ));
            ?>

            <?php
            /**
             * Descrição completa do produto
             */
            Widget::render('general/box-title', array(
                'title' => 'Descrição completa'
            ));
            ?>
            <div id="description">
                <?php echo $objProdutoDetalhe->getDescricao(); ?>
            </div>

            <?php

            /**
             * Características do produto
             */
            if ($objProdutoDetalhe->getCaracteristicas() != '') {
                Widget::render('general/box-title', array(
                    'title' => 'Características'
                ));
                ?>
                <div id="technical-information">
                    <?php echo $objProdutoDetalhe->getCaracteristicas(); ?>
                </div>
                <?php
            }

            /**
             *  Comentários e avaliações
             */

            Widget::render('general/box-title', array(
                'title' => 'Avaliações / Comentários'
            ));
            ?>
            <div id="comments">
                <?php Widget::render('produto/comentarios', array('objProduto' => $objProdutoDetalhe)); ?>
            </div>

        </div>
    </div>
</main>

<?php include QCOMMERCE_DIR . '/includes/footer.php'; ?>

</body>
</html>
