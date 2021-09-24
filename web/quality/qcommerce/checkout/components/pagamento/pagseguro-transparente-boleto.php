<?php

use QPress\Template\Widget;

$valorMinimo            = format_money(Config::get('valor_minimo_boleto'));

$isDisponivel = Config::get('pagseguro.boleto_bancario') && (Config::get('pagseguro.opcao_pagamento') == "transparente");
if ($isDisponivel) {
    $valorTotal = $carrinho->getValorTotal(false);
    $porcentagemDesconto = Config::get('boleto.desconto_pagamento_avista');
    $possuiDesconto = (bool) ($porcentagemDesconto > 0);

    if ($possuiDesconto) {
        if (Config::get('boleto_desconto_tipo') == PedidoFormaPagamentoPeer::BOLETO_DESCONTO_ITENS) {
            $valorTotal = $carrinho->getValorItens() - $carrinho->getValorDesconto(false);
            $valorTotal = aplicarPercentualDesconto($valorTotal, Config::get('boleto.desconto_pagamento_avista')) + $carrinho->getValorEntrega();
            $mensagem = "O desconto será concedido apenas sobre o valor dos itens do pedido";
        } elseif (Config::get('boleto_desconto_tipo') == PedidoFormaPagamentoPeer::BOLETO_DESCONTO_TOTAL) {
            $valorTotal = aplicarPercentualDesconto($carrinho->getValorTotal(), Config::get('boleto.desconto_pagamento_avista'));
            $mensagem = "O desconto será concedido sobre o valor total do pedido.";
        }
    }

    ?>
    <form role="form" class="form-payment form-validate form-disabled-on-load" method="post" action="#" id="form-boleto">
        <input type="hidden" name="forma_pagamento" value="<?php echo PedidoFormaPagamentoPeer::FORMA_PAGAMENTO_PAGSEGURO_BOLETO ?>">

        <div class="panel">
            <div class="panel-body">
                <?php
                Widget::render('checkout/pagamento/title-payment-type', array(
                    'paymentTypeId'     => PedidoFormaPagamentoPeer::FORMA_PAGAMENTO_PAGSEGURO_BOLETO,
                    'paymentTypeName'   => 'Boleto Bancário',
                    'paymentTypeValue'  => $valorTotal,
                    'isOpenedPanel'     => $isOpenedPanel,
                ));
                ?>
                <div id="<?php echo PedidoFormaPagamentoPeer::FORMA_PAGAMENTO_PAGSEGURO_BOLETO ?>" class="panel-collapse collapse <?php echo ($isOpenedPanel ? 'in' : '') ?>">
                    <div class="panel-body">

                        <?php
                        Widget::render('checkout/pagamento/header-payment-type', array(
                            'valorTotal'            => $valorTotal,
                            'valorTotalSemDesconto' => $carrinho->getValorTotal(false),
                            'porcentagemDesconto'   => $porcentagemDesconto,

                        ));
                        ?>

                        <div>
                            <ul class="list-unstyled">
                                <?php if ($possuiDesconto) : ?>
                                    <li><?php echo $mensagem; ?></li>
                                <?php endif; ?>
                                <li>
                                    Será cobrada taxa de R$ 1,00 para cobrir os custos deste meio de pagamento.
                                </li>
                            </ul>
                        </div>
                        <hr>
                        <?php if ($carrinho->getValorTotal(false) < $valorMinimo) : ?>
                            <div class="alert alert-danger">
                                <strong>Atenção!</strong><br>
                                O pagamento com boleto bancário está disponível somente para compras acima de R$ <?php echo $valorMinimo; ?>.
                            </div>
                        <?php else : ?>
                            <img src="<?php echo asset('/img/pagseguro-selos/selo04_300x60.gif') ?>" class=" center-block img-responsive" alt="">
                            <br>
                            <button type="submit" class="btn btn-success btn-block confirm-payment" data-payment-type="boleto">
                                <span class="<?php icon('lock') ?>"></span> Finalizar compra
                            </button>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>

    </form>
    <?php
}
