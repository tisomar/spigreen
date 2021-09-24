<?php
use QPress\Template\Widget;

/**
 * GERA O TOKEN POR AJAX
 */
if ($container->getRequest()->isXmlHttpRequest()) {
    require_once QCOMMERCE_DIR . '/boleto/action/itau-shopline.action.php';
    exit;
}

/**
 * GERA O BOLETO
 */
$pedidoFormaPagamento = PedidoFormaPagamentoQuery::create()
    ->joinWith(PedidoPeer::OM_CLASS)
    ->filterByHashBoleto($router->getArgument(0))
    ->findOne()
;

if (is_null($pedidoFormaPagamento)) {
    header('Cache-Control: no-store, no-cache, must-revalidate, post-check=0, pre-check=0');
    header('Expires: Sat, 26 Jul 1997 05:00:00 GMT');
    redirect_404();
}

include_once __DIR__ . '/../includes/head.php';
?>
<body itemscope itemtype="http://schema.org/AboutPage" data-page="empresa">
<?php include QCOMMERCE_DIR . '/includes/header-checkout.php'; ?>

<main role="main">
    <div class="container">
        <div class="row">
            <div class="col-xs-12 col-sm-offset-3 col-sm-6">
                <br>
                <br>
                <div class="panel panel-body box-primary bg-default">
                    <h2 class="h3">Segunda via do boleto</h2>

                    <p>Desative o bloqueador de popups do seu navegador e clique no botão imprimir.</p>

                    <p class="text-muted small">
                        Atenção! Você receberá um e-mail com a confirmação e todos os detalhes do seu pedido.
                        Por favor, verifique as configurações AntiSpam do seu provedor de e-mail.
                    </p>
                    <br>
                    <form method="post" action="#" target="SHOPLINE" id="itau-shopline">
                        <input type="hidden" value="<?php echo $pedidoFormaPagamento->getUrlAcesso() ?>" name="itau-data-url" />
                        <input id="imprimir" type="submit" class="btn btn-success btn-block" value="IMPRIMIR" />
                    </form>
                </div>
            </div>
        </div>
    </div>
</main>
<script>
    function popupBloqueado() {
        var win = window.open('<?php echo get_url_site() ?>', 'teste', 'toolbar=yes,menubar=yes,resizable=yes,status=no,scrollbars=yes,width=1,height=1');
        win.close();
        return !(win);
    }

    $(function() {

        $('#itau-shopline').submit(function() {
            if (popupBloqueado()) {
                alert('Desative o seu bloqueador de popup');
            } else {
                itau.output();
            }
        });

        if (!popupBloqueado()) {
            $('#imprimir').trigger('click');
        }

    })
</script>
<?php include_once QCOMMERCE_DIR . '/includes/footer-checkout.php'; ?>
</body>
</html>
