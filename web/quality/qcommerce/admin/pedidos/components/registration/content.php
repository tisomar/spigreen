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
        <?php if ($object->getCentroDistribuicao()) :  ?>
            <address>
                <strong>Centro de Distribuição</strong><br />
                <?= $object->getCentroDistribuicao()->getDescricao(); ?>
                <br/>
                <?= 'CEP: ' . $object->getCentroDistribuicao()->getCep(); ?>
            </address>
        <?php endif ?>
    </div>

    <div class="pull-left" style="margin-left: 15px">
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

<!-- classe noprint impede que o botão de imprimir acesse este block -->
<div class="row">
    <div class="col-md-3">
        <?php if ($object->getCentroDistribuicao()) :  ?>
            <address>
                <strong>Centro de Distribuição</strong><br />
                <?= $object->getCentroDistribuicao()->getDescricao(); ?>
                <br/>
                <?= 'CEP: ' . $object->getCentroDistribuicao()->getCep(); ?>
            </address>
        <?php endif ?>

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
    
    <div class="col-md-6">

        <?php
        $lastStatusId = $object->getLastPedidoStatus()->getId();
        foreach ($object->getPedidoFormaPagamentoLista() as $formaPagamento):

            echo '<strong>Forma de Pagamento</strong><br/>';

            switch ($formaPagamento->getFormaPagamento()):
                case PedidoFormaPagamentoPeer::FORMA_PAGAMENTO_BOLETO:
                    ?>
                    <?= $formaPagamento->getFormaPagamento() ?> (venc: <?= $formaPagamento->getDataVencimento('d/m/Y') ?>)
                    <br/>
                    <?php
                    break;

                case PedidoFormaPagamentoPeer::FORMA_PAGAMENTO_CARTAO_CREDITO:
                    ?>
                    <?= $formaPagamento->getBandeira() ?> em <?= $formaPagamento->getNumeroParcelas() ?>x
                    <br/>
                    <?php
                    break;

                case PedidoFormaPagamentoPeer::FORMA_PAGAMENTO_PAGSEGURO:
                    ?>
                    PAGSEGURO
                    <br/>

                    <?php if ($formaPagamento->getTransacaoId() != null) : ?>
                        <?= $formaPagamento->getTransacaoId() ?>
                        <br/>
                    <?php endif; ?>
                    <?php
                    break;

                case PedidoFormaPagamentoPeer::FORMA_PAGAMENTO_BCASH:
                    ?>
                    BCash
                    <br/>
                    <?php
                    break;

                case PedidoFormaPagamentoPeer::FORMA_PAGAMENTO_PAGSEGURO_BOLETO:
                case PedidoFormaPagamentoPeer::FORMA_PAGAMENTO_CARTAO_CREDITO:
                case PedidoFormaPagamentoPeer::FORMA_PAGAMENTO_PAGSEGURO_DEBITO_ONLINE:
                    ?>
                    <?= $formaPagamento->getFormaPagamentoDescricao() ?>
                    <br/>

                    <?= $formaPagamento->getTransacaoId() ?>
                    <br/>
                    <?php
                    break;
                
                case PedidoFormaPagamentoPeer::FORMA_PAGAMENTO_PONTOS:
                    ?>
                    Bônus
                    <br />
                    <?php
                    break;

                case PedidoFormaPagamentoPeer::FORMA_PAGAMENTO_CIELO_CARTAO_DEBITO:
                    ?>
                    <?= 'Débito - ' . $formaPagamento->getBandeira() ?>
                    <br />
                    <p> <?= $formaPagamento->getCodAutorizacao() != null ?  'Codigo de Autorizacão: ' .  $formaPagamento->getCodAutorizacao() : ''  ?></p>
                    <?php
                    break;

                case PedidoFormaPagamentoPeer::FORMA_PAGAMENTO_CIELO_CARTAO_CREDITO:
                    $dadosCartao = CartaoCieloDadosQuery::create()
                        ->filterByPedidoId($formaPagamento->getPedidoId())
                        ->filterByCieloPaymentId($formaPagamento->getCieloPaymentId())
                        ->findOne();

                    ?>
                    <?= 'Cartão de Crédito - ' . $formaPagamento->getBandeira() ?> em <?= $formaPagamento->getNumeroParcelas() ?>x<br/>
                    <?= !empty($dadosCartao) ? 'Numero do Cartão: ' . substr($dadosCartao->getNumero(), -4) : '' ?><br/>
                    <?= $formaPagamento->getCodAutorizacao() != null ? 'Codigo de Autorizacão: ' . $formaPagamento->getCodAutorizacao() : '' ?><br/>
                    <?= $formaPagamento->getTransacaoId() != null ? 'Codigo de Transação: ' . $formaPagamento->getTransacaoId() : '' ?><br/>

                    <?php
                    break;

                case PedidoFormaPagamentoPeer::FORMA_PAGAMENTO_CIELO_BOLETO_BB:
                    ?>
                    <?= $formaPagamento->getFormaPagamentoDescricao() ?><br/>

                    <?php
                    if ($object->isAndamento() && $lastStatusId == 1) :
                    ?>
                        <span class="small">&minus;</span>
                        <a href="<?= $formaPagamento->getUrlAcesso() ?>" target="_blank">
                            <span class="small">imprimir</span>
                        </a>
                        <br/>
                    <?php
                    endif;

                    break;

                case PedidoFormaPagamentoPeer::FORMA_PAGAMENTO_BONUS_FRETE:
                    ?>
                    Bônus Frete
                    <br />
                    <?php
                    break;

                case PedidoFormaPagamentoPeer::FORMA_PAGAMENTO_EM_LOJA:
                    ?>
                    Pagamento em Loja
                    <br />
                    <?php
                    break;

                case PedidoFormaPagamentoPeer::FORMA_PAGAMENTO_TRANSFERENCIA:
                    echo $formaPagamento->getFormaPagamentoDescricao();
                    ?>
                    <br />
                    <a href="<?= $formaPagamento->strPathImg . $formaPagamento->getComprovante() ?>" target="_blank">Visualizar comprovante</a>
                    <br />
                    <?php
                    break;

                default:
                    echo $formaPagamento->getFormaPagamentoDescricao() . '<br>';
            endswitch;
            ?>

            <strong style="margin-top: 5px; display: inline-block;">Valor: </strong>
            <?= number_format($formaPagamento->getValorPagamento() ?? $object->getValorTotal(), 2, ',', '.') ?>

            <br />

            <strong style="margin-top: 5px; display: inline-block;">Status do Pagamento</strong><br />
            <?= $formaPagamento->getStatusLabel() ?>

            <br />
            <br />
            <br />
            <?php
        endforeach;
        ?>

    </div>
