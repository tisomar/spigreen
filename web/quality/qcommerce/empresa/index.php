<?php

use QPress\Template\Widget;
$strIncludesKey = 'empresa';

include_once __DIR__ . '/actions/index.action.php';
include_once __DIR__ . '/../includes/head.php';

?>
<body itemscope itemtype="http://schema.org/AboutPage" data-page="empresa">
<?php include_once QCOMMERCE_DIR . '/includes/javascript_body_inicio.php'; ?>
<?php include_once __DIR__ . '/../includes/facebook_tracking/fb.general.tracking.php'; ?>
<?php Widget::render('general/header'); ?>

<main role="main">
    <?php
    Widget::render('components/breadcrumb', array('links' => array('Home' => '/home', 'Sobre nós' => '')));
    Widget::render('general/page-header', array('title' => 'Sobre nós'));
    Widget::render('components/flash-messages');

    ?>
    <div class="container user-content">
        <div class="row">
            <div class="col-xs-12">
                <?php echo $objConteudo->getDescricao(); ?>
            </div>
        </div>
    </div>

    <?php if (!is_null($objConteudo->getGaleria())) : ?>
        <?php
        $collImagens = GaleriaArquivoQuery::create()
            ->filterByGaleriaId($objConteudo->getGaleriaId())
            ->find();
        ?>
        <div class="container text-center">
            <div class="row">
                <div class="col-xs-12">
                    <br><br>
                    <h2><?php echo $objConteudo->getGaleria()->getNome(); ?></h2>
                    <p><?php echo $objConteudo->getGaleria()->getDescricao(); ?></p>
                    <hr>
                </div>
            </div>
        </div>
        <div class="container">
            <div class="row">
                <?php
                Widget::render('components/carousel', array(
                    'id'            => 'owl-about',
                    'collImages'    => $collImagens
                ));
                ?>
            </div>
        </div>
    <?php endif; ?>
</main>

<?php include QCOMMERCE_DIR . '/includes/footer.php'; ?>
</body>
</html>
