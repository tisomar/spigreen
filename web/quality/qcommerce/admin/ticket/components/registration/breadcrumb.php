<?php

use QPress\Breadcrumb\Breadcrumb;

$bc = new Breadcrumb();
$bc->add('Inicio', get_url_admin() . '/dashboard');
$bc->add('Ticket', $config['routes']['list']);
$bc->add('Cadastro');
$bc->render();
