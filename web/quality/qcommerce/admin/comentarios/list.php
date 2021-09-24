<?php

$pageTitle = 'Comentários';
$_class = ProdutoComentarioPeer::OM_CLASS;

$preQuery   = ProdutoComentarioQuery::create()
    ->orderByStatus(Criteria::DESC)
    ->orderByData(Criteria::DESC)
    ->orderById(Criteria::DESC)
    ->joinWith('ProdutoComentario.Produto')
    ->useProdutoQuery()
        ->filterByDataExclusao(null, Criteria::ISNULL)
    ->endUse();

include QCOMMERCE_DIR . '/admin/_2015/load.page.php';
