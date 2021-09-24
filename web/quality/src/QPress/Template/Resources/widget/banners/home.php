<?php if (count($collBanner) > 0): ?>
    <!-- "
        EXISTIA ESSA CLASSE (//<?php echo (isset($banner_full) && $banner_full) ? 'banner-full' : '' ?>) AGREGADA 
        A PRIMEIRA DIV CLASS OWL-CAROUSEL OWL-THEME QUE ESTAVA BUGANDO O DISTANCIAMENTO ENTRE OS SEGUINTES BLOCOS
    " -->
    <div id="carousel-banner" class="owl-carousel owl-theme">
        <?php foreach ($collBanner as $objBanner): /* @var $objBanner Banner */ ?>
            <div class="item">
                <?php if(isset($banner_full) && $banner_full): ?>                    
                    <?php if($objBanner->getLink() != null):
                        if($objBanner->getTarget() == 'iframe'):?>
                            <a href="<?php echo $objBanner->getLink(); ?>" data-lightbox="iframe" title="<?php echo htmlspecialchars($objBanner->getTitulo()); ?>">
                        <?php else: ?>
                            <a href="<?php echo $objBanner->getLink(); ?>" target="<?php echo $objBanner->getTarget() ?>" title="<?php echo htmlspecialchars($objBanner->getTitulo()); ?>">
                        <?php endif; ?>
                    <?php endif; ?>

                        <?php /* banner full */ ?>
                        <picture>
                            <!--[if IE 9]><video style="display: none;"><![endif]-->
                            <source media="(min-width: 1201px)" srcset="<?php echo asset('/arquivos/banners/'). $objBanner->getImagemLg() ?>">
                            <source media="(min-width: 993px)" srcset="<?php echo asset('/arquivos/banners/'). $objBanner->getImagemMd() ?>">
                            <source media="(min-width: 769px)" srcset="<?php echo asset('/arquivos/banners/'). $objBanner->getImagemMd() ?>">
                            <source media="(min-width: 321px)" srcset="<?php echo asset('/arquivos/banners/'). $objBanner->getImagemSm() ?>">
                            <!--[if IE 9]></video><![endif]-->
                         
                            <img class="img-responsive" style="margin-top: -2vmin" srcset="<?php echo asset('/arquivos/banners/'). $objBanner->getImagemSm(); ?>" alt="<?php echo htmlspecialchars($objBanner->getTitulo()); ?>">
                        </picture>
                    <?php if($objBanner->getLink() != null): ?>
                        </a>
                    <?php endif; ?>

                <?php else: ?>

                <?php /* banner centralizado */ ?>
                    <div class="container">
                        <?php if($objBanner->getLink() != null):
                            if($objBanner->getTarget() == 'iframe'):?>
                                <a href="<?php echo $objBanner->getLink(); ?>" data-lightbox="iframe" title="<?php echo htmlspecialchars($objBanner->getTitulo()); ?>">
                            <?php else: ?>
                                <a href="<?php echo $objBanner->getLink(); ?>" target="<?php echo $objBanner->getTarget() ?>" title="<?php echo htmlspecialchars($objBanner->getTitulo()); ?>">
                            <?php endif; ?>
                        <?php endif; ?>
                                <picture>
                                    <!--[if IE 9]><video style="display: none;"><![endif]-->
                                    <source media="(min-width: 1140px)" srcset="<?php echo asset('/arquivos/banners/'). $objBanner->getImagemLg() ?>">
                                    <source media="(min-width: 992px)" srcset="<?php echo asset('/arquivos/banners/'). $objBanner->getImagemMd() ?>">
                                    <source media="(min-width: 321px)" srcset="<?php echo asset('/arquivos/banners/'). $objBanner->getImagemSm() ?>">
                                    <!--[if IE 9]></video><![endif]-->
                                    <img class="img-responsive" style="margin-top: -2vmin" srcset="<?php echo asset('/arquivos/banners/'). $objBanner->getImagemSm() ?>" alt="<?php echo htmlspecialchars($objBanner->getTitulo()) ?>">
                                </picture>
                        <?php if($objBanner->getLink() != null): ?>
                            </a>
                        <?php endif; ?>
                    </div>
                <?php endif; ?>

            </div>
        <?php endforeach; ?>
    </div>
<?php endif; ?>