<?php

/*
* Faz login na central do cliente.
 */

$objCliente = ClienteQuery::create()->findOneById($request->query->get('id'));
if (!$objCliente) {
    redirect_404admin();
}

ClientePeer::setClienteLogado($objCliente);

$clienteLogado = ClientePeer::getClienteLogado(true);
$planoCliente =  $clienteLogado ? $clienteLogado->getPlano() : null;

if($planoCliente ) {
    redirect('/minha-conta/plano-carreira');
}else{
    redirect('/minha-conta/pedidos');
}


