<?php

$pageTitle = 'Solicitações de Resgate';

$_class = ResgatePeer::OM_CLASS;
$_classQuery = $_class .  'Query';
$_classPeer = $_class .  'Peer';

$typeFilter = '.query';

$preQuery = ResgateQuery::create()
                ->addDescendingOrderByColumn(sprintf("%s = '%s'", ResgatePeer::SITUACAO, Resgate::SITUACAO_PENDENTE))
                ->orderByData(Criteria::DESC);

include QCOMMERCE_DIR . '/admin/_2015/load.page.php';
