<?php

// Removendo um produto da listagem de Produtos Visitados (removido pelo cliente logado)
if (!empty($_GET['remove-visita'])) {
    $visitaRemoverId = (int)$_GET['remove-visita'];
    
    $produtoRemover = ProdutoVisitadoQuery::create()
                        ->filterByClienteId(ClientePeer::getClienteLogado()->getId())
                        ->filterById($visitaRemoverId)
                        ->findOne();
    
    if ($produtoRemover) {
        $produtoRemover->delete();
        FlashMsg::success('Produto visitado removido com sucesso do seu histórico.');
    } else {
        FlashMsg::danger('Não encontramos o produto visitado para ser deletado, talvez ele já tenha sido removido.');
    }
    
    redirectTo(ROOT_PATH . '/minha-conta/visitados/');
    exit;
}

// Removendo todos os produtos visitados deste cliente
if (!empty($_GET['limpar-produtos-visitados'])) {
    ProdutoVisitadoQuery::create()
        ->filterByClienteId(ClientePeer::getClienteLogado()->getId())
        ->delete();
    
    FlashMsg::success('Todos os produtos visitados foram removidos com sucesso do seu histórico.');
    
    redirectTo(ROOT_PATH . '/minha-conta/visitados/');
    exit;
}

// Listando produtos visitados do cliente logado
$arrVisitados = ProdutoVisitadoQuery::create()
        ->filterByClienteId(ClientePeer::getClienteLogado()->getId())
        ->orderByDataVisitado(Criteria::DESC)
        ->setLimit(20)
        ->find();
