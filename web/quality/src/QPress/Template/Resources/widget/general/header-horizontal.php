<?php use QPress\Template\Widget; ?>
<?php Widget::render('nav/menu-mobile'); ?>

<div id="page">
<?php
$totalItensCarrinho = $container->getCarrinhoProvider()->getCarrinho()->countItems();
?>
    <header>
        <section class="top hidden-xs">
            <div class="container">
                <div class="row">
                    <div class="col-md-12">
                        <ul class="info-nav pull-left">
                            <?php if (!ClientePeer::isAuthenticad()): ?>
                                <li>
                                    <a href="<?php echo get_url_site() ?>/cadastro">
                                        <span class="<?php icon('user') ?>"></span> Olá, faça seu cadastro aqui!
                                    </a>
                                </li>
                            <?php else: ?>
                                <li>
                                    <a href="<?php echo get_url_site() ?>/minha-conta/pedidos">
                                        <?php if (ClientePeer::getClienteLogado(true)->getPlanoId()):  ?>
                                            <span class="<?php icon('user') ?>"></span> Olá <?php echo ClientePeer::getClienteLogado()->getPrimeiroNome() ?>, seja bem vindo.
                                        <?php else: ?>
                                            <span class="<?php icon('user') ?>"></span> Olá <?php echo ClientePeer::getClienteLogado()->getPrimeiroNome() ?>, acompanhe seus pedidos!
                                        <?php endif ?>
                                    </a>
                                </li>
                            <?php endif; ?>
                        </ul>
                        <ul class="info-nav pull-right">
                            <?php if (trim(Config::get('contato.whatsapp')) != ''): ?>
                                <li>
                                    <span class="<?php icon('whatsapp') ?>"></span> <?php echo Config::get('contato.whatsapp') ?>
                                </li>
                            <?php endif; ?>
                            <li><a href="<?php echo get_url_site() . '/contato/' ?>">
                                    <span class="<?php icon('phone') ?>"></span> Fale conosco
                                </a>
                            </li>
                            <li><a href="<?php echo get_url_site() . '/perguntas-frequentes/' ?>">
                                    <span class="<?php icon('question-circle') ?>"></span> Ajuda
                                </a>
                            </li>
                            <?php if (ClientePeer::isAuthenticad()): ?>
                                <li><a href="<?php echo get_url_site(); ?>/login/logout">
                                        <span class="<?php icon('sign-out') ?>"></span> Sair
                                    </a>
                                </li>
                            <?php else: ?>
                                <li>
                                    <a href="<?php echo get_url_site(); ?>/minha-conta/pedidos">
                                        <span class="<?php icon('user') ?>"></span> Meus pedidos
                                    </a>
                                </li>
                                <li>
                                    <a href="<?php echo get_url_site() ?>/login">
                                        <span class="<?php icon('sign-in') ?>"></span> Entrar
                                    </a>
                                </li>
                            <?php endif; ?>
                        </ul>
                    </div>
                </div>
            </div>
        </section>
        <section class="middle">
            <div class="container">
                <div class="row hidden-md hidden-lg">
                    <div class="col-xs-2 col-sm-1 link">
                        <a id="hamburger" href="#menu">
                            <span></span>
                        </a>
                    </div>
                    <div class="col-xs-6 logo">
                        <a href="<?php echo get_url_site(); ?>/home">
                            <picture>
                                <img class="img-responsive" src="<?php echo Config::getLogoMobile()->forceUrlImageResize('width=200&height=32') ?>" alt="">
                            </picture>
                        </a>
                    </div>
                    <div class="col-xs-4 col-sm-5 link text-right">
                        <a href="#" class="collapsed link-icon" data-toggle="collapse" data-target=".search-mobile">
                            <span class="<?php icon('search 2x') ?>"></span>
                        </a>
                        <a href="<?php echo get_url_site(); ?>/carrinho" class="link-icon">
                            <span class="quantity-item"><?php echo (int) $totalItensCarrinho ?></span>
                            <span class="<?php icon('shopping-cart 2x') ?>"></span>
                        </a>
                    </div>
                </div>

                <div class="row hidden-xs hidden-sm">
                    <div class="col-md-4">
                        <div class="logo">
                            <a href="<?php echo get_url_site(); ?>/home">
                                <picture>
                                    <img src="<?php echo Config::getLogo()->forceUrlImageResize('width=300&height=70') ?>" alt="">
                                </picture>
                            </a>
                        </div>
                    </div>
                    <div class="col-md-5">
                        <div class="search">
                            <?php Widget::render('forms/search', array('container' => $container)); ?>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="pull-right box-service">
                                <span class="service-icon">
                                    <span class="<?php icon('shopping-cart') ?>"></span>
                                </span>
                            <div class="service-body">
                                <a href="<?php echo get_url_site() ?>/carrinho/">
                                    <h3>Carrinho</h3>
                                    <small class="cart-quantity"><?php echo plural($totalItensCarrinho, '%s item', '%s itens') ?></a></small>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
        <section>
            <div class="container">
                <div class="row">
                    <form role="form" class="search-mobile collapse" action="<?php echo get_url_site(); ?>/busca">
                        <div class="container-fluid">
                            <div class="input-group">
                                <input class="form-control input-sm" type="text" name="buscar-por" required>
                                <span class="input-group-btn">
                                    <button type="submit" class="btn btn-sm btn-primary">Buscar</button>
                                </span>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </section>

    </header>
    <div class="hidden-xs hidden-sm">
        <?php Widget::render('general/menu-desktop', array('full' => true)); ?>
    </div>

