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

$preQuery = EstoqueProdutoQuery::create()->orderByData(Criteria::DESC);

$object_peer = $_class::PEER;
$query_builder = $classQueryName::create(null, $preQuery);

$filters = $request->query->get('filter');

if (!isset($filters['DataDe']) || !$filters['DataDe']) {
    $dataInicialPadrao = new \DateTime('first day of this month');

    if (!is_array($filters)) {
        $filters = array();
    }

    $filters['DataDe'] = $dataInicialPadrao->format('d/m/Y');
    $request->query->set('filter', $filters);
    $request->query->set('is_filter', true);
}

if (!isset($filters['DataAte']) || !$filters['DataAte']) {
    $dataFinalPadrao = new \DateTime('today');

    if (!is_array($filters)) {
        $filters = array();
    }

    $filters['DataAte'] = $dataFinalPadrao->format('d/m/Y');
    $request->query->set('filter', $filters);
    $request->query->set('is_filter', true);
}

include_once QCOMMERCE_DIR . '/admin/estoque/actions/list/filter.basic.action.php';

$page = $request->query->get('page') ? $request->query->get('page') : 1;
$pager = new QPropelPager($query, $object_peer, 'doSelect', $page);

// Define o campo padrão de adicionar caso nenhum esteja definido
$links = array();

$userLogado = UsuarioPeer::getUsuarioLogado()->getId();
$grupoAdmin = PermissaoGrupoUsuarioQuery::create()
    ->filterByUsuarioId($userLogado)
    ->filterByGrupoId(1, Criteria::EQUAL) // Marketing e Logistica
    ->count();