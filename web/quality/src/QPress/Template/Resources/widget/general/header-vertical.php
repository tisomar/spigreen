<?php use QPress\Template\Widget; ?>
<?php Widget::render('nav/menu-mobile'); ?>
<?php $totalItensCarrinho = $container->getCarrinhoProvider()->getCarrinho()->countQuantidadeTotal(); ?>
<?php
$clienteLogado = ClientePeer::getClienteLogado(true);
$RedirectCentralDistribuidor =  $clienteLogado == null ? '/login' : ($clienteLogado->getPlano() == null ? '/minha-conta/pedidos' : '/minha-conta/plano-carreira');
?>

<style>
    .TabSuporte{
        width: 60px;
        border: 0;
    } 

    .TabSuporte li{
        width: 30px;
        margin: 0 !important;
        padding: 0 !important;

    }

    .TabSuporte li .dropLink{
        margin: 0 !important;
        padding: 0 !important;
        height: 100%;
     }

    .TabSuporte li .dropLink span{
        font-size: 20px;
        padding-top: 8px;
        color:#DDD;
    }

    .TabSuporte:hover,
    .TabSuporte li:hover,
    .TabSuporte li .dropLink:hover,
    .TabSuporte li .dropLink span:hover{
        color: #000;
    }
     
</style>
<div id="page">
    <header>
        <section class="top hidden-xs">
            <div class="container">
                <div class="row">
                    <div class="col-md-12" style="padding-top: 10px">
                        <ul class="info-nav pull-left client-name"  style="margin-left: 0;">
                            <?php if (!ClientePeer::isAuthenticad()): ?>
                                <li>
                                    <a href="<?php echo get_url_site() ?>/cadastro">
                                        <span class="<?php icon('user') ?>"></span> Olá, bem-vindo, seja um D.I.S
                                    </a>
                                </li>
                            <?php else: ?>
                                <li>
                                    <a href="<?php echo get_url_site() ?>/minha-conta/pedidos">
                                        <?php if (ClientePeer::getClienteLogado(true)->getPlanoId()):  ?>
                                            <span class="<?php icon('user') ?>"></span> Olá <?php echo ClientePeer::getClienteLogado()->getNome() ?>, seja bem vindo.
                                        <?php else: ?>
                                            <span class="<?php icon('user') ?>"></span> Olá <?php echo ClientePeer::getClienteLogado()->getNome() ?>, acompanhe seus pedidos!
                                        <?php endif ?>
                                    </a>
                                </li>
                            <?php endif; ?>
                        </ul>
                        <ul class="info-nav pull-right">

                            <div class="info-nav pull-right">
                                <ul class="nav nav-tabs btn btn-upper-bar TabSuporte">
                                    <li class="nav-item dropdown">
                                        <a class="nav-link dropdown-toggle dropLink" data-toggle="dropdown" href="#" role="button" aria-haspopup="true" aria-expanded="false">
                                            <span class=" <?php icon('question-circle') ?>"></span>
                                        </a>
                                        <div class="dropdown-menu">
                                            <a class="dropdown-item" href="/minha-conta/ticket">SUPORTE AO D.I.S</a>
                                        </div>
                                    </li>
                                </ul>
                            </div>
                            
                            <?php if(!ClientePeer::isAuthenticad()): ?>
                                <div class="info-nav pull-right">
                                    <a href="<?php echo get_url_site() ?>/login" class="btn btn-upper-bar-login">
                                        <span class="<?php icon('sign-in') ?>"></span> Fazer Login
                                    </a>
                                    <a href="<?php echo get_url_site() ?>/cadastro" class="btn btn-upper-bar">
                                        <span class="<?php icon('user') ?>"></span> Fazer Cadastro
                                    </a>
                                </div>
                            <?php else: ?>
                                <div class="info-nav pull-right">
                                    <a href="<?php echo get_url_site() . $RedirectCentralDistribuidor ?>" class="btn btn-upper-bar">
                                        <span class="<?php icon('users') ?>"></span> Escritório Virtual
                                    </a>
                                    <a href="<?php echo get_url_site(); ?>/login/logout" class="btn btn-upper-bar">
                                        <span class="<?php icon('sign-out') ?>"></span> Sair
                                    </a>
                                </div>
                            <?php endif; ?>
                        </ul>
                        <ul class="menu-tools">
                            <li><a href="https://spigreen.com.br/" target="_blank">Institucional</a></li>
                            <li><a href="https://spigreen.com.br/spirulina-da-spigreen/" target="_blank">Spirulina</a></li>
                            <!-- <li><a href="https://spigreen.com.br/linhas-de-produtos/" target="_blank">Produtos</a></li> -->
                            <li><a href="https://spigreen.com.br/empreenda-com-a-spigreen/" target="_blank">Empreender</a></li>
                            <li><a href="https://spigreen.com.br/faq/" target="_blank">FAQ</a></li>
                        </ul>
                    </div>
                </div>
            </div>
        </section>

        <?php /* show in [ xs | sm ] */ ?>
        <section class="hidden-md hidden-lg header middle-mobile">
            <div class="container">
                <div class="row">
                    <div class="col-xs-2 col-sm-1 link">
                        <a id="hamburger" href="#menu">
                            <span></span>
                        </a>
                    </div>
                    <div class="col-xs-6 logo">
                        <a href="<?php echo get_url_site(); ?>/home">
                            <picture>
                                <img src="https://store.spigreen.com.br/resize/imagecache/8f354a664ade73acb54e6c8035b5553a"
                                     title="Spigreen" alt="logotipo" class="img-responsive logo-resize-mobile" width="300" height="120">
                            </picture>
                        </a>
                    </div>
                    <div class="col-xs-4 col-sm-5 link text-right">
                        <a href="#" class="collapsed link-icon" data-toggle="collapse" data-target=".search-mobile">
                            <span class="<?php icon('search 2x') ?>" style="color: #6ed03f;"></span>
                        </a>
                        <a href="<?php echo get_url_site(); ?>/carrinho" class="link-icon">
                            <span class="quantity-item"><?php echo (int) $totalItensCarrinho ?></span>
                            <span class="<?php icon('shopping-cart 2x') ?>" style="color: #6ed03f;"></span>
                        </a>
                    </div>
                </div>
            </div>
        </section>

        <?php /* show in [ md | lg ] */ ?>
        <section class="hidden-xs hidden-sm middle header">
            <div class="container">
                <!--                <div class="row hidden-md hidden-lg">-->
                <!--                    <div class="col-xs-2 col-sm-1 link">-->
                <!--                        <a id="hamburger" href="#menu">-->
                <!--                            <span></span>-->
                <!--                        </a>-->
                <!--                    </div>-->
                <!--                    <div class="col-xs-6 logo">-->
                <!--                        <a href="--><?php //echo get_url_site(); ?><!--/home">-->
                <!--                            <picture>-->
                <!--                                <img class="img-responsive"-->
                <!--                                     src="--><?php //echo Config::getLogoMobile()->forceUrlImageResize('width=200&height=32') ?><!--"-->
                <!--                                     alt="">-->
                <!--                                <img src="https://spigreen.com.br/wp-content/uploads/2020/09/nov_logotipo_spigreen.svg"-->
                <!--                                     title="Spigreen" alt="logotipo" class="img-responsive logo-resize-mobile"-->
                <!--                                     width="300" height="120">-->
                <!--                            </picture>-->
                <!--                        </a>-->
                <!--                    </div>-->
                <!--                    <div class="col-xs-4 col-sm-5 link text-right">-->
                <!--                        <a href="#" class="collapsed link-icon" data-toggle="collapse" data-target=".search-mobile">-->
                <!--                            <span class="--><?php //icon('search 2x') ?><!--" style="color: #abc24f;"></span>-->
                <!--                        </a>-->
                <!--                        <a href="--><?php //echo get_url_site(); ?><!--/carrinho" class="link-icon">-->
                <!--                            <span class="quantity-item">--><?php //echo (int)$totalItensCarrinho ?><!--</span>-->
                <!--                            <span class="--><?php //icon('shopping-cart 2x') ?><!--" style="color: #abc24f;"></span>-->
                <!--                        </a>-->
                <!--                    </div>-->
                <!--                </div>-->

                <div class="row">
                    <div class="col-md-12">
                        <div class="row">
                            <div class="col-md-4">
                                <div class="logo">
                                    <a href="<?php echo get_url_site(); ?>/home">
                                        <picture>
                                            <!-- <img src="--><?php //echo Config::getLogo()->forceUrlImageResize('width=300&height=120') ?><!--" title="Spigreen" alt="logotipo" class="img-responsive logo-resize">-->
                                            <img src="https://store.spigreen.com.br/resize/imagecache/8f354a664ade73acb54e6c8035b5553a"
                                                 title="Spigreen" alt="logotipo" class="img-responsive logo-resize" width="300" height="120">
                                        </picture>
                                    </a>
                                </div>
                            </div>
                            <div class="col-md-8">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="search">
                                            <?php Widget::render('forms/search', array('container' => $container)); ?>
                                        </div>
                                    </div>
                                    <?php
                                    $md = 3;
                                    if(ClientePeer::isAuthenticad()):
                                        $md = ClientePeer::getClienteLogado(true)->isConsumidorFinal() ? 2 : 3 ?>
                                    <?php endif; ?>

                                    <?php if (
                                        ClientePeer::isAuthenticad() &&
                                        ClientePeer::getClienteLogado(true)->isConsumidorFinal()
                                    ) : ?>
                                        <div class="col-md-<?php echo $md; ?> no-padding-reseller">
                                            <div class="box-service pull-right">
                                                <a href="<?php echo get_url_site() ?>/login/actions/reseller.actions">
                                                    <span class="service-icon">
                                                        <span class="<?php icon('money') ?>"></span>
                                                    </span>
                                                    <div class="service-body">
                                                        <h3>Seja um<br>Revendedor</h3>
                                                    </div>
                                                </a>
                                            </div>
                                        </div>
                                    <?php endif; ?>

                                    <div class="col-md-<?php echo $md; ?>">

                                        <div class="carrinho-sm-width pull-right box-service">
                                            <span class="service-icon">
                                                <span class="<?php icon('shopping-cart') ?>"></span>
                                            </span>
                                            <div class="service-body">
                                                <a href="<?php echo get_url_site() ?>/carrinho/">
                                                    <h3 style="color: #00263d">Carrinho</h3>
                                                    <small class="cart-quantity">
                                                        <?php echo plural($totalItensCarrinho, '%s item', '%s itens') ?>
                                                    </small>
                                                </a>
                                            </div>
                                            <?php
                                            //                                    if (ClientePeer::isAuthenticad() && !ClientePeer::getClienteLogado(true)->isConsumidorFinal()): ?>
                                            <div class="container shopping-cart hidden-xs hidden-sm" id="shopping-cart-id" style="transition-delay: 100ms;">
                                                <?php if ($totalItensCarrinho > 0 ): ?>

                                                    <a href="<?php echo get_url_site(); ?>/carrinho/">
                                                        <div class="row">
                                                            <div class="shopping-cart-header col-md-12">
                                                                <div class="col-md-12">
                                                                    <i style="margin-top:5px;font-size: 20px" class="fa fa-shopping-cart cart-icon"></i>
                                                                    <h4 style="display: inline-block;padding-top: 4px;">Meu Carrinho</h4>

                                                                </div>
                                                            </div> <!--end shopping-cart-header -->
                                                        </div>
                                                    </a>
                                                    <div class="shopping-cart-items scrollable">
                                                        <div class="shopping-cart-items-view">
                                                            <?php
                                                            Widget::render('general/table-products', array(
                                                                'itens' => $container->getCarrinhoProvider()->getCarrinho()->getPedidoItemsJoinProdutoVariacao()
                                                            ));
                                                            ?>


                                                        </div>
                                                    </div>
                                                    <div class="shopping-cart-total">
                                                        <div class="row" style="margin: 0 0">
                                                            <div class="col-md-12 shopping-cart-total-span">
                                                                <p>
                                                                    Total:
                                                                    R$ <?php echo format_money($container->getCarrinhoProvider()->getCarrinho()->getValorItens())?>
                                                                </p>
                                                            </div>
                                                        </div>
                                                        <div class="row clearfix" style="margin: 0 0">
                                                            <div class="col-md-6" style="padding: 0 5px 0 0;">
                                                                <a href="<?php echo get_url_site(); ?>/carrinho/" class="btn btn-block btn-default ">
                                                                    Carrinho
                                                                    <i class="fa fa-cart-arrow-down shopping-cart-btn-icons"></i>
                                                                </a>
                                                            </div>
                                                            <div class="col-md-6" style="padding: 0 0 0 5px;">
                                                                <a href="<?php echo get_url_site(); ?>/checkout/endereco/"
                                                                   class="finalizar btn btn-clean btn-block btn-success">
                                                                    Finalizar Compra
                                                                    <i class="fa fa-lock shopping-cart-btn-icons"></i>
                                                                </a>
                                                            </div>
                                                        </div>
                                                    </div>

                                                <?php else: ?>
                                                    <div class="row">
                                                        <div class="col-sm-6 col-sm-offset-3 text-center bloco">
                                                            <h1 class="title">Ops! Seu carrinho está vazio.</h1>
                                                        </div>
                                                    </div>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-md-<?php echo $md; ?> no-padding-reseller">
                                        <div class="box-service">
                                            <a href="<?php echo get_url_site()?>/minha-conta/pedidos">
                                                <span class="service-icon">
                                                    <span class="<?php icon('archive') ?>"></span>
                                                </span>
                                                <div class="service-body" style="margin-top: 10px">
                                                    <h3 style="color: #00263d">Meus Pedidos</h3>
                                                </div>
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-10 col-md-push-2">
                        <!--                        <div class="row">-->
                        <!--                            --><?php //Widget::render('general/menu-desktop'); ?>
                        <!--                        </div>-->
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

        <?php 
            $categorias = CategoriaQuery::create()->filterByMostrarBarraMenu(true)->orderByOrdem()->find();
        ?>
        <section>
            <div class="second-menu hidden-xs">
                <div class="row">
                    <div class="text-center list-second-menu">
                        <ul class="nav-second-menu">
                            <?php foreach($categorias as $categoria): 
                                $url = $categoria->getUrl() ?? $categoria->getUrlCategoria() ?? '#';
                                ?>
                                <li><a href="<?= get_url_site() . $url ?>"><?= $categoria->getNome() ?></a></li>
                            <?php endforeach ?>
                            <!-- <li><a href="</?= get_url_site(); ?>/produtos/detalhes/cliente-preferencial/">CLIENTE PREFERENCIAL</a></li> -->
                            <!-- <li><a href="</?= get_url_site(); ?>/produtos/detalhes/kit-ouro-1/">KIT OURO</a></li>
                            <li><a href="</?= get_url_site(); ?>/produtos/detalhes/kit-prata/">KIT PRATA</a></li>
                            <li><a href="</?= get_url_site(); ?>/produtos/detalhes/kit-bronze/">KIT BRONZE</a></li> -->
                            <!-- <li><a href="</?= get_url_site(); ?>/produtos/produtos/">PRODUTOS</a></li>
                            <li><a href="</?= get_url_site(); ?>/produtos/acessrios/">ACESSÓRIOS</a></li> -->
                        </ul>
                    </div>
                </div>
            </div>
        </section>
    </header>
