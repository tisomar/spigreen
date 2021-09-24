<?php

use QPress\Breadcrumb\Breadcrumb;

$bc = new Breadcrumb();
$bc->add('Inicio', get_url_admin() . '/dashboard');
$bc->add('Estoque', $config['routes']['list']);
$bc->add('Alterar Estoque');
$bc->render();
