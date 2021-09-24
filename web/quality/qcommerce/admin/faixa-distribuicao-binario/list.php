<?php

$pageTitle = 'Faixas Distribuição Binária';

$_class = FaixasDistribuicaoBinariaPeer::OM_CLASS;
$_classQuery = $_class .  'Query';
$_classPeer = $_class .  'Peer';

$typeFilter = '.query';

$preQuery = FaixasDistribuicaoBinariaQuery::create()
                ->orderByPlanoId();

include QCOMMERCE_DIR . '/admin/_2015/load.page.php';
