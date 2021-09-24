<?php
use QPress\Template\Widget;

/**
 * Actions ============================================================
 */
$strIncludesKey = 'produto-detalhes';

// Busca o produto
$objProduto = ProdutoQuery::create()->findOneById($container->getRequest()->query->get('pid'));


/**
 * Layout ============================================================
 */
include_once __DIR__ . '/../includes/head.php';
?>
<body itemscope itemtype="http://schema.org/WebPage" class="lightbox" data-page="<?php echo $strIncludesKey; ?>">
<?php include_once QCOMMERCE_DIR . '/includes/javascript_body_inicio.php'; ?>

<?php
Widget::render('mfp-modal/header', array(
    'title' => 'Adicionar ao carrinho'
));
?>
<main role="main">
    <div class="box-flash-messages">
        <?php Widget::render('components/flash-messages'); ?>
    </div>
    <div class="container">
        <form class="form-ajax-adicionar-ao-carrinho" data-modal="true" method="post" action="<?php echo get_url_site() ?>/carrinho/actions/adicionar/">
            <?php
            Widget::render(__DIR__ . '/components/layout-variacao/grade.template.php', array(
                'objProduto' => $objProduto,
            ));
            Widget::render("produto-detalhe/btn_comprar", array(
                'objProduto' => $objProduto
            ));
            ?>
        </form>
    </div>
</main>

<?php include_once __DIR__ . '/../includes/footer-lightbox.php' ?>

<?php if ($container->getRequest()->getMethod() == "POST") : ?>
    <script>
        $(function() {
            parent.$('.update-cart').load('<?php echo get_url_site() ?> .cart');
        });
    </script>
<?php endif; ?>

</body>
</html>
