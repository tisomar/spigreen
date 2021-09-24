<?php use QPress\Template\Widget; ?>
<header id="main-header">
    <div class="main-header">
        <div class="container">
            <div class="row">
                <div class="col-xs-8 col-sm-5 col-md-3">
                    <div class="row">
                        <div class="menu pull-left hidden-md hidden-lg col-xs-2">
                            <button type="button" class="nav-item open-menu-mobile pull-left" title="Abrir menu">
                                <span class="<?php icon('bars'); ?>"></span>
                            </button>
                        </div>
                        <div class="logo col-xs-10 col-md-12">
                            <a href="<?php echo get_url_site(); ?>/home">
                                <picture>
                                    <source media="(min-width: 769px)" srcset="<?php echo Config::getLogo()->forceUrlImageResize('width=213&height=64&cropratio=3.32:1') ?>">
                                    <source media="(min-width: 321px)" srcset="<?php echo Config::getLogoMobile()->forceUrlImageResize('width=206&height=60&cropratio=3.44:1') ?>">
                                    <img class="teste img-responsive" src="<?php echo Config::getLogoMobile()->forceUrlImageResize('width=148&height=43&cropratio=3.44:1') ?>" alt="<?php echo Config::get('empresa_nome_fantasia') ?>">
                                </picture>
                            </a>
                        </div>
                    </div>
                </div>
                <div class="col-xs-4 col-sm-7 col-md-9">
                    <div class="row">
                        <div class="col-md-5 col-lg-5 visible-md visible-lg">
                            <?php Widget::render('forms/search', array('container' => $container)); ?>
                        </div>
                        <div class="nav-mobile col-md-7 col-lg-7">
                            <ul class="list list-unstyled text-right">
                                <li class="hidden-sm hidden-md hidden-lg">
                                    <button title="Exibir busca" type="button" class="nav-item collapsed" data-toggle="collapse" data-target=".search-mobile">
                                        <span class="<?php icon('search'); ?>"></span>
                                    </button>
                                </li>
                                <li class="hidden-xs">
                                    <div class="nav-item">
                                        <div class="btn-group pull-left">
                                            <span class="<?php icon('headphones'); ?>"></span>
                                            <button type="button" class="dropdown-toggle" data-toggle="dropdown" aria-expanded="false">
                                                Atendimento <span class="caret"></span>
                                            </button>
                                            <ul class="dropdown-menu" role="menu">
                                                <?php if (Config::get('has_brtalk')): ?>
                                                    <li><a href="<?php echo get_url_site(); ?>/brtalk/cliente/index.php" target="_blank">Atendimento Online</a></li>
                                                <?php endif; ?>
                                                <li><a href="<?php echo get_url_site(); ?>/perguntas-frequentes">Perguntas Frequentes</a></li>
                                                <li><a href="<?php echo get_url_site(); ?>/contato">Contato</a></li>
                                            </ul>
                                        </div>
                                    </div>
                                </li>
                                <?php if(!ClientePeer::isAuthenticad()): ?>
                                <li class="visible-md-inline-block visible-lg-inline-block">
                                    <div class="nav-item">
                                        <span class="<?php icon('sign-in'); ?>"></span>
                                        <a href="<?php echo get_url_site(); ?>/login">
                                            Entrar ou Cadastrar
                                        </a>
                                    </div>
                                </li>
                                <?php endif; ?>
                                <?php if(ClientePeer::isAuthenticad()): ?>
                                <li class="visible-md-inline-block visible-lg-inline-block">
                                    <div class="nav-item">
                                        <div class="dropdown btn-group pull-left">
                                            <span class="<?php icon('user'); ?>"></span>
                                            <button type="button" class="dropdown-toggle" data-toggle="dropdown" aria-expanded="false">
                                                Minha conta <span class="caret"></span>
                                            </button>
                                            <ul class="dropdown-menu" role="menu">
                                                <li><a href="<?php echo get_url_site(); ?>/minha-conta/pedidos">Meus pedidos</a></li>
                                                <li><a href="<?php echo get_url_site(); ?>/minha-conta/avaliacoes">Minhas avaliações</a></li>
                                                <li><a href="<?php echo get_url_site(); ?>/minha-conta/dados">Meus dados</a></li>
                                                <li><a href="<?php echo get_url_site(); ?>/minha-conta/enderecos">Meus endereços</a></li>
                                                <li><a href="<?php echo get_url_site(); ?>/minha-conta/nova-senha">Redefinir senha</a></li>
                                                <li class="divider"></li>
                                                <li><a href="<?php echo get_url_site(); ?>/login/logout">Sair (logout)</a></li>
                                            </ul>
                                        </div>
                                    </div>
                                </li>
                                <?php endif; ?>
                                <li class="hidden-xs hidden-md hidden-lg">
                                    <button title="Exibir busca" type="button" class="nav-item collapsed" data-toggle="collapse" data-target=".search-mobile">
                                        <span class="<?php icon('search'); ?>"></span>
                                        <span class="hidden-xs">Buscar</span>
                                    </button>
                                </li>
                                <li>
                                    <a class="nav-item active" href="<?php echo BASE_URL; ?>/carrinho" title="Carrinho de compras">
                                        <span class="<?php icon('shopping-cart'); ?>"></span>
                                    </a>
                                    <span class="amount"><?php echo $container->getCarrinhoProvider()->getCarrinho()->countItems(); ?></span>
                                </li>
                            </ul>
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

<?php Widget::render('general/menu-mobile', array()); ?>
<?php Widget::render('general/menu-desktop', array('full' => true)); ?>