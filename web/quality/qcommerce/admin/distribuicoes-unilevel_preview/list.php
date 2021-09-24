<?php

$pageTitle = 'Visualização Classificação Unilevel';
$_class = DistribuicaoUnilevelPreviewPeer::OM_CLASS;
$_classQuery = 'DistribuicaoUnilevelPreviewQuery';
$_classPeer = 'DistribuicaoUnilevelPreviewPeer';

$typeFilter = '.query';

$preQuery   = DistribuicaoUnilevelPreviewQuery::create()->filterByDistribuicaoId($request->query->get('distribuicao_id'));

include QCOMMERCE_DIR . '/admin/_2015/load.page.php';
