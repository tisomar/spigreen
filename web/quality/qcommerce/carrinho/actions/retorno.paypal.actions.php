<?php

$token = $container->getRequest()->query->get('token');

if ($token) {
    $gateway = $container->getGatewayManager()->get('PayPal');
        
    $responseNvp = $gateway->consultCheckout($token);
    
    if (!empty($responseNvp['INVNUM']) &&
        isset($responseNvp['TOKEN']) && $responseNvp['TOKEN'] === $token &&
        isset($responseNvp['ACK']) && strtolower($responseNvp['ACK']) === 'success' &&
        isset($responseNvp['PAYERID'])) {
        $pedido = PedidoQuery::create()->findPk($responseNvp['INVNUM']);
        if (!$pedido) {
            redirect_404();
            exit;
        }
                
        $lastPayment = $pedido->getPedidoFormaPagamento();
        if (!$lastPayment) {
            redirect_404();
            exit;
        }
        
        //Tudo ok, vamos confirmar o pagamento
        $responseNvp = $gateway->checkoutPayment($responseNvp);
                
        //Verifica se a chamada ocorreu com sucesso
        if (isset($responseNvp['ACK']) && strtolower($responseNvp['ACK']) === 'success' &&
            isset($responseNvp['PAYMENTINFO_0_TRANSACTIONID']) &&
            isset($responseNvp['PAYMENTINFO_0_PAYMENTSTATUS'])) {
            //salva o id da transação
             $lastPayment->setTransacaoId($responseNvp['PAYMENTINFO_0_TRANSACTIONID']);
             
             //Observacao: a partir deste ponto podemos consultar a transacao usando o transactionid:
             // $gateway->consultTransaction($transactionId);
            
            $isPendente = false;
             
             //Verifica se o pagamento foi efetuado com sucesso.
            if (strtolower($responseNvp['PAYMENTINFO_0_PAYMENTSTATUS']) === 'completed' &&
                (!isset($responseNvp['PAYMENTINFO_0_PENDINGREASON']) || strtolower($responseNvp['PAYMENTINFO_0_PENDINGREASON']) === 'none')) {
                //Sucesso no pagamento
                $lastPayment->setStatus(PedidoFormaPagamentoPeer::STATUS_APROVADO);
            } else {
                //Pagamento pendente
                $lastPayment->setStatus(PedidoFormaPagamentoPeer::STATUS_PENDENTE);
                
                $isPendente = true;
            }
            
            $lastPayment->save();
            
            if ($isPendente) {
                FlashMsg::add('warning', 'Seu pagamento encontra-se pendente no Paypal. Logo após a confirmação do pagamento nossa equipe dará sequencia no processo de despacho de seu pedido.');
            }
            
            redirect('/checkout/confirmacao/' . md5($pedido->getId()));
            exit;
        }
    }
}

FlashMsg::add('danger', 'Falha no processamento do pagamento.');
redirect('/minha-conta/pedidos');
exit;
