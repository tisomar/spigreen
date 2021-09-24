<?php

use QPress\Breadcrumb\Breadcrumb;

$bc = new Breadcrumb();
$bc->add('Inicio', get_url_admin() . '/dashboard');
$bc->add('Configurações');
$bc->add('Usuários', $config['routes']['list']);
$bc->add('Cadastro');
$bc->render();
