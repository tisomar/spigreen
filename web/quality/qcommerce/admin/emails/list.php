<?php

$pageTitle = 'E-mails disparados';
$_class = EmailLogPeer::OM_CLASS;

$preQuery = EmailLogQuery::create()->orderById(Criteria::DESC);

include QCOMMERCE_DIR . '/admin/_2015/load.page.php';
