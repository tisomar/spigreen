<?php
/* @var $objEnderecos Endereco */
use QPress\Template\Widget;

$strIncludesKey = 'minha-conta-meu-plano';
include QCOMMERCE_DIR . "/includes/security.php";
include QCOMMERCE_DIR . "/includes/head.php";
require __DIR__ . '/actions/meu-plano.actions.php';
?>

<body itemscope itemtype="http://schema.org/WebPage" data-page="minha-conta-meu-plano">
<?php include_once QCOMMERCE_DIR . '/includes/javascript_body_inicio.php'; ?>
<?php include_once QCOMMERCE_DIR . '/includes/facebook_tracking/fb.general.tracking.php'; ?>
<?php Widget::render('general/header'); ?>

<main role="main">
    <?php
    Widget::render('components/breadcrumb', array('links' => array('Home' => '/home', 'Minha Conta' => 0, 'Meu Plano' => '')));
    Widget::render('general/page-header', array('title' => 'Financeiro'));
    Widget::render('components/flash-messages');
    ?>
    <div class="container">
        <div class="row">
            <div class="col-xs-12 col-md-3">
                <?php Widget::render('general/menu-account', array('strIncludesKey' => $strIncludesKey)); ?>
            </div>
            <div class="col-xs-12 col-md-9">
                
                <div class="row">
                    <?php $clienteLogado = ClientePeer::getClienteLogado(true) ?>
                    <?php $chave = $clienteLogado->getChaveIndicacao() ?>
                    <?php $patrocinadorDireto = $clienteLogado->getPatrocinadorDireto() ?>
                    <!-- <?php if ($chave || $patrocinadorDireto) : ?>
                        <div class="col-xs-12">
                            <div class="panel panel-default">
                                <div class="panel-body">
                                    <?php if ($chave) : ?>
                                        <span class="<?php icon('info'); ?>"></span> Seu código é:
                                        <strong><?php echo escape($chave) ?></strong><br>
                                    <?php endif ?>
                                    <?php if ($patrocinadorDireto) : ?>
                                        <strong>Patrocinador: </strong><?php echo escape($patrocinadorDireto->getNomeCompleto()) ?>
                                    <?php endif ?>
                                </div>
                            </div>
                        </div>
                    <?php endif ?> -->

                    <div class="col-xs-12">
                        <?php if ($objPlano) : ?>
                            <div class="panel panel-default">
                                <div class="panel-heading">
                                    <h4>
                                        Seu Plano: <?php echo escape($objPlano->getNome()) ?>
                                    </h4>
                                </div>
                                <div class="panel-body">
                                    <p>
                                        Total bônus:
                                        <strong>R$ <?php echo number_format($totalPontos, 2, ',', '.') ?></strong>
                                        <?php if ($resgateDesabilitado): ?>
                                            &nbsp;&nbsp;<a style="color: #666666; cursor: default;" href="#" title="Resgate de pontos desabilitado">solicitar resgate</a>
                                        <?php elseif ($totalPontos > 0) : ?>
                                            &nbsp;&nbsp;<a href="<?php echo get_url_site() ?>/minha-conta/resgate">solicitar resgate</a>
                                        <?php endif ?>
                                    </p>
                                    <p>Total patrocinados:
                                        <strong><?php echo number_format($totalPatrociados, 0, '', '.') ?></strong></p>
                                </div>
                            </div>

                            <div class="panel panel-default">
                                <div class="panel-heading">
                                    <h4>Fazer uma transferência de bônus</h4>
                                </div>
                                <div class="panel-body">

                                        <form id="form_transferencia_pontos" style="display: none;" action="<?php echo $root_path; ?>/minha-conta/meu-plano/actions/transferencia-pontos"
                                              method="post">
                                            <div class="form-group">
                                                <div class="col-md-8">
                                                    <label for="register-email">* E-mail do franqueado:</label>
                                                    <input type="email" class="form-control validity-email"
                                                           id="register-email" name="transferencia_puntos[EMAIL]"
                                                           value="" required="required"
                                                           placeholder="exemplo@exemplo.com">
                                                </div>
                                                <div class="col-md-4">
                                                    <label for="qtd-bonus">* Quantidade de bônus:</label>
                                                    <input id="qtd-bonus" name="transferencia_puntos[QUANTIDADE]"
                                                           value="1" type="number" placeholder="0"
                                                           class="touch-spin text-center form-control"
                                                           min="1" max="<?php echo number_format($totalPontos, 0, '.', '') ?>"
                                                           data-touch-spin-min="1" data-touch-spin-max="<?php echo number_format($totalPontos, 0, '.', '') ?>"
                                                           data-touch-spin-step="1" data-touch-spin-decimals="0"
                                                    >
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <div class="col-md-12">
                                                    <a href="javascript:" class="btn btn-secondary pull-right"
                                                       onclick="$('#form_transferencia_pontos').hide(); $('#show_transferencia_pontos').show();" >Cancelar
                                                    </a>
                                                    <a class="btn btn-success pull-right" title="Fazer Transferência"
                                                       href="javascript:void(0);" data-href="#" data-action="transferencia_pontos">
                                                          <i class="icon-trash"></i> Fazer Transferência
                                                    </a>
                                                </div>
                                            </div>
                                        </form>

                                        <div class="form-group" id="show_transferencia_pontos">
                                            <a href="javascript:"
                                               onclick="$('#form_transferencia_pontos').show(); $('#show_transferencia_pontos').hide();"
                                               class="btn btn-success btn-block">Fazer transferência de bônus</a>
                                        </div>
                                </div>

                            </div>

                        <?php endif; ?>

                        <?php if (!$clienteLogado->getLivreMensalidade()) : ?>
                            <div class="panel panel-default">
                                <div class="panel-heading">
                                    <h4>Mensalidade</h4>
                                </div>
                                <div class="panel-body">
                                    <?php if ($clienteLogado->getVencimentoMensalidade()) : ?>
                                        <p>Data vencimento:
                                            <strong><?php echo $clienteLogado->getVencimentoMensalidade('d/m/Y') ?></strong>
                                        </p>
                                    <?php endif ?>

                                    <form action="<?php echo $root_path; ?>/carrinho/actions/renovar-mensalidade/"
                                          method="post">
                                        <div class="form-group">
                                            <button type="submit" class="btn btn-success btn-block">Renovar
                                                mensalidade
                                            </button>
                                        </div>
                                        <input type="hidden" name="action" value="renovar-mensalidade">
                                    </form>
                                </div>
                            </div>

                            <?php if (count($pedidosMensalidades) > 0) : ?>
                                <div class="panel panel-default">
                                    <div class="panel-heading">
                                        <h4>Pagamentos Mensalidades</h4>
                                    </div>
                                    <div class="panel-body">
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
                                                <?php foreach ($pedidosMensalidades as $key => $pedido) : /* @var $pedido Pedido */ ?>
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
                                                            <a href="<?php echo $urlPedido ?>" class="view-details"
                                                               title="+ detalhes">
                                                                + detalhes
                                                            </a>
                                                        </td>
                                                    </tr>
                                                <?php endforeach; ?>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            <?php endif ?>

                        <?php endif ?>

                    </div>
                </div>
            </div>
        </div>
    </div>
</main>

<?php include QCOMMERCE_DIR . '/includes/footer.php'; ?>
<?php include QCOMMERCE_DIR . '/minha-conta/components/link-modal.php'; ?>
</body>

</html>
