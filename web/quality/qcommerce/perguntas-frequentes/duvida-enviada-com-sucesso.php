<?php
use QPress\Template\Widget;
$strIncludesKey = 'perguntas-frequentes';

include_once __DIR__ . '/../includes/head.php';
?>
<body itemscope itemtype="http://schema.org/AboutPage" data-page="perguntas-frequentes">
<?php include_once QCOMMERCE_DIR . '/includes/javascript_body_inicio.php'; ?>
<?php include_once QCOMMERCE_DIR . '/includes/facebook_tracking/fb.lead.tracking.php'; ?>
<?php Widget::render('general/header'); ?>

<main role="main">
    <?php
    Widget::render('components/breadcrumb', array('links' => array('Home' => '/home', 'Perguntas Frequentes' => '')));
    Widget::render('general/page-header', array('title' => 'Perguntas Frequentes'));
    Widget::render('components/flash-messages');
    ?>
    <div class="container">
        <div class="text-center">
            <h2 class="text-success">Sua dúvida foi enviada com sucesso!</h2>
            <p>
                Nossos atendentes lhe responderão no e-mail informado o mais breve possível.
            </p>
            <br>
            <a href="<?php echo get_url_site() ?>" class="btn btn-theme">Voltar a página inicial</a>
        </div>
    </div>

</main>

<?php include QCOMMERCE_DIR . '/includes/footer.php'; ?>

</body>
</html>
