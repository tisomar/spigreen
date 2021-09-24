<?php

use QPress\Breadcrumb\Breadcrumb;

$bc = new Breadcrumb();
$bc->add('Inicio', get_url_admin() . '/dashboard');
$bc->add('Promoções');
$bc->add('Cupom de desconto', get_url_admin() . '/' . $router->getModule() . '/list');
$bc->add('Cadastro');
$bc->render();
