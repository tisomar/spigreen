<?php

/**
 * Esta rotina seleciona os pedidos que possuem forma de pagamento por boleto
 * que estão vencidos e envia um e-mail ao administrador da loja.
 *
 * Esta rotina pode ser executada 1x ao dia entre às 3h. - * 0 3 * * * /path_to_script
 */

include __DIR__ . '/../includes/include_config.inc.php';

$config = array(
    // Quantidade de dias antecendentes ao vencimento que o sistema deve avisar ao cliente
    'quantidade_dias_apos_vencimento' => 3
);

$arrPedidosBoletoVencido = PedidoFormaPagamentoQuery::create()
        ->select(array('PedidoId'))
        ->joinWith('PedidoFormaPagamento.Pedido')
        ->joinWith('Pedido.Cliente')
        ->filterByStatus(PedidoFormaPagamentoPeer::STATUS_PENDENTE)
        ->filterByFormaPagamento(PedidoFormaPagamentoPeer::FORMA_PAGAMENTO_BOLETO)
        ->filterByDataVencimento(date('Y-m-d', strtotime(sprintf("-%d day", $config['quantidade_dias_apos_vencimento']))))
        ->add(1, 'qp1_pedido_forma_pagamento.ID = (SELECT MAX(p2.ID) FROM qp1_pedido_forma_pagamento p2 WHERE p2.PEDIDO_ID = qp1_pedido_forma_pagamento.PEDIDO_ID)', Criteria::CUSTOM)
        ->find()
        ->toArray();

if (count($arrPedidosBoletoVencido)) {
    $dataVencimento = date('d/m/Y', strtotime(sprintf("-%d day", $config['quantidade_dias_apos_vencimento'])));
    \QPress\Mailing\Mailing::enviarAvisoBoletoVencido($arrPedidosBoletoVencido, $dataVencimento);
}
