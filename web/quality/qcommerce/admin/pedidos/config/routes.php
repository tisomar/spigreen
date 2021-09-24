<?php

$list = $container->getSession()->has('last.page.' . $router->getModule())
    ? $container->getSession()->get('last.page.' . $router->getModule())
    : get_url_admin() . '/' . $router->getModule() . '/list';

$config['routes'] = array('list' => $list);

if ($container->getRequest()->query->has('id')) {
    $id = $container->getRequest()->query->get('id');
    $config['routes']['registration'] = get_url_admin() . '/' . $router->getModule() . '/registration/?id=' . $id;
    $config['routes']['update-status'] = get_url_admin() . '/' . $router->getModule() . '/update-status/?id=' . $id;
}

$colorByStatus = array (
    1 => '#EFA131',
    2 => '#2BBCE0',
    3 => '#4F8EDC',
    4 => '#4F8EDC',
    5 => '#16A085',
    6 => '#ddd254',
    7 => '#54416d'
);
