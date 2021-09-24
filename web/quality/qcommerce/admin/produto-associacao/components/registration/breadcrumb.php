<?php

use QPress\Breadcrumb\Breadcrumb;

$bc = new Breadcrumb();
$bc->add('Inicio', get_url_admin() . '/dashboard');
$bc->add('Catálogo');
$bc->add('Produtos', get_url_admin() . '/produtos/registration/?id=' . $reference);
$bc->add('Associações', $config['routes']['list']);
$bc->add($pageTitle);
$bc->render();
