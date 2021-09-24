<?php

$pageTitle = 'Estado e Centro de Distribuição';

$_class = EstadoCentroDistribuicaoPeer::OM_CLASS;
$_classQuery = 'EstadoCentroDistribuicaoQuery';

$preQuery   = $_classQuery::create();
$rowsPerPage = 50;

include QCOMMERCE_DIR . '/admin/_2015/load.page.php';
