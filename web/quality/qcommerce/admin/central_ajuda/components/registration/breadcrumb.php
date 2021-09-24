<?php

use QPress\Breadcrumb\Breadcrumb;

$bc = new Breadcrumb();
$bc->add('Inicio', get_url_admin() . '/dashboard');
$bc->add('Conteúdos');
$bc->add('Central de Ajuda', get_url_admin() . '/' . $router->getModule() . '/list');
$bc->add('Cadastrar Vídeo Central de Ajuda');
$bc->render();
