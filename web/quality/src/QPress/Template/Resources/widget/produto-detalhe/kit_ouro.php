<?php 

if ($objProdutoDetalhe->getId() === 149):
    $totalRequired = 0;

    $requiredProducts = ProdutoQuery::create()
        ->useProdutoVariacaoQuery()
            ->filterByIsRequiredProduct(1)
        ->endUse()
        ->find();   

    $relatedProducts = ProdutoQuery::create()
        ->useProdutoVariacaoQuery()
            ->filterByIsMaster(true)
            ->filterByDisponivel(true)
            ->filterByDataExclusao(null, Criteria::ISNULL)
        ->endUse()
        ->useProdutoCategoriaQuery()
            ->useCategoriaQuery()
                // ->filterByNrLft($catProduto->getNrLft(), Criteria::LESS_EQUAL)
                // ->filterByNrRgt($catProduto->getNrRgt(), Criteria::GREATER_EQUAL)
                ->filterByIsCategoryByKit(true)
            ->endUse()
        ->endUse()
        ->filterByPlanoId(null, Criteria::ISNULL)
        ->filterByDataExclusao(null, Criteria::ISNULL)
        ->find();
    ?>

    <form
        class="form-ajax-adicionar-kit-ao-carrinho"
        data-modal="true"
        method="post"
        action="/produtos/modal-escolha-produto-plano/?produto_id=<?= $objProdutoDetalhe->getId() ?>"
    >

        <!-- <input type="hidden" class="valor-minimo-kit" value="<?= $objProdutoDetalhe->getValorPromocional() ?>"> -->
        <!-- <input type="hidden" class="valor-maximo-kit" value="<?= $objProdutoDetalhe->getValorBase() ?>"> -->
        <input type="hidden" class="valor-minimo-kit" value="899">
        <input type="hidden" class="valor-maximo-kit" value="1000">

        <style>
            .kit-value-description {
                margin: 20px 0;
                padding: 0 20px;
            }
            .product-kit {
                display: grid;
                grid-template-columns: 2fr 5fr 2fr 3fr;
                grid-gap: 10px;
                align-items: center;
                margin-bottom: 15px;
            }
            .product-kit .input-group {
                max-width: 200px;
            }
            @media screen and (max-width: 767px) {
                .product-kit {
                    grid-template-columns: 2fr 4fr;
                }
            }
        </style>

        <?php
        if(Config::get('produto.proporcao') == '1:1') :
            $aspectRatio = '1x1';
            $imgProportionXs = 'width=100&height=100&cropratio=1:1';
            $imgProportionSm = 'width=100&height=100&cropratio=1:1';
            $imgProportionMd = 'width=100&height=100&cropratio=1:1';
            $imgProportionLg = 'width=100&height=100&cropratio=1:1';
        elseif (Config::get('produto.proporcao') == '4:3') :
            $aspectRatio = '4x3';
            $imgProportionXs = 'width=100&height=75&cropratio=1:0.75';
            $imgProportionSm = 'width=100&height=75&cropratio=1:0.75';
            $imgProportionMd = 'width=100&height=75&cropratio=1:0.75';
            $imgProportionLg = 'width=100&height=75&cropratio=1:0.75';
        elseif (Config::get('produto.proporcao') == '3:4') :
            $aspectRatio = '3x4';
            $imgProportionXs = 'width=75&height=100&cropratio=0.75:1';
            $imgProportionSm = 'width=75&height=100&cropratio=0.75:1';
            $imgProportionMd = 'width=75&height=100&cropratio=0.75:1';
            $imgProportionLg = 'width=75&height=100&cropratio=0.75:1';
        endif;
        
        if(count($requiredProducts) > 0) :
            foreach ($requiredProducts as $product):
                $criteriaCategoriaAcessorios = ProdutoCategoriaQuery::create()->filterByCategoriaId(55);
                $isAcessorio = $product->getProdutoCategorias($criteriaCategoriaAcessorios)->count() > 0;
                $variacao = $product->getProdutoVariacao();
                $price = $isAcessorio ? $variacao->getValor() : $variacao->getValorBase() * 0.6;
                $quantidade = $product->getId() === 135 ? 1 : 1;
                $totalRequired += $price * $quantidade;
                ?>
                <div class="product-kit">
                    <div class="product-image aspect-ratio-<?= $aspectRatio; ?>">
                        <picture>
                            <!--[if IE 9]><video style="display: none;"><![endif]-->
                            <source media="(min-width: 993px)" srcset="<?= $product->getUrlImageResize($imgProportionLg); ?>">
                            <source media="(min-width: 769px)" srcset="<?= $product->getUrlImageResize($imgProportionMd); ?>">
                            <source media="(min-width: 321px)" srcset="<?= $product->getUrlImageResize($imgProportionSm); ?>">
                            <!--[if IE 9]></video><![endif]-->
                            <img
                                class="img-responsive center-block"
                                srcset="<?= $product->getUrlImageResize($imgProportionXs); ?>"
                                alt="<?= htmlspecialchars($product->getNome()); ?>"
                            />
                        </picture>
                    </div>
                    <div><?= $product->getNome() ?></div>
                    <div class="text-center">
                        R$ <?= number_format($price, 2, ',', '.') ?>
                    </div>
                    <div class="text-center">
                        <?= $quantidade ?>
                        <input
                            <?= get_atributes_html([
                                'name' => "quantidade-kit[{$variacao->getId()}]",
                                'value' => $quantidade,
                                'type' => 'hidden',
                                'class' => 'qtd-produto-kit',
                                'data-price' => $price
                            ]) ?>
                        />
                    </div>
                </div>
                <?php
            endforeach;
        endif;

        foreach ($relatedProducts as $product):
            $criteriaCategoriaAcessorios = ProdutoCategoriaQuery::create()->filterByCategoriaId(55);
            $isAcessorio = $product->getProdutoCategorias($criteriaCategoriaAcessorios)->count() > 0;
            $variacao = $product->getProdutoVariacao();
            $price = $isAcessorio ? $variacao->getValor() : $variacao->getValorBase();
            // preço com desconto quando está no valor horiginal
            // $price = $isAcessorio ? $variacao->getValor() : $variacao->getValorBase() * 0.6;
            ?>
            <div class="product-kit">
                <div class="product-image aspect-ratio-<?= $aspectRatio; ?>">
                    <picture>
                        <!--[if IE 9]><video style="display: none;"><![endif]-->
                        <source media="(min-width: 993px)" srcset="<?= $product->getUrlImageResize($imgProportionLg); ?>">
                        <source media="(min-width: 769px)" srcset="<?= $product->getUrlImageResize($imgProportionMd); ?>">
                        <source media="(min-width: 321px)" srcset="<?= $product->getUrlImageResize($imgProportionSm); ?>">
                        <!--[if IE 9]></video><![endif]-->
                        <img
                            class="img-responsive center-block"
                            srcset="<?= $product->getUrlImageResize($imgProportionXs); ?>"
                            alt="<?= htmlspecialchars($product->getNome()); ?>"
                        />
                    </picture>
                </div>
                <div><?= $product->getNome()?></div>
                <div class="text-center">
                    R$ <?= number_format($price, 2, ',', '.') ?>
                </div>
                <div class="text-center">
                    <input
                        <?= get_atributes_html([
                            'name' => "quantidade-kit[{$variacao->getId()}]",
                            'value' => 0,
                            'type' => 'number',
                            'class' => 'touch-spin text-center qtd-produto-kit',
                            'min' => 0,
                            'max' => 100,
                            'placeholder' => 0,
                            'data-price' => round($price, 2)
                        ]) ?>
                    />
                </div>
            </div>
            <?php
        endforeach;
        ?>

        <div class="col-xs-12">
            <div class="kit-value-description text-right">
                <!-- <small>Você deve selecionar um valor entre R$ </?= number_format($objProdutoDetalhe->getValorPromocional(), '2', ',', '.')?> e </?= number_format($objProdutoDetalhe->getValorBase(), '2', ',', '.')?> </small> -->
                <small>Você deve selecionar um valor entre R$ 899,00 e R$ 1.000,00</small>
                <br/>
                <br/>
                Valor total selecionado: <strong>R$ <span><?= number_format($totalRequired, 2, ',', '.') ?></span><strong>
            </div>
        </div>

        <div
            class="col-xs-12 col-sm-6 col-md-3 col-sm-offset-6 col-md-offset-9"
            style="margin-bottom: 20px;"
        >
            <button
                type="submit"
                disabled
                class="add-kit-to-cart btn btn-success btn-block"
            >
                Comprar
            </button>
        </div>
    </form>

    </br>
    </br>
    </br>

