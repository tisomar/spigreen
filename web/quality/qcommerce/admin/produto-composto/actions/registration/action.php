<?php

if (!isset($_class)) {
    trigger_error('você deve definir a classe $_class');
}

use Symfony\Component\HttpFoundation\File\UploadedFile;

$erros = array();

$_classPeer = $_class::PEER;


if ($request->query->has('reference') && $request->query->get('reference') > 0) {
    $object = $_classPeer::retrieveByPK($request->query->get('reference'));
} else {
    throw new Exception('Id do produto não informado.');
}

if ($object->getTaxaCadastro()) {
    throw new Exception('Produto taxa não pode conter essa configuração.');
}

if ($request->getMethod() == 'POST') {
    $sucesso = 0;
    $data = trata_post_array($request->request->get('data'));
    $produtoId = $_GET['reference'];

    $objProduto = ProdutoPeer::retrieveByPK($produtoId);


    $produtoCompostoProduto = isset($request->request->get('data')['produto-composto-id']) ? $request->request->get('data')['produto-composto-id'] : null;
    $produtoCompostoVariacao = isset($request->request->get('data')['produto-composto-variacao-id']) ? $request->request->get('data')['produto-composto-variacao-id'] : null;
    $produtoCompostoQuantidade = isset($request->request->get('data')['produto-composto-quantidade']) ? $request->request->get('data')['produto-composto-quantidade'] : null;
    $produtoCompostoValorIntegracao = isset($request->request->get('data')['produto-composto-valor-integracao']) ? $request->request->get('data')['produto-composto-valor-integracao'] : null;
    $compostoSave = false;

    $produtoCompostoValorIntegracao = array_map(function($item) {
        return (float) str_replace(',', '.',
            preg_replace('/[^\d\,\.]/', '', $item)
        );
    }, $produtoCompostoValorIntegracao);

    $arrProdutosCompostos = ProdutoCompostoQuery::create()->findByProdutoId($objProduto->getId());

    if (count($arrProdutosCompostos) > 0) {
        foreach ($arrProdutosCompostos as $objProdutoComposto) {
            $objProdutoComposto->delete();
        }
    }

    if (count($produtoCompostoProduto) > 0 && count($produtoCompostoQuantidade) > 0) {
        foreach ($produtoCompostoProduto as $key => $value) {
            if (empty($produtoCompostoQuantidade[$key]) || $produtoCompostoQuantidade[$key] <= 0 || empty($value)
                || empty($produtoCompostoVariacao[$key]) || $produtoCompostoVariacao[$key] <= 0) {
                //$erros[$key] = 'Produto sem quantidade ou sem variação informada.';
                continue;
            }

            $objProdutoComposto = ProdutoCompostoQuery::create()
                ->filterByProdutoId($objProduto->getId())
                ->filterByProdutoCompostoVariacaoId($produtoCompostoVariacao[$key])
                ->filterByProdutoCompostoId($value)
                ->findOneOrCreate();

            $objProdutoComposto->setProdutoId($objProduto->getId());
            $objProdutoComposto->setProdutoCompostoId($value);
            $objProdutoComposto->setProdutoCompostoVariacaoId($produtoCompostoVariacao[$key]);
            $objProdutoComposto->setEstoqueQuantidade($produtoCompostoQuantidade[$key]);
            $objProdutoComposto->setValorIntegracao($produtoCompostoValorIntegracao[$key]);

            $objProdutoComposto->save();

            $sucesso++;
        }
    } else {
        $erros['semprodutoestoque'] = 'Sem produto ou sem estoque informado.';
    }

    if ($sucesso > 0 && count($erros) <= 0) {
        $session->getFlashBag()->add('success', 'Registro armazenado com sucesso!');
    } elseif ($sucesso > 0 && count($erros) > 0) {
        if (count($erros)) {
            foreach ($erros as $erro) {
                $session->getFlashBag()->add('error', $erro);
            }
        }

        $session->getFlashBag()->add('success', 'Um ou mais registros foram armazenados com sucesso!');
    }


    redirectTo($config['routes']['registration']);

    exit;
}

$arrProdutosSimples = ProdutoQuery::create()->findByTipoProduto('SIMPLES');

$arrProdutoCompostos = ProdutoCompostoQuery::create()->findByProdutoId($request->query->get('reference'));
