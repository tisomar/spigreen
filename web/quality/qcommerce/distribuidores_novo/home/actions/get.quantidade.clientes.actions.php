<?php
$quantidade = ClienteDistribuidorQuery::create()
    ->filterByClienteRedefacilId(null)
    ->filterByCliente(ClientePeer::getClienteLogado())->count();

echo json_encode(array('quantidade' => $quantidade));
