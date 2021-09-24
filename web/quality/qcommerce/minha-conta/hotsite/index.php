<?php
require __DIR__ . '/actions/hotsite.actions.php';
use QPress\Template\Widget;
$strIncludesKey = 'minha-conta-hotsite';
include QCOMMERCE_DIR . "/includes/security.php";
include QCOMMERCE_DIR . "/includes/head.php";
?>

<body itemscope itemtype="http://schema.org/WebPage" data-page="minha-conta-hotsite">
<?php include_once QCOMMERCE_DIR . '/includes/javascript_body_inicio.php'; ?>
<?php include_once QCOMMERCE_DIR . '/includes/facebook_tracking/fb.general.tracking.php'; ?>
<?php Widget::render('general/header'); ?>

<main role="main">
    <?php
    Widget::render('components/breadcrumb', array('links' => array('Home' => '/home', 'Minha Conta' => 0, 'Hotsite' => '')));
    Widget::render('general/page-header', array('title' => 'Hotsite'));
    Widget::render('components/flash-messages');
    ?>
    <div class="container">
        <div class="row">
            <div class="col-xs-12 col-md-3">
                <?php Widget::render('general/menu-account', array('strIncludesKey' => $strIncludesKey)); ?>
            </div>
            <div class="col-xs-12 col-md-9">
                
                <div class="row">
                    <div class="col-xs-12">
                        <?php \QPress\Template\Widget::render('forms/hotsite', array(
                                'objHotsite' => $objHotsite
                        )); ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>

<?php include QCOMMERCE_DIR . '/includes/footer.php'; ?>
<?php include QCOMMERCE_DIR . '/minha-conta/components/link-modal.php'; ?>
</body>

</html>
