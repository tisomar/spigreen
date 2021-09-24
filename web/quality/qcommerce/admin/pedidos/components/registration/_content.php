<?php
/* @var $object Pedido */
?>

<div class="clearfix noprint">
    <div class="pull-left">
        <h3>
            Pedido #<strong><?php echo $object->getId() ?></strong>
        </h3>
    </div>
    <div class="pull-right noprint">
        <h3><?php echo $object->getStatusLabel(); ?></h3>
    </div>
</div>

<div class="clearfix print">
    <div class="print logo pull-left">
        <?php echo Config::getLogo()->getThumb('width=165&height=64') ?>
    </div>

    <div class="pull-left">
        <?php if ($object->getEndereco()) :  ?>
            <address>
                <strong><?php echo $object->getCliente()->getNomeCompleto() ?></strong><br />
                <?php echo $object->getEndereco()->sprintf('CEP: %cep<br />%logradouro, %numero %complemento &minus; %bairro, %cidade &minus; %uf'); ?>
            </address>
        <?php endif ?>
    </div>

    <div class="pull-right text-right">
        <h3>
            Pedido #<strong><?php echo $object->getId() ?></strong>
        </h3>
        <span class="print"><strong>Data da Compra:</strong> <?php echo $object->getCreatedAt('d/m/Y H:i') ?></span>
    </div>

</div>

<hr/>

