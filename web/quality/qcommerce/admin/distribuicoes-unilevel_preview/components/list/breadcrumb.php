<?php

use QPress\Breadcrumb\Breadcrumb;

$bc = new Breadcrumb();
$bc->add('Inicio', get_url_admin() . '/dashboard');
$bc->add('Clientes');
$bc->add('Classificação Unilevel', get_url_admin() . '/' . 'distribuicoes-unilevel' . '/list');
$bc->add('Visualização Classificação Unilevel');
$bc->render();
