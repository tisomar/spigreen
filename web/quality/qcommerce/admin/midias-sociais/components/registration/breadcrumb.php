<?php

use QPress\Breadcrumb\Breadcrumb;

$bc = new Breadcrumb();
$bc->add('Inicio', get_url_admin() . '/dashboard');
$bc->add('Marketing');
$bc->add('Midias Sociais', get_url_admin() . '/' . $router->getModule() . '/list');
$bc->add('Cadastro');
$bc->render();
