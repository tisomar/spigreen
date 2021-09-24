<?php

use QPress\Breadcrumb\Breadcrumb;

$bc = new Breadcrumb();
$bc->add('Inicio', get_url_admin() . '/dashboard');
$bc->add('Conteúdos');
$bc->add('Alertas e Mensagens', get_url_admin() . '/' . $router->getModule() . '/list');
$bc->add('Cadastro');
$bc->render();
