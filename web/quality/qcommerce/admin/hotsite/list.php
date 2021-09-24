<?php

$pageTitle = 'Hotsites';
$_class = HotsitePeer::OM_CLASS;
$_classQuery = 'HotsiteQuery';
$_classPeer = 'HotsitePeer';

$preQuery   = $_classQuery::create();
$rowsPerPage = 30;

include QCOMMERCE_DIR . '/admin/_2015/load.page.php';
