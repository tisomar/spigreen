<?php

$pageTitle = 'Módulos';
$_class = PermissaoModuloPeer::OM_CLASS;
$preQuery   = PermissaoModuloQuery::create()->orderByTreeLeft()->filterByTreeLevel(0, Criteria::GREATER_THAN);
$rowsPerPage = 9999;
include QCOMMERCE_DIR . '/admin/_2015/load.page.php';
