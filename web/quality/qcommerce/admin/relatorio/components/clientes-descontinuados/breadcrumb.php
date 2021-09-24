<?php

use QPress\Breadcrumb\Breadcrumb;

$bc = new Breadcrumb();
$bc->add('Inicio', get_url_admin() . '/dashboard');
$bc->add('Relatórios');
$bc->add('Clientes Descontinuados', get_url_admin() . '/relatorio/clientes-descontinuados');
$bc->render();
