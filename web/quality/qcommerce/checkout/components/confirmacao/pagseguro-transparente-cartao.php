<?php if (PedidoFormaPagamentoPeer::FORMA_PAGAMENTO_PAGSEGURO_CARTAO_CREDITO == $objFormaPagamento->getFormaPagamento()) :
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
        <?php if ($transaction->getStatus()->getValue() == 3) : ?>
            <p class="text-success">Seu pagamento foi aprovado! A partir de agora, efetuaremos o processo de liberação dos seus produtos.</p>
        <?php else : ?>
            <p>Estamos aguardando a confirmação do seu pagamento para dar sequencia no procesos de liberação dos seus produtos.</p>
        <?php endif;  ?>
    </div>
<?php endif;
