<?php

$pageTitle = 'Classificação Unilevel';
$_class = DistribuicaoUnilevelPeer::OM_CLASS;
$_classQuery = 'DistribuicaoUnilevelQuery';
$_classPeer = 'DistribuicaoUnilevelPeer';

$preQuery   = $_classQuery::create()->orderByData(Criteria::DESC);

include QCOMMERCE_DIR . '/admin/_2015/load.page.php';
