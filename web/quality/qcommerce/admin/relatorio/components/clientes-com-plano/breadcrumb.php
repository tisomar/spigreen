<?php

use QPress\Breadcrumb\Breadcrumb;

$bc = new Breadcrumb();
$bc->add('Inicio', get_url_admin() . '/dashboard');
$bc->add('Relatórios');
$bc->add('Clientes com Plano', get_url_admin() . '/relatorio/clientes-com-plano');
$bc->render();
