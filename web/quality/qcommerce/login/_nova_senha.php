<?php

$strIncludesKey = '';

include_once __DIR__ . '/actions/nova_senha.actions.php';
include_once QCOMMERCE_DIR . '/includes/head.php';
?>

<body>
    <?php include(QCOMMERCE_DIR . "../includes/noscript.inc.php"); ?>
    <?php include(QCOMMERCE_DIR . "../includes/header.php"); ?>
    
    <main role="main">
        <div class="wrapper-grids">
            <div class="col-24">
                <?php if (!FlashMsg::hasMessages()) {
                    FlashMsg::info('Utilize o formulário abaixo para informar a sua nova senha e poder acessar o sistema.');
                }

                FlashMsg::display(); // Exibindo mensagens
                ?>
            </div>
            <div class="col-4 pull-1">
                <?php include("../includes/sidebar.inc.php"); ?>
            </div>
            <div class="col-19">
                <div id="content">
                    <div class="page-title">
                        <h1 class="title">Informar nova senha</h1>
                    </div>

                    <?php echo get_contents(__DIR__ . '/../includes/breadcrumb.inc.php', array('links' => array('Home' => '/home', 'Login' => '/login', 'Recuperar senha' => '/login/recuperar_senha', 'Informar nova senha' => ''))); ?>

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
