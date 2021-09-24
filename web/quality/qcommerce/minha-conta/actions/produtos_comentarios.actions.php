<?php

// Removendo um comentário da listagem de Meus Comentários da Central do Cliente
if (!empty($_GET['remove-avaliacao'])) {
    $comentarioRemoverId = (int)$_GET['remove-avaliacao'];
    
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

// Listando comentários de produtos deste cliente
$arrComentarios = ProdutoComentarioQuery::create()
                    ->filterByClienteId(ClientePeer::getClienteLogado()->getId())
                    ->orderByData(Criteria::DESC)
                    ->find();
