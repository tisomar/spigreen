<?php

use QPress\Template\Widget;

$isDisponivel = Config::get('cielo.cartao_credito');
$botao = 'Finalizar Compra';

if ($isDisponivel) {
    ?>
    <form role="form" class="form-payment form-validate form-disabled-on-load" method="post" action="#" id="form-cartao">
        <input type="hidden" name="forma_pagamento" value="<?php echo PedidoFormaPagamentoPeer::FORMA_PAGAMENTO_CIELO_CARTAO_CREDITO ?>">

        <?php renderPagamentoDivididoFields() ?>

        <div class="panel">
            <div class="panel-body">
                <?php
                Widget::render('checkout/pagamento/title-payment-type', array(
                    'paymentTypeId'     => PedidoFormaPagamentoPeer::FORMA_PAGAMENTO_CIELO_CARTAO_CREDITO,
                    'paymentTypeName'   => 'Cartão de Crédito',
                    'paymentTypeValue'  => $valorRestanteDividido,
                    'isOpenedPanel'     => $isOpenedPanel,
                ));
                ?>

                <div id="<?php echo PedidoFormaPagamentoPeer::FORMA_PAGAMENTO_CIELO_CARTAO_CREDITO ?>" class="panel-collapse collapse <?php echo ($isOpenedPanel ? 'in' : '') ?>">
                    <div class="panel-body">

                        <?php
                        Widget::render('checkout/pagamento/header-payment-type', array(
                            'valorTotal' => $valorRestanteDividido,
                        ));
                        ?>

                        <div class="row">

                            <div class="col-xs-12">
                                <div class="text-center">
                                    <div class="flags">
                                        <?php
                                        foreach (PedidoFormaPagamentoPeer::listBandeirasDisponiveis() as $i => $v) {
                                            echo sprintf('<span class="icon-%s-32"></span>', strtolower($i));
                                        }
                                        ?>
                                    </div>
                                </div>
                            </div>

                            <div class="col-xs-12">
                                <div class="form-group inner-icon">
                                    <label for="number-card">* Número do cartão</label>
                                    <input type="text" id="number-card" name="cartao[numero]" data-mask-clearifnotmatch="true" class="form-control input-credit-card" required>
                                    <span class="icon-"></span>
                                    <input type="hidden" id="flag-card" name="BANDEIRA" value="" class="card_flags">
                                </div>
                            </div>
                            <div class="col-xs-12">
                                <div class="form-group">
                                    <label for="name-card">* Nome do títular</label>
                                    <input type="text" id="name-card" name="cartao[titular]" class="form-control" placeholder="(exatamente como está no cartão)" required>
                                </div>
                            </div>
                            <div class="col-xs-6">
                                <div class="form-group">
                                    <label for="expiration-month">* Mês de Validade:</label>
                                    <?php echo get_form_select(get_months(), '', array(
                                        'id'        => 'expiration-month',
                                        'class'     => 'form-control',
                                        'name'      => 'cartao[validade_mes]',
                                        'required'  => 'required'
                                    )); ?>
                                </div>
                            </div>
                            <div class="col-xs-6">
                                <div class="form-group">
                                    <label for="expiration-year">* Ano de Validade:</label>
                                    <select class="form-control" id="expiration-year" name="cartao[validade_ano]" required>
                                        <option value="">Selecionar</option>
                                        <?php
                                        for ($i = date('Y'); $i <= (date('Y') + 10); $i++) :
                                            printf('<option value="%s">%s</option>', $i, $i);
                                        endfor;
                                        ?>
                                    </select>
                                </div>
                            </div>
                            <div class="col-xs-12">
                                <div class="form-group inner-icon">
                                    <label for="security-code">* Código de Segurança:</label>
                                    <input type="text" id="security-code-debit" class="form-control mask-security-code-card" name="cartao[codigo_seguranca]" required>
                                    <span class="icon-security-code"></span>
                                </div>
                            </div>

                            <div class="col-xs-12">
                                <div class="form-group reload-onchange">
                                    <label for="payment-options">* Opções de pagamento:</label>
                                    <?php $opcoesPagamento = getOpcoesPagamentoMultiplo($carrinho, $valorRestanteDividido); ?>
                                    <select class="form-control" id="payment-options" name="numero_parcelas" required>
                                        <option value="">Selecionar</option>
                                        <?php foreach ($opcoesPagamento as $parcela => $valor) : ?>
                                            <option value="<?php echo $parcela; ?>"><?php echo $parcela ?>x de R$ <?php echo format_money($valor) ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            </div>

                            <div class="col-xs-12">
                                <!-- <button type="submit" class="btn btn-success btn-block confirm-payment" data-payment-type="cartao-credito">
                                    <span class="<?php // icon('lock') ?>"></span> <?php // echo $botao ?>
                                </button> -->
                                <div class="text-right">
                                    <a href="javascript: void 0;" class="link-dividir-pagamento">
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

                            <div class="col-xs-12">
                                <hr>
                                <button type="submit" class="btn btn-success btn-block confirm-payment" data-payment-type="cartao-credito">
                                    <span class="<?php icon('lock') ?>"></span> <?php echo $botao ?>
                                </button>
                            </div>

                            <div class="col-xs-12">
                                <small style="color: #d35400;">
                                    Obs.: Em caso onde for selecionado a opção para dividir o valor, o valor a ser cobrado será o que você informar.
                                </small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
    <?php
}
