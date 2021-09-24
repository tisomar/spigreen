<?php
use QPress\Template\Widget;

$valorMinimo            = format_money(Config::get('valor_minimo_boleto'));
$botao = 'Finalizar Compra';

$isDisponivel = Config::get('cielo-boleto.enabled');
if ($isDisponivel && $valorBoleto == 0) {
    $valorTotal = $valorRestanteDividido;
    $porcentagemDesconto = Config::get('cielo-boleto.desconto_pagamento_avista');
    $possuiDesconto = (bool) ($porcentagemDesconto > 0);

    if ($possuiDesconto) {
        if (Config::get('cielo-boleto.desconto_tipo') == PedidoFormaPagamentoPeer::BOLETO_DESCONTO_ITENS) {
            $valorTotal = $carrinho->getValorItens() - $carrinho->getValorDesconto(false);
            $valorTotal = aplicarPercentualDesconto($valorTotal, Config::get('cielo-boleto.desconto_pagamento_avista')) + $carrinho->getValorEntrega();
            $mensagem = "O desconto será concedido apenas sobre o valor dos itens do pedido";
        } elseif (Config::get('cielo-boleto.desconto_tipo') == PedidoFormaPagamentoPeer::BOLETO_DESCONTO_TOTAL) {
            $valorTotal = aplicarPercentualDesconto($valorRestanteDividido, Config::get('cielo-boleto.desconto_pagamento_avista'));
            $mensagem = "O desconto será concedido sobre o valor total do pedido.";
        }
    }

    if (Config::get('cielo-boleto.provider') == 'BancoDoBrasil2') {
        $formaPgto = PedidoFormaPagamentoPeer::FORMA_PAGAMENTO_CIELO_BOLETO_BB;
        $namePgto = 'Boleto Banco do Brasil';
    } else {
        $formaPgto = PedidoFormaPagamentoPeer::FORMA_PAGAMENTO_CIELO_BOLETO_BRADESCO;
        $namePgto = 'Boleto Bradesco';
    }

    ?>
    <form role="form" class="form-payment form-validate form-disabled-on-load" method="post" action="#" id="form-boleto">
        <input type="hidden" name="forma_pagamento" value="<?php echo $formaPgto ?>">

        <?php renderPagamentoDivididoFields() ?>

        <div class="panel">
            <div class="panel-body">
                <?php
                Widget::render('checkout/pagamento/title-payment-type', array(
                    'paymentTypeId'     => $formaPgto,
                    'paymentTypeName'   => $namePgto,
                    'paymentTypeValue'  => $valorRestanteDividido,
                    'isOpenedPanel'     => $isOpenedPanel,
                ));
                ?>

                <div id="<?php echo $formaPgto ?>" class="panel-collapse collapse <?php echo ($isOpenedPanel ? 'in' : '') ?>">
                    <div class="panel-body">

                        <?php
                        Widget::render('checkout/pagamento/header-payment-type', array(
                            'valorTotal'            => $valorTotal,
                            'valorTotalSemDesconto' => $valorRestanteDividido,
                            'porcentagemDesconto'   => $porcentagemDesconto,
                        ));
                        ?>

                        <div>
                            <ul>
                                <?php if ($possuiDesconto) : ?>
                                    <li><?php echo $mensagem; ?></li>
                                <?php endif; ?>
                                <li>Necessário o desbloqueio de popups</li>
                            </ul>
                        </div>
                        <hr>
                        <?php if ($carrinho->getValorTotal(false) < $valorMinimo) : ?>
                            <div class="alert alert-danger">
                                <strong>Atenção!</strong><br>
                                O pagamento com boleto bancário está disponível somente para compras acima de R$ <?php echo $valorMinimo; ?>.
                            </div>
                        <?php else : ?>
                            <div class="col-xs-12">
                                <!-- <button type="submit" class="btn btn-success btn-block confirm-payment" data-payment-type="cartao-credito">
                                    <span class="<?php // icon('lock') ?>"></span> <?php // echo $botao ?>
                                </button> -->
                                <div class="text-right">
                                    <a href="javascript: void 0;" class="link-dividir-pagamento" style="margin-bottom: 15px; display: inline-block;">
                                        Dividir o pagamento
                                    </a>
                                </div>
                                <div class="form-group" style="display: none;">
                                    <label>Valor pagamento:</label>
                                    <input
                                        class="form-control mask-money"
                                        name="valor_pagamento"
                                        placeholder="Valor deste pagamento"
                                        disabled
                                    />
                                </div>
                            </div>

                            <button type="submit" class="btn btn-success btn-block confirm-payment" data-payment-type="boleto">
                                <span class="<?php icon('lock') ?>"></span> <?php echo $botao ?>
                            </button>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>

    </form>
    <?php
}
