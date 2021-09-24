<style>
    .imgbox img{
        max-width:110%; 
        height:auto;
    }
</style>
<?php if (Config::get('mostrar_marcas')): ?>
    <?php $collMarcas = MarcaQuery::create()->find(); ?>
    <?php if (count($collMarcas) > 0): ?>
        <div class="container">
            <div id="carousel-marca" class="owl-carousel owl-theme">
                <?php foreach ($collMarcas as $objMarca): /* @var $objMarca Marca */ ?>
                    <div class="item">
                        <a href="<?php echo get_url_site() . '/produtos/marca/' . $objMarca->getSlug(); ?>" title="<?php echo htmlspecialchars($objMarca->getNome()) ?>">
                            <!-- <?php echo $objMarca->getThumb('width=80&height=64&cropratio=1.25:1', array('class' => 'img-responsive')); ?> -->
                            <div class='row col-sm-12 imgbox'>
                                <img src="<?php echo asset('/arquivos/banners/'). $objBanner->getImagemMd() ?>" alt="" >
                            </div>
                        </a>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    <?php endif; ?>
<?php endif; ?>