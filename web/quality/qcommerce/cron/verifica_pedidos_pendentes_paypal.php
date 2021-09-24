<?php

/**
 * Esta rotina seleciona os pagamentos do Paypal que possuam um id de transação (ou seja, foram finalizados) mas que ainda estão com o status pendente
 * e consulta o status da transação no Paypal. Conforme o resultado retornado pelo Paypal o status do pagamento é atualizado.
 *
 */

include __DIR__ . '/../includes/include_config.inc.php';

set_time_limit(0);
ini_set('memory_limit', -1);

$gateway = $container->getGatewayManager()->get('PayPal');

//Vamos limitar a consulta em 3 meses para evitar selecionar pagamentos muito antigos.
//Se um pagamento tão antigo ainda está pendente provavelmente está com algum problema, é invalido ou o estado retornado é desconhecido por este script. Vamos ignorar estes pagamentos.
$dataLimite = new DateTime();
$dataLimite->modify('-3 months');

$pagamentos = PedidoFormaPagamentoQuery::create()
                    ->filterByFormaPagamento(PedidoFormaPagamentoPeer::FORMA_PAGAMENTO_PAYPAL)
                    ->filterByTransacaoId(null, Criteria::NOT_EQUAL)
                    ->filterByStatus(PedidoFormaPagamentoPeer::STATUS_PENDENTE)
                    ->filterByCreatedAt($dataLimite, Criteria::GREATER_THAN)
                    ->find();

foreach ($pagamentos as $pagamento) {
    $transactionId = $pagamento->getTransacaoId();
            
    $responseNvp = $gateway->consultTransaction($transactionId);
        
    if (!isset($responseNvp['ACK']) || strtolower($responseNvp['ACK']) !== 'success' || !isset($responseNvp['PAYMENTSTATUS'])) {
        continue;
    }
            
    if (strtolower($responseNvp['PAYMENTSTATUS']) === 'completed' &&
        (!isset($responseNvp['PENDINGREASON']) || strtolower($responseNvp['PENDINGREASON']) === 'none')) {
        //pagamento aprovado
        $pagamento->setStatus(PedidoFormaPagamentoPeer::STATUS_APROVADO);
        $pagamento->save();
    } elseif (strtolower($responseNvp['PAYMENTSTATUS']) === 'denied') {
        //negado
        $pagamento->setStatus(PedidoFormaPagamentoPeer::STATUS_NEGADO);
        $pagamento->save();
    }
}
