<?php

use QPress\Breadcrumb\Breadcrumb;

$bc = new Breadcrumb();
$bc->add('Inicio', get_url_admin() . '/dashboard');
$bc->add('Clientes');
$bc->add('Solicitações de Resgate', $config['routes']['list']);
$bc->add('Cadastro');
$bc->render();
