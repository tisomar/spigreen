<?php

use QPress\Breadcrumb\Breadcrumb;

$bc = new Breadcrumb();
$bc->add('Inicio', get_url_admin() . '/dashboard');
$bc->add('Clientes');
$bc->add('Bônus Destaque', get_url_admin() . '/' . 'participacao-resultado' . '/list');
$bc->add('Relatório Distribuição');
$bc->render();
