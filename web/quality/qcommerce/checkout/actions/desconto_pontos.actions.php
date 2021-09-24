<?php

if ($container->getRequest()->request->has('action')) {
    $carrinho = $container->getCarrinhoProvider()->getCarrinho();
    
    switch ($container->getRequest()->request->get('action')) {
        case 'remove':
            $carrinho->unregisterDescontoPontos();

            break;
    }
}

redirect('/checkout/pagamento');
