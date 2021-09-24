<?php

use QPress\Breadcrumb\Breadcrumb;

$bc = new Breadcrumb();
$bc->add('Inicio', get_url_admin() . '/dashboard');
$bc->add('Relatórios');
$bc->add('Pedidos para faturamento', get_url_admin() . '/relatorio/vendas-pontos');
$bc->render();
