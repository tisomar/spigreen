<?php

use QPress\Breadcrumb\Breadcrumb;

$bc = new Breadcrumb();
$bc->add('Inicio', get_url_admin() . '/dashboard');
$bc->add('Catálogo');
$bc->add('Produtos');
$bc->add('Uol Shopping');
$bc->add('Cadastro');
$bc->render();
