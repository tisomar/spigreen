<?php
use QPress\Template\Widget;

require __DIR__ . '/actions/cliente-pedidos.actions.php';

$strIncludesKey = 'minha-conta-visualizacao-clientes-preferencais-finais';

include QCOMMERCE_DIR . "/includes/security.php";
include QCOMMERCE_DIR . "/includes/head.php";
?>

<body itemscope itemtype="http://schema.org/WebPage" data-page="minha-conta-cliente-pedidos">
<?php include_once QCOMMERCE_DIR . '/includes/javascript_body_inicio.php'; ?>
<?php include_once QCOMMERCE_DIR . '/includes/facebook_tracking/fb.general.tracking.php'; ?>
<?php Widget::render('general/header'); ?>

<main role="main">
    <?php
    Widget::render(
        'components/breadcrumb',
        array('links' => array('Home' => '/home', 'Minha Conta' => 0, 'Pedidos do cliente ' . $clientePedido->getNome() => ''))
    );
    Widget::render('general/page-header', array('title' => 'Pedidos do cliente'));
    Widget::render('components/flash-messages');
    ?>
    <div class="container">
        <div class="row">
            <div class="col-xs-12 col-md-3">
                <?php Widget::render('general/menu-account', array('strIncludesKey' => $strIncludesKey)); ?>
            </div>
            <div class="col-xs-12 col-md-9">
                <div class="row">
                    <div class="col-sm-8">
                        <h3>
                            Pedidos do cliente <?= $clientePedido->getNome() ?>
                        </h3>
                    </div>
                </div>
                <br>
                <div class="row">
                    <div class="col-xs-12">
                        <div class="panel panel-default">
                            <div class="panel-heading">
                                <table style="width:100%;margin-bottom:1rem;color:#212529">
                                    <tr>
                                        <td>
                                            <h4>Pedidos</h4>
                                        </td>
                                        <td>
                                            <button id="btn_voltar_pagina" type="button" class="btn btn-default btn-label" style="float:right; line-height: 20px; width: 100px">
                                                <i class="fa fa-arrow-left"></i> Voltar
                                            </button>
                                        </td>
                                    </tr>
                                </table>
                            </div>
                            <div class="panel-body">
                                <div class="table-responsive no-label">
                                    <form id="form_filtros" action="<?php echo get_url_site().'/minha-conta/visualizacao-clientes-preferencais-finais/cliente-pedidos?' ?>"
                                          role="form" method="get" class="form-disabled-on-load">
                                        <input type="hidden" name="cliente" value="<?= $clientePedido->getId() ?>">
                                        <table class="table">
                                            <tbody>
                                            <tr>
                                                <td>
                                                    <input type="text" class="form-control datepicker" id="periodo-inicio"
                                                           name="inicio" value="<?php echo ($dtInicio) ? $dtInicio->format('d/m/Y') : '' ?>"
                                                           placeholder="Inicio">
                                                </td>
                                                <td>
                                                    <input type="text" class="form-control datepicker" id="periodo-fim"
                                                           name="fim" value="<?php echo ($dtFim) ? $dtFim->format('d/m/Y') : '' ?>"
                                                           placeholder="Fim">
                                                </td>
                                                <td class="text-left" style="width: 10%; background-color: #f9f9f9;">
                                                    <button class="btn btn-block btn-action">
                                                        <i class="fa fa-search"></i>
                                                    </button>
                                                </td>
                                                <td class="text-left" style="width: 10%; background-color: #f9f9f9;">
                                                    <button id="btn_limpar_filtros" type="button" class="btn btn-block btn-action" style="background-color: #cbcbcb;">
                                                        <i class="fa fa-trash-o"></i>
                                                    </button>
                                                </td>
                                            </tr>
                                            </tbody>
                                        </table>
                                    </form>
                                </div>
                                <?php if (count($pager)) : ?>
                                    <div class="table-vertical">
                                        <table class="table table-striped">
                                            <thead>
                                            <tr>
                                                <th>Nº do Pedido</th>
                                                <th>Data Pedido</th>
                                                <th>Data Pagamento</th>
                                                <th>Pontos</th>
                                                <th>Valor Total</th>
                                            </tr>
                                            </thead>
                                            <?php
                                            /* @var $pedido Pedido */
                                            foreach ($pager as $pedido) : ?>
                                                <tr>
                                                    <td>#<?php echo $pedido->getId(); ?></td>
                                                    <td><?php echo $pedido->getCreatedAt('d/m/Y'); ?></td>
                                                    <td><?php echo $pedido->getDataConfirmacaoPagamento('d/m/Y'); ?></td>
                                                    <td><?= $pedido->getValorPontos() ?></td>
                                                    <td>R$ <?= format_money($pedido->getValorTotal()) ?></td>
                                                </tr>
                                            <?php endforeach; ?>
                                        </table>
                                    </div>
                                <?php else : ?>
                                    <div class="row">
                                        <div class="col-xs-12">
                                            <div class="panel panel-default">
                                                <div class="panel-body">
                                                    <span class="<?php icon('info'); ?>"></span>
                                                    Este cliente não possui pedidos no período!
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                        <?php
                        if (count($pager)) :
                            Widget::render('components/pagination', array(
                                'pager' => $pager,
                                'href'  => get_url_site() . '/minha-conta/visualizacao-clientes-preferencais-finais/cliente-pedidos/',
                                'queryString' => $queryString,
                                'align' => 'center'
                            ));
                        endif;
                        ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>

<script>
    $(document).ready(function() {
        $('#btn_limpar_filtros').on('click',function () {
            $('#periodo-inicio').val('');
            $('#periodo-fim').val('');
            $('#form_filtros').submit();
        });

        $('#btn_voltar_pagina').on('click', function () {
            javascript:history.go(-1);
        });
    });
</script>

<?php include QCOMMERCE_DIR . '/includes/footer.php'; ?>
<?php include QCOMMERCE_DIR . '/minha-conta/components/link-modal.php'; ?>