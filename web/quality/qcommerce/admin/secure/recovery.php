<?php
require __DIR__ . '/actions/' . $router->getAction() . '.action.php';
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="pt-br">
    <head>
        <?php include_once QCOMMERCE_DIR . '/admin/_2015/layout/head.php'; ?>
        <script>
            $(function() {
                $('form [type="submit"]').click(function() {
                    $(this).parents('form').addClass('validate');
                });
                $('.close').click(function() {
                    $(this).parents('.alert').slideUp(function() {
                        $(this).remove()
                    });
                })
            });
        </script>
    </head>
    <body class="focusedform">
        <form action="" class="form-horizontal" method="POST" style="margin-bottom: 0px !important;">
            <div class="verticalcenter">
                <div class="panel panel-primary">
                    <div class="panel-body">

                        <img src="<?php echo Config::getLogo()->forceUrlImageResize('width=165&height=64') ?>" alt="Logo"  class="center-block" />
                        <h4 class="text-center" style="margin-bottom: 25px;">Esqueceu sua senha <i class="icon-question-sign"></i></h4>

                        <?php include QCOMMERCE_DIR . '/admin/_2015/layout/flash-messages.php'; ?>

                        <div class="form-group">
                            <div class="col-sm-12">
                                <div class="input-group">
                                    <span class="input-group-addon"><i class="icon-user"></i></span>
                                    <input type="text" class="form-control" autofocus id="username" placeholder="Seu Login" required name="login" value="<?php echo $request->request->get('login') ?>" />
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="panel-footer">
                        <a href="<?php echo get_url_admin() ?>/secure/login/" class="pull-left btn btn-link" style="padding-left:0">&laquo; ir para login</a>
                        <div class="pull-right">
                            <button type="submit" class="btn btn-primary"><i class="icon-ok"></i> Solicitar</button>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </body>
</html>