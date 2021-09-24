<?php
$pageTitle = 'Desconto Fidelidade';

$_class = DescontoFidelidadePeer::OM_CLASS;
$_classQuery = $_class .  'Query';
$_classPeer = $_class .  'Peer';

//$preQuery = PlanoQuery::create()
//        ->useProdutoRelatedByProdutoIdQuery(null, Criteria::LEFT_JOIN)
//            ->useProdutoVariacaoQuery(null, Criteria::LEFT_JOIN)
//                ->orderByValorBase()
//            ->endUse()
//        ->endUse();

include_once QCOMMERCE_DIR . '/admin/_2015/load.page.php';
