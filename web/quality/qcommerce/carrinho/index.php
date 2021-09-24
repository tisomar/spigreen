<?php
/* @var $container \QPress\Container\Container */

use QPress\Template\Widget;

$strIncludesKey = "carrinho";

require_once __DIR__ . '/actions/carrinho.actions.php';

ClearSaleMapper\Manager::set('page', 'cart');

include_once QCOMMERCE_DIR . '/includes/head.php';
?>
<body itemscope itemtype="http://schema.org/WebPage" data-page="carrinho">
<?php
include_once QCOMMERCE_DIR . '/includes/javascript_body_inicio.php';
include_once QCOMMERCE_DIR . '/includes/facebook_tracking/fb.cart.tracking.php';

Widget::render('general/header');

$errorMessage = false;

if ($calculated) :
    foreach ($frete as $calcRow) :
        $errorMessage = $errorMessage ? $errorMessage : ($calcRow['query']->hasErro() ? $calcRow['query']->getErro() : false);
    endforeach;
endif;

?>

<main role="main">
    <div class="update-onload">
        <?php
        Widget::render('components/breadcrumb', array('links' => array('Home' => '/home', 'Meu carrinho de compras' => '')));
        Widget::render('general/page-header', array('title' => 'Carrinho'));
        Widget::render('components/flash-messages');

        if ($carrinho->countItems()) : ?>
            <div class="container">
                <div class="header-cart hidden-xs hidden-sm">
                    <?php Widget::render('carrinho/buttons'); ?>
                </div>
            </div>

            <div class="container">
                <form role="form" method="post" class="form-product" action="<?php echo $root_path; ?>/carrinho/actions/atualizar/">
                    <?php
                    Widget::render('general/table-products', array(
                        'editable'          => true,
                        'strIncludesKey'    => $strIncludesKey,
                        'itens'             => $container->getCarrinhoProvider()->getCarrinho()->getPedidoItemsJoinProdutoVariacao()
                    ));
                    ?>
                </form>
            </div>

            <div class="container">
                <?php
                Widget::render('general/subtotal', array( 'value' => $carrinho->getValorItens()));
                Widget::render('general/discount', array( 'value' => $carrinho->getValorDesconto()));
                ?>
                <form method="post" class="form-frete form-horizontal <?php echo ($calculated && !$errorMessage) ? 'collapse' : ''; ?>" role="form">
                    <div class="box-secondary box-secondary-first">
                        <div class="row">
                            <div class="col-xs-5 col-sm-8">
                                <label for="frete-cep" class="control-label">Calcular frete</label>
                            </div>`
                            <div class="col-xs-7 col-sm-4">
                                <div class="input-group">
                                    <input id="frete-cep" class="form-control mask-cep" type="text" placeholder="Seu CEP" name="CEP" value="<?php echo $container->getSession()->get('CEP_SIMULACAO') ?>" required>
                                    <span class="input-group-btn">
                                        <button type="submit" class="btn btn-primary">Ok</button>
                                    </span>
                                </div>
                                <a href="http://www.buscacep.correios.com.br/sistemas/buscacep/" target="_blank" class="text-muted"><small>Não sabe seu CEP? Clique aqui</small></a>
                            </div>
                        </div>
                    </div>
                </form>

                <?php if ($calculated) :?>
                    <div class="box-secondary box-secondary-first">
                        <div class="row">
                            <?php if (!$errorMessage) : ?>
                                <div class="col-xs-3">
                                    <a href=".form-frete" data-toggle="collapse" title="Informar outro CEP">
                                                <span class="visible-xs">
                                                    <br>
                                                    Editar
                                                </span>
                                                <span class="hidden-xs btn">
                                                    <span class="<?php icon('edit'); ?>"></span>
                                                    Informar outro CEP
                                                </span>
                                    </a>
                                </div>
                                <div class="col-xs-9 text-right">
                                    <a href="?cancelar-simulacao-frete=1" id="cancelar-simulacao-frete" title="Cancelar consulta"><span class="<?php icon('close'); ?>"></span></a>
                                    <?php if (!count($frete)) : ?>
                                        <?php Widget::render('components/alert', array(
                                        'type'      =>  'danger',
                                        'class'     =>  'text-center',
                                        'message'   =>  'Nenhuma forma de frete disnpon&iacute;vel no momento'
                                    )); ?>
                                    <?php elseif (!is_null($address)) : ?>
                                        Estimativa de Frete para <?php echo $address['cidade'] , '/' , $address['uf'] ?>:
                                    <?php else : ?>
                                        Estimativa de Frete:
                                    <?php endif; ?>
                                    <?php if ($frete && count($frete)) : ?>
                                    <hr />
                                    <table class="table table-condensed pull-right" style="width: 450px">
                                        <?php foreach ($frete as $calcRow) : ?>
                                            <?php if ($calcRow['query']) : ?>
                                            <tr>
                                                <td><?php echo $calcRow['modality']->getTitulo() ?></td>
                                                <td><span class="text-success">R$ <?php echo $calcRow['query']->getValor(); ?></span><br></td>
                                                <td>
                                                    <span class="small text-muted">
                                                        Entrega em até <?php echo $calcRow['query']->getPrazoExtenso(); ?>
                                                        após a confirmação do pagamento
                                                    </span>
                                                </td>
                                            </tr>
                                            <?php elseif ($calcRow['modality']) : ?>
                                            <tr>
                                                <td colspan="3"><?php echo $calcRow['modality']->getTitulo() ?></td>
                                            </tr>
                                            <?php endif; ?>
                                        <?php endforeach; ?>
                                    </table>
                                    <?php endif; ?>
                                </div>
                            <?php else : ?>
                                <div class="col-xs-12">
                                    <?php Widget::render('components/alert', array(
                                        'type'      =>  'danger',
                                        'class'     =>  'text-center',
                                        'message'   =>  $errorMessage
                                    )); ?>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endif; ?>

                <?php
                #$valorFreteSimulado = isset($frete) ? $frete->getValor() : 0;
                $valorFreteSimulado = 0;
                $valorTotal = $container->getCarrinhoProvider()->getCarrinho()->getValorItens() + format_number($valorFreteSimulado, UsuarioPeer::LINGUAGEM_INGLES);
                Widget::render('carrinho/total', array('value' => $valorTotal, 'totalPontos'=> $somaTotalPontos));
                ?>
            </div>

            <div class="container">
                <div class="form-group">
                    <?php Widget::render('carrinho/buttons', array('container' => $container)); ?>
                </div>
            </div>
        <?php else : ?>
            <div class="container text-center">
                <div class="row">
                    <h2>Seu carrinho de compras está vazio!</h2>
                    <p>
                        Para adicionar produtos ao seu carrinho, navegue pelas categorias ou utilize a busca do site.
                        <br>
                        Você pode clicar no botão abaixo para ser redirecionado à página de produtos.
                    </p>
                    <div class="col-xs-12 col-md-8 col-md-offset-2 col-lg-6 col-lg-offset-3">
                        <div class="form-group">
                            <a href="<?php echo get_url_site(); ?>" class="btn btn-block btn-theme">Voltar para a home</a>
                        </div>
                    </div>
                </div>
            </div>
        <?php endif; ?>
    </div>
</main>

<?php include QCOMMERCE_DIR . '/includes/footer.php'; ?>
</body>
</html>