<?php
use QPress\Template\Widget;

// Estará disponivel caso o cliente possua bônus e ainda não tenha escolhido pagamento por bônus anteriormente.
$gerenciador = new GerenciadorPontos(Propel::getConnection(), $logger);

$isDisponivel =
    $carrinho->getDescontoPontos() === null &&
    $gerenciador->getTotalPontosDisponiveisParaResgate(
        ClientePeer::getClienteLogado(),
        null,
        null,
        Extrato::TIPO_INDICACAO_DIRETA,
        true
    ) > 0;

$bonus = $gerenciador->getTotalPontosDisponiveisParaResgate(
    ClientePeer::getClienteLogado(),
    null,
    null,
    Extrato::TIPO_INDICACAO_DIRETA,
    true
);

$totalBonus = $bonus - $valorBonusUtilizados;
$totalPagamento = $totalBonus > $valorRestanteDividido ? $valorRestanteDividido : $totalBonus;

if ($totalPagamento > $valorBonusUtilizados && $valorBonusUtilizados == 0) {
    ?>
    <form role="form" class="form-payment form-validate form-disabled-on-load" method="post" action="#"  id="">
        <input type="hidden" name="forma_pagamento" value="<?php echo PedidoFormaPagamentoPeer::FORMA_PAGAMENTO_PONTOS ?>">

        <?php renderPagamentoDivididoFields() ?>

        <div class="panel">
            <div class="panel-body">
                <?php
//                Widget::render('checkout/pagamento/title-payment-type', array(
//                    'paymentTypeId'     => PedidoFormaPagamentoPeer::FORMA_PAGAMENTO_PONTOS,
//                    'paymentTypeName'   => 'Bônus',
//                    'paymentTypeValue'  => $carrinho->getValorTotal(),
//                    'isOpenedPanel'     => $isOpenedPanel,
//                ));
                Widget::render('checkout/pagamento/title-payment-type', array(
                    'paymentTypeId'     => PedidoFormaPagamentoPeer::FORMA_PAGAMENTO_PONTOS,
                    'paymentTypeName'   => 'Bônus',
                    'paymentTypeValue'  => $totalPagamento,
                    'isOpenedPanel'     => $isOpenedPanel,
                ));
                ?>

                <div id="<?php echo PedidoFormaPagamentoPeer::FORMA_PAGAMENTO_PONTOS ?>" class="panel-collapse collapse <?php echo ($isOpenedPanel ? 'in' : '') ?>">
                    <div class="panel-body">
                        <?php
                        Widget::render('checkout/pagamento/header-payment-type', array(
                            'valorTotal'            => $totalPagamento,
                        ));

//                        Widget::render('checkout/pagamento/header-payment-type', array(
//                            'valorTotal'            => $carrinho->getValorTotal(),
//                        ));
                        ?>

                        <ul class="list-unstyled">
                            <li>O pagamento será feito utilizando seus bônus disponíveis para resgate.</li>
                            <li>O total de bônus disponíveis no momento é de: <br> R$ <?php echo number_format($totalBonus, '2', ',', '.') ?></li>
                        </ul>
                        <hr>
                        <div class="custom-control custom-switch">
                            <input type="checkbox" class="custom-control-input" id="switch1" name="example">
                            <label class="custom-control-label" for="switch1">Utilizar valor parcial de bônus</label>
                        </div>
                        <br>
                        <div class="form-group imp-val" hidden>
                            <label for="valorBonus">Valor a utilizar</label>
                            <input
                                type="text"
                                class="form-control mask-money"
                                id="valorBonus"
                                name="valor_pagamento"
                                max="<?= $totalPagamento ?>"
                                value="<?= number_format($totalPagamento, 2, ',', '.') ?>"
                            >
                        </div>
                        <br>

                        <button type="submit" class="btn btn-success btn-block confirm-payment" data-payment-type="debito_online">
                            <span class="<?php icon('lock') ?>"></span> Finalizar compra
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </form>
<script>
    $('#switch1').on('change', function() {
        if($(this). is(":checked")){
            $('.imp-val').attr('hidden', false);
        }else{
            $('#valorBonus').val('');
            $('.imp-val').attr('hidden', true);
        }
    })
</script>
    <?php
}
