<?php if (count($collBanner) > 0): ?>
    <div class="hidden-xs">
        <div id="advantage-banner" class="container owl-carousel owl-theme">
            <?php foreach ($collBanner as $objBanner): /* @var $objBanner Banner */ ?>
                <div class="item">
                    <?php if($objBanner->getLink() != null):
                        if($objBanner->getTarget() == 'iframe'):?>
                            <a href="<?php echo $objBanner->getLink(); ?>" data-lightbox="iframe" title="<?php echo htmlspecialchars($objBanner->getTitulo()); ?>">
                        <?php else: ?>
                            <a href="<?php echo $objBanner->getLink(); ?>" target="<?php echo $objBanner->getTarget() ?>" title="<?php echo htmlspecialchars($objBanner->getTitulo()); ?>">
                        <?php endif; ?>
                    <?php endif; ?>
                        <div class='row col-sm-12 imgbox'>
                            <img src="<?php echo asset('/arquivos/banners/'). $objBanner->getImagemMd() ?>" alt="" >
                        </div>
                        <!-- CASH IMAGE? -->
                        <!-- <?php echo $objBanner->setStrImagem('ImagemMd')->getThumb("width=1140&height=89&cropratio=12.80:1", array('class' => 'img-responsive')); ?> -->
                    <?php if(!is_null($objBanner->getLink())): ?>
                        </a>
                    <?php endif; ?>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
<?php endif; ?>