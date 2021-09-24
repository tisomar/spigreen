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

    $produto_variacao_id = $container->getRequest()->request->get('produto_variacao_id', array());
    $quantidade = 1;

    if ($produto_variacao_id > 0) {
        $objProdutoVariacao = ProdutoVariacaoQuery::create()->findOneById($produto_variacao_id);
        $objProduto = $objProdutoVariacao ? $objProdutoVariacao->getProduto() : null;
        $objPlano = ($objProduto && $objProduto->isKitAdesao()) ? $objProduto->getPlano() : null;

        $quantidade =
            $request->request->get('quantidade_pv', [])
                [$objProdutoVariacao->getProdutoId()]
                [$objProdutoVariacao->getId()]
            ?? 1;

        // Não pode adicionar um combo ao carrrinho este ja possua algum combo
        if ($objPlano && $cart->getPlano()) {
            $msgErro = 'Não é permitido adicionar mais de um combo ao carrinho.';
        }

        // Não pode adquirir um plano igual ou inferior de outro já adquirido
        if (($planoCliente && $objPlano) && $objPlano->getNivel() <= $planoCliente->getNivel()) {
            $msgErro = 'Você já possui um plano igual ou superior a este.';
        }

        if ($objProduto->isKitAdesao() && $quantidade > 1) :
            $msgErro = 'Não é permitido adicionar mais de um plano ao carrinho.';
        endif;

        // Apenas continuará o processo se não foi encontrado erro
        if ($msgErro === null) {
            $totalEnviado++;

            $con = Propel::getConnection();
            $con->beginTransaction();

            if (ProdutoVariacaoPeer::addProdutoVariacaoToCart($container, $produto_variacao_id, $quantidade)) {
                $totalAdicionado++;
            }

            $con->commit();

            if (ClientePeer::isAuthenticad() && ClientePeer::getClienteLogado(true)->getTipoConsumidor() == 0) {
                $msgErro = 'Você não tem revendedor selecionado e será redirecionado.';
            } elseif (ClientePeer::isAuthenticad() &&
                ClientePeer::getClienteLogado(true)->getTipoConsumidor() > 0 &&
                ClientePeer::getClienteLogado(true)->getTaxaCadastro()) {
                $cliente = ClientePeer::getClienteLogado(true);
                $objPedido = null;

                if (stripos($_SERVER['REQUEST_URI'], '/checkout/confirmacao/') !== false) {
                    $objPedido = PedidoQuery::create()
                        ->where('md5(Pedido.Id) LIKE ?', $router->getArgument(0))
                        ->filterByClienteId(ClientePeer::getClienteLogado()->getId())
                        ->findOne();
                }

                $pedidoAtivo = PedidoQuery::create()
                    ->filterByClassKey(PedidoPeer::CLASS_KEY_PEDIDO, Criteria::EQUAL)
                    ->filterByTaxaCadastro(1, Criteria::EQUAL)
                    ->filterByStatus('CANCELADO', Criteria::NOT_EQUAL)
                    ->filterByClienteId($cliente->getId(), Criteria::EQUAL)
                    ->find();

                $pedidoAtivo = count($pedidoAtivo);

                $confirmacao = $pedidoAtivo > 0
                    && stripos($_SERVER['REQUEST_URI'], '/checkout/confirmacao/') !== false
                    && $objPedido instanceof Pedido;

                if (!$confirmacao) {
                    if ($pedidoAtivo > 0 && stripos($_SERVER['REQUEST_URI'], '/checkout/pagamento/') !== false) {
                        FlashMsg::warning(nl2br(Config::get('cliente.aguardando_taxa_cadastro_msg')));
                        redirect('/home');
                    } else {
                        if (isset($_SERVER['REQUEST_URI']) && !empty($_SERVER['REQUEST_URI']) && stripos($_SERVER['REQUEST_URI'], '/checkout/pagamento') === false) {
                            $produtoTaxa = ProdutoPeer::retrieveByPK(ProdutoPeer::PRODUTO_TAXA_ID);
                            /** @var Produto $produtoTaxa */
                            $container->getCarrinhoProvider()->save();
                            $carrinho = $container->getCarrinhoProvider()->getCarrinho();
                            ProdutoVariacaoPeer::addProdutoTaxaCadastroToCart($container, $produtoTaxa);
                            $carrinho->setEndereco($cliente->getEnderecoPrincipal());
                            $carrinho->save();

                            redirect('/checkout/pagamento');
                        } else {
                            FlashMsg::warning(nl2br(Config::get('cliente.pagar_taxa_cadastro_msg')));
                        }
                    }
                }
            }
        }
    }
}

if ($totalEnviado > 0) {
    if ($totalAdicionado > 0) {
        if ($totalAdicionado == $totalEnviado) {
            FlashMsg::success(plural($totalAdicionado, "Produto adicionado com sucesso!", "Produtos adicionados com sucesso!"));
            if (!$clienteConsultor && !$franqueadoCliente && !$isNewReseller) {
                redirect('/login/verify-access');
            } else {
                redirect('/carrinho');
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

if ($isAjax) {
    FlashMsg::clear('success');
    $status = $totalEnviado > 0 && $totalAdicionado == $totalEnviado ? 'success' : 'error';
    header('Content-type: application/json');
    die(json_encode(array('status' => $status)));
}


if (!$clienteConsultor && (!$clienteLogado || ($clienteLogado && !$franqueadoCliente && !$isNewReseller))) {
    redirect('/login/verify-access');
} else {
    if (!isset($produto_id)) {
        $produtoIDRequest = $container->getRequest()->request->get('produto_variacao_id');

        //Pega o primeiro elemento do array retornado do request
        $produtoVariacaoId = array_shift($produtoIDRequest);
        $produto_id = ProdutoVariacaoQuery::create()->findOneById($produtoVariacaoId)->getProdutoId();
    }

    redirectTo(ProdutoPeer::retrieveByPK($produto_id)->getUrlDetalhes());
}

exit;
