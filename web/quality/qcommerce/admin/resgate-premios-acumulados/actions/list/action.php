<?php

$_class = 'ResgatePremiosAcumulados';

$classQueryName = $_classQuery = $_class . 'Query';
$object_peer    = $_class::PEER;

$preQuery = ResgatePremiosAcumuladosQuery::create()
         ->addDescendingOrderByColumn(sprintf("%s = '%s'", ResgatePremiosAcumuladosPeer::SITUACAO, ResgatePremiosAcumulados::SITUACAO_PENDENTE))
         ->orderByData(Criteria::DESC);
            
$query_builder  = $classQueryName::create(null, $preQuery);

$filters = $request->query->get('filter');

include_once QCOMMERCE_DIR . '/admin/' . $router->getModule() . '/actions/' . $router->getAction() . '/filter.basic.action.php';

$page = $request->query->get('page', 1);
$pager = new QPropelPager($query_builder, $object_peer, 'doSelect', $page, 30);