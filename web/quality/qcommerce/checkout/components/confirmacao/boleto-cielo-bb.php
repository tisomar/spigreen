<?php
if ($objFormaPagamento->getFormaPagamento() == PedidoFormaPagamentoPeer::FORMA_PAGAMENTO_CIELO_BOLETO_BB) : ?>
    <div class="col-xs-4 col-sm-4 col-md-2">
        <div class="text-center">
            <span class="icon-boleto-32"></span>
        </div>
    </div>
    <div class="col-xs-8 col-sm-8 col-md-4">
        <p class="small">
            <?php echo $objFormaPagamento->getFormaPagamentoDescricao() ?><br>
            Valor <span class="text-success">R$ <?php echo format_money($objFormaPagamento->getValorPagamento() ?? $objPedido->getValorTotal()) ?></span>
        </p>
    </div>
    <div class="col-xs-12 col-sm-12 col-md-6">
        <a href="<?php echo $objFormaPagamento->getUrlAcesso() ?>" target="_blank" class="btn btn-success btn-block">
            <span class="<?php icon('print') ?>"></span>
            IMPRIMIR
        </a>
    </div>

<?php endif;
