<?php

use QPress\Breadcrumb\Breadcrumb;

$bc = new Breadcrumb();
$bc->add('Inicio', get_url_admin() . '/dashboard');
$bc->add('Relatório');
$bc->add('Clientes por Cidade', get_url_admin() . '/relatorio/clientes-por-cidade');
$bc->render();
