<?php

$pageTitle = 'Banners';
$_class = BannerPeer::OM_CLASS;

$preQuery = BannerQuery::create()->orderByTipo();

include QCOMMERCE_DIR . '/admin/_2015/load.page.php';
