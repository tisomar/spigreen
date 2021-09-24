<?php

$pageTitle = 'Distribuições';
$_class = DistribuicaoPeer::OM_CLASS;
$_classQuery = 'DistribuicaoQuery';
$_classPeer = 'DistribuicaoPeer';

$preQuery   = DistribuicaoQuery::create()->orderByData(Criteria::DESC);

include QCOMMERCE_DIR . '/admin/_2015/load.page.php';
