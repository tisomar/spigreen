<?php
use QPress\Template\Widget;

$gerenciador = new GerenciadorPontosClientePreferencial();

$pontosDisponiveis = $gerenciador->getTotalPontosDisponiveis(ClientePeer::getClienteLogado());
$valorTotal = $pontosDisponiveis > $valorRestanteDividido ? $valorRestanteDividido : $pontosDisponiveis;

if ($carrinho->getDescontoPontos() === null && $pontosDisponiveis > $valorBonusCP) :
    ?>
    <form role="form" class="form-payment form-validate form-disabled-on-load" method="post" action="#" id="">
        <input type="hidden" name="forma_pagamento" value="<?php echo PedidoFormaPagamentoPeer::FORMA_PAGAMENTO_PONTOS_CLIENTE_PREFERENCIAL ?>">
        
        <?php renderPagamentoDivididoFields() ?>

        <div class="form-group imp-val" hidden>
            <input
                type="text"
                class="form-control mask-money"
                name="valor_pagamento"
                value="<?= number_format($valorTotal, 2, ',', '.') ?>"
            >
        </div>

        <div class="panel">
            <div class="panel-body">
                <?php
                Widget::render('checkout/pagamento/title-payment-type', array(
                    'paymentTypeId'     => PedidoFormaPagamentoPeer::FORMA_PAGAMENTO_PONTOS_CLIENTE_PREFERENCIAL,
                    'paymentTypeName'   => 'Pontos',
                    'paymentTypeValue'  => $valorTotal,
                    'isOpenedPanel'     => $isOpenedPanel,
                ));
                ?>

                <div id="<?php echo PedidoFormaPagamentoPeer::FORMA_PAGAMENTO_PONTOS_CLIENTE_PREFERENCIAL ?>" class="panel-collapse collapse <?php echo ($isOpenedPanel ? 'in' : '') ?>">
                    <div class="panel-body">
                        <?php
                        Widget::render('checkout/pagamento/header-payment-type', array(
                            'valorTotal' => $valorTotal,
                        ));
                        ?>
                        <ul class="list-unstyled">
                            <li>O pagamento será feito utilizando seus pontos disponíveis.</li>
                            <li>Caso o total de pontos não seja suficiente, você deverá escolher outra forma de pagamento para completar o valor restante.</li>
                        </ul>
                        <hr>
                        <button type="submit" class="btn btn-success btn-block confirm-payment" data-payment-type="debito_online">
                            <span class="<?php icon('lock') ?>"></span> Finalizar compra
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </form>
    <?php
endif;