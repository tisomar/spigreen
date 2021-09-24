<?php
use QPress\Template\Widget;
$strIncludesKey = 'checkout-confirmacao';
require_once __DIR__ . '/../includes/security.php';
include_once __DIR__ . '/../classes/Tracking.php';
include_once __DIR__ . '/actions/confirmacao.actions.php';

$csDescription =  'code=' . $objPedido->getId();
$csPaymentType = isset($_SESSION['cs_payment_type']) ? $_SESSION['cs_payment_type'] : null;
if ($csPaymentType) {
    $csDescription .= ', payment-type=' . $csPaymentType;
    unset($_SESSION['cs_payment_type']);
}
ClearSaleMapper\Manager::set('page', 'purchase-confirmation');
ClearSaleMapper\Manager::set('description', $csDescription);

include_once __DIR__ . '/../includes/head.php';
?>
<body itemscope itemtype="http://schema.org/WebPage" data-page="<?php echo $strIncludesKey; ?>">
<?php include_once QCOMMERCE_DIR . '/includes/javascript_body_inicio.php'; ?>
<?php include_once QCOMMERCE_DIR . '/includes/facebook_tracking/fb.payment_confirmation.tracking.php'; ?>
<?php include_once QCOMMERCE_DIR . '/includes/header-checkout.php'; ?>

<main role="main">
    <?php
    Widget::render('general/steps-checkout', array('active' => 4, 'progress' => '100'));
    Widget::render('components/flash-messages');
    ?>
    <?php
    Widget::render('general/page-header', array(
        'title'         =>  'Seu pedido foi finalizado com sucesso!',
        'titleClass'    =>  'text-success'
    ));
    ?>
    <div class="container">


        <div class="row">
            <div class="col-xs-12 col-sm-6 col-md-6">
                <div class="panel panel-body box-primary  bg-default">
                    <h2 class="h3">O número do seu pedido é <span class="text-success"><?php echo $objPedido->getId() ?></span></h2>
                    <p>
                        Atenção! Você receberá um e-mail com a confirmação e todos os detalhes do seu pedido.
                    </p>
                    <p>
                        Por favor, verifique as configurações AntiSpam do seu provedor de e-mail.
                    </p>
                    <p class="text-muted">
                        <a class="btn-link" href="<?php echo $root_path; ?>/minha-conta/pedidos">
                            Clique aqui para acompanhar seu pedido.
                        </a>
                    </p>
                </div>
            </div>
            <div class="col-xs-12 col-sm-6  col-md-6">
                <div class="panel panel-body box-primary">
                    <h2 class="h3">Forma de Pagamento</h2>
                    <?php
                    foreach ($objPedido->getPedidoFormaPagamentoLista() as $objFormaPagamento):
                        ?>
                        <div class="row" style="margin-top: 15px">
                            <?php

                            # Boleto PHP
                            include __DIR__ . '/components/confirmacao/boleto-php.php';

                            # Pagseguro (Checkout Transparente)
                            include __DIR__ . '/components/confirmacao/pagseguro-transparente-boleto.php';
                            include __DIR__ . '/components/confirmacao/pagseguro-transparente-cartao.php';
                            include __DIR__ . '/components/confirmacao/pagseguro-transparente-debito.php';

                            # Itau Shopline
                            include __DIR__ . '/components/confirmacao/itau-shopline.php';

                            /** Boleto Cielo BB */
                            include __DIR__ . '/components/confirmacao/boleto-cielo-bb.php';

                            /** Cielo Cartão de Crédito */
                            include __DIR__ . '/components/confirmacao/cielo-cartao-credito.php';

                            /** Cielo Cartão de Débito */
                            include __DIR__ . '/components/confirmacao/cielo-cartao-debito.php';

                            # SuperPay - Cartão de Crédito
                            include __DIR__ . '/components/confirmacao/superpay-cartao-credito.php';

                            # PagSeguro - Redirecionamento
                            include __DIR__ . '/components/confirmacao/pagseguro-redirecionamento.php';

                            # PayPal - Redirecionamento
                            include __DIR__ . '/components/confirmacao/paypal-redirecionamento.php';

                            # Faturamento Direto
                            include __DIR__ . '/components/confirmacao/faturamento-direto.php';

                            # Bônus Frete
                            include __DIR__ . '/components/confirmacao/pontos.php';

                            # Bônus Frete
                            include __DIR__ . '/components/confirmacao/bonus-frete.php';

                            # Pagamento em loja
                            include __DIR__ . '/components/confirmacao/pagamento-em-loja.php';

                            # Pagamento em loja
                            include __DIR__ . '/components/confirmacao/pontos-cliente-preferencial.php';

                            # Transferencia
                            include __DIR__ . '/components/confirmacao/transferencia.php';
                            ?>
                        </div>
                        <?php
                    endforeach;
                    ?>
                    <br>
                <?php if ($objPedido->getPedidoFormaPagamento()->getFormaPagamento() != PedidoFormaPagamentoPeer::FORMA_PAGAMENTO_PONTOS) : ?>
                    <p>
                        A entrega do pedido só será autorizada mediante a confirmação de pagamento.
                    </p>
                <?php endif ?>

                    <?php if (Config::getGateway() == 'pagseguro') : ?>
                        <img src="<?php echo asset('/img/pagseguro-selos/selo04_300x60.gif') ?>" class=" center-block img-responsive" alt="">
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <?php if (Config::get('ebit_banner_finalizacao')  != '') : ?>
            <div class="text-center">
                <?php echo Config::get('ebit_banner_finalizacao') ?>
            </div>
            <br>
        <?php endif; ?>

        <div class="row">
            <div class="col-xs-12 col-sm-6 col-sm-offset-3 col-lg-4 col-lg-offset-4">
                <div class="form-group">
                    <a class="btn btn-default btn-block" href="<?php echo get_url_site(); ?>">
                        <span class="<?php icon('arrow-left') ?>"></span> Voltar à página inicial
                    </a>
                </div>
            </div>
        </div>
    </div>
</main>

<?php include_once QCOMMERCE_DIR . '/includes/footer-checkout.php'; ?>
<?php
if (true == \Config::get('google_track_ecommerce')) :
    echo Tracking::generateGoogleTracking($objPedido);
endif;
?>

<script>

    let idPedido = <?= $objPedido->getId() ?>;
    let ValorTotal = <?= $objPedido->getValorTotal() ?>;
    let ValorEntrega = <?= $objPedido->getValorEntrega() ?>;

    console.log([
        "ID Pedido "+idPedido,
        "ValotTotal "+ValorTotal,
        "ValorEntrega "+ValorEntrega
    ]);

    gtag('event', 'purchase', {
        "transaction_id": "<?= $objPedido->getId() ?>",
        "affiliation": "Store Spigreen",
        "value": <?= $objPedido->getValorTotal() ?>,
        "currency": "BRL",
        "tax": 0,
        "shipping": <?= $objPedido->getValorEntrega() ?>,
        "items": ""
    });
</script>
</body>
</html>