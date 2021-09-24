<?php
$list = $container->getSession()->has('last.page.' . $router->getModule())
        ? $container->getSession()->get('last.page.' . $router->getModule())
        : get_url_admin() . '/' . $router->getModule() . '/list';

$config['routes'] = array(
    'list' => $list,
    'registration' => get_url_admin() . '/' . $router->getModule() . '/registration',
);
