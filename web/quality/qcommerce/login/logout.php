<?php
if (ClientePeer::isAuthenticad()) {
    $cliente = ClientePeer::getClienteLogado();
    $cartActive = $container->getCarrinhoProvider()->getCarrinho();

    if ($cliente->getTaxaCadastro()) {
        $objPedidoItem = PedidoItemQuery::create()
                                ->filterByPedidoId($cartActive->getId())
                                ->filterByProdutoVariacaoId(ProdutoPeer::PRODUTO_TAXA_ID)
                                ->findOne();
        if ($objPedidoItem instanceof PedidoItem) {
            $container->getCarrinhoProvider()->getCarrinho()->removeItem($objPedidoItem->getId());
        }
    }

    foreach ($cartActive->getPedidoItems() as $pedidoItem) {
        $container->getCarrinhoProvider()->getCarrinho()->removeItem($pedidoItem->getId());
    }

    ClientePeer::doLogout();

    $container->getCarrinhoProvider()
        ->getCarrinho()
        ->updatePedidoItemsByTabelaPrecoId(null);

    $container->getSession()->remove('fromFranqueado');
    $container->getSession()->remove('slugFranqueado');
    $container->getSession()->remove('PATROCINADOR_HOTSITE_ID');
    $container->getSession()->remove('resellerLoggedActive');
    $container->getSession()->remove('resellerActive');

    //session_destroy();
    redirectTo($root_path . '/home/');
    exit();
} else {
    redirectTo($root_path . '/login');
    exit();
}
