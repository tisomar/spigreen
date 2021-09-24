<?php
/* @var $container \QPress\Container\Container */

foreach ($container->getRequest()->request->get('item') as $item_id => $quantidade) {
    $objPedidoItem = PedidoItemQuery::create()->findOneById($item_id);

    if (!empty($objPedidoItem->getPedido()->getLastPedidoStatus())) :
        FlashMsg::add('danger', 'Este pedido já foi concluído. Por favor inicie outro.');

        exit();
    endif;

    // Não é permitido adicionar mais de um produto combo no mesmo carrinho
    // Verifica apenas o produo com id 123 porque é o único combo ativo no momento
    if ($objPedidoItem->getProdutoVariacao()->getProduto()->isKitAdesao() && $quantidade > 1) {
        FlashMsg::add('danger', 'Não é permitido adicionar mais de um plano ao carrinho.');

        exit();
    }

    if ($objPedidoItem->getProdutoVariacao()->getSomaTotalEstoque() > 0) {
        if ($objPedidoItem->getProdutoVariacao()->getProduto()->isProdutoSimples()) {
            //echo $objPedidoItem->getProdutoVariacao()->getSomaTotalEstoque();
            if ($quantidade > $objPedidoItem->getProdutoVariacao()->getSomaTotalEstoque()) {
                FlashMsg::info(sprintf(
                    'O produto <b>%s</b> possui apenas <b>%s</b> no estoque.',
                    $objPedidoItem->getProdutoVariacao()->getProduto()->getNome(),
                    plural($objPedidoItem->getProdutoVariacao()->getSomaTotalEstoque(), '%s item', '%s itens')
                ));
                $quantidade = $objPedidoItem->getProdutoVariacao()->getSomaTotalEstoque();
            }

            $objPedidoItem->setQuantidade($quantidade);
            $objPedidoItem->save();
        } else {
            $estoqueComposto = ProdutoVariacaoPeer::checkEstoqueProdutoComposto($objPedidoItem->getProdutoVariacao(), $quantidade);

            if (is_numeric($estoqueComposto)
                && $estoqueComposto !== false
            ) {
                $quantidade = (int)$estoqueComposto;
            } elseif ($estoqueComposto === false) {
                FlashMsg::add('info', 'O item ' . $objPedidoItem->getProdutoVariacao()->getProdutoNomeCompleto(' - ', '', ' - ') . ' teve seu estoque zerado, e por isso' .
                    ' tivemos que removê-lo do seu carrinho.');

                $objPedidoItem->delete();
            }

            if ($objPedidoItem->getQuantidade() <> $quantidade) {
                $objPedidoItem->setQuantidade($quantidade);
                $objPedidoItem->save();
            }
        }
    } else {
        FlashMsg::add('info', 'O item ' . $objPedidoItem->getProdutoVariacao()->getProdutoNomeCompleto(' - ', '', ' - ') . ' teve seu estoque zerado, e por isso' .
            ' tivemos que removê-lo do seu carrinho.');

        $objPedidoItem->delete();
    }
}

$container->getCarrinhoProvider()->getCarrinho()->resetFrete();

if (!$request->isXmlHttpRequest()) {
    redirect('/carrinho');
}
