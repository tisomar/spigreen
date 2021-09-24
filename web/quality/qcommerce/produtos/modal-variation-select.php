<?php
use QPress\Template\Widget;
$strIncludesKey = 'produto-detalhes';
include_once __DIR__ . '/../includes/head.php';

$productId  = $container->getRequest()->query->get('produto_id');
$objProduto = ProdutoPeer::retrieveByPK($productId);

$produtoLayoutVariacao = Config::get('produto_layout_variacao');

?>
<body itemscope itemtype="http://schema.org/WebPage" class="lightbox" data-page="<?php echo $strIncludesKey; ?>">
<?php include_once QCOMMERCE_DIR . '/includes/javascript_body_inicio.php'; ?>

<?php
Widget::render('mfp-modal/header', array(
    'title' => $objProduto->getNome()
));
?>

<main role="main">

    <div class="box-flash-messages">
        <?php Widget::render('components/flash-messages'); ?>
    </div>

    <div class="container">
        <form class="form-ajax-adicionar-ao-carrinho" data-modal="true" method="post" action="#">
            <div class="row">

                <?php $classCols = 'col-xs-12'; ?>
                <?php if ($produtoLayoutVariacao != Config::PRODUTO_LAYOUT_VARIACAO_GRADE) : ?>
                    <?php $classCols = 'col-xs-12 col-sm-6' ?>
                    <div class="col-xs-12 col-sm-6">
                        <div data-content-id="produto_detalhe_galeria_fotos_<?php echo $objProduto->getId() ?>">
                            <?php
                            Widget::render('produto-detalhe/galeria-fotos', array(
                                'objProduto' => $objProduto,
                                'disableVerticalGallery' => false,
                            ));
                            ?>
                        </div>
                    </div>
                <?php endif; ?>

                <div class="<?php echo $classCols; ?> product-info">
                    <div class="box-secondary">
                        <?php
                        Widget::render('produto-detalhe/variacao-component', array(
                            'objProdutoDetalhe' => $objProduto,
                            'produtoVariacaoLayout' => $produtoLayoutVariacao,
                        ));
                        ?>
                    </div>
                </div>
            </div>
        </form>
    </div>

</main>
<?php include_once __DIR__ . '/../includes/footer-lightbox.php' ?>

<script>
    $(function() {
        $(document).ready(function() {
            $('.box-exibir-grade').hide();
        });
    });
</script>

</body>
</html>
