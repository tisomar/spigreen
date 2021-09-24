<?php

/* @var $container \QPress\Container\Container */
if (!isset($erros)) {
    $erros = array();
}
$product = ProdutoPeer::retrieveByPK($_GET['reference']);

if ($container->getRequest()->request->has('variacao')) {
    $data = trata_post_array($container->getRequest()->request->get('variacao'));
    $atributos = ($container->getRequest()->request->get('atributo_valor'));
    $ordemAtributos = array_keys($atributos);

    $arrValoresAtributos = array();
    $produtoAtributoIdByDescricao = array();

    foreach ($atributos as $produtoAtributoId => $valores) {
        $objProdutoAtributo = ProdutoAtributoQuery::create()->findOneById($produtoAtributoId);

        if ($objProdutoAtributo->isCor()) {
            $collProdutoCor = ProdutoCorQuery::create()->filterById($valores)->find();

            $valores = $propriedadeByAtributo = array();
            foreach ($collProdutoCor as $objProdutoCor) { /* @var $objProdutoCor ProdutoCor */
                $valores[$objProdutoCor->getId()] = $objProdutoCor->getNome();
                $propriedadeAtributoByAtributo[$objProdutoCor->getNome()] = $objProdutoCor;
            }
        } else {
            if (is_string($valores)) {
                $valores = array_map('trim', explode(",", $valores));
            }
        }

        $produtoAtributoIdByDescricao += array_combine($valores, array_fill(0, count($valores), $produtoAtributoId));

        $arrValoresAtributos[$produtoAtributoId] = $valores;
    }

    $combinacoes = array_combine_variacoes($arrValoresAtributos, '##');

    foreach ($combinacoes as $combinacao) {
        if (count($erros) == 0) {
            $atributosVariacao = explode('##', $combinacao);

            $produto_variacao_id = ProdutoVariacaoPeer::retrieveByCombinacao(array_combine($ordemAtributos, $atributosVariacao), $product->getId());

            if (!$produto_variacao_id) {
                $objProdutoVariacao = new ProdutoVariacao();

                $objProdutoVariacao->setByArray($data);
                $objProdutoVariacao->setProdutoId($product->getId());

                if ($objProdutoVariacao->myValidate($erros) && !$erros) {
                    $objProdutoVariacao->save();

                    foreach ($atributosVariacao as $i => $descricao) {
                        $objProdutoVariacaoAtributo = new ProdutoVariacaoAtributo();
                        $objProdutoVariacaoAtributo->setProdutoVariacaoId($objProdutoVariacao->getId());
                        $objProdutoVariacaoAtributo->setProdutoAtributoId($ordemAtributos[$i]);
                        $objProdutoVariacaoAtributo->setDescricao($descricao);
                        $objProdutoVariacaoAtributo->setPropriedade(isset($propriedadeAtributoByAtributo[$descricao]) ? $propriedadeAtributoByAtributo[$descricao] : null);

                        $objProdutoVariacaoAtributo->save();
                    }
                }
            } else {
                $erros[] = 'Você já adicionou esta variação.';
            }
        }
    }

    if (count($erros) == 0) {
        $container->getRequest()->request->set('variacao', array());
        $container->getRequest()->request->set('atributo_valor', array());
        $container->getRequest()->request->set('atributo_id', array());
        $container->getSession()->getFlashBag()->add('success', 'Registro(s) adicionado(s) com sucesso!');
    } else {
        foreach ($erros as $erro) {
            $container->getSession()->getFlashBag()->add('error', $erro);
        }
    }
}

// Alteração das variações em lote
if ($container->getRequest()->request->has('lote')) {
    $hasFilter = $container->getRequest()->request->get('filtro[atributo]', null, true) != '';

    $arrProdutoVariacoes = ProdutoVariacaoQuery::create()
        ->filterByIsMaster(false)
        ->filterByProdutoId($_GET['reference'])
        ->addAscendingOrderByColumn(ProdutoVariacaoPeer::ID)
        ->_if($hasFilter)
        ->useProdutoVariacaoAtributoQuery()
        ->filterByProdutoAtributoId($container->getRequest()->request->get('filtro[atributo]', null, true))
        ->filterByDescricao($container->getRequest()->request->get('filtro[valor]', null, true))
        ->endUse()
        ->_endif()
        ->find();

    foreach ($arrProdutoVariacoes as $objProdutoVariacao) {
        if (strpos($container->getRequest()->request->get('lote[campo]', null, true), 'ATRIBUTO-') !== false) {
            list(, $idProdutoAtributo) = explode('-', $container->getRequest()->request->get('lote[campo]', null, true));

            $objProdutoVariacaoAtributo = ProdutoVariacaoAtributoQuery::create()
                ->filterByProdutoVariacaoId($objProdutoVariacao->getId())
                ->filterByProdutoAtributoId($idProdutoAtributo)
                ->findOne();

            if ($objProdutoVariacaoAtributo instanceof ProdutoVariacaoAtributo) {
                $objProdutoVariacaoAtributo->setDescricao($container->getRequest()->request->get('lote[valor]', null, true));
                $objProdutoVariacaoAtributo->save();
                unset($objProdutoVariacaoAtributo);
            }
        } else {
            $data = array (
                $container->getRequest()->request->get('lote[campo]', null, true) => $container->getRequest()->request->get('lote[valor]', null, true)
            );

            $objProdutoVariacao->setByArray($data);

            $objProdutoVariacao->save();
        }
    }

    $container->getSession()->getFlashBag()->add('success', 'Registros atualizados com sucesso!');
}

include QCOMMERCE_DIR . '/admin/_2015/actions/' . $router->getAction() . '/action.php';
