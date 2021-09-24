<?php

use QPress\Breadcrumb\Breadcrumb;

$bc = new Breadcrumb();
$bc->add('Inicio', get_url_admin() . '/dashboard');

if ($context == GaleriaPeer::OM_CLASS) {
    $bc->add('CMS');
    $bc->add('Galeria');
}

$bc->add('Imagens');
$bc->add('Cadastro');
$bc->render();
