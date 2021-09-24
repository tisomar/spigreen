<?php
/* @var $container \QPress\Container\Container */

if ($container->getRequest()->query->has('id')) {
    $objPedidoItem = PedidoItemQuery::create()->findPk($container->getRequest()->query->get('id'));
    if ($objPedidoItem) {
        //Verfica se o item foi adicionado ao carrinho por ser um produto inicial de kit de adesão (esses itens não podem ser removidos diretamente).
        if ($objPedidoItem->getPlanoId()) {
            FlashMsg::warning('Não é permitido remover o produto inicial de um kit de adesão.');

            redirect('/carrinho');
            exit;
        }
        
        $objPlanoId = null;
        //verifica se o item que estamos removendo é um kit de adesão.
        if ($objPedidoItem->getProdutoVariacao()->getProduto()->isKitAdesao()) {
            $objPlanoId = $objPedidoItem->getProdutoVariacao()->getProduto()->getPlanoId();
        }
        
        
        $container->getCarrinhoProvider()->getCarrinho()->removeItem($objPedidoItem->getId());
        
        //se foi removido um kit de adesão com produto inicial, também temos que remover o produto inicial do carrinho.
        if ($objPlanoId) {
            $itensProdutoInicial = PedidoItemQuery::create()
                                        ->filterByPedido($container->getCarrinhoProvider()->getCarrinho())
                                        ->filterByPlanoId($objPlanoId)
                                        ->find();
            foreach ($itensProdutoInicial as $itemProdutoInicial) {
                $container->getCarrinhoProvider()->getCarrinho()->removeItem($itemProdutoInicial->getId());
            }
        }
        
        $container->getCarrinhoProvider()->getCarrinho()->resetFrete();
        FlashMsg::success("O produto foi removido do carrinho.");
    }
}

redirect('/carrinho');
exit;
