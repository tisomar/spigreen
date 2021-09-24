<?php

$pageTitle = 'Categorias';
$_class = CategoriaPeer::OM_CLASS;
$_classQuery = 'CategoriaQuery';
$_classPeer = 'CategoriaPeer';

$preQuery   = $_classQuery::create()->filterByNrLvl(0, Criteria::GREATER_THAN)->orderByNrLft();
$rowsPerPage = 50;

include QCOMMERCE_DIR . '/admin/_2015/load.page.php';
