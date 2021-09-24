<?php

$pageTitle = 'Bônus Destaque';
$_class = ParticipacaoResultadoPeer::OM_CLASS;
$_classQuery = 'ParticipacaoResultadoQuery';
$_classPeer = 'ParticipacaoResultadoPeer';

$preQuery   = ParticipacaoResultadoQuery::create()->filterbyTipo(ParticipacaoResultado::TIPO_DESTAQUE)->orderByData(Criteria::DESC);

include QCOMMERCE_DIR . '/admin/_2015/load.page.php';
