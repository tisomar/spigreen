<?php

use QPress\Breadcrumb\Breadcrumb;

$bc = new Breadcrumb();
$bc->add('Inicio', get_url_admin() . '/dashboard');
$bc->add('Clientes');
$bc->add('Distribuições', get_url_admin() . '/' . 'distribuicoes' . '/list');
$bc->add('Preview Distribuição');
$bc->render();
