<?php
$clienteLogado = ClientePeer::getClienteLogado(true);
$planoCliente =  $clienteLogado ? $clienteLogado->getPlano() : null;

if ($clienteLogado && $planoCliente && $objProdutoVariacao->getProduto()->getAplicaDescontoPlano()) :
    list($valor) = $objProdutoVariacao->getValorFidelidade();
else :
    $valor = $objProdutoVariacao->getValor();
endif;

if (ClientePeer::isAuthenticad() || Config::get('clientes.ocultar_preco') == 0) :
    ?>
    <?php if ($valor <  $objProdutoVariacao->getValorBase()) : ?>
        <s>De R$ <?= format_money($objProdutoVariacao->getValorBase()) ?></s>
    <?php else : ?>
        Por apenas
    <?php endif; ?>

    <div class="price text-success">
        <span>R$</span>
        <?= format_money($valor); ?>
    </div>

    <span class="payment-installment"><?= get_descricao_valor_parcelado($valor, $objProdutoVariacao->getProduto()->getParcelamentoIndividual()) ?></span>
    <?php
endif;