</div>

<style>

    /*.logo-resize-mobile{*/
    /*    height: 44px;*/
    /*    width: 100%;*/
    /*    margin-right: 15px;*/
    /*    margin-left: 15px;*/
    /*}*/

    /*.logo-resize {*/
    /*    !*height: 70px;*!*/
    /*    !*width: 100%;*!*/
    /*    !*object-fit: cover;*!*/
    /*    height: 74px;*/
    /*    width: 100%;*/
    /*    object-fit: cover;*/
    /*    margin-right: 15px;*/
    /*}*/

    .shopping-cart-items-view .table-vertical {
        height: 100%;
        max-height: 50vh;
        overflow: auto;
    }
</style>

<script>
    $(function() {
        window.addEventListener('resize', function() {
            $('body').scroll();
        });

        $('body').scroll(function() {
            var $header = $('.header');

            if (this.scrollTop >= $header.prev().outerHeight() + $header.prev().offset().top) {
                $('#menu-desktop').hide();

                var $offset = $('<div>')
                    .addClass('offset')
                    .css(
                        'height',
                        $header.outerHeight()
                    );

                $header.css({
                    position: 'fixed',
                    width: this.clientWidth,
                    top: 0,
                    zIndex: 999
                });

                if ($('.offset').length === 0) {
                    $header.after($offset);
                }
            } else {
                $('#menu-desktop').show();

                $header.css({
                    position: 'relative',
                    width: 'auto',
                });

                $('.offset').remove();
            }
        });
    });
</script>