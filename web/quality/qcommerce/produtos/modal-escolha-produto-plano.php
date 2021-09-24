<?php
use QPress\Template\Widget;
$strIncludesKey = 'produto-detalhes';

$clienteLogado = ClientePeer::getClienteLogado(true);
$planoCliente =  $clienteLogado ? $clienteLogado->getPlano() : null;

$productId  = $container->getRequest()->query->get('produto_id');
$objProduto = ProdutoPeer::retrieveByPK($productId);

if ($request->getMethod() == 'POST'):
    header('Content-Type: application/json');

    try {
        $con = Propel::getConnection();
        $con->beginTransaction();

        $carrinho = $container->getCarrinhoProvider()->getCarrinho();
        $carrinho->resetFrete();

        $objPlano = $objProduto ? $objProduto->getPlano() : null;

        //Não pode adicionar um combo ao carrrinho este ja possua algum combo
        if ($objPlano && $carrinho->getPlano()):
            throw new Exception('Não é permitido adicionar mais de um kit ao carrinho.');
        endif;

        // Não pode adquirir um plano igual ou inferior de outro já adquirido
        if ($objPlano && $planoCliente && $objPlano->getNivel() <= $planoCliente->getNivel()):
            throw new Exception('Você já possui um plano igual ou superior a este.');
        endif;

        $valorTotal = 0;

        foreach ($request->request->get('quantidade-kit') as $id => $qtd):
            if ($qtd > 0):
                $variacao = ProdutoVariacaoPeer::retrieveByPK($id);
                if (!$variacao->isDisponivel()):
                    throw new Exception(sprintf('O produto <b>%s</b> não está disponível para venda.',
                        $variacao->getProdutoNomeCompleto(' &minus; ')
                    ));
                endif;

                $produto = $variacao->getProduto();
                
                // Verifica se existe um item no carrinho com esta variação e adiciona caso não exista.
                $item = PedidoItemQuery::create()
                    ->filterByProdutoVariacaoId($variacao->getId())
                    ->filterByPedidoId($carrinho->getId())
                    ->filterByPlanoId(null, Criteria::ISNOTNULL)
                    ->findOneOrCreate();

                $criteriaCategoriaAcessorios = ProdutoCategoriaQuery::create()->filterByCategoriaId(55);
                $isAcessorio = $produto->getProdutoCategorias($criteriaCategoriaAcessorios)->count() > 0;

                // valor con desconto
                // $valor = round($isAcessorio ? $variacao->getValor() : $variacao->getValorBase() * 0.6, 2);
                $valor = round($isAcessorio ? $variacao->getValor() : $variacao->getValorBase(), 2);

                // desconto de 40% nos itens do kit platinum
                if($productId == 199):
                    $price40PercDesconto = $valor * 0.4;
                    $valor = !$isAcessorio ?  ($valor - $price40PercDesconto) : $valor;
                endif;

                $valorTotal += $valor * $qtd;

                $item->setQuantidade($qtd);
                $item->setPlanoId($objProduto->getPlanoId());
                $item->setValorUnitario($valor);
                $item->setValorPontosUnitario(0);
                $item->setPeso($produto->getPeso());
                $item->setValorCusto($produto->getValorCusto());

                $carrinho->addItem($item);
            endif;
        endforeach;

        $variacao = $objProduto->getProdutoVariacao();

        if (!$variacao->isDisponivel()):
            throw new Exception(sprintf('O produto <b>%s</b> não está disponível para venda.',
                $variacao->getProdutoNomeCompleto(' &minus; ')
            ));
        endif;

        // Valida o estoque.
        if ($variacao->getEstoqueAtual() < $qtd):
            throw new Exception(printf(
                'O produto <b>%s</b> possui apenas <b>%s</b> no estoque.',
                $variacao->getProdutoNomeCompleto(' &minus; '),
                plural($variacao->getEstoqueAtual(), '%s item', '%s itens')
            ));
        endif;
                
        // Verifica se existe um item no carrinho com esta variação e adiciona caso não exista.
        $item = PedidoItemQuery::create()
            ->filterByProdutoVariacaoId($variacao->getId())
            ->filterByPedidoId($carrinho->getId())
            ->findOneOrCreate();

        $item->setQuantidade(1);
        $item->setValorUnitario($valorTotal);
        $item->setValorPontosUnitario($objProduto->getValorPontos());
        $item->setValorCusto($objProduto->getValorCusto());
        $item->setPeso($objProduto->getPeso());

        $carrinho->addItem($item);

        $con->commit();
        echo json_encode([
            'success' => true,
        ]);
    } catch (Exception $e) {
        $con->rollBack();

        http_response_code(400);
        echo json_encode([
            'success' => false,
            'message' => $e->getMessage(),
        ]);
    }

    exit;
endif;

include_once __DIR__ . '/../includes/head.php';

