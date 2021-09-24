<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

$usuario = UsuarioPeer::getUsuarioLogado();

$gruposUser = PermissaoGrupoUsuarioQuery::create()
    ->filterByUsuarioId($usuario->getId())
    ->find();

$grupoIds = [];
foreach($gruposUser as $grupos) :
    $grupoIds[] = $grupos->getGrupoID();
endforeach;

if (!isset($_class)) {
     trigger_error('você deve definir a classe $_class');
}

require_once QCOMMERCE_DIR . '/admin/includes/config.inc.php';
require_once QCOMMERCE_DIR . '/admin/includes/security.inc.php';

$preQuery = TicketQuery::create()->filterByGrupoId($grupoIds)->orderByData(Criteria::DESC);

if(in_array(1, $grupoIds) || in_array(9, $grupoIds)) :
    $preQuery = TicketQuery::create()->orderByData(Criteria::DESC);
endif;

$container->getSession()->set('last.page.' . $router->getModule(), $container->getRequest()->getRequestUri());
$classQueryName = $_class . 'Query';

if (!isset($preQuery)) {
    $preQuery = null;
}

$object_peer = $_class::PEER;
$query_builder = $classQueryName::create(null, $preQuery);

include_once QCOMMERCE_DIR . '/admin/ticket/actions/list/filter.basic.action.php';

$page = $request->query->get('page') ? $request->query->get('page') : 1;
$pager = new QPropelPager($query_builder, $object_peer, 'doSelect', $page);

// Define o campo padrão de adicionar caso nenhum esteja definido
$links = array();

if ($request->query->get('ex') == 'a') {
    $objTicket = TicketQuery::create()->findOneById($request->query->get('id'));
    $objTicket->delete();
    redirectTo(explode('?', $config['routes']['list'])[0]);
}

$listaClientesQuery = ClienteQuery::create()
    ->select(['ID', 'NOME'])
    ->withColumn(ClientePeer::ID, 'ID')
    ->withColumn(
        sprintf(
            'IF(%s IS NOT NULL, %s, %s)',
            ClientePeer::CNPJ,
            ClientePeer::RAZAO_SOCIAL,
            ClientePeer::NOME
        ),
        'NOME'
    )
    ->filterByVago(0)
    ->addAscendingOrderByColumn('NOME')
    ->find()
    ->toArray();

$listaClientes = [
    '' => 'Selecione o cliente'
];

foreach ($listaClientesQuery as $cliente) :
    $listaClientes[$cliente['ID']] = $cliente['NOME'];
endforeach;
