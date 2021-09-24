<?php

/* @var $container QPress\Container\Container */

if (!isset($args[0])) {
    redirectTo(get_url_site() . '/minha-conta/pedidos');
    exit; // ------
}

$objPedido = PedidoQuery::create()
    ->filterByCliente(ClientePeer::getClienteLogado())
    ->findOneById($args[0]);

if (is_null($objPedido)) {
    redirect_404();
}
