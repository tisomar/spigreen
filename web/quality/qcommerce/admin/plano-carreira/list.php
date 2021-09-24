<?php

$pageTitle = 'Plano de Carreira';

$_class = PlanoCarreiraPeer::OM_CLASS;
$_classQuery = $_class .  'Query';
$_classPeer = $_class .  'Peer';

$typeFilter = '.query';

$preQuery = PlanoCarreiraQuery::create()
                ->orderByPontos();

include QCOMMERCE_DIR . '/admin/_2015/load.page.php';
