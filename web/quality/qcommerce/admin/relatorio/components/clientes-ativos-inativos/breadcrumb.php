<?php

use QPress\Breadcrumb\Breadcrumb;

$bc = new Breadcrumb();
$bc->add('Inicio', get_url_admin() . '/dashboard');
$bc->add('Relatórios');
$bc->add('Clientes Ativos/Inativos', get_url_admin() . '/relatorio/clientes-ativos-inativos ');
$bc->render();