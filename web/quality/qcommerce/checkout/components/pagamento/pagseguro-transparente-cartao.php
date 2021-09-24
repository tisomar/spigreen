<?php
use QPress\Template\Widget;
$isDisponivel = Config::get('pagseguro.cartao_credito') && (Config::get('pagseguro.opcao_pagamento') == "transparente");
if ($isDisponivel) {
    ?>
    <form role="form" class="form-payment form-payment-pagseguro-transparente form-validate form-disabled-on-load" method="post" action="#" id="form-cartao">
        <input type="hidden" name="forma_pagamento" value="<?php echo PedidoFormaPagamentoPeer::FORMA_PAGAMENTO_PAGSEGURO_CARTAO_CREDITO ?>">

        <div class="panel">
            <div class="panel-body">
                <?php
                Widget::render('checkout/pagamento/title-payment-type', array(
                    'paymentTypeId'     => PedidoFormaPagamentoPeer::FORMA_PAGAMENTO_PAGSEGURO_CARTAO_CREDITO,
                    'paymentTypeName'   => 'Cartão de Crédito',
                    'paymentTypeValue'  => $carrinho->getValorTotal(),
                    'isOpenedPanel'     => $isOpenedPanel,
                ));
                ?>
                
                <div id="<?php echo PedidoFormaPagamentoPeer::FORMA_PAGAMENTO_PAGSEGURO_CARTAO_CREDITO ?>" class="panel-collapse collapse <?php echo ($isOpenedPanel ? 'in' : '') ?>">
                    <div class="panel-body">

                        <?php
                        Widget::render('checkout/pagamento/header-payment-type', array(
                            'valorTotal'            => $carrinho->getValorTotal(),
                        ));
                        ?>

                        <div class="row">
                            <div class="col-xs-12">
                                <div class="form-group inner-icon">
                                    <label for="number-card">* Número do cartão</label>
                                    <input type="text" id="number-card" name="cartao[numero]" autocomplete="off" placeholder="____ ____ ____ ____" maxlength="16" class="form-control input-credit-card-pagseguro" required>
                                    <span class="icon-"></span>
                                    <input type="hidden" id="flag-card" name="BANDEIRA" value="" class="card_flags">
                                </div>
                            </div>

                            <div class="col-xs-12 col-sm-6">
                                <label for="expiration-year">* Data de Validade:</label>
                                <div class="row">
                                    <div class="col-xs-6">
                                        <div class="form-group">
                                            <input type="text" class="form-control mask-mes" id="expiration-month" name="cartao[validade_mes]" required placeholder="Mês">
                                        </div>
                                    </div>
                                    <div class="col-xs-6">
                                        <div class="form-group">
                                            <input type="text" class="form-control mask-year" id="expiration-year" name="cartao[validade_ano]" required placeholder="Ano">
                                        </div>
                                    </div>

                                </div>
                            </div>

                            <div class="col-xs-12 col-sm-6">
                                <div class="form-group inner-icon">
                                    <label for="security-code">*Código de Segurança:</label>
                                    <input type="text" id="security-code" class="form-control mask-security-code-card" name="cartao[codigo_seguranca]" required>
                                    <span class="icon-security-code"></span>
                                </div>
                            </div>

                            <div class="col-xs-12">
                                <div class="form-group reload-onchange">
                                    <label for="payment-options">* Opções de parcelamento:</label>
                                    <?php $opcoesPagamento = getOpcoesPagamento($carrinho); ?>
                                    <select class="form-control" id="payment-options" name="numero_parcelas" required disabled>
                                        <option value="">Preencha os dados de cartão</option>
                                    </select>
                                </div>
                            </div>

                            <div class="clearfix"></div>

                            <hr>

                            <div class="col-xs-12">
                                <div class="form-group">
                                    <label for="name-card">* Nome do titular</label>
                                    <input type="text" id="name-card" name="pagseguro[card][titular]" class="form-control" placeholder="(exatamente como está no cartão)" required>
                                </div>
                            </div>

                            <div class="col-xs-12 col-sm-6">
                                <div class="form-group">
                                    <label for="name-card">* CPF do titular</label>
                                    <input type="text" id="" placeholder="___.___.___-__" name="pagseguro[card][cpf]" class="form-control mask-cpf" required>
                                </div>
                            </div>
                            <div class="col-xs-12 col-sm-6">
                                <div class="form-group">
                                    <label for="name-card">* Data de <abbr title="Nascimento">Nasc.</abbr> do titular</label>
                                    <input type="text" id="" placeholder="__/__/____" name="pagseguro[card][data_nascimento]" class="form-control mask-date" required>
                                </div>
                            </div>

                        </div>
                        <hr>
                        <img src="<?php echo asset('/img/pagseguro-selos/selo04_300x60.gif') ?>" class=" center-block img-responsive" alt="">
                        <br>
                        <button type="submit" class="btn btn-success btn-block confirm-payment" data-payment-type="cartao-credito">
                            <span class="<?php icon('lock') ?>"></span> Finalizar compra
                        </button>
                    </div>
                </div>
            </div>
        </div>

    </form>
    <?php
}