<?php

$cliente = ClientePeer::retrieveByPK($_GET['cliente_id']);
$clienteLogadoId = ClientePeer::getClienteLogado()->getId();
if (!$cliente instanceof Cliente) :
    exit_403();
endif;

$firstDay = date('Y-m') . '-01 00:00:00';
$lastDay = date('Y-m-d') . ' 23:59:59';
$pedidos = PedidoQuery::create()
                ->filterByClienteId($cliente->getId())
                ->filterByStatus('CANCELADO', Criteria::NOT_EQUAL)
                ->having('STATUS_PGTO > 1 AND STATUS_PGTO < 6')

                ->withColumn('(
                        SELECT 
                            PEDIDO_STATUS_ID 
                        FROM qp1_pedido_status_historico
                        WHERE 
                          PEDIDO_ID = ID
                          AND UPDATED_AT between "' . $firstDay . '" and "' . $lastDay . '"
                        ORDER BY 
                          PEDIDO_STATUS_ID DESC
                        LIMIT 1)', 'STATUS_PGTO')
                ->filterByClassKey(1)
                ->find();
