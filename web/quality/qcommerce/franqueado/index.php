<?php

use QPress\Template\Widget;
$strIncludesKey = 'franqueado';

include_once __DIR__ . '/actions/index.actions.php';
include_once __DIR__ . '/../includes/head.php';

?>
<body itemscope itemtype="http://schema.org/AboutPage" data-page="franqueado">
<?php include_once QCOMMERCE_DIR . '/includes/javascript_body_inicio.php'; ?>
<?php include_once __DIR__ . '/../includes/facebook_tracking/fb.general.tracking.php'; ?>
<?php Widget::render('general/header'); ?>

<main role="main">
    <?php
    Widget::render('components/flash-messages');
    $url = 'http://oxi3.com.br/ambiente_testes/web/arquivos/banners/3e47970cc51f8546d078e5892e3be48d560cbf53.jpeg';
    
    ?>
    <section>
        <div class="bg-banner-franqueado banner-full" style="background: #F0F0F0 <?php echo isset($url) && !empty($url) ? sprintf('url(%s)', $url) : '' ?>; background-size: cover; background-position: 50% 50%; ">
            <div class="container">
                <div class="row franqueado" style="">       
                    
                    <div class="visible-xs">
                        <div class="col-xs-12">
                            <?php echo $objHotsite->getThumb('width=155&height=124&cropratio=1.25:1', array('class' => 'center-block img-responsive')); ?>
                        </div>                      
                    </div>
                    
                    <div class="col-xs-12 col-sm-7 descricao">
                        <?php echo $objHotsite->getDescricao() ?>
                    
                        <div class="tit">
                            <?php echo $objHotsite->getNome() ?>
                            <br>
                            <small class="muted"><?php echo $objHotsite->getEmail() ?></small>
                        </div>
                    </div>
                    <div class="hidden-xs col-sm-5">
                    <?php $foto = asset('/arquivos/hotsite/').$objHotsite->getFoto() ?>
                        <img class="center-block img-responsive" width="300" height="300" src="<?php echo $foto ?>" alt="">
                        <div class="center-block text-center">
                            <a class="btn btn-theme" href="<?php echo get_url_site() . '/home/validPatrocinador?codigo_patrocinador=' . $objHotsite->getCliente()->getChaveIndicacao(); ?>">Torne-se um distribuidor</a>
                        </div>
                    </div>
                    
                    <div class="visible-xs center-block text-center">
                        <a class="btn btn-theme" href="<?php echo get_url_site() . '/home/validPatrocinador?codigo_patrocinador=' . $objHotsite->getCliente()->getChaveIndicacao(); ?>">Torne-se um distribuidor</a>
                    </div>
                </div>
            </div>
        </div>
    </section>
    
    <div class="container">
        
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
