<?php

use QPress\Breadcrumb\Breadcrumb;

$bc = new Breadcrumb();
$bc->add('Inicio', get_url_admin() . '/dashboard');
$bc->add('Clientes');
$bc->add('Distribuições Bônus Produtos', get_url_admin() . '/' . 'bonus-produtos' . '/list');
$bc->add('Relatório Bônus Produtos');
$bc->render();
