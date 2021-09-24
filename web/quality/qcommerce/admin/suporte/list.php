<?php

$pageTitle = 'Suporte';
$_class = SuportePeer::OM_CLASS;

$preQuery = SuporteQuery::create()
    ->orderById();

include QCOMMERCE_DIR . '/admin/_2015/load.page.php';
