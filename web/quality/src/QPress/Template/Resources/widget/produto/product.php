<?php
use QPress\Template\Widget;

/* @var $objProduto Produto */

/**
 * Verifica se o produto possui variações e analisa se há alguma variação disponível para venda.
 */
$isDisponivel = $objProduto->isDisponivel();

$addBuyButton       = isset($addBuyButton) ? $addBuyButton : (bool) Config::get('mostrar_botao_comprar_listagem');
$addQuantityInput   = $addBuyButton && (isset($addQuantityInput) ? $addQuantityInput : (bool) Config::get('mostrar_campo_quantidade_listagem'));

if(Config::get('produto.proporcao') == '1:1') :
    $aspectRatio = '1x1';
    $imgProportionXs = 'width=330&height=330&cropratio=1:1';
    $imgProportionSm = 'width=330&height=330&cropratio=1:1';
    $imgProportionMd = 'width=330&height=330&cropratio=1:1';
    $imgProportionLg = 'width=330&height=330&cropratio=1:1';
elseif (Config::get('produto.proporcao') == '4:3') :
    $aspectRatio = '4x3';
    $imgProportionXs = 'width=320&height=240&cropratio=1.333:1';
    $imgProportionSm = 'width=320&height=240&cropratio=1.333:1';
    $imgProportionMd = 'width=320&height=240&cropratio=1.333:1';
    $imgProportionLg = 'width=320&height=240&cropratio=1.333:1';
elseif (Config::get('produto.proporcao') == '3:4') :
    $aspectRatio = '3x4';
    $imgProportionXs = 'width=330&height=440&cropratio=0.75:1';
    $imgProportionSm = 'width=330&height=440&cropratio=0.75:1';
    $imgProportionMd = 'width=330&height=440&cropratio=0.75:1';
    $imgProportionLg = 'width=330&height=440&cropratio=0.75:1';
endif;

$numeroColunas = !isset($numeroColunas) ? 4 : $numeroColunas;

$iconCrossSelling = isset($iconCrossSelling) ? $iconCrossSelling : false;

$classColumn = $iconCrossSelling == false ? ($numeroColunas == 4 ? 'col-md-3' : 'col-md-4') : 'col-md-2';
$clienteLogado = ClientePeer::getClienteLogado(true);
$planoCliente =  $clienteLogado ? $clienteLogado->getPlano() : null;

if ($clienteLogado && $planoCliente && $objProduto->getAplicaDescontoPlano()) :
    list($valor, $porcentagem,  $mesesAtivo) = $objProduto->getValorFidelidade();
    $getDescricaoParcelado = $objProduto->getDescricaoParceladoFidelidade();
else :
    $valor = $objProduto->getValor();
    $porcentagem = $objProduto->getPercentualDesconto();
    $getDescricaoParcelado = $objProduto->getDescricaoParcelado();
endif;

