<?php

if (FALSE === ClientePeer::isAuthenticad()):
    FlashMsg::info('Você deve estar logado para acessar a área desejada, por favor, faça o login e tente novamente.');

    $redirecionar = '/login/';

    if (isset($_SERVER['REQUEST_URI']) && !empty($_SERVER['REQUEST_URI'])) {
        $redirecionar .= '?redirecionar=' . urlencode(get_url_site() . get_request_uri());
    }

    redirect($redirecionar);
else:
    $cliente = ClientePeer::getClienteLogado(true);
    $franqueado = ClientePeer::getFranqueadoSelecionado($container);
    $isNewReseller =
        $container->getSession()->has('resellerLoggedActive') ||
        $container->getSession()->has('resellerActive')
            ? true : false;

    /** Cliente quer ser revendedor e está partindo de um cadastro comum. */

    if ($cliente->getTaxaCadastro()):
        $objPedido = null;

        if (stripos($_SERVER['REQUEST_URI'], '/checkout/confirmacao/') !== false):
            $objPedido = PedidoQuery::create()
                ->where('md5(Pedido.Id) LIKE ?', $router->getArgument(0))
                ->filterByClienteId(ClientePeer::getClienteLogado()->getId())
                ->findOne();
        endif;

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

        if (!$confirmacao):
            if ($pedidoAtivo > 0 && stripos($_SERVER['REQUEST_URI'], '/checkout/pagamento/') !== false):
                FlashMsg::warning(nl2br(Config::get('cliente.aguardando_taxa_cadastro_msg')));
                redirect('/home');
            else:
                if (isset($_SERVER['REQUEST_URI'])
                    && !empty($_SERVER['REQUEST_URI'])
                    && stripos($_SERVER['REQUEST_URI'], '/checkout/pagamento') === false
                    && stripos($_SERVER['REQUEST_URI'], '/checkout/confirmacao') === false
                ):
                    $produtoTaxa = ProdutoPeer::retrieveByPK(ProdutoPeer::PRODUTO_TAXA_ID);
                    /** @var Produto $produtoTaxa */
                    $container->getCarrinhoProvider()->save();
                    $carrinho = $container->getCarrinhoProvider()->getCarrinho();
                    ProdutoVariacaoPeer::addProdutoTaxaCadastroToCart($container, $produtoTaxa);

                    if (!$franqueado && !$isNewReseller):
                        $carrinho->setEndereco($cliente->getEnderecoPrincipal());
                    endif;

                    $carrinho->save();

                    if (!$franqueado && !$isNewReseller):
                        redirect('/checkout/pagamento');
                    else:
                        $isCheckout = stripos($_SERVER['REQUEST_URI'], '/checkout/') === false;

                        if ($isCheckout):
                            redirect('/checkout/endereco');
                        endif;
                    endif;
                else:
                    FlashMsg::warning(nl2br(Config::get('cliente.pagar_taxa_cadastro_msg')));
                endif;
            endif;
        else:
            $aviso = AvisoCompraMensalQuery::create()
                ->filterByClienteId($cliente->getId())
                ->filterByData(date('Y-m-d'))
                ->filterByVisualizado(0)
                ->findOne();

            if ($aviso instanceof AvisoCompraMensal):
                $_SESSION['MODAL_AVISO'] = $aviso->getMensagem();
                $_SESSION['MODAL_AVISO_TITULO'] = $aviso->getTitulo();
                $aviso->setVisualizado(true);
                $aviso->save();
            endif;
        endif;
    endif;
endif;