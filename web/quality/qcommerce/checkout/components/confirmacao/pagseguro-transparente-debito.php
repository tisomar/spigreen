<?php
if (PedidoFormaPagamentoPeer::FORMA_PAGAMENTO_PAGSEGURO_DEBITO_ONLINE == $objFormaPagamento->getFormaPagamento()) :
    $gateway = $container->getGatewayManager()->get('PagSeguroTransparente');

    $transaction = $gateway->searchByCode($objFormaPagamento->getTransacaoId());

    ?>
    <div class="col-xs-6 col-sm-5 col-md-3">
        <div class="text-center">
            <span class="icon-pagseguro"></span>
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
            <span class="<?php icon('check') ?>"></span>
            EFETUAR PAGAMENTO
        </a>
    </div>
    <br><br><br>
    <div class="col-xs-12">
        <ul>
            <li class="text-success">
                Após clicar no botão <b>"Efetuar Pagamento"</b>, você será direcionado para página do seu banco onde poderá realizar o pagamento.
            </li>
        </ul>
    </div>
<?php endif;
