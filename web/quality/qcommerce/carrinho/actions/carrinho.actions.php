<?php

$view = array();
$isSimulacao = true;
$address = $frete = null;
$calculated = false;
$carrinho = $container->getCarrinhoProvider()->getCarrinho();
$carrinho->unregisterCupom();

if ($carrinho->countPedidoFormaPagamentos()) :
    $carrinho->getPedidoFormaPagamentos()->delete();
endif;

// INFORMANDO O TOTAL DE PONTOS DA COMPRA
$somaTotalPontos = 0;
foreach ($carrinho->getPedidoItems() as $item):
    $kit = in_array($item->getProdutoVariacao()->getProduto()->getId(), [2, 123]);
    if (!$kit) :
        $valorPontos = $item->getProdutoVariacao()->getProduto()->getValorPontos();
        $qtdProdutos = $item->getQuantidade();
        $somaTotalPontos += $qtdProdutos * $valorPontos;
    endif;
endforeach;

/**
 * Consulta de Frete
 */
include_once QCOMMERCE_DIR . '/carrinho/actions/cep.actions.php';

if ($container->getSession()->has('CEP_SIMULACAO')) :
    # Consulta o endereço no site dos correios para saber se é um CEP válido para consultar o frete.
    $address = \QPress\Correios\CorreiosEndereco::consultaCepViaCep($container->getSession()->get('CEP_SIMULACAO'));

    if (!is_null($address)) :
        # Cria o pacote com as informações de peso e dimensão do produto para calcular o frete
        $package = $carrinho->generatePackage($container->getSession()->get('CEP_SIMULACAO')); // CEP value from form
        # Calcula o frete disponível para este pacote
        $frete = $somaTotalPontos >= 1000 ? 0 : \QPress\Frete\Manager\FreteManager::calcularFreteCompleto($package);
        $somaTotalPontos >= 1000 ? $frete === 0 : $frete = \QPress\Frete\Manager\FreteManager::calcularFreteCompleto($package);

        $calculated = true;
    else :
        if ($container->getRequest()->getMethod() == 'POST' && $container->getRequest()->request->get('CEP')) :
            FlashMsg::add('danger', 'Não foi possível simular o frete com o CEP informado. Por favor, verifique se o mesmo foi digitado corretamente.');
        endif;
    endif;
endif;

if ($carrinho->getValorTotal() > 0 && $carrinho->getValorTotal() < Config::get('valor_minimo_boleto')) {
    FlashMsg::info('Para pagamento com boleto bancário, você deve atingir no mínimo R$ ' . format_money(Config::get('valor_minimo_boleto')) . ' em compras.');
}

if ($carrinho->countItems() && !is_null($carrinho->getCliente())) {
    $carrinho->checkMinValueForSale();
}

?>