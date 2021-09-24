<?php
if (PedidoFormaPagamentoPeer::FORMA_PAGAMENTO_PAGSEGURO_BOLETO == $objFormaPagamento->getFormaPagamento()) :
    ?>
    <div class="col-xs-6 col-sm-5 col-md-3">
        <div class="text-center">
            <span class="icon-pagseguro-32"></span>
        </div>
    </div>
    <div class="col-xs-8 col-sm-8 col-md-4">
        <p class="small">
            <?php echo $objFormaPagamento->getFormaPagamentoDescricao() ?><br>
            Valor <span class="text-success">R$ <?php echo format_money($objFormaPagamento->getValorPagamento() ?? $objPedido->getValorTotal()) ?></span>
        </p>
    </div>
    <div class="col-xs-12 col-sm-12 col-md-5">
        <a href="<?php echo $objFormaPagamento->getUrlAcesso() ?>" target="_blank" class="btn btn-success btn-block">
            <span class="<?php icon('print') ?>"></span>
            IMPRIMIR
        </a>
    </div>
    <br><br><br>
    <div class="col-xs-12">
        <ul>
            <li class="text-success">
                Clique no botão imprimir para gerar o boleto e efetuar o pagamento.
            </li>
            <li>
                Será cobrada taxa de R$ 1,00 para cobrir os custos deste meio de pagamento.
            </li>
            <li>
                Lembre-se de efetuar o pagamento até a data de vencimento do boleto. Após essa data o boleto perderá a validade e seu pedido será cancelado automaticamente.
            </li>

        </ul>
    </div>
    <?php
endif;
