<?php
use QPress\Template\Widget;

$isDisponivel = Config::get('pagseguro.debito_online') && (Config::get('pagseguro.opcao_pagamento') == "transparente");
if ($isDisponivel) {
    ?>
    <form role="form" class="form-payment form-validate form-disabled-on-load" method="post" action="#"  id="form-debito">
        <input type="hidden" name="forma_pagamento" value="<?php echo PedidoFormaPagamentoPeer::FORMA_PAGAMENTO_PAGSEGURO_DEBITO_ONLINE ?>">
        <div class="panel">
            <div class="panel-body">
                <?php
                Widget::render('checkout/pagamento/title-payment-type', array(
                    'paymentTypeId'     => PedidoFormaPagamentoPeer::FORMA_PAGAMENTO_PAGSEGURO_DEBITO_ONLINE,
                    'paymentTypeName'   => 'Débito Online',
                    'paymentTypeValue'  => $carrinho->getValorTotal(),
                    'isOpenedPanel'     => $isOpenedPanel,
                ));
                ?>

                <div id="<?php echo PedidoFormaPagamentoPeer::FORMA_PAGAMENTO_PAGSEGURO_DEBITO_ONLINE ?>" class="panel-collapse collapse <?php echo ($isOpenedPanel ? 'in' : '') ?>">
                    <div class="panel-body">

                        <?php
                        Widget::render('checkout/pagamento/header-payment-type', array(
                            'valorTotal'            => $carrinho->getValorTotal(),
                        ));
                        ?>

                        Selecione o banco:
                        <ul id="bank-options" class="list-unstyled list-group"></ul>
                        <br>
                        <ul>
                            <li>Você terá até 8 horas para realizar o débito online se optar por essa forma de pagamento.</li>
                            <li>Após clicar no botão <b>"Efetuar Pagamento"</b>, você será direcionado para página do seu banco onde poderá realizar o pagamento.</li>
                        </ul>

                        <hr>

                        <img src="<?php echo asset('/img/pagseguro-selos/selo04_300x60.gif') ?>" class=" center-block img-responsive" alt="">
                        <br>
                        <button type="submit" class="btn btn-success btn-block confirm-payment" data-payment-type="debito_online">
                            <span class="<?php icon('lock') ?>"></span> Finalizar compra
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </form>

    <?php
}
