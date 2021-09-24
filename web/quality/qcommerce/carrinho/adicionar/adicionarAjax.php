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

//if(ClientePeer::isAuthenticad() && ClientePeer::getClienteLogado(true)->getTipoConsumidor() == 0){

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

//}

if ($container->getRequest()->getMethod() == 'POST') {
    $cart = $container->getCarrinhoProvider()->getCarrinho();
    $cart->resetFrete();

    $produto_id = $container->getRequest()->request->get('product_id', array());
    $produto_variacao_id = $container->getRequest()->request->get('product_variation_id', array());
    $quantidade = $container->getRequest()->request->get('quantidade', array());

    if (count($produto_variacao_id) > 0) {
//        foreach ($quantidade_pv as $produto_id => $array_variacoes) {
//            foreach ($array_variacoes as $produto_variacao_id => $quantidade) {
        if ($quantidade > 0) {
            $objProdutoVariacao = ProdutoVariacaoQuery::create()->findOneById($produto_variacao_id);
            $objProduto = $objProdutoVariacao ? $objProdutoVariacao->getProduto() : null;
            $objPlano = ($objProduto && $objProduto->isKitAdesao()) ? $objProduto->getPlano() : null;


            //caso o carrinho ja possua algum plano, nao podemos deixar o cliente adicionar outro
            if ($objPlano && $cart->getPlano()) {
                $msgErro = 'Não é permitido adicionar mais de um combo ao carrinho.<br>Por favor, remova o combo que está atualmente em seu carrinho antes de adicionar um novo combo.';
//                        continue;
            }

            if ($planoCliente && $objPlano) {
//                        $valorPlanoCliente = $planoCliente->getValor() > 0 ? $planoCliente->getValor() : ProdutoQuery::create()->findOneByPlanoId($planoCliente->getId())->getValor();
//                        $valorPlanoAdicionar = $objPlano->getValor() > 0 ? $objPlano->getValor() : ProdutoQuery::create()->findOneByPlanoId($objPlano->getId())->getValor();
//
//                        if($valorPlanoCliente >= $valorPlanoAdicionar){
//                            //nao podemos deixar o cliente contratar um plano menor que o plano que ele ja possui.
//                            continue; //ignora item
//                        }
                $msgErro = 'Você já possue um combo ativo.<br>O produto não pode ser comprado, escolha outro produto.';
//                        continue;
            }

            $totalEnviado++;

            $con = Propel::getConnection();
            $con->beginTransaction();

            if (ProdutoVariacaoPeer::addProdutoVariacaoToCart($container, $produto_variacao_id, $quantidade)) {
                $totalAdicionado++;

                //Verifica se é um kit
                /*if ($objPlano) {
                            //caso o plano exija a compra de um produto inicial também devemos adicionar este produto inicial ao carrinho
                            $objProdutoInicial = $objPlano->getProdutoInicial();
                            if ($objProdutoInicial) {
                                $itemProdutoInicial = ProdutoVariacaoPeer::addProdutoVariacaoToCart($container, $objProdutoInicial->getProdutoVariacao()->getId(), 1);
                                if ($itemProdutoInicial) {
                                    //sinaliza que este produto foi adicionado ao carrinho por ser um produto inicial de kit de adesao.
                                    $itemProdutoInicial->setPlanoId($objPlano->getId());
                                    $itemProdutoInicial->save();
                                }
                            }
                }*/
            }

            $con->commit();
        }
//            }
//        }
    }
}


if ($totalEnviado > 0) {
    if ($totalAdicionado > 0) {
        if ($totalAdicionado == $totalEnviado) {
            //FlashMsg::success(plural($totalAdicionado, "Produto adicionado com sucesso!", "Produtos adicionados com sucesso!") . ' - <a href="' . get_url_site() . '/carrinho/">Clique aqui para finalizar sua compra!</a>');
            FlashMsg::success(plural($totalAdicionado, "Produto adicionado com sucesso!", "Produtos adicionados com sucesso!"));
//            if(!$clienteConsultor && !$franqueadoCliente && !$isNewReseller){
//                redirect('/login/verify-access');
//            } else {
//                redirect('/carrinho');
//            }
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
            FlashMsg::success(plural(($totalAdicionado), "%s produto foi adicionado com sucesso!", "%s produtos foram adicionados com sucesso!"));
            FlashMsg::success(plural(($totalEnviado - $totalAdicionado), "%s produto não pode ser adicionado!", "%s produtos não puderam ser adicionados!"));
        }
    } else {
        FlashMsg::danger($msgErro ? $msgErro : 'Houve um problema ao tentar inserir o produto ao carrinho!');
    }
} else {
    FlashMsg::danger($msgErro ? $msgErro : 'Você deve informar o produto que deseja inserir no carrinho!');
}

//if ($isAjax) {

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
//    redirectTo(ProdutoPeer::retrieveByPK($produto_id)->getUrlDetalhes());

//}


exit;
