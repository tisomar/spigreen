<?php
use QPress\Template\Widget;
$strIncludesKey = '';
include_once __DIR__ . '/../includes/head.php';
?>
<body itemscope itemtype="http://schema.org/WebPage" data-page="pagina-nao-encontrada">
<?php include_once QCOMMERCE_DIR . '/includes/javascript_body_inicio.php'; ?>
    <?php include_once QCOMMERCE_DIR . '/includes/facebook_tracking/fb.general.tracking.php'; ?>
    <?php Widget::render('general/header'); ?>
    
    <main role="main">
        <?php
            Widget::render('components/breadcrumb', array('links' => array('Home' => '/home', 'Página não encontrada' => '')));
            Widget::render('components/flash-messages');
        ?>
        <div class="container">
            <div class="row">
                <div class="col-xs-12 col-md-8 col-md-offset-2 col-lg-6 col-lg-offset-3">
                    <div class="text-center">
                        <h1>404</h1>
                        <h2>Oops! Sua página não foi encontrada!</h2>
                        <p>A página que você está tentando acessar não existe ou não esta mais disponível.</p>
                    </div>
                    <div class="form-group">
                        <a href="<?php echo $root_path; ?>/home" class="btn btn-theme btn-block">Voltar a home</a>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <?php include QCOMMERCE_DIR . '/includes/footer.php'; ?>
</body>
</html>