<?php endif;?>


<script>
    $('.qtd-produto-kit').on('change', function() {      
        var $this = $(this),
            valorTotal = $.map($('.qtd-produto-kit'), function(item) {
                var $item = $(item),
                quantidade = $item.val(),
                valor = $item.data('price')

                return quantidade * valor
            })
            .reduce(function(prev, curr) {
                return prev + curr
            }, 0)

        // if($(this).val() > 1) {
        //     alert('É permitido selecionar apenas 1 (uma) unidade de cada item!')

        //     $(this).parent().find('button.bootstrap-touchspin-up').attr('disabled', true)
        //     $this.trigger('touchspin.stopspin')
        //     $this.trigger('touchspin.downonce')
        // }else if($(this).val() <= 0){
        //     $(this).parent().find('button.bootstrap-touchspin-up').attr('disabled', false)
        // }

        if (valorTotal > $('.valor-maximo-kit').val()) {
            alert('Valor máximo ultrapassado!')

            $this.trigger('touchspin.stopspin')
            $this.trigger('touchspin.downonce')
        } else {
            var formatted = '';
            
            if (valorTotal >= 1000) {
                formatted += parseInt(valorTotal / 1000) + '.'
            }

            formatted += valorTotal.toFixed(2).replace('.', ',').substr(-6)

            $('.kit-value-description span').text(formatted)

            $('.add-kit-to-cart').prop('disabled', valorTotal <= $('.valor-minimo-kit').val())
        }
    })
</script>