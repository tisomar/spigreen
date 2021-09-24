<?php

use QPress\Breadcrumb\Breadcrumb;

$bc = new Breadcrumb();
$bc->add('Inicio', get_url_admin() . '/dashboard');
$bc->add('Relatório');
$bc->add('Controle estoque', get_url_admin() . '/relatorio/controle-estoque');
$bc->render();
