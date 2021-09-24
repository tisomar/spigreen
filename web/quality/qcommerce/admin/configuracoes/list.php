<?php
$pg  = ParametroGrupoQuery::create()->findOneByAlias($args[0]);
$pageTitle = $pg ? $pg->getNome() : 'Configurações';
$_class = ParametroPeer::OM_CLASS;

if (!isset($args[0])) {
    exit('argumento inválido');
}

$preQuery = ParametroQuery::create()
    ->_if(!UsuarioPeer::getUsuarioLogado()->isMaster())
        ->filterByIsConfiguracaoSistema(0)
    ->_endif()
    ->useParametroGrupoQuery()
        ->filterByAlias($args[0])
    ->endUse()
    ->orderByIsConfiguracaoSistema()
    ->orderByOrdem(Criteria::ASC)
;


$rowsPerPage = 9999;

include QCOMMERCE_DIR . '/admin/_2015/load.page.php';
