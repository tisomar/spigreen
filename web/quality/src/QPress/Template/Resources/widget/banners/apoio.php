<style>
    .imgbox img{
        max-width:110%; 
        height:auto;
    }
</style>

<?php if (count($collBanner) > 0): ?>

    <div id="apoio-banner" class="container" style="">
        <div class="row">
            <?php foreach ($collBanner as $objBanner): /* @var $objBanner Banner */ ?>
                <div class="col-sm-4 apoio-banner-item">
                    <?php if(!is_null($objBanner->getLink())):
                        if($objBanner->getTarget() == 'iframe'):?>
                            <a href="<?php echo $objBanner->getLink(); ?>" data-lightbox="iframe" title="<?php echo htmlspecialchars($objBanner->getTitulo()); ?>">
                        <?php else: ?>
                            <a href="<?php echo $objBanner->getLink(); ?>" target="<?php echo $objBanner->getTarget() ?>" title="<?php echo htmlspecialchars($objBanner->getTitulo()); ?>">
                        <?php endif; ?>
                    <?php endif; ?>
                        <div class='row col-sm-12 imgbox'>
                            <img src="<?php echo asset('/arquivos/banners/'). $objBanner->getImagemMd() ?>" alt="" >
                        </div>
                       
                        <!-- // IMAGEM SALVA POR CACHE? -->
                        <!-- <?php echo $objBanner->setStrImagem('ImagemMd')->getThumb("width=390&height=260&cropratio=1.5:1", array('class' => 'img-responsive')); ?> -->
                    
                        <?php if(!is_null($objBanner->getLink())): ?>
                        </a>
                    <?php endif; ?>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
<?php endif; ?>
