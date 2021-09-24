<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="pt-br">
    <head>
        <?php include_once QCOMMERCE_DIR . '/admin/_2015/layout/head.php'; ?>
    </head>
    <body>
        <?php include_once QCOMMERCE_DIR . '/admin/_2015/layout/header.bar.php'; ?>
        <?php include_once QCOMMERCE_DIR . '/admin/_2015/layout/navbar.top.php'; ?>
        <div id="page-container">
            <?php include_once QCOMMERCE_DIR . '/admin/_2015/layout/navbar.side.php'; ?>
            <div id="page-content">
                <div id='wrap'>
                    <div id="page-heading">
                        <?php include QCOMMERCE_DIR . '/admin/' . $router->getModule() . '/components/' . $router->getAction() . '/breadcrumb.php' ?>
                        <h1><?php echo $pageTitle ?></h1>
                        <div class="options">
                            <div class="btn-toolbar">
                                <?php
                                if (file_exists(QCOMMERCE_DIR . '/admin/' . $router->getModule() . '/components/' . $router->getAction() . '/links.php')) {
                                    include QCOMMERCE_DIR . '/admin/' . $router->getModule() . '/components/' . $router->getAction() . '/links.php';
                                }
                                ?>
                            </div>
                        </div>
                    </div>
                    <div>
                        <?php include QCOMMERCE_DIR . '/admin/' . $router->getModule() . '/components/' . $router->getAction() . '/content.php'; ?>
                    </div>
                </div>
            </div>
            <?php include_once QCOMMERCE_DIR . '/admin/_2015/layout/footer.php'; ?>
        </div>
        <?php include_once QCOMMERCE_DIR . '/admin/_2015/layout/javascripts.php'; ?>
    </body>
</html>