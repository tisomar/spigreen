<?php

$carrinho = $container->getCarrinhoProvider()->getCarrinho();

$step = 'endereco';
include __DIR__ . '/validate.step.actions.php';

if ($request->getMethod() == 'POST') {
    # Verifica se o cliente selecionou um enedereço para entrega e avança para a tela
    # onde é escolhido o tipo de frete.
    if ($request->request->has('endereco_id')) {
        $endereco = EnderecoQuery::create()
            ->filterByCliente(ClientePeer::getClienteLogado())
            ->filterById($request->request->get('endereco_id'))
            ->findOne();

        if ($endereco instanceof Endereco) {
            $carrinho->setEndereco($endereco)->setCidadeId($endereco->getCidadeId())->save();
            $carrinho->resetFrete();

            redirectTo('/checkout/frete');
        }
    }
}
