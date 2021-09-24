<?php

$strIncludesKey = '';

include_once __DIR__ . '/actions/index.action.php';
include_once __DIR__ . '/../includes/head.php';

?>

<body itemscope itemtype="http://schema.org/WebPage">
<?php include_once QCOMMERCE_DIR . '/includes/javascript_body_inicio.php'; ?>
    <?php include_once QCOMMERCE_DIR . '/includes/facebook_tracking/fb.general.tracking.php'; ?>
    <?php Widget::render('general/header'); ?>
    
    <main role="main">
        <div class="wrapper-grids">
            <div class="col-24">
                <?php FlashMsg::display(); // Exibindo mensagens ?>
            </div>
            <div class="col-4 pull-1">
                <?php include_once QCOMMERCE_DIR . '/includes/sidebar.inc.php'; ?>
            </div>
            <div class="col-19">
                <div id="content">
                  <!-- 
                        TODO: Fazer esta página
                  -->
                </div>
            </div>
        </div>
    </main>

    <?php include_once QCOMMERCE_DIR . '/includes/footer.php'; ?>
</body>
</html>