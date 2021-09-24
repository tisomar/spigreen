<?php

use QPress\Breadcrumb\Breadcrumb;

$bc = new Breadcrumb();
$bc->add('Inicio', get_url_admin() . '/dashboard');
$bc->add('Relatórios');
$bc->add('Bônus disponíveis', get_url_admin() . '/relatorio/bonus-disponiveis');
$bc->render();
