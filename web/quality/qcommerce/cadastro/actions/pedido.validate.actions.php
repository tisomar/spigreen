<?php
/**
 * Created by PhpStorm.
 * User: Giovan
 * Date: 25/10/2018
 * Time: 18:17
 */

/**
 * Cria um registro com a forma de pagamento escolhida
 */

# Insere o id do pedido junto com os dados do pagamento.
$post['PEDIDO_ID'] = $carrinho->getId();

# Cria um novo registro de pagamento com status pendente
$objFormaPagamento = new PedidoFormaPagamento();
$objFormaPagamento->setStatus(PedidoFormaPagamentoPeer::STATUS_PENDENTE);
$objFormaPagamento->setByArray($post);

# Caso o pagamento selecionado for boleto, aplica-se o desconto com base na configuração de desconto total ou somente nos itens.
if ($objFormaPagamento->getFormaPagamento() == PedidoFormaPagamentoPeer::FORMA_PAGAMENTO_BOLETO ||
    $objFormaPagamento->getFormaPagamento() == PedidoFormaPagamentoPeer::FORMA_PAGAMENTO_PAGSEGURO_BOLETO ||
    $objFormaPagamento->getFormaPagamento() == PedidoFormaPagamentoPeer::FORMA_PAGAMENTO_ITAUSHOPLINE) {
    // --------
    $valorTotal = $carrinho->getValorTotal(false);
    $porcentagemDesconto = Config::get('boleto.desconto_pagamento_avista');
    $possuiDesconto = (bool) ($porcentagemDesconto > 0);
    if ($possuiDesconto) {
        if (Config::get('boleto_desconto_tipo') == PedidoFormaPagamentoPeer::BOLETO_DESCONTO_ITENS) {
            $valorTotal = $carrinho->getValorItens() - $carrinho->getValorDesconto(false);
        }
        # Aplica o desconto
        $valorDesconto = ($valorTotal * ($porcentagemDesconto / 100));
        $objFormaPagamento->setValorDesconto($valorDesconto);
        // -------
    }
}

/**
 * Seleciona o gateway desejado para criar a transação de pagamento
 */
$aditionalParameters = array();

$cartAdress = true;

switch ($objFormaPagamento->getFormaPagamento()) {
    case PedidoFormaPagamentoPeer::FORMA_PAGAMENTO_PAGSEGURO_BOLETO:
    case PedidoFormaPagamentoPeer::FORMA_PAGAMENTO_PAGSEGURO_DEBITO_ONLINE:
    case PedidoFormaPagamentoPeer::FORMA_PAGAMENTO_PAGSEGURO_:
        $gatewayName = 'PagSeguroTransparente';
        $aditionalParameters = $container->getRequest()->request->get('pagseguro', array(), true);
        break;

    case PedidoFormaPagamentoPeer::FORMA_PAGAMENTO_PAGSEGURO:
        $gatewayName = 'PagSeguro';
        break;

    case PedidoFormaPagamentoPeer::FORMA_PAGAMENTO_BOLETO:
        $gatewayName = 'BoletoPHP';
        break;

    case PedidoFormaPagamentoPeer::FORMA_PAGAMENTO_:
        if (!$carrinho->getEndereco()) {
            $cartAdress = false;
            $enderecoCliente = $carrinho->getCliente()->getEnderecoPrincipal();
            if ($enderecoCliente instanceof Endereco) {
                $carrinho->setEndereco($enderecoCliente)->setCidadeId($enderecoCliente->getCidadeId())->save();
                $cartAdress = true;
            }
        }

        $gatewayName = 'SuperPayRest';
        break;

    case PedidoFormaPagamentoPeer::FORMA_PAGAMENTO_PAYPAL:
        $gatewayName = 'PayPal';
        break;

    case PedidoFormaPagamentoPeer::FORMA_PAGAMENTO_ITAUSHOPLINE:
        $gatewayName = 'ItauShopline';
        break;

    case PedidoFormaPagamentoPeer::FORMA_PAGAMENTO_FATURAMENTO_DIRETO:
    case PedidoFormaPagamentoPeer::FORMA_PAGAMENTO_PONTOS:
        $gatewayName = null;
        break;

    default:
        $gatewayName = false;
        break;
}

if ($cartAdress == false) {
    FlashMsg::add('danger', 'Pagamento com cartão sem endereço selecionado não é permitido. <br>Favor cadastre um endereço na central do cliente.');
    redirect('/checkout/pagamento');
}

if ($gatewayName === false) {
    FlashMsg::add('danger', 'A forma de pagamento selecionada não está disponível no momento.');
    redirect('/checkout/pagamento');
}

# Adiciona a forma de pagamento ao Carrinho e salva.
$carrinho->addPedidoFormaPagamento($objFormaPagamento);
$carrinho->save();

