<?php

$pageTitle = 'Estoque';
$_class = EstoqueProdutoPeer::OM_CLASS;
$_classQuery = 'EstoqueProdutoQuery';

$preQuery = $_classQuery::create()->orderByData(Criteria::DESC);

include QCOMMERCE_DIR . '/admin/_2015/load.page.php';
