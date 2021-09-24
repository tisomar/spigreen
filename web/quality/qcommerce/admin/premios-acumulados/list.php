<?php

$pageTitle = 'Prêmios acumulados';

$_class = PremiosAcumuladosPeer::OM_CLASS;
$_classQuery = $_class .  'Query';
$_classPeer = $_class .  'Peer';

$typeFilter = '.query';

$preQuery = PremiosAcumuladosQuery::create()
                ->orderByPontosResgate();

include QCOMMERCE_DIR . '/admin/_2015/load.page.php';
