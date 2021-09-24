<?php

use QPress\Breadcrumb\Breadcrumb;

$bc = new Breadcrumb();
$bc->add('Inicio', get_url_admin() . '/dashboard');
$bc->add('Clientes');
$bc->add('Bônus Aceleração', get_url_admin() . '/' . $router->getModule() . '/list');
$bc->add('Cadastro');
$bc->render();
