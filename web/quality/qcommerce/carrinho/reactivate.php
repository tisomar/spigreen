<?php
/**
 * Reativa um carrinho para a sessão novamente
 */
$hash = $router->getArgument(0);

$carrinho = PedidoQuery::create()
        ->where('md5(CONCAT(Pedido.Id, Pedido.ClienteId)) LIKE ?', $hash)
        ->filterByStatus(PedidoPeer::STATUS_ANDAMENTO)
        ->filterByClassKey(PedidoPeer::CLASS_KEY_CARRINHO)
    ->findOne();

$container->getCarrinhoProvider()->restoreCart($carrinho);
$container->getCarrinhoProvider()->getCarrinho()->checkStock();

redirect('/carrinho');
