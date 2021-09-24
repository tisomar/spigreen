<?php
if (Config::get('sistema.versao_demo')) {
    \QPress\Template\Widget::render('general/topo-demo');
}
?>
<div class="wrapper">
    <header id="main-header" class="header-checkout">
        <div class="container">
            <div class="row">
                <div class="col-xs-6">
                    <a href="<?php echo $root_path; ?>" class="logo">
                        <picture>
                            <!--[if IE 9]><video style="display: none;"><![endif]-->
                            <source media="(min-width: 769px)" srcset="<?php echo Config::getLogo()->forceUrlImageResize('width=213&height=64') ?>">
                            <source media="(min-width: 321px)" srcset="<?php echo Config::getLogoMobile()->forceUrlImageResize('width=206&height=60') ?>">
                            <!--[if IE 9]></video><![endif]-->
                            <img class="img-responsive" srcset="<?php echo Config::getLogoMobile()->forceUrlImageResize('width=148&height=43') ?>" alt="<?php echo Config::get('empresa_nome_fantasia') ?>">
                        </picture>
                    </a>
                </div>
                <div class="col-xs-6">
                    <span class="icon-ambiente-seguro"></span>
                </div>
            </div>
        </div>
    </header>
