<?php

// Removendo um comentário da listagem de Meus Comentários da Central do Cliente
if (!empty($_GET['remove-avaliacao'])) {
    $comentarioRemoverId = (int) $_GET['remove-avaliacao'];

    $produtoRemover = ProdutoComentarioQuery::create()
            ->filterByClienteId(ClientePeer::getClienteLogado()->getId())
            ->filterById($comentarioRemoverId)
            ->findOne();

    if ($produtoRemover) {
        $produtoRemover->delete();
        FlashMsg::success('Comentário removido com sucesso.');
    } else {
        FlashMsg::danger('Não encontramos o comentário para ser deletado, talvez ele já tenha sido removido.');
    }

    redirectTo(ROOT_PATH . '/minha-conta/avaliacoes/');
    exit;
}

$page = is_numeric($router->getArgument(0)) && (int)$router->getArgument(0) > 0 ? $router->getArgument(0) : 1;

// Listando comentários de produtos deste cliente
$arrComentarios = ProdutoComentarioQuery::create()
        ->joinWith('ProdutoComentario.Produto')
        ->filterByClienteId(ClientePeer::getClienteLogado()->getId())
        ->orderByData(Criteria::DESC)
        ->paginate($page, 5);
