<?php

use QPress\Breadcrumb\Breadcrumb;

$bc = new Breadcrumb();
$bc->add('Inicio', get_url_admin() . '/dashboard');
$bc->add('Transportadora');
$bc->add('Região');
$bc->add('Faixas de Peso');
$bc->add('Cadastro');
$bc->render();
