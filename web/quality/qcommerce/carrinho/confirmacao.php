<?php
/* @var $container \QPress\Container\Container */
$strIncludesKey = '';

require_once __DIR__ . '/../includes/security.php';

include_once __DIR__ . '/actions/confirmacao.actions.php';

include_once __DIR__ . '/../includes/head.php';

include_once __DIR__ . '/../classes/Tracking.php';
?>
<body itemscope itemtype="http://schema.org/WebPage">
    <header class="header" role="banner">
        <div class="wrapper-grids">
            <div class="col-24">
                <div class="logo align-center" itemtype="http://schema.org/Organization" itemscope>
                    <a itemprop="url" href="<?php echo get_url_site() ?>/home" title="Home">
                        <?php echo Config::getLogo()->getThumb('width=165&height=64') ?>
<!--                        <img itemprop="logo" alt="" src="--><?php //echo asset('/img/min/logo.png') ?><!--">-->
                    </a>
                </div>
            </div>
            <div class="push-5 pull-5 col-14">
                <div class="progress-bar">
                    <div class="bar" style="width:100%;"></div>
                </div>
                <ol class="list-primary">
                    <li class="active">Identificação</li>
                    <li class="active">Pagamento</li>
                    <li class="active">Confirmação</li>
                </ol>
            </div>
        </div>
    </header>

<main role="main" class="main">
    <div class="wrapper-grids">
        <div class="col-24">
            <?php
            echo get_contents(__DIR__ . '/../includes/breadcrumb.inc.php', array(
                'links' => array(
                    'Home' => '/home',
                    'Carrinho' => '',
                    'Identificação' => '',
                    'Pagamento' => '', 'Confirmação' => ''
                )));
            ?>
        </div>

        <div class="col-24">
            
            <div class="text-group align-center">
                <h1 class="title-detail title-big">PEDIDO CONCLUÍDO</h1>
            </div>
            <div class="text-group align-center">
                <h5 class="align-center">
                    <strong class="success">Seu pedido foi finalizado com sucesso!</strong>
                </h5>
                <p>
                    <strong> 
                        A confirmação do seu pedido foi enviada para o e-mail <em><?php echo $carrinho->getCliente()->getEmail() ?></em>
                        <br>
                        O prazo de entrega é de <?php echo $carrinho->getFretePrazo(); ?> após a confirmação do pagamento.
                    </strong>
                </p>
            </div>
            
            <?php
            echo Config::get('ebit_banner_finalizacao');
            ?>

            <?php
            include __DIR__ . '/components/confirmacao/' . strtolower($carrinho->getPedidoFormaPagamento()->getFormaPagamento()) . '.php';
            ?>
        </div>
        <?php
        if (\Config::get('google_track_ecommerce') == 1) {
            echo Tracking::generateGoogleTracking($carrinho);
        }
        ?>

    </div>
</main>

<?php include_once __DIR__ . '/../includes/footer.php'; ?>
</body>
</html>