</div>

<?php
/* @var $objPedidoStatus PedidoStatus */
/* @var $objPedidoStatusHistorico PedidoStatusHistorico */
?>

<br />

<?php if( !$isMarketingGroup): ?>
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
                            if($objPedidoStatus->getLabelPreConfirmacao() == 'Aguardando Pagamento' && $isFinanceGroup) :
                                $classColumn = 'warning text-warning';
                                $classIcon = 'icon-exclamation-sign';
                                $title = '<b>' . $objPedidoStatus->getLabelPreConfirmacao() . '</b>';
                                $subtitle = 'em andamento';
                                $btn = '<button class="btn btn-sm btn-success proximo-status">'
                                    . '<span>Avançar</span> <i class="icon-arrow-right"></i>'
                                    . '</button>';
                            elseif($objPedidoStatus->getLabelPreConfirmacao() != 'Aguardando Pagamento'):
                                $classColumn = 'warning text-warning';
                                $classIcon = 'icon-exclamation-sign';
                                $title = '<b>' . $objPedidoStatus->getLabelPreConfirmacao() . '</b>';
                                $subtitle = 'em andamento';
                                $btn = '<button class="btn btn-sm btn-success proximo-status">'
                                    . '<span>Avançar</span> <i class="icon-arrow-right"></i>'
                                    . '</button>';
                            endif;
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
<?php endif;?>