<div class="row noprint">

    <div class="col-md-3">

        <address>
            <?php if ($object->getEndereco()) :  ?>
                <strong><?php echo $object->getCliente()->getNomeCompleto() ?></strong><br />
                <?php echo $object->getEndereco()->sprintf('CEP: %cep<br />%logradouro, %numero %complemento<br />%bairro, %cidade &minus; %uf'); ?>
            <?php endif ?>
        </address>

        <strong>Data da Compra:</strong> <?php echo $object->getCreatedAt('d/m/Y H:i') ?>

    </div>

    <div class="col-md-3">
        <strong>Forma de Entrega</strong><br />
        <?php echo $container->getFreteManager()->getModalidade($object->getFrete())->getTitulo() ?>
        &minus; <?php echo $object->getFretePrazo() ?>
        <br />
        <br />
        <?php
        if ($object->getPedidoRetiradaLoja()) {
            echo sprintf(
                '<b>Loja:</b><br>%s<br><b>Endereço:</b><br>%s<br><b>Telefone:</b><br>%s',
                $object->getPedidoRetiradaLoja()->getNome(),
                nl2br($object->getPedidoRetiradaLoja()->getEndereco()),
                $object->getPedidoRetiradaLoja()->getTelefone()
            );
        } else {
            ?>
            <strong>Código de Rastreio</strong><br />
            <?php echo edit_inline($object->getCodigoRastreio(), $_class, 'CodigoRastreio', $object->getId()) ?>
            <?php
        }
        ?>
    </div>

    <div class="col-md-3">

        <?php
        switch ($object->getPedidoFormaPagamento()->getFormaPagamento()) {
            case PedidoFormaPagamentoPeer::FORMA_PAGAMENTO_BOLETO:
                ?>

                <strong>Forma de Pagamento</strong><br />
                <?php echo $object->getPedidoFormaPagamento()->getFormaPagamento() ?> (venc: <?php echo $object->getPedidoFormaPagamento()->getDataVencimento('d/m/Y') ?>)
                <br /><br />

                <strong>Status do Pagamento</strong><br />
                <h4><?php echo $object->getPedidoFormaPagamento()->getStatusLabel() ?></h4>

                <?php
                break;

            case PedidoFormaPagamentoPeer::FORMA_PAGAMENTO_CARTAO_CREDITO:
                ?>

                <strong>Forma de Pagamento</strong><br />
                <?php echo $object->getPedidoFormaPagamento()->getBandeira() ?> em <?php echo $object->getPedidoFormaPagamento()->getNumeroParcelas() ?>x
                <br /><br />

                <strong>Status do Pagamento</strong><br />
                <?php echo $object->getPedidoFormaPagamento()->getStatusLabel() ?>

                <?php
                break;

            case PedidoFormaPagamentoPeer::FORMA_PAGAMENTO_PAGSEGURO:
                ?>

                <strong>Forma de Pagamento</strong><br />
                <?php echo 'PAGSEGURO' ?>
                <?php if ($object->getPedidoFormaPagamento()->getTransacaoId() != null) : ?>
                <br />
                    <?php echo $object->getPedidoFormaPagamento()->getTransacaoId() ?>
                <?php endif; ?>
                <br />

                <strong>Status do Pagamento</strong><br />
                <h4><?php echo $object->getPedidoFormaPagamento()->getStatusLabel() ?></h4>
                <?php
                break;

            case PedidoFormaPagamentoPeer::FORMA_PAGAMENTO_BCASH:
                ?>

                <strong>Forma de Pagamento</strong><br />
                <?php echo 'BCash' ?>
                <br /><br />

                <strong>Status do Pagamento</strong><br />
                <?php echo $object->getPedidoFormaPagamento()->getStatusLabel() ?>

                <?php
                break;

            case PedidoFormaPagamentoPeer::FORMA_PAGAMENTO_PAGSEGURO_BOLETO:
            case PedidoFormaPagamentoPeer::FORMA_PAGAMENTO_CARTAO_CREDITO:
            case PedidoFormaPagamentoPeer::FORMA_PAGAMENTO_PAGSEGURO_DEBITO_ONLINE:
                ?>

                <strong>Forma de Pagamento</strong><br />
                <?php echo $object->getPedidoFormaPagamento()->getFormaPagamentoDescricao() ?>
                <br>
                <?php echo $object->getPedidoFormaPagamento()->getTransacaoId() ?>
                <br /><br />

                <strong>Status do Pagamento</strong><br />
                <h4><?php echo $object->getPedidoFormaPagamento()->getStatusLabel() ?></h4>

                <?php
                break;
            
            case PedidoFormaPagamentoPeer::FORMA_PAGAMENTO_PONTOS:
                ?>

                <strong>Forma de Pagamento</strong><br />
                <?php echo 'Pontos' ?>
                <br /><br />

                <strong>Status do Pagamento</strong><br />
                <?php echo $object->getPedidoFormaPagamento()->getStatusLabel() ?>

                <?php
                break;

            case PedidoFormaPagamentoPeer::FORMA_PAGAMENTO_CIELO_CARTAO_DEBITO:
                ?>

                <strong>Forma de Pagamento</strong><br />
                <?php echo 'Débito - ' . $object->getPedidoFormaPagamento()->getBandeira() ?>
                <br /><br />

                <strong>Status do Pagamento</strong><br />
                <?php echo $object->getPedidoFormaPagamento()->getStatusLabel() ?>

                <?php
                break;

            case PedidoFormaPagamentoPeer::FORMA_PAGAMENTO_CIELO_CARTAO_CREDITO:
                ?>

                <strong>Forma de Pagamento</strong><br />
                <?php echo 'Cartão de Crédito - ' . $object->getPedidoFormaPagamento()->getBandeira() ?> em <?php echo $object->getPedidoFormaPagamento()->getNumeroParcelas() ?>x
                <br /><br />

                <strong>Status do Pagamento</strong><br />
                <?php echo $object->getPedidoFormaPagamento()->getStatusLabel() ?>

                <?php
                break;

            case PedidoFormaPagamentoPeer::FORMA_PAGAMENTO_CIELO_BOLETO_BB:
                ?>

                <strong>Forma de Pagamento</strong><br />
                <?php echo $object->getPedidoFormaPagamento()->getFormaPagamentoDescricao() ?>

                <?php
                break;

            default:
                break;
        }
        ?>

    </div>
