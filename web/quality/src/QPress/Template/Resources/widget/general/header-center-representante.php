<?php use QPress\Template\Widget; ?>
    <header id="main-header" style="padding: 20px 0px;border-bottom: 1px solid #f2f2f2;">
    <div class="container">
        <div class="row">
            <div class="col-xs-3 hidden-md hidden-lg">
            </div>
            <div class="col-xs-6 col-md-12">
                <div class="logo">
                    <a href="<?php echo get_url_site(); ?>/home">
                        <picture>
                            <source media="(min-width: 769px)" srcset="<?php echo Config::getLogo()->getUrlImageResize('width=213&height=69&cropratio=3.09:1') ?>">
                            <img class="img-responsive center-block" src="<?php echo Config::getLogoMobile()->getUrlImageResize('width=130&height=42&cropratio=3.09:1') ?>" alt="">
                        </picture>
                    </a>
                </div>
            </div>
            <div class="col-xs-3  hidden-md hidden-lg text-right">

            </div>
        </div>
    </div>


</header>
