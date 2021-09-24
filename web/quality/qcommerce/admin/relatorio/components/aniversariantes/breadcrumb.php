<?php

use QPress\Breadcrumb\Breadcrumb;

$bc = new Breadcrumb();
$bc->add('Inicio', get_url_admin() . '/dashboard');
$bc->add('Relatórios');
$bc->add('Aniversariantes', get_url_admin() . '/relatorio/aniversariantes');
$bc->render();