?>
<article class="product clearfix <?php echo (isset($carousel)) ? 'product-carousel' : 'col-xs-12 ' . $classColumn; ?> <?php echo $iconCrossSelling ? 'product-cross-selling' : '' ?>">
    <form  data-modal="false" method="post" action="<?php echo get_url_site() ?>/carrinho/actions/adicionar/">

        <?php if($valor < $objProduto->getValorBase() && $isDisponivel): ?>
            <div class="tag-promotion">-<?php echo $porcentagem; ?>%</div>
        <?php endif; ?>

        <div class="row clearfix">

            <a href="<?php echo $objProduto->getUrlDetalhes(); ?>" title="<?php echo htmlspecialchars($objProduto->getNome()); ?>">
                <div class="product-image col-xs-12 col-sm-3 col-md-12 aspect-ratio-<?php echo $aspectRatio; ?>">
                    <picture>
                        <!--[if IE 9]><video style="display: none;"><![endif]-->
                        <source media="(min-width: 993px)" srcset="<?php echo $objProduto->getUrlImageResize($imgProportionLg); ?>">
                        <source media="(min-width: 769px)" srcset="<?php echo $objProduto->getUrlImageResize($imgProportionMd); ?>">
                        <source media="(min-width: 321px)" srcset="<?php echo $objProduto->getUrlImageResize($imgProportionSm); ?>">
                        <!--[if IE 9]></video><![endif]-->
                        <img class="img-responsive center-block" srcset="<?php echo $objProduto->getUrlImageResize($imgProportionXs); ?>" alt="<?php echo htmlspecialchars($objProduto->getNome()); ?>">
                    </picture>
                </div>
            </a>


            <div class="product-info col-xs-12 col-sm-9 col-md-12">
                <a href="<?php echo $objProduto->getUrlDetalhes(); ?>" title="<?php echo htmlspecialchars($objProduto->getNome()); ?>">
                    <h1 class="tit"><?php echo (htmlspecialchars($objProduto->getNome())); ?></h1>
                    <p class="description">
                        <?php echo resumo($objProduto->getDescricao(), 100); ?> <br>
                        <?php if($objProduto->getMarcaId()): ?>
                            <strong>Marca: </strong> <?php echo $objProduto->getMarca()->getNome(); ?>
                        <?php endif; ?>
                    </p>
                </a>

                <?php if ($isDisponivel): ?>
                    <?php if (ClientePeer::isAuthenticad() || Config::get('clientes.ocultar_preco') == 0): ?>
                        <a href="<?php echo $objProduto->getUrlDetalhes(); ?>" title="<?php echo htmlspecialchars($objProduto->getNome()); ?>">
                            <p class="container-price">
                                <?php if ($valor < $objProduto->getValorBase()): ?>
                                    <span class="price-label hidden-xs">De: <del>R$ <?php echo format_money($objProduto->getValorBase()); ?></del></span>
                                    <span class="text-price hidden-xs">Por</span>
                                <?php else: ?>
                                    <span class="price-label hidden-xs">Por apenas</span>
                                    <span class="text-price hidden-xs"><br></span>
                                <?php endif; ?>

                                <span class="price"><span>R$</span> <?php echo format_money($valor); ?></span>
                                <?php echo  $getDescricaoParcelado; ?>
                            </p>
                        </a>
                        <?php if ($addBuyButton): ?>
                            <div class="row">
                                <?php if ($objProduto->getIsPlanoSelecaoItensByClientes()): ?>
                                    <div class="col-xs-12 col-sm-6 col-md-12">
                                        <button data-href="<?php echo get_url_site() ?>/produtos/modal-escolha-produto-plano/?produto_id=<?php echo $objProduto->getId() ?>"
                                                class="add-to-cart-variation btn btn-success btn-block">
                                            Comprar
                                        </button>
                                    </div>
                                <?php elseif (!$objProduto->hasVariacoes()): ?>
                                    <?php if ($addQuantityInput): ?>
                                        <div class="col-xs-5 col-sm-3 col-md-5 qtd-spin">
                                            <?php
                                            Widget::render('produto_variacao/input_quantidade', array(
                                                'objProdutoVariacao'    => $objProduto->getProdutoVariacao(),
                                                'inputType'             => 'number'
                                            ));
                                            ?>
                                        </div>
                                        <?php $classCol = 'col-xs-7 col-sm-6 col-md-7'; ?>
                                    <?php else: ?>
                                        <?php $classCol = 'col-xs-12'; ?>
                                        <?php
                                        Widget::render('produto_variacao/input_quantidade', array(
                                            'objProdutoVariacao'    => $objProduto->getProdutoVariacao(),
                                            'inputType'             => 'hidden'
                                        ));
                                        ?>
                                    <?php endif; ?>

                                    <div class="<?php echo $classCol ?>">
                                        <?php
                                        Widget::render('produto-detalhe/btn_comprar', array(
                                            'objProduto' => $objProduto
                                        ));
                                        ?>
                                    </div>
                                <?php else: ?>
                                    <div class="col-xs-12 col-sm-6 col-md-12">
                                        <button data-href="<?php echo get_url_site() ?>/produtos/modal-variation-select/?produto_id=<?php echo $objProduto->getId() ?>"
                                                class="add-to-cart-variation btn btn-success btn-block">
                                            Comprar
                                        </button>
                                    </div>
                                <?php endif; ?>
                            </div>
                        <?php endif; ?>
                    <?php endif; ?>

                <?php else: ?>

                    <?php if (ClientePeer::isAuthenticad() || Config::get('clientes.ocultar_preco') == 0): ?>
                        <div class="row">
                            <div class="col-xs-12 col-sm-6 col-md-12">
                                <?php if (Config::get('has_aviseme')):
                                    ?>
                                    <button style="<?php echo $addBuyButton == false ? 'margin: 14px 0 13px' : ''; ?>"
                                            data-href="<?php echo get_url_site() . '/produtos/avise-me/?pvid=' . $objProduto->getProdutoVariacao()->getId(); ?>"
                                            class="avise-me btn avise-me-product-list btn-primary btn-block cboxElement" title="Avise-me!" data-lightbox="iframe">
                                        Avise-me quando disponível
                                    </button>
                                    <script id="__initLightBox_aviseme">
                                        $(function() {
                                            initLightbox();
                                            $('#__initLightBox_aviseme').remove();
                                        });
                                    </script>
                                <?php else: ?>
                                    <button style="<?php echo $addBuyButton == false ? 'margin: 14px 0 13px' : ''; ?>"
                                            href="javascript:void(0)"
                                            disabled class="avise-me btn avise-me-product-list btn-primary btn-block btn-smcboxElement"
                                            title="Produto indisponível no momento!">
                                        Produto indisponível
                                    </button>
                                <?php endif; ?>
                            </div>
                        </div>
                    <?php endif; ?>
                <?php endif; ?>
            </div>
        </div>
        <?php
        Widget::render('produto-detalhe/campo-produto-variacao-id', array(
            'objProduto' => $objProduto
        ));
        ?>
    </form>
</article>
<?php if ($iconCrossSelling): ?>
    <div class="col-md-1">
        <?php if ($iconCrossSelling == "plus"): ?>
            <div style="text-align: center; margin-top: 150px;">
                <span class="icon-plus"></span>
            </div>
        <?php elseif ($iconCrossSelling == "equals"): ?>
            <div style="text-align: center; margin-top: 150px;">
                <span class="icon-equal"></span>
            </div>
        <?php endif; ?>
    </div>
<?php endif; ?>
