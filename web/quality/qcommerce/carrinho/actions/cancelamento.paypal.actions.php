<?php

$token = $container->getRequest()->query->get('token');

if ($token) {
    $gateway = $container->getGatewayManager()->get('PayPal');
    
    //vamos consultar a transacao para localizar o id do pedido.
    
    $responseNvp = $gateway->consultCheckout($token);
       
    if (!empty($responseNvp['INVNUM'])) {
        $pedido = PedidoQuery::create()->findPk($responseNvp['INVNUM']);
                
        if ($pedido) {
            $lastPayment = $pedido->getPedidoFormaPagamento();
            if ($lastPayment) {
                $lastPayment->setStatus(PedidoFormaPagamentoPeer::STATUS_CANCELADO);
                $lastPayment->save();
                
                FlashMsg::add('info', 'Seu pagamento foi cancelado.');
                redirect('/minha-conta/pedidos');
                exit;
            } else {
                redirect_404();
                exit;
            }
        } else {
            redirect_404();
            exit;
        }
    }
}

FlashMsg::add('danger', 'Falha na comunicação com o servidor do PayPal.');
redirect('/minha-conta/pedidos');
exit;