<?php /* ?>
    <header id="main-header">
        <div class="main-header">

            <div id="header-top" class="hidden-xs hidden-sm">
                <div class="container">
                    <div class="row">
                        <div class="col-xs-12">
                            <div class="pull-left">
                                <ul class="options">
                                    <?php if (trim(Config::get('contato.whatsapp')) != ''): ?>
                                        <li>
                                            <span class="<?php icon('whatsapp') ?>"></span> Whatsapp: <?php echo Config::get('contato.whatsapp') ?>
                                        </li>
                                    <?php endif; ?>
                                    <li><a href="<?php echo get_url_site() . '/contato/' ?>">
                                            <span class="<?php icon('phone') ?>"></span> Fale conosco
                                        </a>
                                    </li>
                                    <li><a href="<?php echo get_url_site() . '/perguntas-frequentes/' ?>">
                                            <span class="<?php icon('question-circle') ?>"></span> Ajuda
                                        </a>
                                    </li>
                                </ul>
                            </div>
                            <span class="pull-right">
                                <ul class="options">
                                    <?php if (ClientePeer::isAuthenticad()): ?>
                                        <li>
                                            <a href="<?php echo get_url_site(); ?>/minha-conta/pedidos">
                                                <span class="<?php icon('shopping-cart') ?>"></span> Meus pedidos
                                            </a>
                                        </li>
                                        <li><a href="<?php echo get_url_site(); ?>/minha-conta/dados">
                                                <span class="<?php icon('user') ?>"></span> Dados cadastrais
                                            </a>
                                        </li>
                                        <li><a href="<?php echo get_url_site(); ?>/login/logout">
                                                <span class="<?php icon('sign-out') ?>"></span> Sair
                                            </a>
                                        </li>
                                    <?php else: ?>
                                        <li>
                                            <a href="<?php echo get_url_site(); ?>/minha-conta/pedidos">
                                                <span class="<?php icon('user') ?>"></span> Meus pedidos
                                            </a>
                                        </li>
                                        <li><span class="<?php icon('sign-in') ?>"></span>
                                            <a href="<?php echo get_url_site() ?>/login">Entrar</a>
                                        </li>
                                    <?php endif; ?>

                                </ul>
                            </span>
                        </div>
                    </div>
                </div>
            </div>

            <div class="header-middle">
                <div class="container">
                    <div class="row">
                        <div class="col-xs-7 col-sm-5 col-md-4">
                            <div class="menu pull-left hidden-md hidden-lg">
                                <button type="button" class="nav-item open-menu-mobile" title="Abrir menu">
                                    <span class="<?php icon('bars'); ?>"></span>
                                </button>
                            </div>
                            <div class="logo">
                                <a href="<?php echo get_url_site(); ?>/home">
                                    <picture>
                                        <source media="(min-width: 769px)" srcset="<?php echo Config::getLogo()->getUrlImageResize('width=280&height=70') ?>">
                                        <source media="(min-width: 321px)" srcset="<?php echo Config::getLogoMobile()->getUrlImageResize('width=223&height=60') ?>">
                                        <img class="img-responsive" src="<?php echo Config::getLogoMobile()->getUrlImageResize('width=150&height=30') ?>" alt="">
                                    </picture>
                                </a>
                            </div>
                        </div>
                        <div class="col-xs-5 col-sm-7 col-md-8">

                            <div class="row row-center">

                                <div class="col-md-7 hidden-xs hidden-sm">
                                    <?php Widget::render('forms/search', array('container' => $container)); ?>
                                </div>

                                <div class="col-md-5 hidden-xs hidden-sm">
                                    <ul id="header-nav-list">
                                        <li>
                                            <a href="#">
                                                <span class="<?php icon('user') ?>"></span>
                                                Entrar
                                            </a>
                                        </li>
                                        <li>
                                            <a herf="#">
                                                <span class="<?php icon('shopping-cart') ?>"></span>
                                                Minha compra
                                            </a>
                                        </li>

                                    </ul>
<!--                                    <div class="carrinho">-->
<!--                                        <a href="--><?php //echo BASE_URL; ?><!--/carrinho" title="Acesse seu carrinho de compras">-->
<!--                                            <span class="--><?php //icon('shopping-cart') ?><!--"></span>-->
<!--                                            <div class="items">-->
<!--                                                <span class="hidden-sm">-->
<!--                                                    <span class="minha-sacola hidden-sm">MINHA COMPRA<br></span>-->
<!--                                                    --><?php //echo plural($container->getCarrinhoProvider()->getCarrinho()->countItems(), '%s item', '% itens', 'nenhum item'); ?>
<!--                                                </span>-->
<!--                                            </div>-->
<!--                                        </a>-->
<!--                                    </div>-->
                                </div>

                                <div class="nav-mobile col-xs-12 hidden-md hidden-lg">
                                    <ul class="list pull-right">
                                        <li class="hidden-xs">
                                            <div class="nav-item">
                                                <div class="btn-group pull-left">
                                                    <button title="Clique e veja nossos canais de atendimento" type="button" class="dropdown-toggle" data-toggle="dropdown" aria-expanded="false">
                                                        <span class="fa fa-comments"></span>
                                                        <span class="hidden-xs">&nbsp;Atendimento</span>
                                                    </button>
                                                    <ul class="dropdown-menu" role="menu">
                                                        <li><a href="<?php echo get_url_site() ?>/brtalk/cliente/index.php" target="_blank">Atendimento Online</a></li>
                                                        <li><a href="<?php echo get_url_site() ?>/perguntas-frequentes">Perguntas Frequentes</a></li>
                                                        <li><a href="<?php echo get_url_site() ?>/contato">Contato</a></li>
                                                    </ul>
                                                </div>
                                            </div>
                                        </li>
                                        <li class="hidden-sm hidden-md hidden-lg">
                                            <button title="Exibir busca" type="button" class="nav-item collapsed" data-toggle="collapse" data-target=".search-mobile">
                                                <span class="<?php icon('search'); ?>"></span>
                                            </button>
                                        </li>
                                        <li class="hidden-xs hidden-md hidden-lg">
                                            <button title="Exibir busca" type="button" class="nav-item collapsed" data-toggle="collapse" data-target=".search-mobile">
                                                <span class="<?php icon('search'); ?>"></span>
                                                <span class="hidden-xs">Buscar</span>
                                            </button>
                                        </li>
                                        <li class="hidden-md hidden-lg">
                                            <a class="nav-item" href="<?php echo BASE_URL; ?>/carrinho" title="Carrinho de compras">
                                                <span class="<?php icon('shopping-cart'); ?>"></span>
                                                <span class="amount"><?php echo $container->getCarrinhoProvider()->getCarrinho()->countItems(); ?></span>
                                            </a>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <form role="form" class="search-mobile collapse" action="<?php echo get_url_site(); ?>/busca">
            <div class="container-fluid">
                <div class="input-group">
                    <input class="form-control input-sm" type="text" name="buscar-por" required>
                <span class="input-group-btn">
                    <button type="submit" class="btn btn-sm btn-primary">Buscar</button>
                </span>
                </div>
            </div>
        </form>
    </header>

    <nav class="header-categories">
        <div class="container">
            <div class="row">
                <div class="col-md-12">
                    <?php Widget::render('general/menu-desktop', array('full' => false)); ?>
                </div>
            </div>
        </div>
    </nav>

<?php Widget::render('general/menu-mobile', array()); ?>
<?php */ ?>