$produtoLayoutVariacao = Config::get('produto_layout_variacao');

$catProduto = CategoriaPeer::retrieveByPK(3);

$totalRequired = 0;

if($productId == 145):
    $requiredProducts = ProdutoQuery::create()
    ->useProdutoVariacaoQuery()
        ->filterByIsRequiredProduct(1)
    ->endUse()
    ->filterById(153, Criteria::NOT_EQUAL)
    ->find(); 
else:
    $requiredProducts = ProdutoQuery::create()
    ->useProdutoVariacaoQuery()
        ->filterByIsRequiredProduct(1)
    ->endUse()
    ->find();   
endif;

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
<body itemscope itemtype="http://schema.org/WebPage" class="lightbox" data-page="<?php echo $strIncludesKey; ?>">
<?php include_once QCOMMERCE_DIR . '/includes/javascript_body_inicio.php'; ?>

<?php
Widget::render('mfp-modal/header', array(
    'title' => $objProduto->getNome()
));
?>

<main role="main">

    <div class="box-flash-messages">
        <?php Widget::render('components/flash-messages'); ?>
    </div>

    <div class="container">
        <form class="form-ajax-adicionar-kit-ao-carrinho" data-modal="true" method="post" action="#">
            <input type="hidden" name="kit-id" value="<?= $objProduto->getId() ?>"/>
            <div class="row">
                <div class="col-xs-12 col-sm-6 col-sm-offset-3">
                    <div data-content-id="produto_detalhe_galeria_fotos_<?= $objProduto->getId() ?>">
                        <?php
                        Widget::render('produto-detalhe/galeria-fotos', array(
                            'objProduto' => $objProduto,
                            'disableVerticalGallery' => false,
                        ));
                        ?>
                    </div>
                </div>

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

                <div class="col-xs-12 product-info" style="margin-top: 20px;">
                    <div class="box-secondary">
                    <?php if($objProduto->getId() === 145) : ?>
                        <input type="hidden" class="valor-minimo-kit" value="300">
                        <input type="hidden" class="valor-maximo-kit" value="400">
                    <?php endif ?>
                    <?php if($objProduto->getId() === 149) : ?>
                        <input type="hidden" class="valor-minimo-kit" value="899">
                        <input type="hidden" class="valor-maximo-kit" value="1000">
                    <?php endif ?>
                    <?php if($objProduto->getId() === 199) : ?>
                        <input type="hidden" class="valor-minimo-kit" value="1992">
                        <input type="hidden" class="valor-maximo-kit" value="2050">
                    <?php endif ?>
                    <!-- <input type="hidden" class="valor-minimo-kit" value="<?= $objProduto->getValorPromocional() ?>"> -->
                    <!-- <input type="hidden" class="valor-maximo-kit" value="<?= $objProduto->getValorBase() ?>"> -->

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
                        
                        if(count($requiredProducts) > 0 ) :
                            
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

                            // desconto de 40% nos itens do kit platinum
                            if($objProduto->getId() === 199) :
                                $price40PercDesconto = $price * 0.4;
                                $price -= $price40PercDesconto;
                            endif;
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
                    </div>
                </div>

                <div class="col-xs-12">
                    <div class="kit-value-description text-right">
                        <?php if($objProduto->getIsPlanoSelecaoItensByClientes()):?>
                        <!-- <small>Você deve selecionar um valor entre R$ </?= number_format($objProduto->getValorPromocional(), '2', ',', '.')?> e </?= number_format($objProduto->getValorBase(), '2', ',', '.')?> </small> -->
                        <?php endif ?>
                        <?php if($objProduto->getId() === 145) : ?>
                            <small>Você deve selecionar um valor entre R$ 300,00 e R$ 400,00</small>
                        <?php endif ?>
                        <?php if($objProduto->getId() === 149) : ?>
                            <small>Você deve selecionar um valor entre R$ 899,00 e R$ 1.000,00</small>
                        <?php endif ?>
                        <?php if($objProduto->getId() === 199) : ?>
                            <small>Você deve selecionar um valor entre R$ 1.992,00 e R$ 2.050,00 </small>
                        <?php endif ?>
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
            </div>
        </form>
    </div>

</main>
<?php include_once __DIR__ . '/../includes/footer-lightbox.php' ?>

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

        // </?php if($objProduto->getId() === 199):?>
        //     if($(this).val() > 4) {
        //         alert('É permitido selecionar apenas 4 (quatro) unidade de cada item!')

        //         $(this).parent().find('button.bootstrap-touchspin-up').attr('disabled', true)
        //         $this.trigger('touchspin.stopspin')
        //         $this.trigger('touchspin.downonce')
        //     }else if($(this).val() <= 0){
        //         $(this).parent().find('button.bootstrap-touchspin-up').attr('disabled', false)
        //     }
        // </?php endif?>

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

</body>
</html>
