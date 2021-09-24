<?php

$pageTitle = 'Pedidos';

$_class = PedidoPeer::OM_CLASS;
$_classQuery = $_class .  'Query';
$_classPeer = $_class .  'Peer';

$filterAction = 'query';

$preQuery = $_classQuery::create()->orderByCreatedAt(Criteria::DESC)->orderById(Criteria::DESC)->filterByClassKey(1);

include QCOMMERCE_DIR . '/admin/_2015/load.page.php';
