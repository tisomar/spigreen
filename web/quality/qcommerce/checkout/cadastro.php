<?php
use QPress\Template\Widget;
$strIncludesKey = 'checkout-cadastro';
include QCOMMERCE_DIR . '/cadastro/actions/cadastro.actions.php';
include_once __DIR__ . '/../includes/head.php';
?>
<body itemscope itemtype="http://schema.org/WebPage">
<?php include_once QCOMMERCE_DIR . '/includes/javascript_body_inicio.php'; ?>
    <?php include_once QCOMMERCE_DIR . '/includes/facebook_tracking/fb.lead.tracking.php'; ?>
    <?php include QCOMMERCE_DIR . '/includes/header-checkout.php'; ?>

    <main role="main">
        <?php
            Widget::render('components/flash-messages');
            Widget::render('general/steps-checkout', array('active' => 1, 'progress' => '25'));
        ?>

        <div class="container">
            <form role="form" method="post" class="form-disabled-on-load">
                <input type="hidden" name="redirecionar" value="<?php echo $request->request->get('redirecionar'); ?>">

                <?php Widget::render('forms/tipo-de-pessoa'); ?>

                <div class="row">
                    <div id="company-data" class="collapse col-xs-12 col-md-6">
                        <hr>
                        <?php Widget::render('forms/dados-juridicos', array('objCliente' => $objCliente)); ?>
                    </div>
                    <div class="col-xs-12 col-md-6">
                        <hr>
                        <?php Widget::render('forms/dados-pessoais', array('objCliente' => $objCliente)); ?>
                    </div>
                    <div class="col-xs-12 col-md-6">
                        <hr>
                        <div class="jumbotrom">
                            <?php Widget::render('forms/dados-endereco', array('objEndereco' => new Endereco())); ?>
                        </div>
                    </div>
                </div>

                <?php Widget::render('forms/receber-newsletter'); ?>

                <div class="form-group">
                    <div class="row">
                        <div class="col-xs-12">
                            <button type="submit" class="btn btn-primary btn-block">Cadastrar</button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </main>

    <?php include_once __DIR__ . '/../includes/footer-checkout.php'; ?>
</body>
</html>
