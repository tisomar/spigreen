<?php

$pageTitle = 'Clientes';
$_class = ClientePeer::OM_CLASS;
$_classQuery = 'ClienteQuery';

$preQuery = $_classQuery::create()->orderByCreatedAt(Criteria::DESC)->orderByNome()->orderByNomeFantasia();

include QCOMMERCE_DIR . '/admin/_2015/load.page.php';
