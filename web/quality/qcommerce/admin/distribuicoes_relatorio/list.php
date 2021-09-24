<?php

$pageTitle = 'Relatório Distribuição';
$_class = DistribuicaoClientePeer::OM_CLASS;
$_classQuery = 'DistribuicaoClienteQuery';
$_classPeer = 'DistribuicaoClientePeer';

$typeFilter = '.query';

$preQuery   = DistribuicaoClienteQuery::create()
                ->filterByDistribuicaoId($request->query->get('distribuicao_id'))
                ->filterByNomeCliente();

include QCOMMERCE_DIR . '/admin/_2015/load.page.php';
