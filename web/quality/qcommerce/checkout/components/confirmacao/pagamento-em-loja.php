<?php
if ($objFormaPagamento->getFormaPagamento() == PedidoFormaPagamentoPeer::FORMA_PAGAMENTO_EM_LOJA) : ?>
    <div class="col-xs-4 col-sm-4 col-md-2">
        <div class="text-center">
            <span class="icon-equal"></span>
        </div>
    </div>
    <div class="col-xs-8 col-sm-8 col-md-10">
        <p class="small">
            Pagamento em Loja<br>
            Valor <span class="text-success">R$ <?php echo format_money($objFormaPagamento->getValorPagamento() ?? $objPedido->getValorTotal()) ?></span>
        </p>
    </div>
    <?php
endif;
