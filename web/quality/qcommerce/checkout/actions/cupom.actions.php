<?php

# Atualiza o cupom de desconto
if ($container->getRequest()->request->has('action-cupom')) {
    $carrinho = $container->getCarrinhoProvider()->getCarrinho();
    
    switch ($container->getRequest()->request->get('action-cupom')) {
        case 'add':
            $cupom_desconto = $container->getRequest()->request->get('cupom_desconto');
            if (CupomPeer::isValid($cupom_desconto, $carrinho)) {
                if ($carrinho->registerCupom($cupom_desconto)) {
                    FlashMsg::success('Cupom de desconto validado com sucesso!');
                }
            }

            break;

        case 'remove':
            $carrinho->unregisterCupom();

            break;
    }
}

redirect('/checkout/pagamento');
