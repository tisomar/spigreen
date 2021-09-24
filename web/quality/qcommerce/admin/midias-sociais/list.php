<?php

$pageTitle = 'Midias Sociais';
$_class = RedePeer::OM_CLASS;

$preQuery = RedeQuery::create()->orderByNome();

include QCOMMERCE_DIR . '/admin/_2015/load.page.php';
