<?php
/* @var $object Produto */

if (!isset($_class)) {
    trigger_error('você deve definir a classe $_class');
}

$getProdutoTaxa = false;

if ($request->query->has('id')) {
    $object = $_classPeer::retrieveByPK($request->query->get('id'));
    $getProdutoTaxa = $object->getTaxaCadastro();
} else {
    $object = new $_class();
}
if (!$object instanceof $_class) {
    redirectTo($config['routes']['list']);
    exit; // -----------------------
}
$erros = array();

$centroDistribuicao = CentroDistribuicaoQuery::create()->filterByStatus(1)->find();
// $estoqueProduto = EstoqueProdutoQuery::create()->filterByProdutoVariacao($object->getProdutoVariacao());

if ($request->getMethod() == 'POST') {
    $object->setByArray(trata_post_array($container->getRequest()->request->get('produto')));
    $object->getProdutoVariacao()->setByArray(trata_post_array($container->getRequest()->request->get('produto_variacao')));
    
    if(!empty($container->getRequest()->request->get('produto_variacao')['VALOR_INTEGRACAO_ADMIN'])) :
        $valor = $container->getRequest()->request->get('produto_variacao')['VALOR_INTEGRACAO_ADMIN'];
        $valor = str_replace(',','.',str_replace(['.', 'R$', ' '],'',$valor));
        $object->getProdutoVariacao()->setValorIntegracaoAdmin($valor);
    endif; 

    if ($object->getId() == 1) {
        $object->getProdutoVariacao()->setValorPromocional('R$ 0,00');
    }

    $object->myValidate($erros);
    $object->getProdutoVariacao()->myValidate($erros);

    if (count($erros) == 0) {
        $object->getProdutoCategorias()->delete();
        $object->clearProdutoCategorias();

        if (!is_null($container->getRequest()->request->get('produto_categoria')) && count($container->getRequest()->request->get('produto_categoria')) > 0) {
            foreach ($container->getRequest()->request->get('produto_categoria') as $categoria_id) {
                $object->addProdutoCategoria(new ProdutoCategoria($categoria_id));
            }
        }
        
        $isNew = $object->isNew();

        $object->save();
        $object->getProdutoVariacao()->save();

        if ($container->getRequest()->request->get('atualizar_seo') == 1) {
            SeoPeer::cadastrarSeoProduto($object);
        }

        if ($isNew) {
            $session->getFlashBag()->add('success', 'Registro armazenado com sucesso!<br />Envie as imagens para este produto.');
            redirectTo(get_url_admin() . '/pmidia/list/?context=Produto&reference=' . $object->getId());
        } else {
            $redirectOnSuccess = $container->getRequest()->request->get('redirectToOnSuccess');
            switch ($redirectOnSuccess) {
                case 'edit':
                    $redirectUrl = $container->getRequest()->getRequestUri();
                    break;

                case 'new':
                    $redirectUrl = $config['routes']['registration'];
                    break;

                case 'list':
                    $redirectUrl = $config['routes']['list'];
                    break;

                default:
                    $redirectUrl = $redirectOnSuccess;
                    break;
            }

            $session->getFlashBag()->add('success', 'Registro armazenado com sucesso!');
            redirectTo($redirectUrl);
        }
        exit; // -----------------------
    }

    if (count($erros)) {
        foreach ($erros as $type => $erro) {
            $session->getFlashBag()->add('error', $erro);
        }
    }
}

if (!$container->getRequest()->request->has('atualizar_seo')) {
    $container->getRequest()->request->set('atualizar_seo', 1);
}
