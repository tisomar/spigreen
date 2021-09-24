<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
if (!isset($_class)) {
     trigger_error('você deve definir a classe $_class');
}

require_once QCOMMERCE_DIR . '/admin/includes/config.inc.php';
require_once QCOMMERCE_DIR . '/admin/includes/security.inc.php';

$container->getSession()->set('last.page.' . $router->getModule(), $container->getRequest()->getRequestUri());

$classQueryName = $_class . 'Query';

if (!isset($preQuery)) {
    $preQuery = null;
}

$preQuery = ClienteQuery::create()->orderByCreatedAt(Criteria::DESC)->orderById(Criteria::DESC);

$object_peer = $_class::PEER;
$query_builder = $classQueryName::create(null, $preQuery);

include_once QCOMMERCE_DIR . '/admin/clientes/actions/list/filter.basic.action.php';

if ($request->query->get('ex') == 'a') {
    $objCliente = ClienteQuery::create()->findOneById($request->query->get('id'));
    $countParameter = Config::get('sistema.cadastro_vago');

    Config::saveParameter('sistema.cadastro_vago', $countParameter + 1);

    $retorno = $objCliente->zerarCadastro($countParameter + 1);

    redirectTo(explode('?', $config['routes']['list'])[0]);
}

$page = $request->query->get('page') ? $request->query->get('page') : 1;
$pager = new QPropelPager($query_builder, $object_peer, 'doSelect', $page);

// Define o campo padrão de adicionar caso nenhum esteja definido
$links = array();

$usuario = UsuarioPeer::getUsuarioLogado();

$countGrupos = PermissaoGrupoUsuarioQuery::create()
    ->filterByUsuarioId($usuario->getId())
    ->filterByGrupoId([5, 7, 8, 9], Criteria::NOT_IN) // Marketing, Logistica, Administrador e informatica
    ->count();

$podeAlterarCliente = $usuario->getId() == 1 || $countGrupos > 0;
