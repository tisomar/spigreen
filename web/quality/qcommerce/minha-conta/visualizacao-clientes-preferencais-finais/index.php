<?php
require __DIR__ . '/actions/visualizacao-clientes-preferenciais-finais.actions.php';

use QPress\Template\Widget;

$strIncludesKey = 'minha-conta-visualizacao-clientes-preferencais-finais';
include QCOMMERCE_DIR . "/includes/security.php";
include QCOMMERCE_DIR . "/includes/head.php";
?>

<body itemscope itemtype="http://schema.org/WebPage" data-page="minha-conta-visualizacao-clientes-preferenciais-finais">
<?php include_once QCOMMERCE_DIR . '/includes/javascript_body_inicio.php'; ?>
<?php include_once QCOMMERCE_DIR . '/includes/facebook_tracking/fb.general.tracking.php'; ?>
<?php Widget::render('general/header'); ?>

<main role="main">
    <?php
    Widget::render(
        'components/breadcrumb',
        array('links' => array('Home' => '/home', 'Minha Conta' => 0, 'Clientes preferenciais e finais' => ''))
    );
    Widget::render('general/page-header', array('title' => 'Clientes preferenciais e finais'));
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
                            Clientes preferenciais e finais
                        </h3>
                    </div>
                </div>
                <br>
                <div class="row">
                    <div class="col-xs-12">
                        <div class="panel panel-default">
                            <div class="panel-heading">
                                <h4>Clientes</h4>
                            </div>
                            <div class="panel-body">
                                <div class="table-responsive no-label">
                                    <form id="form_filtros" action="<?php echo get_url_site().'/minha-conta/visualizacao-clientes-preferencais-finais/' ?>"
                                          role="form" method="get" class="form-disabled-on-load">
                                        <table class="table">
                                            <tbody>
                                            <tr>
                                                <td style="width: 80%; background-color: #f9f9f9;">
                                                    <input id="filtro_cliente" name="cliente" class="form-control"
                                                           placeholder="Nome do Cliente" value="<?= $filtroNome ?>">
                                                </td>
                                                <td style="width: 80%; background-color: #f9f9f9;">
                                                    <select id="filtro_tipo_cliente" name="tipo_cliente" class="form-control">
                                                        <option value="todos" <?= !$filtroTipoCliente ? 'selected' : '' ?>>Todos</option>
                                                        <option value="final" <?= $filtroTipoCliente == 'final' ? 'selected' : '' ?>>Final</option>
                                                        <option value="preferencial" <?= $filtroTipoCliente == 'preferencial' ? 'selected' : '' ?>>Preferencial</option>
                                                    </select>
                                                </td>
                                                <td style="width: 10%; background-color: #f9f9f9;">
                                                    <button class="btn btn-block btn-action">
                                                        <i class="fa fa-search"></i>
                                                    </button>
                                                </td>
                                                <td style="width: 10%; background-color: #f9f9f9;">
                                                    <button id="btn_limpar_filtros" type="button" class="btn btn-block btn-action" style="background-color: #cbcbcb;">
                                                        <i class="fa fa-trash-o"></i>
                                                    </button>
                                                </td>
                                            </tr>
                                            </tbody>
                                        </table>
                                    </form>
                                </div>
                                <div class="content table-responsive">
                                    <table class="table table-striped table-clientes">
                                        <thead>
                                        <tr>
                                            <td>Nome</td>
                                            <td>Tipo</td>
                                            <td>E-mail</td>
                                            <td class="center">Geração</td>
                                            <td class="center">Pedidos mês</td>
                                            <td class="center">Total pontos mês</td>
                                            <td class="center"></td>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        <?php foreach ($pager as $cliente) : ?>
                                            <tr class="table-clientes-registros">
                                                <td class="td_cliente_nome"><?= $cliente['Nome'] ?></td>
                                                <td class="td_tipo_cliente"><?= $cliente['Tipo'] ?></td>
                                                <td><?= $cliente['Email'] ?></td>
                                                <td class="center"><?= $cliente['Nivel'] - $clienteLogado->getTreeLevel() ?></td>
                                                <td class="center"><?= PedidoPeer::getTotalPedidoMesAtual($cliente['Id']) ?></td>
                                                <td class="center"><?= PedidoPeer::getValorTotalPontosPedidosMesAtual($cliente['Id']) ?></td>
                                                <td>
                                                    <?php if (PedidoPeer::getTotalPedidoMesAtual($cliente['Id']) > 0) : ?>
                                                        <a href="<?= $root_path . '/minha-conta/visualizacao-clientes-preferencais-finais/cliente-pedidos/?cliente='.$cliente['Id']?>">
                                                            <i class="fa fa-search"></i>
                                                        </a>
                                                    <?php endif; ?>
                                                </td>
                                            </tr>
                                        <?php endforeach;?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                        <?php
                        Widget::render('components/pagination', array(
                            'pager' => $pager,
                            'href' => get_url_site() . '/minha-conta/visualizacao-clientes-preferencais-finais/',
                            'queryString' => $queryString,
                            'align' => 'center'
                        ));
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
            $('#filtro_cliente').val('');
            $('#filtro_tipo_cliente').val('todos');
            $('#form_filtros').submit();
        });
    });
</script>

<?php include QCOMMERCE_DIR . '/includes/footer.php'; ?>
<?php include QCOMMERCE_DIR . '/minha-conta/components/link-modal.php'; ?>