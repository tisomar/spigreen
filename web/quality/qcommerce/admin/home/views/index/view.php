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
                <?php
                if (file_exists(QCOMMERCE_DIR . '/admin/' . $route->getModule() . '/components/' . $route->getAction() . '/breadcrumb.php')) {
                    include QCOMMERCE_DIR . '/admin/' . $route->getModule() . '/components/' . $route->getAction() . '/breadcrumb.php';
                }
                ?>
                <h1><?php echo $pageTitle ?></h1>
                <div class="options">
                    <div class="btn-toolbar">
                        <div class="btn-group">
                            <?php
                            if (file_exists(QCOMMERCE_DIR . '/admin/' . $route->getModule() . '/components/' . $route->getAction() . '/links.php')) {
                                include QCOMMERCE_DIR . '/admin/' . $route->getModule() . '/components/' . $route->getAction() . '/links.php';
                            } elseif (file_exists(QCOMMERCE_DIR . '/admin/_2015/components/' . $route->getAction() . '/links.php')) {
                                include QCOMMERCE_DIR . '/admin/_2015/components/' . $route->getAction() . '/links.php';
                            }
                            ?>
                        </div>
                    </div>
                </div>
            </div>
            <div class="container">
                <div class="row">
                    <div class="col-md-12">
                        <div class="panel panel-primary">
                            <div class="panel-body">
                                <?php include QCOMMERCE_DIR . '/admin/_2015/layout/flash-messages.php'; ?>
                                <?php
                                if (!file_exists(QCOMMERCE_DIR . '/admin/' . $route->getModule() . '/components/' . $route->getAction() . '/content.php')) {
                                    throw new Exception('Você deve criar o arquivo "/' . $route->getModule() . '/components/' . $route->getAction() . '/content.php" do seu módulo');
                                }
                                include QCOMMERCE_DIR . '/admin/' . $route->getModule() . '/components/' . $route->getAction() . '/content.php';
                                ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <?php include_once QCOMMERCE_DIR . '/admin/_2015/layout/footer.php'; ?>
</div>
<?php include_once QCOMMERCE_DIR . '/admin/_2015/layout/javascripts.php'; ?>
</body>
</html>
