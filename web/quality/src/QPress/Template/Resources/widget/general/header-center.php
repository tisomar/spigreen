<?php use QPress\Template\Widget; ?>
<header id="main-header">
    <div class="header-top bg-default hidden-xs hidden-sm">
        <div class="container">
            <div class="row">
                <div class="col-md-7">
                    <p>Seja bem vindo ao Qcommerce, a sua loja virtual!</p>
                </div>
                <div class="col-md-3">
                    <?php Widget::render('forms/search', array()); ?>
                </div>
                <div class="col-md-2">
                    <ul class="list-unstyled text-right">
                        <li><a href="<?php echo get_url_site(); ?>/contato" class="<?php icon('headphones'); ?>" title="Atendimento"></a></li>
                        <li><a href="<?php echo get_url_site(); ?>/login" class="<?php icon('user'); ?>" title="Entrar ou cadastrar"></a></li>
                        <li><a href="<?php echo get_url_site(); ?>/carrinho" class="<?php icon('shopping-cart'); ?>" title="Carrinho"></a></li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
    <div class="container">
        <div class="row">
            <div class="col-xs-3 hidden-md hidden-lg">
                <button type="button" class="btn nav-item open-menu-mobile collapsed">
                    <span class="<?php icon('bars'); ?>"></span>
                </button>
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
                <a class="btn nav-item" href="<?php echo BASE_URL; ?>/carrinho">
                    <span class="<?php icon('shopping-cart'); ?>"></span> (<?php echo $container->getCarrinhoProvider()->getCarrinho()->countItems(); ?>)
                </a>
            </div>
        </div>
    </div>

    <form role="form" class="search-mobile collapse" action="<?php echo get_url_site(); ?>/busca">
        <div class="container-fluid">
            <div class="input-group">
                <input class="form-control input-sm" type="text" name="buscar-por" required>
                <span class="input-group-btn">
                    <button type="submit" class="btn btn-sm btn-theme">
                        <span class="<?php icon('search'); ?>"></span>
                    </button>
                </span>
            </div>
        </div>
    </form>
</header>

<?php Widget::render('general/menu-mobile', array('search' => true)); ?>
<?php Widget::render('general/menu-desktop', array('full' => true)); ?>