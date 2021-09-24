<?php
if ($objFormaPagamento->getFormaPagamento() == PedidoFormaPagamentoPeer::FORMA_PAGAMENTO_PAGSEGURO) : ?>
    <div class="col-xs-6 col-sm-5 col-md-3">
        <div class="text-center">
            <span class="icon-<?php echo strtolower($objFormaPagamento->getFormaPagamento()) ?>"></span>
        </div>
    </div>
    <div class="col-xs-6 col-sm-7 col-md-4">
        <p class="small">
            <?php echo $objFormaPagamento->getFormaPagamentoDescricao() ?><br>
            Valor <span class="text-success">R$ <?php echo format_money($objFormaPagamento->getValorPagamento() ?? $objPedido->getValorTotal()) ?></span>
        </p>
    </div>

<?php endif;
