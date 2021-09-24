<?php
use QPress\Template\Widget;

include QCOMMERCE_DIR . '/includes/security.php';
include __DIR__ . '/actions/endereco.actions.php';

$carrinho = $container->getCarrinhoProvider()->getCarrinho();
$colEnderecos = ClientePeer::getClienteLogado()->getEnderecos();

$strIncludesKey = 'checkout-endereco';
include_once __DIR__ . '/../includes/head.php';

?>
<body itemscope itemtype="http://schema.org/WebPage" data-page="checkout-endereco">
<?php include_once QCOMMERCE_DIR . '/includes/javascript_body_inicio.php'; ?>
<?php include_once QCOMMERCE_DIR . '/includes/facebook_tracking/fb.payment_process.tracking.php'; ?>
<?php include QCOMMERCE_DIR . '/includes/header-checkout.php'; ?>

<main role="main">
    <?php
    Widget::render('general/steps-checkout', array('active' => 1, 'progress' => '0'));
    Widget::render('components/flash-messages');
    Widget::render('general/page-header', array('title' => 'Endereço de entrega'));
    Widget::render('carrinho/buttonContinuaCompra');

    ?>
    <div class="container">
        <?php
        if ($colEnderecos->count() == 0) {
            Widget::render('components/alert', array(
                'type'      => 'info',
                'message'   => 'Adicione um endereço para finalizar a sua compra.'
            ));
        } else {
            echo '<h3>Escolha o endereço que você deseja receber a sua mercadoria.</h3><br>';
        }
        ?>
        <div class="row equals">
            <?php if ($colEnderecos->count() > 0) : ?>
                <?php foreach ($colEnderecos as $oEndereco) : ?>
                    <div class="col-xs-12 col-sm-6 col-md-4">
                        <?php
                        Widget::render('general/delivery-address', array(
                            'editable'          => true,
                            'address'           => $oEndereco,
                            'strIncludesKey'    => $strIncludesKey
                        ));
                        ?>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>

            <div class="col-xs-12 col-sm-6 col-md-4">
                <a href="<?php echo get_url_site(); ?>/minha-conta/enderecos/cadastro" class="btn btn-block btn-default add-address" data-lightbox="iframe">
                    <span class="<?php icon('plus-circle 3x'); ?>"></span>
                    <br>
                    Adicionar novo endereço
                </a>
            </div>
        </div>
    </div>
    <br>
</main>

<?php include QCOMMERCE_DIR . '/includes/footer-checkout.php'; ?>
</body>
</html>
