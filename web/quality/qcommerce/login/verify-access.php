<?php
use QPress\Template\Widget;
$strIncludesKey = '';
$isNewReseller = $franqueadoNoValid = false;
require_once __DIR__ . '/actions/verify.actions.php';
include_once __DIR__ . '/../includes/head.php';
?>
<body itemscope itemtype="http://schema.org/WebPage" id="verify-cliente">
<?php include_once QCOMMERCE_DIR . '/includes/javascript_body_inicio.php'; ?>
    <?php include_once QCOMMERCE_DIR . '/includes/facebook_tracking/fb.general.tracking.php'; ?>
    <?php Widget::render('general/header-center-representante'); ?>

    <main role="main">
        <?php
            Widget::render('components/flash-messages');
        ?>
        <div class="container">
            <?php if (!$isNewReseller && !$franqueadoNoValid) : ?>
                <div class="row vdivide" id="menus">
                    <div class="col-xs-12 col-sm-6">
                        <?php Widget::render('general/title-representante', array('title' => 'Fazer login ou revender produtos')); ?>
                        <?php echo ConteudoPeer::get('comprar_revenda_inicial')->getDescricao() ?>
                        <a href="<?php echo get_url_site() ?>/login" id="revender" class="btn btn-theme btn-block">Revender produto Spigreen</a>
                    </div>
                    <div class="col-xs-12 visible-xs hidden-sm hidden-md hidden-lg" style="padding: 0 8px; margin-bottom: -8px">
                        <hr>
                    </div>
                    <div class="col-xs-12 col-sm-6">
                        <?php Widget::render('general/title-representante', array('title' => 'Desejo comprar produtos')); ?>
                        <?php echo ConteudoPeer::get('comprar_produtos_inicial')->getDescricao() ?>
                        <a href="<?php echo get_url_site() ?>/login" id="comprar" class="btn btn-theme btn-block">Comprar produto Spigreen</a>
                    </div>
                </div>
            <?php endif; ?>


            <?php

            $displayReseller = ($isNewReseller) ? 'block' : 'none';
            $btnResellerVoltar = $displayReseller == 'block' ? 'style="display: none;"' : '';
            $classBtnResellerVoltar = $displayReseller == 'block' ? 'col-xs-offset-3 ' : '';
            ?>
            <div class="row" id="revenda" style="display: <?php echo $displayReseller ?>">
                <div class="col-xs-12">
                    <div class="row">
                        <div class="col-xs-3" <?php echo $btnResellerVoltar ?>>
                            <div class="heading text-center" style="padding: 10px 0px">
                                <a id="voltar-revender" class="center-block">
                                    <p>Voltar</p>
                                </a>
                            </div>
                        </div>
                        <div class="<?php echo $classBtnResellerVoltar ?>col-xs-6 col-xs-offset-right-3">
                            <?php Widget::render('general/title-representante', array('title' => 'Fazer login ou revender produtos')); ?>
                        </div>
                    </div>
                    <div class="row">
                        <?php
                        Widget::render('forms/login_produtos', array('redirect' => isset($redirect) ? $redirect : null,
                            'type' => 'revenda'));
                        ?>
                    </div>
                </div>
            </div>

            <?php

            $displayFranq = ($franqueadoNoValid) ? 'block' : 'none';
            $btnFranqVoltar = $displayFranq == 'block' ? 'style="display: none;"' : '';
            $classBtnFranqVoltar = $displayFranq == 'block' ? 'col-xs-offset-3 ' : '';

            ?>

            <div class="row" id="produtos" style="display: <?php echo $displayFranq ?>">
                <div class="col-xs-12">
                    <div class="row">
                        <div class="col-xs-3" <?php echo $btnFranqVoltar ?>>
                            <div class="heading text-center" style="padding: 10px 0px">
                                <a id="voltar-produtos" class="">Voltar</a>
                            </div>
                        </div>
                        <div class="<?php echo $classBtnFranqVoltar ?>col-xs-6 col-xs-offset-right-3">
                            <?php Widget::render('general/title-representante', array('title' => 'Desejo comprar produtos')); ?>
                        </div>
                    </div>
                    <div class="row">
                        <?php
                        Widget::render('general/revendedor-select', array('redirect' => isset($redirect) ? $redirect : null,
                            'type' => 'produtos'));
                        ?>
                    </div>

                </div>
            </div>
        </div>
    </main>



    <?php include QCOMMERCE_DIR . '/includes/footer-checkout.php'; ?>

    <script>
        $(document).ready(function() {
            $('body').on('click', '#revender', function (e) {
                $('#menus').hide('fast');
                $('#revenda').show('fast');
            });

            $('body').on('click', '#comprar', function (e) {
                $('#menus').hide('fast');
                $('#produtos').show('fast');
            });

            $('body').on('click', '#voltar-revender', function (e) {
                $('#revenda').hide('fast');
                $('#menus').show('fast');
            });

            $('body').on('click', '#voltar-produtos', function (e) {
                $('#produtos').hide('fast');
                $('#menus').show('fast');
            });
        });
    </script>
</body>
</html>