# Seleciona o Gateway
if (!is_null($gatewayName)) {
    $gateway = $container->getGatewayManager()->get($gatewayName);
    $gateway->initialize($aditionalParameters);

    # Executa o pagamento e recebe o retorno do gateway
    /* @var $objResponse \QPress\Gateway\Response\AbstractResponse */
    $objResponse = $gateway->purchase($carrinho);

    # Caso houver algum erro, o sistema cancela a forma de pagamento e recarrega a página de pagamento novamente
    # para o cliente selecionar outra forma de pagamento.
    if (!$objResponse->isSuccessful()) {
        $carrinho->getPedidoFormaPagamento()->setStatus(PedidoFormaPagamentoPeer::STATUS_CANCELADO);
        $carrinho->getPedidoFormaPagamento()->setObservacao($objResponse->getCode() . ' - ' . $objResponse->getMessage());
        $carrinho->getPedidoFormaPagamento()->save();
        FlashMsg::add('danger', 'Não foi possível finalizar seu pedido. Tente outra forma de pagamento ou entre em contato conosco.');
        FlashMsg::add('danger', $objResponse->getCode() . ' - ' . $objResponse->getMessage());
        redirect('/checkout/pagamento/retry');
    }

    # Salva a url de acesso e a referencia da transação
    $carrinho
        ->getPedidoFormaPagamento()
        ->setUrlAcesso($objResponse->getUrl())
        ->setTransacaoId($objResponse->getTransactionReference())
        ->save();


    # Verifica se a forma de pagamento é fora da loja e redireciona o cliente.
    if ($objResponse->isRedirect()) {
        # Salva a url de redirecionamento
        $carrinho
            ->getPedidoFormaPagamento()
            ->setUrlAcesso($objResponse->getUrl())
            ->save();

        # Finaliza o carrinho de compras.
        $container->getCarrinhoProvider()->finalizarCarrinho($carrinho);

        # Redireciona o cliente.
        $objResponse->redirect();
        exit; // ------------------
    }

    # Caso não necessite redirecionar, verifica o status do pagamento e conclui a compra.
    switch ($objResponse->getStatus()) {
        case PedidoFormaPagamentoPeer::STATUS_PENDENTE:
            $response['message'] = 'Assim que seu pagamento for confirmado daremos sequência no processo de despacho da sua compra.';
            $carrinho->getPedidoFormaPagamento()->setStatus(PedidoFormaPagamentoPeer::STATUS_PENDENTE);

            break;

        case PedidoFormaPagamentoPeer::STATUS_NEGADO:
            $response['message'] = 'Recebemos a informação da operadora que seu pagamento foi negado.';
            $carrinho->getPedidoFormaPagamento()->setStatus(PedidoFormaPagamentoPeer::STATUS_NEGADO);

            break;

        case PedidoFormaPagamentoPeer::STATUS_CANCELADO:
            $response['message'] = 'Falha ao tentar se comunicar com a operadora';
            $carrinho->getPedidoFormaPagamento()->setStatus(PedidoFormaPagamentoPeer::STATUS_CANCELADO);

            break;

        case PedidoFormaPagamentoPeer::STATUS_APROVADO:
            $response['message'] = 'Seu pagamento foi confirmado com sucesso. Nossos atendentes dar&atilde;o sequ&ecirc;ncia no processo de entrega.' .
                ' A cada mudan&ccedil;a de status voc&ecirc; receber&aacute; um e-mail informando o status do pedido.<br>' .
                ' Agradecemos a prefer&ecirc;ncia!';

            $carrinho->getPedidoFormaPagamento()->setStatus(PedidoFormaPagamentoPeer::STATUS_APROVADO);

            break;

        default:
            $response['message'] = $objResponse->getStatus();

            break;
    }

    # Se a forma de pagamento for boleto, calcula a data de vencimento.
    if ($carrinho->getPedidoFormaPagamento()->getFormaPagamento() == PedidoFormaPagamentoPeer::FORMA_PAGAMENTO_BOLETO) {
        $diasParaVencimento = Config::get('boleto.quantidade_dias_vencimento');
        $carrinho->getPedidoFormaPagamento()->setDataVencimento(date('Y-m-d', strtotime('+' . $diasParaVencimento . ' days')));
    }

    $carrinho->getPedidoFormaPagamento()->save();

    # Caso o status seja cancelado ou negado, redireciona o cliente para tentar um repagamento com outra forma.
    $isSuccess = !in_array($carrinho->getPedidoFormaPagamento()->getStatus(), array(PedidoFormaPagamentoPeer::STATUS_NEGADO, PedidoFormaPagamentoPeer::STATUS_CANCELADO));
    if ($isSuccess == false) {
        FlashMsg::add('danger', $response['message']);
    } else {
        # Conclui a compra.
        $con = Propel::getConnection();
        $con->beginTransaction();

        $container->getCarrinhoProvider()->finalizarCarrinho($carrinho);

        $con->commit();

        $container->getSession()->remove('CEP_SIMULACAO');
    }
}
