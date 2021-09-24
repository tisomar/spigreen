<?php

$pageTitle = 'Usuários';
$_class = UsuarioPeer::OM_CLASS;

if (!UsuarioPeer::getUsuarioLogado()->isMaster()) {
    $preQuery = UsuarioQuery::create()->filterByMaster(false);
}

include QCOMMERCE_DIR . '/admin/_2015/load.page.php';
