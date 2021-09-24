<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="pt-br">
    <head>
        <?php include_once QCOMMERCE_DIR . '/admin/_2015/layout/head.php'; ?>
    </head>
    <body class="static-header">
        <?php include_once QCOMMERCE_DIR . '/admin/_2015/layout/header.bar.php'; ?>
        <?php include_once QCOMMERCE_DIR . '/admin/_2015/layout/navbar.top.php'; ?>
        <div id="page-container">
            <?php include_once QCOMMERCE_DIR . '/admin/_2015/layout/navbar.side.php'; ?>
            <div id="page-content">
                <div id='wrap'>
                    <div id="page-heading">
                        <span class="hidden-xs">
                            <?php include QCOMMERCE_DIR . '/admin/' . $router->getModule() . '/components/' . $router->getAction() . '/breadcrumb.php' ?>
                        </span>
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

                    <div class="col-xs-12">
                        <?php include QCOMMERCE_DIR . '/admin/_2015/layout/flash-messages.php'; ?>
                    </div>

                    <?php
                    # Adiciona o arquivo de filtro, caso exista.
                    if (file_exists(QCOMMERCE_DIR . '/admin/' . $router->getModule() . '/components/' . $router->getAction() . '/filter.php')) {
                        ?>
                        <div class="col-xs-12">
                            <div class="panel">
                                <div class="panel-heading">
                                    <h4><i class="icon-filter"></i> Filtros</h4>
                                    <div class="options">
                                        <a class="panel-collapse" href="#"><i class="icon-chevron-down"></i></a>
                                    </div>
                                </div>
                                <div class="panel-body collapse in">
                                    <?php
                                    include_once QCOMMERCE_DIR . '/admin/' . $router->getModule() . '/components/' . $router->getAction() . '/filter.php';
                                    ?>
                                </div>
                            </div>
                        </div>
                        <?php
                    }
                    ?>
                    <div class="col-xs-12">
                        <?php include QCOMMERCE_DIR . '/admin/' . $router->getModule() . '/components/' . $router->getAction() . '/content.php'; ?>
                    </div>
                </div>
            </div>
            <?php include_once QCOMMERCE_DIR . '/admin/_2015/layout/footer.php'; ?>
        </div>
        <?php include_once QCOMMERCE_DIR . '/admin/_2015/layout/javascripts.php'; ?>
    </body>
</html>