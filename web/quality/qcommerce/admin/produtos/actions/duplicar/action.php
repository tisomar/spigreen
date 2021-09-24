<?php
/* @var $object Produto */

$object = ProdutoPeer::retrieveByPK($router->getArgument(0));
if (!$object instanceof $_class) {
    redirectTo(get_url_admin() . '/404');
    exit; // -----------------------
}

if ($request->getMethod() == 'POST') {
    $dataPost = $container->getRequest()->request->all();

    $con = Propel::getConnection();
    $con->beginTransaction();

    $erros = array();

    try {
        $dateTime = new DateTime();

        ## Produto
        $clone = $object->copy();
        $clone->setByArray($dataPost['produto']);
        $clone->setDataCriacao($dateTime);
        $clone->setDataAtualizacao($dateTime);
        $clone->setNotaAvaliacao(0);
        $clone->setQuantidadeAvaliacao(0);
        $clone->save();

        ## ProdutoVariacao (Master=1)
        $cloneProdutoVariacaoMaster = $object->getProdutoVariacao()->copy();
        $cloneProdutoVariacaoMaster->setByArray($dataPost['produto_variacao']);
        $cloneProdutoVariacaoMaster->setDataCriacao($dateTime);
        $cloneProdutoVariacaoMaster->setDataAtualizacao($dateTime);
        $cloneProdutoVariacaoMaster->setProduto($clone);
        if (ProdutoVariacaoQuery::create()->filterBySku($cloneProdutoVariacaoMaster->getSku())->count() > 0) {
            throw new Exception('Já há um produto com a referência <b>' . $cloneProdutoVariacaoMaster->getSku() . '</b> cadastrada no sistema. Por favor, informe outra.');
        }
        $cloneProdutoVariacaoMaster->save();

        ## Categorias
        foreach ($object->getProdutoCategorias() as $pc) { /** @var ProdutoCategoria $pc */
            $cloneProdutoCategoria = $pc->copy();
            $cloneProdutoCategoria->setProduto($clone);
            $cloneProdutoCategoria->save();
        }

        $options = isset($dataPost['options']) && is_array($dataPost['options']) ? $dataPost['options'] : array();

        ## Fotos
        if (in_array('produto_foto', $options)) {
            foreach ($object->getFotos() as $f) { /** @var Foto $f */
                if ($f->isImagemExists()) {
                    $source = $f->_getAbsolutePathImagem() . $f->getImagem();
                    $sourceExt = pathinfo($source, PATHINFO_EXTENSION);
                    $newName = sprintf('%s.%s', md5(sha1(uniqid())), $sourceExt);
                    $dest = $f->_getAbsolutePathImagem() . $newName;
                    copy($source, $dest);
                    $cloneFoto = $f->copy();
                    $cloneFoto->setProduto($clone);
                    $cloneFoto->setImagem($newName);
                    $cloneFoto->save();
                }
            }
        }

        ## Atributos
        $produtoAtributoMap = array();
        if (in_array('produto_atributo', $options)) {
            foreach ($object->getProdutoAtributos() as $pa) { /** @var ProdutoAtributo $pa */
                $cloneProdutoAtributo = $pa->copy();
                $cloneProdutoAtributo->setProduto($clone);
                $cloneProdutoAtributo->save();
                $produtoAtributoMap[$pa->getId()] = $cloneProdutoAtributo->getId();
            }
        }

        ## Variações
        $produtoVariacaoMap = array();
        if (in_array('produto_variacao', $options)) {
            foreach ($object->getProdutoVariacaos(ProdutoVariacaoQuery::create()->filterByIsMaster(0)) as $pv) { /** @var ProdutoVariacao $pv */
                $cloneProdutoVariacao = $pv->copy();
                $cloneProdutoVariacao->setProduto($clone);
                $cloneProdutoVariacao->save();
                $produtoVariacaoMap[$cloneProdutoVariacao->getId()] = $pv;
            }

            foreach ($produtoVariacaoMap as $newPvId => $pv) { /** @var ProdutoVariacao $pv */
                foreach ($pv->getProdutoVariacaoAtributos() as $pva) { /** @var ProdutoVariacaoAtributo $pva */
                    $clonePVA = $pva->copy();
                    $clonePVA->setProdutoVariacaoId($newPvId);
                    $clonePVA->setProdutoAtributoId($produtoAtributoMap[$pva->getProdutoAtributoId()]);
                    $clonePVA->save();
                }
            }
        }

        $con->commit();

        $container->getSession()->getFlashBag()->add('success', 'Produto duplicado com sucesso');
        redirectTo(get_url_admin() . '/produtos/registration?id=' . $clone->getId());
    } catch (Exception $e) {
        $con->rollBack();

        $container->getSession()->getFlashBag()->add('error', $e->getMessage());
    }
}
