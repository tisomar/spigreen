<?php

$pageTitle = 'Centros de Distribuição';

$_class = CentroDistribuicaoPeer::OM_CLASS;
$_classQuery = 'CentroDistribuicaoQuery';
$_classPeer = 'CentroDistribuicaoPeer';
$preQuery = $_classQuery::create()->orderByCep();
$rowsPerPage = 10;

include QCOMMERCE_DIR . '/admin/_2015/load.page.php';
