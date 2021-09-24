<?php

use QPress\Breadcrumb\Breadcrumb;

$bc = new Breadcrumb();
$bc->add('Inicio', get_url_admin() . '/dashboard');
$bc->add('Relatórios');
$bc->add('Solicitação Resgate', get_url_admin() . '/relatorio/solicitacao-resgate');
$bc->render();
