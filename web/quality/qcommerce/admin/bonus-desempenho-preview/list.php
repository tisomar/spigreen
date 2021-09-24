<?php

$pageTitle = 'Preview Bônus Desempenho';
$_class = ParticipacaoResultadoClientePeer::OM_CLASS;
$_classQuery = 'ParticipacaoResultadoClienteQuery';
$_classPeer = 'ParticipacaoResultadoClientePeer';

$typeFilter = '.query';

$preQuery   = ParticipacaoResultadoClienteQuery::create()->filterByParticipacaoResultadoId($request->query->get('participacao_resultado_id'));

include QCOMMERCE_DIR . '/admin/_2015/load.page.php';
