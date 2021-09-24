<?php

if (!isset($_class)) {
    trigger_error('você deve definir a classe $_class');
}

$container->getSession()->set('last.page.' . $router->getModule(), $container->getRequest()->getRequestUri());

$classQueryName = $_class . 'Query';

if (!isset($preQuery)) {
    $preQuery = null;
}

$object_peer = $_class::PEER;
$query_builder = $classQueryName::create(null, $preQuery);

include_once QCOMMERCE_DIR . '/admin/' . $router->getModule() . '/actions/' . $router->getAction() . '/filter.basic.action.php';

$page = $request->query->get('page') ? $request->query->get('page') : 1;
$pager = new QPropelPager($query_builder, $object_peer, 'doSelect', $page);

$usuario = UsuarioPeer::getUsuarioLogado();

// Se o usuario estiver no grupo de marketing
$idGruposSelecionados = PermissaoGrupoUsuarioQuery::create()->select(array('GrupoId'))->filterByUsuarioId($usuario->getId())->find()->toArray();
$isBlockGroup = false;
if(in_array(7, $idGruposSelecionados) || in_array(9, $idGruposSelecionados) || in_array(5, $idGruposSelecionados)) :
    $isBlockGroup = true;
endif;

$isFinanceGroup = false;
if(in_array(6, $idGruposSelecionados) || in_array(1, $idGruposSelecionados)) :
    $isFinanceGroup = true;
endif;

$countGrupos = PermissaoGrupoUsuarioQuery::create()
    ->filterByUsuarioId($usuario->getId())
    ->filterByGrupoId([5, 6, 7, 8, 9], Criteria::NOT_IN) // Marketing e Logistica
    ->count();

$podeCancelarPedido = $usuario->getId() == 1 || $countGrupos > 0;
$podeAlterarCliente = $usuario->getId() == 1 || $countGrupos > 0;
