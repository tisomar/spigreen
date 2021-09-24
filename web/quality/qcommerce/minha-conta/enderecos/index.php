<?php
/* @var $objEnderecos Endereco */
use QPress\Template\Widget;
$strIncludesKey = 'minha-conta-enderecos';
include QCOMMERCE_DIR . "/includes/security.php";
include QCOMMERCE_DIR . "/includes/head.php";
include 'actions/enderecos.actions.php';
$isLightbox = $request->query->get('isLightbox');
?>
<?php if ($isLightbox == false) : ?>
    <body itemscope itemtype="http://schema.org/WebPage" data-page="minha-conta-enderecos">
    <?php include_once QCOMMERCE_DIR . '/includes/javascript_body_inicio.php'; ?>
    <?php include_once QCOMMERCE_DIR . '/includes/facebook_tracking/fb.general.tracking.php'; ?>
    <?php Widget::render('general/header'); ?>

    <main role="main">
        <?php
        Widget::render('components/breadcrumb', array('links' => array('Home' => '/home', 'Minha Conta' => 0, 'Meus Endereços' => '')));
        Widget::render('general/page-header', array('title' => 'Meus Endereços'));
        Widget::render('components/flash-messages');
        ?>
        <div class="container">
            <div class="row">
                <div class="col-xs-12 col-md-3">
                    <?php Widget::render('general/menu-account', array('strIncludesKey' => $strIncludesKey)); ?>
                </div>
                <div class="col-xs-12 col-md-9">
                    <div class="row">
                        <div class="col-sm-8">
                            <h3>
                                Gerencie seus endereços de entrega.
                            </h3>
                        </div>
                        <div class="col-sm-4">
                            <a href="<?php echo get_url_site(); ?>/minha-conta/enderecos/cadastro?isLightbox=1" class="btn btn-theme btn-sm btn-block pull-right" data-lightbox="iframe">
                                <span class="<?php icon('plus-circle'); ?>"></span>
                                Adicionar novo endereço
                            </a>
                        </div>
                    </div>
                    <br>
                    <div class="row">
                        <div class="col-xs-12">
                            <?php if ($arrEnderecos->count()) : ?>
                                <?php foreach ($arrEnderecos as $oEndereco) : ?>
                                    <?php Widget::render('general/delivery-address', array(
                                        'editable'          => true,
                                        'address'           => $oEndereco,
                                    )); ?>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <?php include QCOMMERCE_DIR . '/includes/footer.php'; ?>
    <?php include QCOMMERCE_DIR . '/minha-conta/components/link-modal.php'; ?>
    </body>

<?php else :?>
    <body itemscope itemtype="http://schema.org/WebPage">
    <header class="container">
        <h1 class="h2">Meus endereços</h1>
    </header>
    <main role="main">
        <div class="container">
            <?php Widget::render('components/flash-messages'); ?>
            <?php include QCOMMERCE_DIR . '/minha-conta/enderecos/components/enderecos.php'; ?>
        </div>
    </main>
    <?php include_once QCOMMERCE_DIR . '/includes/footer-lightbox.php' ?>
    </body>
<?php endif; ?>
</html>
