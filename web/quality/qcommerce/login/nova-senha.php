<?php
use QPress\Template\Widget;
$isCentral = ClientePeer::isAuthenticad();
$strIncludesKey = 'login-nova-senha';
include_once QCOMMERCE_DIR . '/login/actions/nova_senha.actions.php';
include_once QCOMMERCE_DIR . '/includes/head.php';
?>

<body itemscope itemtype="http://schema.org/WebPage">
<?php include_once QCOMMERCE_DIR . '/includes/javascript_body_inicio.php'; ?>
    <?php include_once QCOMMERCE_DIR . '/includes/facebook_tracking/fb.general.tracking.php'; ?>
    <?php Widget::render('general/header'); ?>

    <main role="main">
        <?php
            Widget::render('components/breadcrumb', array('links' => array('Home' => '/home', 'Login' => '/login', 'Nova senha' => '')));
            Widget::render('general/page-header', array('title' => 'Nova senha'));
            Widget::render('components/flash-messages');
        ?>
        <div class="container">
            <br>
            <p class="text-center">
                Após salvar sua senha você será direcionado para a página de login.
            </p>
            <br>
            <div class="row">
                <div class="col-xs-12 col-sm-8 col-sm-offset-2 col-md-6 col-md-offset-3">
                    <?php Widget::render('forms/nova-senha', array(
                        'btnClass'          =>  'btn-theme'
                    )); ?>
                </div>
            </div>
        </div>
    </main>

<?php include QCOMMERCE_DIR . '/includes/footer.php'; ?>
</body>
</html>