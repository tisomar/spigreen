<?php

/* @var $container QPress\Container\Container */
$carrinho = $container->getCarrinhoProvider()->getCarrinho();

/**
 * Verifica se o carrinho de compras possui itens.
 */
if ($carrinho->countItems() == 0) {
    FlashMsg::add('info', 'Seu carrinho de compras está vazio. Adicione alguns produtos nele para finalizar a compra.');
    redirect('/carrinho/finalizacao');
}

/**
 * Verifica se o cliente selecionou algum endereço.
 */
if (is_null($carrinho->getEndereco())) {
    FlashMsg::add('info', 'Você deve selecionar um endereço para entrega.');
    redirect('/carrinho/finalizacao');
}

/**
 * Verifica se o cliente selecionou alguma forma de entrega.
 */
if (is_null($carrinho->getFrete())) {
    FlashMsg::add('info', 'Você deve selecionar uma forma de entrega.');
    redirect('/carrinho/finalizacao');
}

if ($container->getRequest()->getMethod() == 'POST') {
    // Dados do pagamento.
    $request_pagamento = escape_post($container->getRequest()->request->all());

    // Insere o id do pedido junto com os dados do pagamento.
    $request_pagamento['pedido_id'] = $carrinho->getId();

    // Cria um novo registro de pagamento.
    $forma_pagamento = new PedidoFormaPagamento();
    $forma_pagamento->setStatus(PedidoFormaPagamentoPeer::STATUS_PENDENTE);
    $forma_pagamento->setByArray($request_pagamento);

    // Caso seja pagamento via boleto, aplica o desconto com base na configuração de desconto total ou somente nos itens.
    if ($forma_pagamento->getFormaPagamento() == PedidoFormaPagamentoPeer::FORMA_PAGAMENTO_BOLETO) {
        $valorItens = 0.0;

        if (Config::get('boleto_desconto_tipo') == PedidoFormaPagamentoPeer::BOLETO_DESCONTO_ITENS) {
            $valorItens = $carrinho->getValorItens() - $carrinho->getValorDesconto(false);
        }

        if (Config::get('boleto_desconto_tipo') == PedidoFormaPagamentoPeer::BOLETO_DESCONTO_TOTAL) {
            $valorItens = $carrinho->getValorTotal();
        }

        $valor_desconto = ($valorItens * (Config::get('boleto.desconto_pagamento_avista') / 100));
        $forma_pagamento->setValorDesconto($valor_desconto);
    }

    // Adiciona a forma de pagamento ao Carrinho e salva.
    $carrinho->addPedidoFormaPagamento($forma_pagamento);
    $carrinho->setSituacaoClearSale(json_encode(['ip' => $_SERVER['REMOTE_ADDR'], 'session_id' => session_id()]));
    $carrinho->save();

    // -------------------------------

    /**
     * Seleciona o gateway desejado para criar a transação de pagamento
     */
    switch ($forma_pagamento->getFormaPagamento()) {
        case PedidoFormaPagamentoPeer::FORMA_PAGAMENTO_PAGSEGURO:
            $gatewayName = 'PagSeguro';
            break;

        case PedidoFormaPagamentoPeer::FORMA_PAGAMENTO_BCASH:
            $gatewayName = 'BCash';
            break;

        case PedidoFormaPagamentoPeer::FORMA_PAGAMENTO_BOLETO:
        case PedidoFormaPagamentoPeer::FORMA_PAGAMENTO_CARTAO_CREDITO:
            $gatewayName = 'SuperPayRest';
            break;
    }

    // Seleciona o Gateway
    $gateway = $container->getGatewayManager()->get($gatewayName);

    // Executa o pagamento e recebe o retorno do gateway
    $objResponse = $gateway->purchase($carrinho); /* @var $objResponse \QPress\Gateway\Response\AbstractResponse */

    // Caso contenha erros, cancela a forma de pagamento e recarrega a página de pagamento novamente.
    if (!$objResponse->isSuccessful()) {
        $carrinho->getPedidoFormaPagamento()->setStatus(PedidoFormaPagamentoPeer::STATUS_CANCELADO);
        $carrinho->getPedidoFormaPagamento()->setObservacao($objResponse->getCode() . ' - ' . $objResponse->getMessage());
        $carrinho->getPedidoFormaPagamento()->save();
        $carrinho->setPedidoFormaPagamento(null);

        FlashMsg::add('erro', 'Não foi possível finalizar seu pedido. O seguinte erro ocorreu ao tentar finalizar seu pedido com a forma de pagamento escolhida: <br />' . $objResponse->getMessage());
        redirect('/carrinho/finalizacao/retry');
    }
    // Não contém erros
    else {
        $freteDescricao = $container->getFreteManager()->getModalidade($carrinho->getFrete())->getTitulo();

        if ($objResponse->isRedirect()) {
            $carrinho->getPedidoFormaPagamento()
                ->setUrlAcesso($objResponse->getUrl())
                ->save();

            /**
             * Finaliza o carrinho de compras.
             */
            $container->getCarrinhoProvider()->finalizarCarrinho($carrinho, $freteDescricao);
            $objResponse->redirect();
            exit;
        } else {
            switch ($objResponse->getStatus()) {
                case 'aguardando_pagamento':
                    $response['message'] = 'Assim que seu pagamento for confirmado daremos sequência no processo de despacho da sua compra.';
                    $carrinho->getPedidoFormaPagamento()->setStatus(PedidoFormaPagamentoPeer::STATUS_PENDENTE);

                    break;

                case 'negada':
                    $response['message'] = 'Recebemos a informação da operadora que seu pagamento foi negado.';
                    $carrinho->getPedidoFormaPagamento()->setStatus(PedidoFormaPagamentoPeer::STATUS_NEGADO);

                    break;

                case 'falha_na_operadora':
                    $response['message'] = 'Falha na operadora';
                    $carrinho->getPedidoFormaPagamento()->setStatus(PedidoFormaPagamentoPeer::STATUS_NEGADO);

                    break;

                case 'paga':
                case 'ja_paga':
                case 'paga_nao_capturada':
                    $response['message'] = 'Seu pagamento foi confirmado com sucesso. Nossos atendentes dar&atilde;o sequ&ecirc;ncia no processo de entrega.' .
                        ' A cada mudan&ccedil;a de status voc&ecirc; receber&aacute; um e-mail informando o status do pedido.<br>' .
                        ' Agradecemos a prefer&ecirc;ncia!';

                    $carrinho->getPedidoFormaPagamento()->setStatus(PedidoFormaPagamentoPeer::STATUS_APROVADO);

                    break;

                default:
                    $response['message'] = $objResponse->getStatus();

                    break;
            }

            $carrinho->getPedidoFormaPagamento()->setUrlAcesso($objResponse->getUrl());
            $carrinho->getPedidoFormaPagamento()->setTransacaoId($objResponse->getTransactionReference());

            if ($carrinho->getPedidoFormaPagamento()->getFormaPagamento() == PedidoFormaPagamentoPeer::FORMA_PAGAMENTO_BOLETO) {
                $diasParaVencimento = Config::get('boleto.quantidade_dias_vencimento');
                $carrinho->getPedidoFormaPagamento()->setDataVencimento(date('Y-m-d', strtotime('+' . $diasParaVencimento . ' days')));
            }

            $carrinho->getPedidoFormaPagamento()->save();

            if ($carrinho->getPedidoFormaPagamento()->getStatus() == PedidoFormaPagamentoPeer::STATUS_NEGADO || $carrinho->getPedidoFormaPagamento()->getStatus() == PedidoFormaPagamentoPeer::STATUS_CANCELADO) {
                FlashMsg::add('erro', $response['message']);
                redirect('/carrinho/finalizacao/retry');
            }


            /**
             * Caso a transação não possua erro, finaliza o carrinho de compras.
             * - Alterar a classe para "Pedido"
             * - Retirar os itens comprados do estoque
             */
            $container->getCarrinhoProvider()->finalizarCarrinho($carrinho, $freteDescricao);
        }
    }
}
