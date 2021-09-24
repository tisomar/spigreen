<?php
use QPress\Template\Widget;
if ($valorEmLoja == 0) :
?>
<form role="form" class="form-payment form-validate form-disabled-on-load" method="post" action="#" id="">
    <input type="hidden" name="forma_pagamento" value="<?php echo PedidoFormaPagamentoPeer::FORMA_PAGAMENTO_EM_LOJA ?>">

    <?php renderPagamentoDivididoFields() ?>

    <div class="panel">
        <div class="panel-body">
            <?php
            Widget::render('checkout/pagamento/title-payment-type', array(
                'paymentTypeId'     => PedidoFormaPagamentoPeer::FORMA_PAGAMENTO_EM_LOJA,
                'paymentTypeName'   => 'Pagamento em Loja',
                'paymentTypeValue'  => $valorRestanteDividido,
                'isOpenedPanel'     => $isOpenedPanel,
            ));
            ?>

            <div id="<?= PedidoFormaPagamentoPeer::FORMA_PAGAMENTO_EM_LOJA ?>" class="panel-collapse collapse <?php echo ($isOpenedPanel ? 'in' : '') ?>">
                <div class="panel-body">
                    <?php
                    Widget::render('checkout/pagamento/header-payment-type', array(
                        'valorTotal' => $valorRestanteDividido,
                    ));
                    ?>
                    
                    <ul class="list-unstyled">
                        <li>O pagamento será realizado em loja física.</li>
                    </ul>
                    <hr>
                    
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
                    
                    <button type="submit" class="btn btn-success btn-block confirm-payment" data-payment-type="debito_online">
                        <span class="<?php icon('lock') ?>"></span> Finalizar compra
                    </button>
                </div>
            </div>
        </div>
    </div>
</form>

<?php endif ?>
