<?php

use QPress\Breadcrumb\Breadcrumb;

$bc = new Breadcrumb();
$bc->add('Inicio', get_url_admin() . '/dashboard');
$bc->add('Catálogo');
$bc->add('Combo', get_url_admin() . '/' . $router->getModule() . '/list');
$bc->add('Cadastro');
$bc->render();
