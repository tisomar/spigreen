<?php
/**
 * Created by PhpStorm.
 * User: Giovan
 * Date: 17/12/2018
 * Time: 11:40
 */

$franqueadoNoValid = false;
$isNewReseller = false;

if ($container->getRequest()->query->has('noFranqueado')) {
    $franqueadoNoValid = true;
}



if (!$franqueadoNoValid && !ClientePeer::isAuthenticad()) {
    $container->getSession()->set('resellerActive', true);
    redirect('/login/verify-access');
} elseif (!$franqueadoNoValid && ClientePeer::isAuthenticad() && ClientePeer::getClienteLogado(true)->getTipoConsumidor() == 0) {

    /** @var Produto $produtoTaxa */
    /** @var Cliente $cliente */

    $container->getSession()->remove('fromFranqueado');
    $container->getSession()->remove('slugFranqueado');

    $cliente = ClientePeer::getClienteLogado();

    $container->getSession()->set('resellerLoggedActive', true);

    $coll = CategoriaQuery::create()
        ->_if(Config::get('mostrar_todas_categorias') == 0)
        ->add('1', CategoriaPeer::queryCategoriasComProdutosAtivos(), Criteria::CUSTOM)
        ->addOr('2', CategoriaPeer::queryProdutosAtivos(), Criteria::CUSTOM)
        ->_endif()
        ->filterByCombo(true)

        ->filterByParentDisponivel(true)
        ->filterByDisponivel(true)
        ->filterByNrLvl(array('min' => 1, 'max' => 2))
        ->orderByNrLft()
        ->findOne();

    if ($cliente->getTaxaCadastro()) {
        $produtoTaxa = ProdutoPeer::retrieveByPK(ProdutoPeer::PRODUTO_TAXA_ID);
        $container->getCarrinhoProvider()->save();
        $carrinho = $container->getCarrinhoProvider()->getCarrinho();
        ProdutoVariacaoPeer::addProdutoTaxaCadastroToCart($container, $produtoTaxa);
        $carrinho->save();
    }

    if ($coll) {
        redirect('/produtos/' . $coll->getSlug() . '/');
    } else {
        redirect('/home/');
    }
}

redirect('/login/verify-access');
