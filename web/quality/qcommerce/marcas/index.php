<?php
use QPress\Template\Widget;
$strIncludesKey = 'marcas';
include_once __DIR__ . '/actions/index.actions.php';
include_once __DIR__ . '/../includes/head.php';
?>
<body itemscope itemtype="http://schema.org/WebPage" data-page="marcas">
<?php include_once QCOMMERCE_DIR . '/includes/javascript_body_inicio.php'; ?>
    <?php include_once QCOMMERCE_DIR . '/includes/facebook_tracking/fb.general.tracking.php'; ?>
    <?php Widget::render('general/header'); ?>

    <main role="main">
        <?php
            Widget::render('components/breadcrumb', array('links' => array('Home' => '/home', 'Marcas' => '')));
            Widget::render('general/page-header', array('title' => 'Marcas'));
            Widget::render('components/flash-messages');
        ?>
        <div class="container">
            <div class="row">
                <?php foreach ($pager->getResults() as $objMarca) : ?>
                <div class="list-unstyled text-center col-xs-6 col-sm-3 col-md-2">
                    <a href="<?php echo $objMarca->getUrlListagem() ?>" class="thumbnail">
                        <?php echo $objMarca->getThumb('width=155&height=124&cropratio=1.25:1', array('class' => 'img-responsive')); ?>
                    </a>
                </div>
                <?php endforeach; ?>
            </div>

            <?php
                Widget::render('components/pagination', array(
                    'pager' => $pager,
                    'href'  => get_url_site() . '/marcas/',
                    'align' => 'center'
                ));
                ?>
        </div>
    </main>

    <?php include QCOMMERCE_DIR . '/includes/footer.php'; ?>
</body>
</html>
