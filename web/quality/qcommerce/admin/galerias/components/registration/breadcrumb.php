<?php

use QPress\Breadcrumb\Breadcrumb;

$bc = new Breadcrumb();
$bc->add('Inicio', get_url_admin() . '/dashboard');
$bc->add('CMS');
$bc->add('Galerias', $config['routes']['list']);
$bc->add('Cadastro');
$bc->render();
