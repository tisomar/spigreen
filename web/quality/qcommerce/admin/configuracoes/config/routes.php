<?php
$list = $container->getSession()->has('last.page.' . $router->getModule())
    ? $container->getSession()->get('last.page.' . $router->getModule())
    : get_url_admin() . '/' . $router->getModule() . '/list';

$config['routes'] = array(
    'list' => get_url_admin() . '/' . $router->getModule() . '/registration/' . $router->getArgument(0),
);