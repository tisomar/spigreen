<?php

use QPress\Breadcrumb\Breadcrumb;

$bc = new Breadcrumb();
$bc->add('Inicio', get_url_admin() . '/dashboard');
$bc->add('Distribuição Binária');
$bc->add('Faixas Distribuição Binária');
$bc->render();
