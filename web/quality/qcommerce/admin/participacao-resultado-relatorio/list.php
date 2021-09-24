<?php

$pageTitle = 'Relatório Bônus Destaque';
$_class = ParticipacaoResultadoClientePeer::OM_CLASS;
$_classQuery = 'ParticipacaoResultadoClienteQuery';
$_classPeer = 'ParticipacaoResultadoClientePeer';

$typeFilter = '.query';

$preQuery   = ParticipacaoResultadoClienteQuery::create()->filterByParticipacaoResultadoId($request->query->get('participacao_resultado_id'));
$preQuery->filterByTotalPontos(0, Criteria::GREATER_THAN);

include QCOMMERCE_DIR . '/admin/_2015/load.page.php';
