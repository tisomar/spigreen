<?php
if ($objFormaPagamento->getFormaPagamento() == PedidoFormaPagamentoPeer::FORMA_PAGAMENTO_ITAUSHOPLINE) : ?>
    <form method="post" action="#" target="SHOPLINE" onsubmit="itau.output();" id="itau-shopline">
        <div class="col-xs-4 col-sm-4 col-md-2">
            <div class="text-center">
                <span class="icon-<?php echo strtolower($objFormaPagamento->getFormaPagamento()) ?>"></span>
            </div>
        </div>
        <div class="col-xs-8 col-sm-8 col-md-4">
            <p class="small">
                <?php echo $objFormaPagamento->getFormaPagamentoDescricao() ?><br>
                Valor <span class="text-success">R$ <?php echo format_money($objFormaPagamento->getValorPagamento() ?? $objPedido->getValorTotal()) ?></span>
            </p>
        </div>
        <div class="col-xs-12 col-sm-12 col-md-6">
            <input type="hidden" value="<?php echo $objFormaPagamento->getUrlAcesso() ?>" name="itau-data-url" />
            <button type="submit" class="btn btn-success btn-block">
                <span class="<?php icon('print') ?>"></span>
                IMPRIMIR
            </button>
        </div>
    </form>

<?php endif;
