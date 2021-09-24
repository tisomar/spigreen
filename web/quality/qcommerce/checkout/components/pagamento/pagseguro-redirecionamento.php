<?php
use QPress\Template\Widget;

$isDisponivel = Config::get('meio_pagamento.pagseguro') && (Config::get('pagseguro.opcao_pagamento') == "padrao");

$botao = 'Finalizar Compra';

if ($isDisponivel) {
    ?>
    <form role="form" class="form-payment form-validate form-disabled-on-load" method="post" action="#"  id="">
        <input type="hidden" name="forma_pagamento" value="<?php echo PedidoFormaPagamentoPeer::FORMA_PAGAMENTO_PAGSEGURO ?>">
        <div class="panel">
            <div class="panel-body">
                <?php
                Widget::render('checkout/pagamento/title-payment-type', array(
                    'paymentTypeId'     => PedidoFormaPagamentoPeer::FORMA_PAGAMENTO_PAGSEGURO,
                    'paymentTypeName'   => 'PagSeguro',
                    'paymentTypeValue'  => $carrinho->getValorTotal(),
                    'isOpenedPanel'     => $isOpenedPanel,
                ));
                ?>
                <div id="<?php echo PedidoFormaPagamentoPeer::FORMA_PAGAMENTO_PAGSEGURO ?>" class="panel-collapse collapse <?php echo ($isOpenedPanel ? 'in' : '') ?>">
                    <div class="panel-body">

                        <?php
                        Widget::render('checkout/pagamento/header-payment-type', array(
                            'valorTotal'            => $carrinho->getValorTotal(),
                        ));
                        ?>

                        <ul class="list-unstyled">
                            <li>Você será redirecionado para o ambiente do PagSeguro para efetuar o pagamento.</li>
                            <li>Após a conclusão, você será redirecionado ao site novamente.</li>
                            <li>Por favor, aguarde a finalização do processo.</li>
                        </ul>
                        <hr>

                        <img src="<?php echo asset('/img/pagseguro-selos/selo04_300x60.gif') ?>" class=" center-block img-responsive" alt="">
                        <br>
                        <button type="submit" class="btn btn-success btn-block confirm-payment" data-payment-type="debito_online">
                            <span class="<?php icon('lock') ?>"></span> <?php echo $botao ?>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </form>

    <?php
}
