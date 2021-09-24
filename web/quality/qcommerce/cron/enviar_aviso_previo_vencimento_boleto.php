<?php

/**
 * Esta rotina seleciona os pedidos que possuem forma de pagamento por boleto
 * que ainda estão pendentes e envia um e-mail ao cliente informando sobre o
 * vencimento.
 *
 * Esta rotina pode ser executada 1x ao dia entre às 3h. - * 0 3 * * * /path_to_script
 */
/* @var $objPedidoFormaPagamento PedidoFormaPagamento */

include __DIR__ . '/../includes/include_config.inc.php';

$config = array(
    // Quantidade de dias antecendentes ao vencimento que o sistema deve avisar ao cliente
    'quantidade_dias_antecendente' => 1
);

$coll = PedidoFormaPagamentoQuery::create()
        ->joinWith('PedidoFormaPagamento.Pedido')
        ->joinWith('Pedido.Cliente')
        ->filterByStatus(PedidoFormaPagamentoPeer::STATUS_PENDENTE)
        ->filterByFormaPagamento(PedidoFormaPagamentoPeer::FORMA_PAGAMENTO_BOLETO)
        ->filterByDataVencimento(date('Y-m-d', strtotime(sprintf("+%d day", $config['quantidade_dias_antecendente']))))
        ->add(1, 'qp1_pedido_forma_pagamento.ID = (SELECT MAX(p2.ID) FROM qp1_pedido_forma_pagamento p2 WHERE p2.PEDIDO_ID = qp1_pedido_forma_pagamento.PEDIDO_ID)', Criteria::CUSTOM)
        ->find();

foreach ($coll as $objPedidoFormaPagamento) {
    \QPress\Mailing\Mailing::enviarAvisoVencimentoBoleto($objPedidoFormaPagamento);
}
