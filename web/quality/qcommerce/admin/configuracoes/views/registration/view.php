<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="pt-br">
    <head>
        <?php include_once QCOMMERCE_DIR . '/admin/_2015/layout/head.php'; ?>
    </head>
    <body class="static-header">
        <div id="page-container">
            <div>
                <div id='wrap'>
                    <div id="page-heading">
                        <h1><?php echo $pageTitle ?></h1>
                        <div class="options">
                            <div class="btn-toolbar">
                                <?php
                                if (file_exists(QCOMMERCE_DIR . '/admin/' . $router->getModule() . '/components/' . $router->getAction() . '/links.php')) {
                                    include QCOMMERCE_DIR . '/admin/' . $router->getModule() . '/components/' . $router->getAction() . '/links.php';
                                } else {
                                    include QCOMMERCE_DIR . '/admin/_2015/components/' . $router->getAction() . '/links.php';
                                }
                                ?>
                            </div>
                        </div>
                    </div>
                    <div class="container">
                        <div class="col-md-12">
                            <?php include QCOMMERCE_DIR . '/admin/_2015/layout/flash-messages.php'; ?>
                            <?php include QCOMMERCE_DIR . '/admin/' . $router->getModule() . '/components/' . $router->getAction() . '/content.php'; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <?php include_once QCOMMERCE_DIR . '/admin/_2015/layout/javascripts.php'; ?>
    </body>
</html>
