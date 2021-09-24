<?php

use QPress\Breadcrumb\Breadcrumb;

$bc = new Breadcrumb();
$bc->add('Inicio', get_url_admin() . '/dashboard');
$bc->add('Clientes');
$bc->add('Bônus Aceleração', get_url_admin() . '/' . 'bonus-aceleracao' . '/list');
$bc->add('Preview Distribuição');
$bc->render();
