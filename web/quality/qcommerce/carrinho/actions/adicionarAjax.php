<?php
$isAjax = $container->getRequest()->isXmlHttpRequest();

$clienteLogado = ClientePeer::getClienteLogado(true);
$planoCliente =  $clienteLogado ? $clienteLogado->getPlano() : null;

$isNewReseller = ClientePeer::isResellerActived();

$totalEnviado       = 0;
$totalAdicionado    = 0;

$msgErro = null;

$franqueadoCliente = false;
$clienteConsultor = false;

if (ClientePeer::isAuthenticad() && !$clienteLogado->isConsumidorFinal()) {
    $clienteConsultor = true;
}

if ($container->getSession()->has('fromFranqueado')) {
    $slug = $container->getSession()->get('slugFranqueado');

    $objHotsite = HotsiteQuery::create()
        ->filterBySlug($slug)
        ->findOne();

    if ($objHotsite instanceof Hotsite) {
        $franqueadoCliente = $objHotsite->getCliente();
    }
} else {
    $container->getSession()->set('noFranqueado', true);
}

if ($container->getRequest()->getMethod() == 'POST') {
    $cart = $container->getCarrinhoProvider()->getCarrinho();
    $cart->resetFrete();

    $produto_id = $container->getRequest()->request->get('product_id', array());
    $produto_variacao_id = $container->getRequest()->request->get('product_variation_id', array());
    $quantidade = $container->getRequest()->request->get('quantidade', array());

    if (!is_array($produto_variacao_id) || count($produto_variacao_id) > 0) {
        if ($quantidade > 0) {
            $currQty = ProdutoVariacaoPeer::getCurrentQtyCartProduct($container, $produto_variacao_id);

            $totalEnviado    = $currQty;
            $totalAdicionado = $currQty;

            $objProdutoVariacao = ProdutoVariacaoQuery::create()->findOneById($produto_variacao_id);
            $objProduto = $objProdutoVariacao ? $objProdutoVariacao->getProduto() : null;
            $objPlano = ($objProduto && $objProduto->isKitAdesao()) ? $objProduto->getPlano() : null;

            //Não pode adicionar um combo ao carrrinho este ja possua algum combo
            if ($objPlano && $cart->getPlano()) {
                $msgErro = 'Não é permitido adicionar mais de um plano ao carrinho.';
            }

            // Não pode adquirir um plano igual ou inferior de outro já adquirido
            if (($planoCliente && $objPlano) && $objPlano->getNivel() <= $planoCliente->getNivel()) {
                $msgErro = 'Você já possui um plano igual ou superior a este.';
            }

            // Apenas continuará o processo se não foi encontrado erro
            if ($msgErro === null) {
                $totalEnviado++;

                $con = Propel::getConnection();
                $con->beginTransaction();

                if (ProdutoVariacaoPeer::addProdutoVariacaoToCart($container, $produto_variacao_id, $currQty + $quantidade)) {
                    $totalAdicionado++;
                }

                $con->commit();
            } else {
//                FlashMsg::danger($msgErro);
                header('Content-type: application/json');
                die(json_encode(array('status' => 'error', 'message' => $msgErro)));
            }
        }
    }
}

if ($totalEnviado > 0) {
    if ($totalAdicionado > 0) {
        if ($totalAdicionado == $totalEnviado) {
//            FlashMsg::success(plural($totalAdicionado, "Produto adicionado com sucesso!", "Produtos adicionados com sucesso!"));

            if ($clienteConsultor && !$franqueadoCliente && !$isNewReseller) {
                $status = $totalEnviado > 0 && $totalAdicionado == $totalEnviado ? 'success' : 'error';
                header('Content-type: application/json');
                die(json_encode(array('status' => 'success')));
            } else {
                FlashMsg::clear('success');
                $status = $totalEnviado > 0 && $totalAdicionado == $totalEnviado ? 'success' : 'error';
                header('Content-type: application/json');
                die(json_encode(array('status' => $status)));
            }
        } else {
//            FlashMsg::success(plural(($totalAdicionado), "%s produto foi adicionado com sucesso!", "%s produtos foram adicionados com sucesso!"));
//            FlashMsg::success(plural(($totalEnviado - $totalAdicionado), "%s produto não pode ser adicionado!", "%s produtos não puderam ser adicionados!"));
        }
    } else {
//        FlashMsg::danger($msgErro ? $msgErro : 'Houve um problema ao tentar inserir o produto ao carrinho!');
    }
} else {
//    FlashMsg::danger($msgErro ? $msgErro : 'Você deve informar o produto que deseja inserir no carrinho!');
}

if ($clienteConsultor && !$franqueadoCliente && !$isNewReseller) {
    $status = $totalEnviado > 0 && $totalAdicionado == $totalEnviado ? 'success' : 'error';
    header('Content-type: application/json');
    echo json_encode(['status' => 'success']);
} else {
    FlashMsg::clear('success');
    $status = $totalEnviado > 0 && $totalAdicionado == $totalEnviado ? 'success' : 'error';
    header('Content-type: application/json');
    echo json_encode(['status' => $status]);
}
