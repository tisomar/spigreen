<?php

use QPress\Breadcrumb\Breadcrumb;

$bc = new Breadcrumb();
$bc->add('Inicio', get_url_admin() . '/dashboard');
$bc->add('Vendas');
$bc->add('Pedidos', $config['routes']['list']);
$bc->add('Detalhes');
$bc->render();
