<?php

$pageTitle = 'Bônus Aceleração';
$_class = ParticipacaoResultadoPeer::OM_CLASS;
$_classQuery = 'ParticipacaoResultadoQuery';
$_classPeer = 'ParticipacaoResultadoPeer';

$preQuery   = ParticipacaoResultadoQuery::create()->filterByTipo(ParticipacaoResultado::TIPO_ACELERACAO)->orderByData(Criteria::DESC);

include QCOMMERCE_DIR . '/admin/_2015/load.page.php';
