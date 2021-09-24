<?php
use QPress\Template\Widget;

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

$strIncludesKey = 'minha-conta-pedidos';
require_once QCOMMERCE_DIR . '/includes/security.php';
require_once __DIR__ . '/actions/index.actions.php';
require_once QCOMMERCE_DIR . '/includes/head.php';
?>
<body itemscope itemtype="http://schema.org/WebPage" data-page="minha-conta-pedidos">
<?php include_once QCOMMERCE_DIR . '/includes/javascript_body_inicio.php'; ?>
<?php include_once QCOMMERCE_DIR . '/includes/facebook_tracking/fb.general.tracking.php'; ?>
<?php Widget::render('general/header'); ?>

<main role="main">
    <?php
    Widget::render('components/breadcrumb', array('links' => array('Home' => '/home', 'Minha Conta' => '/minha-conta/pedidos', 'Pedidos' => '')));
    Widget::render('general/page-header', array('title' => 'Meus Pedidos'));
    Widget::render('components/flash-messages');
    ?>
    <div class="container">
        <div class="row">
            <div class="col-xs-12 col-md-3">
                <?php Widget::render('general/menu-account', array('strIncludesKey' => $strIncludesKey)); ?>
            </div>
            <div class="col-xs-12 col-md-9">
                <h3>Acompanhe e verifique o andamento de seus pedidos!</h3>

                <?php if (count($collPedidos)) : ?>
                    <div class="table-vertical">
                        <table class="table table-striped">
                            <thead>
                            <tr>
                                <th>Nº do Pedido</th>
                                <th>Data</th>
                                <th>Status</th>
                                <th></th>
                            </tr>
                            </thead>
                            <?php foreach ($collPedidos as $key => $pedido) : /* @var $pedido Pedido */ ?>
                                <?php $urlPedido = $root_path . '/minha-conta/pedidos/detalhes/' . $pedido->getId(); ?>
                                <tr>
                                    <td data-title="Nº do Pedido">
                                        <a href="<?php echo $urlPedido ?>">
                                            #<?php echo $pedido->getId(); ?>
                                        </a>
                                    </td>
                                    <td data-title="Data">
                                        <a href="<?php echo $urlPedido ?>">
                                            <?php echo $pedido->getCreatedAt('d/m/Y'); ?>
                                        </a>
                                    </td>
                                    <td data-title="Status">
                                        <a href="<?php echo $urlPedido ?>">
                                            <?php if ($pedido->getStatus() === PedidoPeer::STATUS_FINALIZADO) : ?>
                                                <span class="<?php icon('check-circle'); ?> text-success"></span>
                                            <?php elseif ($pedido->getStatus() === PedidoPeer::STATUS_ANDAMENTO) : ?>
                                                <span class="<?php icon('clock-o'); ?> text-warning"></span>
                                            <?php elseif ($pedido->getStatus() === PedidoPeer::STATUS_CANCELADO) : ?>
                                                <span class="<?php icon('ban'); ?> text-danger"></span>
                                            <?php endif; ?>

                                            <?php if ($pedido->getStatus() === PedidoPeer::STATUS_FINALIZADO) : ?>
                                                Pedido Finalizado
                                            <?php elseif ($pedido->getStatus() === PedidoPeer::STATUS_ANDAMENTO) : ?>
                                                <?php echo $pedido->getLastPedidoStatus()->getLabelPreConfirmacao(); ?>
                                            <?php elseif ($pedido->getStatus() === PedidoPeer::STATUS_CANCELADO) : ?>
                                                Pedido Cancelado
                                            <?php endif; ?>
                                        </a>
                                    </td>
                                    <td class="text-right">
                                        <a href="<?php echo $urlPedido ?>" class="view-details" title="+ detalhes">
                                            + detalhes
                                        </a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </table>
                    </div>

                    <?php
//                        Widget::render('components/pagination', array(
//                            'pager' => $collPedidos,
//                            'href'  => get_url_site() . '/minha-conta/pedidos/',
//                            'align' => 'center'
//                        ));
                    Widget::render('components/pagination', array(
                        'pager' => $collPedidos,
                        'href'  => get_url_site() . '/minha-conta/pedidos/',
                        'queryString' => $queryString,
                        'align' => 'center'
                    ));
                    ?>

                <?php else : ?>
                    <div class="row">
                        <div class="col-xs-12">
                            <div class="panel panel-default">
                                <div class="panel-body">
                                    <span class="<?php icon('info'); ?>"></span> Nenhum pedido realizado!
                                    <br>
                                    <a class="" href="<?php echo get_url_site() . '/produtos/produtos' ?>">
                                        Clique aqui
                                    </a> e inicie suas compras agora mesmo!
                                </div>
                            </div>
                        </div>
                        <br>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</main>

<?php include QCOMMERCE_DIR . '/includes/footer.php'; ?>
<?php include QCOMMERCE_DIR . '/minha-conta/components/link-modal.php'; ?>
</body>
</html>
