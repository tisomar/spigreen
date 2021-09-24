<?php

$pageTitle = 'Variações';
$_class = ProdutoVariacaoPeer::OM_CLASS;


$produto = ProdutoQuery::create()->findOneById($_GET['reference']);

if ($produto instanceof Produto && $produto->getTaxaCadastro()) {
    throw new Exception('Produto taxa não pode conter essa configuração.');
}

$arrProdutoAtributos = ProdutoAtributoQuery::create()->filterByProdutoId($_GET['reference'])->addAscendingOrderByColumn(ProdutoAtributoPeer::ORDEM)->find();

if (count($arrProdutoAtributos) < 1) {
    $container->getSession()->getFlashBag()->add('warning', 'Você deve cadastrar os atributos antes de gerar as variações.<br />As variações só poderão ser incluídas com os atributos definidos.');
    redirectTo(get_url_admin() . '/produto-atributos/list/?context=' . $_GET['context'] . '&reference=' . $_GET['reference']);
    exit;
}

$preQuery = ProdutoVariacaoQuery::create()
        ->filterByProdutoId($_GET['reference'])
        ->filterByIsMaster(false)
        ->addAscendingOrderByColumn(ProdutoVariacaoPeer::ID)
        ->_if($container->getRequest()->request->get('filtro[atributo]', null, true) != '')
        ->useProdutoVariacaoAtributoQuery()
        ->filterByProdutoAtributoId($container->getRequest()->request->get('filtro[atributo]', null, true))
        ->filterByDescricao($container->getRequest()->request->get('filtro[valor]', null, true))
        ->endUse()

        ->_endif();

$rowsPerPage = 999;
include QCOMMERCE_DIR . '/admin/_2015/load.page.php';
