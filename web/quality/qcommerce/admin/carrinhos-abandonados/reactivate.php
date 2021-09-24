<?php
$pedido_id = $container->getRequest()->query->get('pedido_id');
$objPedido = PedidoPeer::retrieveByPK($pedido_id);

if ($objPedido->isAbandonado()) {
    $objPedido->setDataAvisoAbandono(date('Y-m-d H:i:s'));
    $objPedido->save();
    \QPress\Mailing\Mailing::enviarCarrinhoAbandonado($objPedido);
    $container->getSession()->getFlashBag()->set('success', 'Solicitação enviada com sucesso para o pedido #' . $pedido_id);
} else {
    $container->getSession()->getFlashBag()->set('error', 'Este pedido não é considerado abandonado pelo sistema.');
}

redirect('/admin/carrinhos-abandonados/list');