</div>

<?php
/* @var $objPedidoStatus PedidoStatus */
/* @var $objPedidoStatusHistorico PedidoStatusHistorico */
?>

<br />

<div class="row noprint">
    <h4>Processo do pedido</h4>


    <div class="table-responsive no-label">
        <table class="table table-condensed table-bordered">
            <tbody>
            <tr>
                <?php foreach ($allStatus as $objPedidoStatus) : ?>
                    <?php
                    $objPedidoStatusHistorico = PedidoStatusHistoricoQuery::create()
                        ->filterByPedidoId($object->getId())
                        ->filterByPedidoStatusId($objPedidoStatus->getId())
                        ->findOne();

                    $title = $objPedidoStatus->getLabelPreConfirmacao();
                    $classColumn = '';
                    $classIcon = '';
                    $subtitle = '';
                    $btn = '';

                    if (!is_null($objPedidoStatusHistorico)) {
                        if ($objPedidoStatusHistorico->getIsConcluido()) {
                            $title = '<b>' . ($objPedidoStatus->getLabelPosConfirmacao()) . '</b>';
                            $classColumn = 'success text-success';
                            $classIcon = 'icon-ok';
                            $subtitle = $objPedidoStatus->getLabelPosConfirmacao() . '<br />em ' . $objPedidoStatusHistorico->getUpdatedAt('d/m/Y H:i');
                        } else {
                            $classColumn = 'warning text-warning';
                            $classIcon = 'icon-exclamation-sign';
                            $title = '<b>' . $objPedidoStatus->getLabelPreConfirmacao() . '</b>';
                            $subtitle = 'em andamento';
                            $btn = '<button class="btn btn-sm btn-success proximo-status">'
                                . '<span>Avançar</span> <i class="icon-arrow-right"></i>'
                                . '</button>';
                        }
                    } else {
                        $classColumn = 'active text-muted';
                        $classIcon = 'icon-time';
                    }

                    if (!$object->isAndamento()) {
                        $btn = null;
                    }
                    ?>

                    <td class="text-center <?php echo $classColumn ?>">
                        <i class="<?php echo $classIcon ?>"></i>
                        <?php echo $title ?><br /><br />
                        <em><?php echo $subtitle ?></em><br />
                        <?php echo $btn ?>
                    </td>
                <?php endforeach; ?>

                <?php
                if ($object->isCancelado()) {
                    ?>
                    <td class="text-center danger text-danger">
                        <b>PEDIDO CANCELADO</b>
                        <h2 class="text-danger"><i class="icon-ban-circle"></i></h2>
                    </td>
                    <?php
                }
                ?>

            </tr>
            </tbody>
        </table>
    </div>
</div>

<div class="row">
    <legend>Pontos Distribuidos</legend>
    <div class="table-responsive">
        <table class="table">
            <thead>
            <th>Cliente</th>
            <th>Tipo</th>
            <th class="text-right">Pontos</th>
            </thead>
            <tbody>
            <?php $totalPontos = 0; ?>
            <?php foreach ($arrListaPontos as $objExtrato) : /** @var $objExtrato Extrato */?>
                <?php $totalPontos += $objExtrato->getPontos();?>
                <tr>
                    <td data-title="Cliente"><?php echo $objExtrato->getCliente()->getNomeCompleto(); ?></td>
                    <td data-title="Tipo"><?php echo $objExtrato->getTipoDesc();?></td>
                    <td data-title="Pontos" class="text-right"><?php echo number_format($objExtrato->getPontos(), 2, ',', '.');?></td>
                </tr>
            <?php endforeach;?>
            </tbody>
        </table>
    </div>
    <div class="pull-right">
        <hr>
        <h3 class="text-right well-mini"><small>Total: </small> <?php echo number_format($totalPontos, 2, ',', '.'); ?><small> pontos</small></h3>
    </div>
</div>

