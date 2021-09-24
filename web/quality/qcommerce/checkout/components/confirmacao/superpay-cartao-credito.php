<?php if ($objFormaPagamento->getFormaPagamento() == PedidoFormaPagamentoPeer::FORMA_PAGAMENTO_CARTAO_CREDITO) : ?>
    <div class="col-xs-4 col-sm-4 col-md-2">
        <div class="text-center">
            <span class="icon-<?php echo strtolower($objFormaPagamento->getBandeira()) ?>"></span>
        </div>
    </div>

    <div class="col-xs-8 col-sm-6 col-md-8">
        <p class="small">
            <?php echo $objFormaPagamento->getFormaPagamentoDescricao() ?>
            <br>
            Valor:
            <span class="text-success">R$ <?php echo format_money($objFormaPagamento->getValorPagamento() ?? $objPedido->getValorTotal()) ?></span>
            <span class="small">em <?php echo $objFormaPagamento->getNumeroParcelas() ?>x
                de <?php echo format_money($objFormaPagamento->getValorPorParcela()) ?></span>
        </p>
    </div>

    <div class="col-xs-12">
        <p class="text-success">Seu pagamento está em análise e assim que confirmado daremos sequência no processo liberação do seu pedido.</p>
    </div>

<?php endif; ?>
