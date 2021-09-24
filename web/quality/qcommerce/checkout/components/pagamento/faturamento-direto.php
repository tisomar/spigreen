<?php
use QPress\Template\Widget;

$opcoes = FaturamentoDiretoQuery::create()
    ->orderByNome()
    ->filterByPadrao(true)
    ->find();

$opcoesExclusivas = array();
if (ClientePeer::getClienteLogado()) {
    $opcoesExclusivas = FaturamentoDiretoQuery::create()
        ->orderByNome()
        ->filterByPadrao(false)
        ->useFaturamentoDiretoClienteQuery()
        ->filterByClienteId(ClientePeer::getClienteLogado()->getId())
        ->endUse()
        ->find();
}

$isDisponivel = count($opcoes) + count($opcoesExclusivas) > 0;
$botao = 'Finalizar Compra';
if ($isDisponivel) {
    ?>
    <form role="form" class="form-payment form-validate form-disabled-on-load" method="post" action="#"  id="">
        <input type="hidden" name="forma_pagamento" value="<?php echo PedidoFormaPagamentoPeer::FORMA_PAGAMENTO_FATURAMENTO_DIRETO ?>">

        <div class="panel">
            <div class="panel-body">
                <?php
                Widget::render('checkout/pagamento/title-payment-type', array(
                    'paymentTypeId'     => PedidoFormaPagamentoPeer::FORMA_PAGAMENTO_FATURAMENTO_DIRETO,
                    'paymentTypeName'   => 'Faturamento Direto',
                    'paymentTypeValue'  => $carrinho->getValorTotal(),
                    'isOpenedPanel'     => $isOpenedPanel,
                ));
                ?>

                <div id="<?php echo PedidoFormaPagamentoPeer::FORMA_PAGAMENTO_FATURAMENTO_DIRETO ?>" class="panel-collapse collapse <?php echo ($isOpenedPanel ? 'in' : '') ?>">
                    <div class="panel-body">

                        <?php
                        Widget::render('checkout/pagamento/header-payment-type', array(
                            'valorTotal'            => $carrinho->getValorTotal(),
                        ));
                        ?>

                        <p><?php echo Config::get('faturamento_direto.mensagem_tela_pagamento') ?></p>
                        <br>

                        <?php $valorItens = $carrinho->getValorTotal() - $carrinho->getValorEntrega() ?>
                        <ul class="list-unstyled">
                            <?php foreach ($opcoes as $opcao) { /* @var $opcao FaturamentoDireto */ ?>
                                <li>
                                    <?php if ($valorItens > $opcao->getValorMinimoCompra()) : ?>
                                        <label><input type="radio" required="" name="faturamento_direto_opcao" value="<?php echo $opcao->getNome() ?>" /> <?php echo $opcao->getNome() ?></label>
                                    <?php else : ?>
                                        <label><input type="radio" disabled="" name="" /> <?php echo $opcao->getNome() ?></label>
                                        <br>
                                        <span class="text-muted small">disponivel somente para compra acima de R$ <?php echo format_money($opcao->getValorMinimoCompra()) ?></span>
                                    <?php endif; ?>
                                    <hr>
                                </li>
                            <?php } ?>
                            <?php foreach ($opcoesExclusivas as $opcao) { /* @var $opcao FaturamentoDireto */ ?>
                                <li>
                                    <?php if ($valorItens > $opcao->getValorMinimoCompra()) : ?>
                                        <label><input type="radio" required="" name="faturamento_direto_opcao" value="<?php echo $opcao->getNome() ?>" /> <?php echo $opcao->getNome() ?></label>
                                    <?php else : ?>
                                        <label><input type="radio" disabled="" name="" /> <?php echo $opcao->getNome() ?></label>
                                        <br>
                                        <span class="text-muted small">disponivel somente para compra acima de R$ <?php echo format_money($opcao->getValorMinimoCompra()) ?></span>
                                    <?php endif; ?>
                                    <hr>
                                </li>
                            <?php } ?>
                        </ul>
                        <button type="submit" class="btn btn-success btn-block confirm-payment" data-payment-type="boleto">
                            <span class="<?php icon('lock') ?>"></span> <?php echo $botao ?>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </form>
    <?php
}
