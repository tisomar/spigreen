<?php

use QPress\Breadcrumb\Breadcrumb;

$bc = new Breadcrumb();
$bc->add('Inicio', get_url_admin() . '/dashboard');
$bc->add('Relatórios');
$bc->add('Rede de Clientes', get_url_admin() . '/relatorio/cliente-rede');
$bc->render();
