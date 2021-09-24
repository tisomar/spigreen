<?php

/* @var $container \QPress\Container\Container */
/* @var $gateway QPress\Gateway\Services\PagSeguro\PagSeguro */

$transaction_code = $container->getRequest()->query->get('transaction_id');

try {
    $gateway = $container->getGatewayManager()->get('PagSeguro');
    $responseConsult = $gateway->consult($transaction_code);
    
    $pedido = PedidoQuery::create()->findOneById($responseConsult['pedido_id']);

    $lastPayment = $pedido->getPedidoFormaPagamento();
    $lastPayment->setTransacaoId($transaction_code);

    switch ($responseConsult['status']) {
        case "PAID":
            $lastPayment->setStatus(PedidoFormaPagamentoPeer::STATUS_APROVADO);
            $response['message'] =
                "Recebemos a confirmação de seu pagamento. Nossa equipe 
                dar� sequencia no processo de despacho de seu pedido.";
            break;

        case "CANCELLED":
            $lastPayment->setStatus(PedidoFormaPagamentoPeer::STATUS_CANCELADO);
            $response['message'] = "Recebemos a informação de que seu pagamento não p�de ser feito.";

            break;

        default:
            $response['message'] =
                'Assim que seu pagamento for confirmado daremos sequ�ncia no processo de despacho da sua compra.';

            break;
    }

    $lastPayment->save();

    redirect('/checkout/confirmacao/' . md5($pedido->getId()));
} catch (PagSeguroServiceException $e) {
    // TODO: throw exception
}
