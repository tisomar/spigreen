<?php
$strIncludesKey = 'login';

include_once __DIR__ . '/actions/recuperar_senha.actions.php';
include_once __DIR__ . '/../includes/head.php';
?>

<body itemscope itemtype="http://schema.org/WebPage">
    <?php include(QCOMMERCE_DIR . "/includes/header.php"); ?>

    <main role="main" class="recuperar-senha">
        <div class="wrapper-grids">
            <div class="col-24">
                <?php if (!FlashMsg::hasMessages()) {
                    FlashMsg::info('Esqueceu sua senha? Nós o ajudaremos a recuperá-la! Por favor, digite o seu e-mail no campo abaixo para enviarmos as instruções para você.');
                }

                FlashMsg::display(); // Exibindo mensagens
                ?>
            </div>
            <div class="col-4 pull-1">
                <?php include(QCOMMERCE_DIR . "/includes/sidebar.inc.php"); ?>
            </div>
            <div class="col-19">
                <div id="content">
                    <div class="page-title">
                        <h1 class="title">Recuperar senha</h1>
                    </div>

                    <?php echo get_contents(__DIR__ . '/../includes/breadcrumb.inc.php', array('links' => array('Home' => '/home', 'Login' => '/login', 'Recuperar senha' => ''))); ?>

                    <?php include("components/form.inc.php"); ?>

                </div>
            </div>
        </div>
    </main>

    <?php include QCOMMERCE_DIR . '/includes/footer.php'; ?>
    <?php if ($strIncludesKey !== '') : ?>
        <script type="text/javascript" src="<?php echo $root_path; ?>/js/min/<?php echo $strIncludesKey; ?>.js"></script>
    <?php endif; ?>
</body>
</html>