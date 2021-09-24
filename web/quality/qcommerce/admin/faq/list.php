<?php

$pageTitle = 'FAQ';
$_class = FaqPeer::OM_CLASS;

$preQuery = FaqQuery::create()
    ->orderByDataPergunta(Criteria::DESC);

include QCOMMERCE_DIR . '/admin/_2015/load.page.php';
