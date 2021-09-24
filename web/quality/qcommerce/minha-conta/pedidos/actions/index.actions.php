<?php
//$collPedidos = PedidoQuery::create()
//    ->filterByClassKey(PedidoPeer::CLASSKEY_PEDIDO)
//    ->filterByCliente(ClientePeer::getClienteLogado())
//    ->orderByCreatedAt(Criteria::DESC)
//    ->paginate($container->getRequest()->query->get('page', 1));


$collPedidos = PedidoQuery::create()
    ->filterByClassKey(PedidoPeer::CLASSKEY_PEDIDO)
    ->filterByCliente(ClientePeer::getClienteLogado())
    ->orderByCreatedAt(Criteria::DESC);
//->paginate($container->getRequest()->query->get('page', 1));


$page = (int)$router->getArgument(0);

if ($page < 1) :
    $page = 1;
endif;

$collPedidos = $collPedidos->paginate($page, 10);

$queryString = '';

if ($qs = $request->getQueryString()) :
    $queryString = '?' . $qs;
endif;