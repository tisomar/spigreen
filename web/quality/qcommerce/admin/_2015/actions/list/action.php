<?php

if (!isset($_class)) :
    trigger_error('você deve definir a classe $_class');
endif;

$container->getSession()->set('last.page.' . $router->getModule(), $container->getRequest()->getRequestUri());
$classQueryName = $_class . 'Query';

if (!isset($preQuery)) :
    $preQuery = null;
endif;

$object_peer = $_class::PEER;
$query_builder = $classQueryName::create(null, $preQuery);
$typeFilter = !isset($typeFilter) || $typeFilter != '.query' ? '' : $typeFilter;

include_once QCOMMERCE_DIR . '/admin/_2015/actions/list/filter.basic.action' . $typeFilter . '.php';
//include_once QCOMMERCE_DIR . '/admin/_2015/actions/list/filter.basic.action.query.php';

$rowsPerPage = !isset($rowsPerPage) ? 20 : $rowsPerPage;
$page = $request->query->get('page') ? $request->query->get('page') : 1;
$pager = new QPropelPager($query_builder, $object_peer, 'doSelect', $page, $rowsPerPage);
