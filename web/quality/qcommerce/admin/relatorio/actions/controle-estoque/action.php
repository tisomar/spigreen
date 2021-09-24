<?php

$_class = 'EstoqueProduto';
$query = EstoqueProdutoQuery::create();

if ($container->getRequest()->query->has('sql_estoque')) {
    parse_str($container->getRequest()->query->get('sql_estoque'), $output);

    foreach ($output as $phpName => $value) {
        $value = trim($value);
        $methodName = 'filterBy' . $phpName;

        if ($value === '' || $value === null || !method_exists('EstoqueProdutoQuery', $methodName)) {
            continue;
        }

        $query->$methodName($value);
    }
}

$classQueryName = $_classQuery = $_class . 'Query';
$object_peer    = $_class::PEER;

$query_builder  = $classQueryName::create(null, $query);

include_once QCOMMERCE_DIR . '/admin/relatorio/actions/controle-estoque/filter.basic.action.query.php';

$page = $request->query->get('page', 1);
$pager = new QPropelPager($query_builder, $object_peer, 'doSelect', $page, 30);
