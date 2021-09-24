<?php
use QPress\Template\Widget;
$strIncludesKey = 'produtos';


include_once __DIR__ . '/actions/index.actions.php';

if (isset($objCategoria)) {
    ClearSaleMapper\Manager::set('page', 'category');
    ClearSaleMapper\Manager::set('description', $objCategoria->getNome());
}

include_once __DIR__ . '/../includes/head.php';
?>
<body itemscope itemtype="http://schema.org/WebPage">
<?php include_once QCOMMERCE_DIR . '/includes/javascript_body_inicio.php'; ?>

<?php include_once QCOMMERCE_DIR . '/includes/facebook_tracking/fb.general.tracking.php'; ?>
<?php Widget::render('general/header'); ?>

<main role="main" >
    <?php
    Widget::render('components/breadcrumb', array('links' => $breadcrumb));
    Widget::render('general/page-header', array('title' => isset($objCategoria) ? $objCategoria->getNome() : 'Produtos'));
    Widget::render('components/flash-messages');
    ?>
    <div class="container">
        <?php include QCOMMERCE_DIR . '/produtos/components/filtro.php'; ?>

        <?php if (isset($objCategoria) && $objCategoria->getBanner() != '') : ?>
            <div class="row">
                <div class="col-xs-12">
                    <?php echo $objCategoria->getThumb('height=114', array('class' => 'center-block img-responsive')); ?>
                </div>
            </div>
        <?php endif; ?>

        <?php

        $querySubCategorias = CategoriaQuery::create()
            ->filterByParentDisponivel(true)
            ->filterByDisponivel(true);

        $children = isset($objCategoria) ? $objCategoria->getDescendants($querySubCategorias) : array();
        if (Config::get('menu_lateral_categorias') && count($children) > 0) : ?>
            <div class="row">
                <div class="col-xs-12 col-sm-4 col-md-3">
                    <aside class="category">
                        <ul class="list-unstyled">
                            <?php foreach ($children as $categoria) : /* @var $categoria Categoria */
                                $categoria->clearAllReferences(true);
                                ?>
                                <?php if ($categoria->getLevel() - 1 == $objCategoria->getLevel()) : ?>
                                <li><a class="active bg-default" href="<?php echo $categoria->getUrl(); ?>"><?php echo $categoria->getNome(); ?></a></li>
                                <?php else : ?>
                                <li><a href="<?php echo $categoria->getUrl(); ?>"><?php echo $categoria->getNome(); ?></a></li>
                                <?php endif; ?>
                            <?php endforeach; ?>
                        </ul>
                    </aside>
                </div>
                <div class="col-xs-12 col-sm-8 col-md-9">
                    <?php Widget::render('produto/product-list', array('collProdutos' => $collProdutos, 'url' => $url, 'numeroColunas' => 3)); ?>
                </div>
            </div>
        <?php else : ?>
            <?php Widget::render('produto/product-list', array('collProdutos' => $collProdutos, 'url' => $url, 'numeroColunas' => 4)); ?>
        <?php endif; ?>

    </div>
</main>

<?php include QCOMMERCE_DIR . '/includes/footer.php'; ?>
</body>
</html>