<div class="row">
    <legend>Bônus Distribuidos</legend>
    <div class="table-responsive">
        <table class="table">
            <thead>
            <th>Cliente</th>
            <th>Tipo</th>
            <th class="text-right">Bonus</th>
            </thead>
            <tbody>
            <?php
            $totalPontos = 0;
            $totalBonus = 0;
            
            foreach ($arrListaPontos as $objExtrato) : /** @var $objExtrato Extrato */
                $totalPontos += $objExtrato->getPedido()->getTotalPontosProdutos($objExtrato->getTipo());
                $totalBonus += $objExtrato->getPontos();
                ?>
                <tr>
                    <td data-title="Cliente"><?= $objExtrato->getCliente()->getNomeCompleto() ?></td>
                    <td data-title="Tipo"><?= $objExtrato->getTipoDesc() ?></td>
                    <td data-title="Pontos" class="text-right"><?= 'R$ '. number_format($objExtrato->getPontos(), 2, ',', '.') ?></td>
                </tr>
                <?php
            endforeach;
            ?>
            </tbody>
        </table>
    </div>

    <?php if(count($bonusProdutos) > 0) : ?>
        <legend>Bônus Produtos disponíveis</legend>
        <div class="table-responsive">
            <table class="table">
                <thead>
                    <th>Data</th>
                    <th>Plano carreira</th>
                    <th class="text-right">Descrição</th>
                    <th class="text-right">Valor total bonificação</th>
                </thead>
                <tbody>
                <?php
                $totalPontos = 0;
                $totalBonus = 0;
                
                foreach ($bonusProdutos as $bonusProdutos) : /** @var $objExtrato Extrato */
                    ?>
                    <tr id="bonusProdutos_<?=$bonusProdutos->getId()?>">
                        <td data-title="Cliente"><?= date('d/m/Y', strtotime($bonusProdutos->getData())) ?></td>
                        <td data-title="Graduacao"><?= $bonusProdutos->getGraduacao() ?></td>
                        <td data-title="Observacao" class="text-right"><?= $bonusProdutos->getObservacao() ?></td>
                        <td data-title="ValorTotal" class="text-right">R$<?= number_format($bonusProdutos->getValorTotalBonificacao(), '2', ',', '.') ?></td>
                        <td class="text-right" data-title="Ações">
                        <div class="btn-group">
                            <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown">
                                Ações <span class="caret"></span>
                            </button>
                            <ul class="dropdown-menu text-left" role="menu">
                                <li>
                                    <a title="Cancelar"
                                        href="javascript:void(0);"
                                        class="confirmacao"
                                        data-href="<?php echo get_url_admin() . '/bonus-produtos-preview/confirmar' ?>"
                                        data-clienteid="<?php echo $bonusProdutos->getClienteId() ?>"
                                        data-distribuicaoid="<?php echo $bonusProdutos->getDistribuicaoId() ?>"
                                        data-extratoid="<?php echo $bonusProdutos->getId()?>"
                                        data-message="Você deseja confirmar esta distribuição?">
                                        <span class="icon-check"></span> Confirmar
                                    </a>
                                </li>
                            </ul>
                        </div>
                    </td>
                    </tr>
                    <?php
                endforeach;
                ?>
                </tbody>
            </table>
        </div>
    <?php endif ?>
    <div class="pull-right">
        <hr>
        <h3 class="text-right well-mini"><small>Pontos do pedido: </small> <?php echo number_format($object->getValorPontos(), 2, ',', '.'); ?><small> pontos</small></h3>
        <h3 class="text-right well-mini"><small>Bônus distribuídos no pedido: </small> <?php echo 'R$ ' . number_format($totalBonus, 2, ',', '.'); ?><small> bônus</small></h3>
    </div>
