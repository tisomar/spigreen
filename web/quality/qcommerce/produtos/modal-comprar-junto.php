<?php
use QPress\Template\Widget;

$strIncludesKey = 'produto-detalhes';
include_once __DIR__ . '/../includes/head.php';

$produtos = json_decode($container->getRequest()->query->get('relacionados'));

$objProduto     = ProdutoPeer::retrieveByPK($container->getRequest()->query->get('produto'));
$colProdutos    = ProdutoQuery::create()
    ->filterById($produtos, Criteria::IN)
    ->find();

$totalSemDesconto = $objProduto->getValorBase();
$totalComDesconto = $objProduto->getValor();

?>
<body itemscope itemtype="http://schema.org/WebPage" class="lightbox" data-page="<?php echo $strIncludesKey; ?>">
<?php include_once QCOMMERCE_DIR . '/includes/javascript_body_inicio.php'; ?>

<?php
Widget::render('mfp-modal/header', array(
    'title' => 'Compre junto!'
));
?>
<main role="main">

    <div class="box-flash-messages">
        <?php Widget::render('components/flash-messages'); ?>
    </div>

    <div class="container">
        <form class="form-ajax-adicionar-ao-carrinho" data-modal="true" method="post" action="<?php echo get_url_site() ?>/carrinho/actions/adicionar/">
            <div class="row">
                <div class="col-xs-12 col-sm-8">

                    <?php
                    Widget::render('produto-comprar-junto/produto-modal', array(
                        'objProduto' => $objProduto,
                    ));

                    foreach ($colProdutos as $objProdutoRelacionado) {
                        Widget::render('produto-comprar-junto/produto-modal', array(
                            'objProduto' => $objProdutoRelacionado,
                        ));
                        $totalSemDesconto += $objProdutoRelacionado->getValorBase();
                        $totalComDesconto += $objProdutoRelacionado->getValor();
                    }

                    ?>

                </div>

                <div class="col-xs-12 col-sm-4">
                    <div data-content-id="produto_detalhe_compre_junto_price" class="box-secondary bg-default">
                        <?php
                        Widget::render('produto-comprar-junto/wrapper-total-price-box', array(
                            'totalSemDesconto'  => $totalSemDesconto,
                            'totalComDesconto'  => $totalComDesconto,
                            'submit'            => true
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