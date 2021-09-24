<?php
use QPress\Template\Widget;
$strIncludesKey = 'login-esqueci-senha';
include_once __DIR__ . '/actions/recuperar_senha.actions.php';
include_once __DIR__ . '/../includes/head.php';
?>
<body itemscope itemtype="http://schema.org/WebPage">
<?php include_once QCOMMERCE_DIR . '/includes/javascript_body_inicio.php'; ?>
    <?php include_once QCOMMERCE_DIR . '/includes/facebook_tracking/fb.general.tracking.php'; ?>
    <?php Widget::render('general/header'); ?>

    <main role="main">
        <?php
            Widget::render('components/breadcrumb', array('links' => array('Home' => '/home', 'Login' => '/login', 'Esqueci minha senha' => '')));
            Widget::render('general/page-header', array('title' => 'Recuperação de senha'));
            Widget::render('components/flash-messages');
        ?>
        <div class="container">
            <br>
            <p class="text-center">As instruções para recuperação de senha serão enviadas para o seu e-mail.</p>
            <br>
            <div class="row">
                <div class="col-xs-12 col-sm-8 col-sm-offset-2 col-md-6 col-md-offset-3">
                    <form role="form" method="post" class="form-disabled-on-load">
                        <div class="form-group">
                            <label for="forgot-password-email">* E-mail:</label>
                            <input class="form-control validity-email" type="email" id="forgot-password-email" name="email" value="<?php echo isset($_POST['email']) ? $_POST['email'] : '' ?>" required>
                        </div>
                        <div class="form-group">
                            <button type="submit" class="btn btn-theme btn-block">Enviar solicitação</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </main>

    <?php include QCOMMERCE_DIR . '/includes/footer.php'; ?>
</body>
</html>