</div>
<div class="row">
    <legend>Clear Sale</legend>
    <div class="col-xs-12">
        <?php
        if (!$object->getIntegrouClearSale()) : ?>
        <button class="btn btn-primary btn-sm" id="clearSaleSend" data-id="<?php echo $object->getId() ?>">Enviar Pedido</button>
        <?php else :
            $keyStatus = $object->getSituacaoClearSale();
            $allStatus = [
                \ClearSale\Status::APPROVAL_AUTOMATIC => 'Aprovado',
                \ClearSale\Status::APPROVAL_MANUAL => 'Aprovado',
                \ClearSale\Status::APPROVAL_POLICIES => 'Aprovado',
                \ClearSale\Status::DENIED_AUTOMATIC => 'Negado',
                \ClearSale\Status::DENIED_POLICIES => 'Negado',
                \ClearSale\Status::DENIED_WITHOUT_SUSPICION => 'Negado',
                \ClearSale\Status::MANUAL_ANALYSIS => 'An&aacute;lise Manual',
                \ClearSale\Status::NEW_ORDER => 'Aguardando',
                \ClearSale\Status::SUSPENSION_MANUAL => 'Suspenso',
                \ClearSale\Status::CANCELLED_CUSTOMER => 'Cancelado',
                \ClearSale\Status::FRAUD_CONFIRMED => 'Fraude',
                \ClearSale\Status::PAYMENT_APPROVED => 'Pagamento Aprovado',
                \ClearSale\Status::PAYMENT_DENIED => 'Pagamento Negado',
            ];
            $labels = [
                'Pagamento Negado' => 'label-danger',
                'Negado' => 'label-danger',
                'Cancelado' => 'label-danger',
                'Fraude' => 'label-danger',
                'Suspenso' => 'label-danger',
                'Aprovado' => 'label-success',
                'Pagamento Aprovado' => 'label-success',
                'Aguardando' => 'label-info',
                'An&aacute;lise Manual' => 'label-warning',
                'Status Desconhecido' => 'label-warning',
            ];
            $clearStatus = array_key_exists($keyStatus, $allStatus) ? $allStatus[$keyStatus] : 'Status Desconhecido';
            $clearLabel = array_key_exists($clearStatus, $labels) ? $labels[$clearStatus] : 'label-default';
            ?>
            <span class="label <?php echo $clearLabel; ?>">
                <?php echo $clearStatus; ?>
            </span><br /><br />
            <button class="btn btn-info btn-sm" id="clearSaleUpdate" data-id="<?php echo $object->getId() ?>">Atualizar Status</button>
        <?php endif; ?>
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
            <?php
            foreach ($object->getPedidoItems() as $objPedidoItem) : /* @var $objPedido PedidoItem */
                ProdutoVariacaoPeer::disableSoftDelete();
                ProdutoPeer::disableSoftDelete();
                ProdutoAtributoPeer::disableSoftDelete();
                ProdutoVariacaoAtributoPeer::disableSoftDelete();

                $variacao = $objPedidoItem->getProdutoVariacao();
                $produto = $variacao->getProduto();
                ?>
                <tr>
                    <td data-title="Código"><?= $variacao->getProdutoId() ?></td>
                    <td data-title="Produto">
                        <?php echo $variacao->getProdutoNomeCompleto() . '<br />';
                        echo 'Ref.: ';
                        if ($variacao->getSku() != '') {
                            echo $variacao->getSku();
                        } else {
                            echo $produto->getSku();
                        } ?>
                    </td>
                    <td data-title="Quantidade" class="text-right"><?= $objPedidoItem->getQuantidade() ?></td>
                    <td data-title="Valor Unitário" class="text-right">R$ <?= format_money($objPedidoItem->getValorUnitario()) ?></td>
                    <td data-title="Valor Total" class="text-right">R$ <?= format_money($objPedidoItem->getValorTotal()) ?></td>
                </tr>
                <?php
                if (!empty($produto->getPlanoId())):
                    $produtoKit = $variacao->getProdutoNomeCompleto();

                    foreach ($object->getPedidoItemsAll($produto->getPlanoId()) as $objPedidoItem):
                        ProdutoVariacaoPeer::disableSoftDelete();
                        ProdutoPeer::disableSoftDelete();
                        ProdutoAtributoPeer::disableSoftDelete();
                        ProdutoVariacaoAtributoPeer::disableSoftDelete();
        
                        $variacao = $objPedidoItem->getProdutoVariacao();
                        $produto = $variacao->getProduto();
                        ?>
                        <tr>
                            <td data-title="Código"><?= $variacao->getProdutoId() ?></td>
                            <td data-title="Produto">
                                <?= $variacao->getProdutoNomeCompleto() ?>
                                |
                                <strong>Produto pertencente ao <?= $produtoKit ?></strong>
                                <br />
                                Ref.: <?= $variacao->getSku() != '' ? $variacao->getSku() : $produto->getSku() ?>
                            </td>
                            <td data-title="Quantidade" class="text-right"><?= $objPedidoItem->getQuantidade() ?></td>
                            <td data-title="Valor Unitário" class="text-right">R$ <?= format_money($objPedidoItem->getValorUnitario()) ?></td>
                            <td data-title="Valor Total" class="text-right">R$ <?= format_money($objPedidoItem->getValorTotal()) ?></td>
                        </tr>
                        <?php
                    endforeach;
                endif;
            endforeach;
            ?>
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
        $('#clearSaleSend').click(function () {
            var originalText = $(this).text();
            if ($(this).hasClass('sending')) {
                return false;
            }
            $(this).addClass('sending');
            $.ajax({
                url: window.root_path + "/admin/ajax/pedido-clear-sale",
                type: 'GET',
                dataType: 'json',
                data: {
                    id: $(this).data('id')
                },
                beforeSend: function () {
                    $('#clearSaleSend').text('Enviando...');
                },
                complete: function () {
                    $('#clearSaleSend').removeClass('sending').text(originalText);
                },
                success: function (data) {
                    if (typeof data.message !== 'undefined') {
                        alert(data.message);
                    }
                    if (data.success) {
                        window.location.href = window.location.href;
                    }
                },
                error: function () {
                    alert('Erro ao tentar envio para Clear Sale');
                }
            });
        });
        $('#clearSaleUpdate').click(function () {
            var originalText = $(this).text();
            if ($(this).hasClass('sending')) {
                return false;
            }
            $(this).addClass('sending');
            $.ajax({
                url: window.root_path + "/admin/ajax/pedido-clear-status",
                type: 'GET',
                dataType: 'json',
                data: {
                    id: $(this).data('id')
                },
                beforeSend: function () {
                    $('#clearSaleUpdate').text('Consultando...');
                },
                complete: function () {
                    $('#clearSaleUpdate').removeClass('sending').text(originalText);
                },
                success: function (data) {
                    if (typeof data.message !== 'undefined') {
                        alert(data.message);
                    }
                    if (data.success) {
                        window.location.href = window.location.href;
                    }
                },
                error: function () {
                    alert('Erro ao tentar consultar Clear Sale');
                }
            });
        });


        $('a.confirmacao').click(function(){
            var $this = $(this);
            var clienteid = $this.data('clienteid');
            var distribuicaoid = $this.data('distribuicaoid');
            var extratoid = $this.data('extratoid');
            var message = $this.data('message');
            if (clienteid && distribuicaoid && message) {
                bootbox.confirm({
                    message: message,
                    buttons: {
                        confirm: {
                            label: 'Sim'
                        },
                        cancel: {
                            label: 'Não'
                        }
                    },
                    callback: function(result){
                        if (result) {
                            const url = "<?php echo  get_url_admin(). '/pedidos' ?>";
                            console.log(url)

                            $.ajax({
                                url: window.root_path + "/admin/ajax/confirmar_bonus_produtos_distribuido",
                                type: "POST",
                                data: {id: clienteid, distribuicaoid: distribuicaoid},
                                success: function(data){
                                    var obj = JSON.parse(data);
                                    var message = obj.message;
                                    var status = obj.status;
                                    if(status === 'success') {
                                        $(`#bonusProdutos_${extratoid}`).remove();
                                    }
                                }
                            });
                        }
                    }
                });
            }
            return true;
        });
    });
</script>