<?php

use QPress\Breadcrumb\Breadcrumb;

$bc = new Breadcrumb();
$bc->add('Inicio', get_url_admin() . '/dashboard');
$bc->add('Relatórios');
$bc->add('Alteração de Rede', get_url_admin() . '/relatorio/alteracao-rede');
$bc->render();
