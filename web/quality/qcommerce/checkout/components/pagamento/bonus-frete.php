<?php
use QPress\Template\Widget;

// Estara disponivel caso o cliente possua bônus frete e ainda não tenha escolhido pagamento por bônus frete anteriormente.
$gerenciador = new GerenciadorPontos(Propel::getConnection(), $logger);
$totalBonusFrete = $gerenciador->getTotalBonusFrete(ClientePeer::getClienteLogado());
$isDisponivel = $carrinho->getDescontoPontos() === null && $totalBonusFrete > 0;
// Valor de pagament por bônus frete fica contendo somente o valor dos items do carrinho que são acessório.
$valor_produtos_acessorios = $carrinho->getValorAcessorios();

if ($valor_produtos_acessorios > 0 && $valor_produtos_acessorios > $totalBonusFrete) :
    $valor_produtos_acessorios = $totalBonusFrete;
endif;

$valor_produtos_acessorios = $valor_produtos_acessorios > $valorRestanteDividido ? $valorRestanteDividido : $valor_produtos_acessorios;

if ($valor_produtos_acessorios > $valorBonusFreteUtilizados) :
    ?>
    <form role="form" class="form-payment form-validate form-disabled-on-load" method="post" action="#"  id="">
        <input type="hidden" name="forma_pagamento" value="<?php echo PedidoFormaPagamentoPeer::FORMA_PAGAMENTO_BONUS_FRETE ?>">

        <?php renderPagamentoDivididoFields() ?>

        <div class="panel">
            <div class="panel-body">
                <?php
                Widget::render('checkout/pagamento/title-payment-type', array(
                    'paymentTypeId'     => PedidoFormaPagamentoPeer::FORMA_PAGAMENTO_BONUS_FRETE,
                    'paymentTypeName'   => 'Bônus Frete',
                    'paymentTypeValue'  => $valor_produtos_acessorios,
                    'isOpenedPanel'     => $isOpenedPanel,
                ));
                ?>

                <div id="<?php echo PedidoFormaPagamentoPeer::FORMA_PAGAMENTO_BONUS_FRETE ?>" class="panel-collapse collapse <?php echo ($isOpenedPanel ? 'in' : '') ?>">
                    <div class="panel-body">
                        <?php
                        Widget::render('checkout/pagamento/header-payment-type', array(
                            'valorTotal' => $valor_produtos_acessorios,
                        ));
                        ?>

                        <ul class="list-unstyled">
                            <li>O pagamento será feito utilizando seus bônus de frete disponíveis para resgate.</li>
                            <li>Caso o total de bônus não seja suficiente, você deverá escolher outra forma de pagamento para completar o valor restante.</li>
                        </ul>
                        <hr>

                        <input
                            type="hidden"
                            name="valor_pagamento"
                            value="<?= number_format($valor_produtos_acessorios, 2, ',', '.') ?>"
                        />

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
