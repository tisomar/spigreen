<?php

if (!empty($_POST)) {
    //Procura um produto que seja mensalidade
    $mensalidade = ProdutoQuery::create()
                    ->filterByMensalidade(true)
                    ->findOne();
    
    if ($mensalidade) {
        $cart = $container->getCarrinhoProvider()->getCarrinho();
        $cart->resetFrete();
        
        //Remove todos os item. Vamos deixar apenas a mensalidade
        $cart->getPedidoItems()->delete();
        
        //adiciona a mensalidade ao carrinho e redireciona para a pagina de pagamento
        if (ProdutoVariacaoPeer::addProdutoVariacaoToCart($container, $mensalidade->getProdutoVariacao()->getId(), 1)) {
            redirect('/checkout/pagamento');
        }
    }
}

redirect('/minha-conta/meu-plano');
