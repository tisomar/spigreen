<?php

$config['routes'] = array(
    'list' => get_url_admin() . '/' . $router->getModule() . '/list/?context=' . $_GET['context'] . '&reference=' . $_GET['reference'],
    'registration' => get_url_admin() . '/' . $router->getModule() . '/registration/?context=' . $_GET['context'] . '&reference=' . $_GET['reference'],
);

$config['routes'][GaleriaPeer::OM_CLASS]['registration'] = get_url_admin() . '/galerias/registration/?id=' . $_GET['reference'];

$config['dimensions'][GaleriaPeer::OM_CLASS] = array(
    "shortDesc" => "Você pode inserir imagens com as dimensões que achar ideal.",
);
