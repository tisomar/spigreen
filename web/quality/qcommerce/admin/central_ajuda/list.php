<?php

$pageTitle = 'Central de Ajuda';
$_class = AjudaPaginaVideoPeer::OM_CLASS;
$_classQuery = 'AjudaPaginaVideoQuery';
$_classPeer = 'AjudaPaginaVideoPeer';

$preQuery   = $_classQuery::create();
$rowsPerPage = 50;

include QCOMMERCE_DIR . '/admin/_2015/load.page.php';
