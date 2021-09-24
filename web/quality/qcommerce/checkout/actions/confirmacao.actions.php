<?php
/* @var $objPedido Pedido */
$objPedido = PedidoQuery::create()
    ->where('md5(Pedido.Id) LIKE ?', $router->getArgument(0))
    ->filterByClienteId(ClientePeer::getClienteLogado()->getId())
    ->findOne();

if (!$objPedido instanceof Pedido) {
    redirect_404();
}

if (Config::get('sistema.versao_demo')) {
    FlashMsg::info('Simulação de pedido realizado com sucesso!');
}
