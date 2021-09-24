<?php

/**
 * Valida se tem franqueado na sessão
 */
$franqueado = ClientePeer::getFranqueadoSelecionado($container);


/**
 *
 * valida se o cadastro quer virar revendedor.
 *
 */
$isNewReseller =
    $container->getSession()->has('resellerLoggedActive') ||
    $container->getSession()->has('resellerActive')
        ? true : false;

# Associa o cliente autenticado ao carrinho.
$carrinho->setClienteId(ClientePeer::getClienteLogado()->getId())->save();

$carrinho->checkQuantityPerItems();
#$carrinho->checkPlanForCustomer();

# Verifica se o carrinho de compras possui itens.
if ($carrinho->countItems() == 0) {
    FlashMsg::add('info', 'Seu carrinho de compras está vazio. Adicione alguns produtos nele para finalizar a sua compra.');
    redirect('/carrinho');
}

if (!$carrinho->checkStock()) {
    redirect('/carrinho');
}

if (!$carrinho->checkMinValueForSale()) {
    redirect('/carrinho');
}

if (!$carrinho->isPagamentoMensalidade() && !$carrinho->isPagamentoTaxaCadastro()) {
    // Verifica se o carrinho de compras possui itens.
    // Solicitação do cliente para não obrigar mais a comprar combo primeiramente
    //if ($carrinho->checkTypeClientAndCombo() > 0) {
    //    FlashMsg::add('info', 'Você precisa comprar um Combo para sua ativação.<br>O produto foi removido automaticamente do carrinho.');
    //    redirect('/carrinho');
    //}
}

switch ($step) {
    # Se o passo for pagamento, é necessário verificar se o cliente selecionou um endereço e um frete.
    case 'pagamento':
        if ($franqueado) {
            # Verifica se o cliente selecionou alguma forma de entrega.
            if (is_null($carrinho->getFrete())) {
                FlashMsg::add('info', 'Você precisa selecionar uma forma de entrega.');
                redirect('/checkout/frete');
            }
        } elseif (!$carrinho->isPagamentoMensalidade() && !$carrinho->isPagamentoTaxaCadastro()) {
            # Verifica se o cliente selecionou algum endereço.
            if (is_null($carrinho->getEndereco())) {
                FlashMsg::add('info', 'Você precisa selecionar um endereço para entrega.');
                redirect('/checkout/endereco');
            }

            # Verifica se o cliente selecionou alguma forma de entrega.
            if (is_null($carrinho->getFrete())) {
                FlashMsg::add('info', 'Você precisa selecionar uma forma de entrega.');
                redirect('/checkout/frete');
            }
        } // else: pagamentos de mensalidade não precisam de endereco de entrega e nem de frete.

        if ($container->getRequest()->getMethod() == 'POST') {
            if (!isset($request_pagamento['forma_pagamento'])) {
                FlashMsg::add('danger', 'Você precisa selecionar uma forma de pagamento.');
                redirect('/checkout/pagamento');
            }

            switch ($request_pagamento['forma_pagamento']) {

                /**
                 * Para pagamento via Faturamento Direto, as seguintes regras devem ser satisfeitas:
                 *  1. Verificar se o cliente selecionou alguma opção;
                 *  2. Verificar se a opção selecionada realmente existe;
                 *  3. Verificar se a opção selecionada não é padrão e se está liberada para o cliente;
                 */
                case PedidoFormaPagamentoPeer::FORMA_PAGAMENTO_FATURAMENTO_DIRETO:
                    if (!isset($request_pagamento['faturamento_direto_opcao']) || $request_pagamento['faturamento_direto_opcao'] == '') {
                        FlashMsg::add('danger', 'Você precisa selecionar uma forma de pagamento.');
                        redirect('/checkout/pagamento');
                    } else {
                        $opcao = FaturamentoDiretoQuery::create()->filterByNome($request_pagamento['faturamento_direto_opcao'])->findOne();
                        if (!$opcao instanceof FaturamentoDireto) {
                            FlashMsg::add('danger', 'Você precisa selecionar uma forma de pagamento.');
                            redirect('/checkout/pagamento');
                        } else {
                            if ($opcao->getPadrao() == false) {
                                $isAvailable = (bool)FaturamentoDiretoClienteQuery::create()
                                        ->filterByClienteId($carrinho->getClienteId())
                                        ->filterByFaturamentoDiretoId($opcao->getId())
                                        ->count() > 0;

                                if (!$isAvailable) {
                                    FlashMsg::add('danger', 'Você precisa selecionar uma forma de pagamento.');
                                    redirect('/checkout/pagamento');
                                }
                            }
                        }
                    }
                    break;
            }
        }

        break;

    # Se o passo for pagamento, é necessário verificar se o cliente selecionou um endereço.
    case 'frete':
        $carrinho->unregisterCupom();

        if ($carrinho->countPedidoFormaPagamentos()) {
            $carrinho->getPedidoFormaPagamentos()->delete();
        }

        # Verifica se o cliente selecionou algum endereço.
        if (is_null($carrinho->getEndereco())) {
            FlashMsg::add('info', 'Você precisa selecionar um endereço para entrega.');
            redirect('/checkout/endereco');
        }

        break;

    default:
        $carrinho->unregisterCupom();

        if ($carrinho->countPedidoFormaPagamentos()) {
            $carrinho->getPedidoFormaPagamentos()->delete();
        }

        break;
}