<div class="row">
    <legend class="noprint">Itens do Pedido</legend>
    <div class="table-responsive">
        <table class="table">
            <thead>
            <th>#</th>
            <th>Produto</th>
            <th class="text-right">Quantidade</th>
            <th class="text-right">Valor Unitário</th>
            <th class="text-right">Valor Total</th>
            </thead>
            <tbody>
            <?php foreach ($object->getPedidoItems() as $objPedidoItem) : /* @var $objPedido PedidoItem */
                ProdutoVariacaoPeer::disableSoftDelete();
                ProdutoPeer::disableSoftDelete();
                ProdutoAtributoPeer::disableSoftDelete();
                ProdutoVariacaoAtributoPeer::disableSoftDelete();
                ?>
                <tr>
                    <td data-title="Código"><?php echo $objPedidoItem->getProdutoVariacao()->getProdutoId() ?></td>
                    <td data-title="Produto">
                        <?php echo $objPedidoItem->getProdutoVariacao()->getProdutoNomeCompleto() . '<br />';
                        echo 'Ref.: ';
                        if ($objPedidoItem->getProdutoVariacao()->getSku() != '') {
                            echo $objPedidoItem->getProdutoVariacao()->getSku();
                        } else {
                            echo $objPedidoItem->getProdutoVariacao()->getProduto()->getSku();
                        } ?>
                    </td>
                    <td data-title="Quantidade" class="text-right"><?php echo $objPedidoItem->getQuantidade() ?></td>
                    <td data-title="Valor Unitário" class="text-right">R$ <?php echo format_money($objPedidoItem->getValorUnitario()) ?></td>
                    <td data-title="Valor Total" class="text-right">R$ <?php echo format_money($objPedidoItem->getValorTotal()) ?></td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    <div class="pull-right">
        <p class="text-right">Subtotal: <strong>R$ <?php echo format_money($object->getValorItens()) ?></strong></p>


        <?php if ($object->getValorDescontoBy(CupomPeer::OM_CLASS) > 0) : ?>
            <p class="text-right">
                Cupom de desconto: &minus;<strong>R$ <?php echo format_money($object->getValorDescontoBy(CupomPeer::OM_CLASS)); ?></strong>
            </p>
        <?php endif; ?>
        
            <?php if ($object->getValorDescontoBy(DescontoPagamentoPontosPeer::OM_CLASS) > 0) : ?>
            <p class="text-right">
                Utilização de pontos: &minus;<strong>R$ <?php echo format_money($object->getValorDescontoBy(DescontoPagamentoPontosPeer::OM_CLASS)); ?></strong>
            </p>
            <?php endif; ?>

        <?php if ($object->getValorDescontoBy(PedidoFormaPagamentoPeer::OM_CLASS) > 0) : ?>
            <p class="text-right">
                Desconto no boleto: &minus;<strong>R$ <?php echo format_money($object->getValorDescontoBy(PedidoFormaPagamentoPeer::OM_CLASS)); ?></strong>
            </p>
        <?php endif; ?>


        <p class="text-right">
            Forma de Entrega (<?php echo $container->getFreteManager()->getModalidade($object->getFrete())->getTitulo() ?>):
            <strong>R$ <?php echo format_money($object->getValorEntrega()); ?></strong>
        </p>
        <hr>
        <h3 class="text-right well-mini"><small>Total: </small> R$ <?php echo format_money($object->getValorTotal()) ?></h3>
    </div>
</div>

<form action="<?php echo $config['routes']['update-status'] ?>" id="form-proximo-passo" method="POST"></form>

<script>
    $(function() {
        $('.proximo-status').on('click', function(ev) {
            ev.preventDefault();
            bootbox.confirm("Você tem certeza de que deseja concluir o processo atual e avançar para o próximo?", function(result) {
                if (result == true) {
                    $('#form-proximo-passo').submit();
                }
            });
        });
    });
</script>
