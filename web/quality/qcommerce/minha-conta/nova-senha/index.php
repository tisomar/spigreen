<?php
use QPress\Template\Widget;
$isCentral = ClientePeer::isAuthenticad();
$strIncludesKey = 'minha-conta-nova-senha';
include_once QCOMMERCE_DIR . '/login/actions/nova_senha.actions.php';
include_once QCOMMERCE_DIR . '/includes/head.php';
?>

<body itemscope itemtype="http://schema.org/WebPage">
<?php include_once QCOMMERCE_DIR . '/includes/javascript_body_inicio.php'; ?>
<?php include_once QCOMMERCE_DIR . '/includes/facebook_tracking/fb.general.tracking.php'; ?>
<?php Widget::render('general/header'); ?>

<main role="main">
    <?php
    Widget::render('components/breadcrumb', array('links' => array('Home' => '/home','Minha conta' => '/minha-conta/pedidos','Definir nova senha' => '')));
    Widget::render('general/page-header', array('title' => 'Definir nova senha'));
    Widget::render('components/flash-messages');
    ?>
    <div class="container">
        <div class="row">
            <div class="col-xs-12 col-md-3">
                <?php Widget::render('general/menu-account'); ?>
            </div>
            <div class="col-xs-12 col-md-9">
                <h3>Defina uma nova senha com no mínimo 6 caracteres.</h3>
                <?php Widget::render('forms/nova-senha', array(
                    'strIncludesKey'    =>  $strIncludesKey,
                    'btnClass'          =>  'btn-theme'
                )); ?>
            </div>
        </div>
    </div>
</main>

<?php include QCOMMERCE_DIR . '/includes/footer.php'; ?>
<?php include QCOMMERCE_DIR . '/minha-conta/components/link-modal.php'; ?>
</body>
</html>