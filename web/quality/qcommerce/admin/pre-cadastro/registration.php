<?php

$pageTitle = 'Configurações Pré Cadastro';
$_class = ParametroPeer::OM_CLASS;
$_classQuery = 'ParametroQuery';
$_classPeer = 'ParametroPeer';

$typeFilter = '.query';

$preQuery   = ParametroQuery::create()->filterByParametroGrupoId(52);

include QCOMMERCE_DIR . '/admin/_2015/load.page.php';
