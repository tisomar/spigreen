<?php

$config['routes'] = array(
    'list' => get_url_admin() . '/' . $router->getModule() . '/list/?context=' . $_GET['context'] . '&reference=' . $_GET['reference'],
    'registration' => get_url_admin() . '/' . $router->getModule() . '/registration/?context=' . $_GET['context'] . '&reference=' . $_GET['reference'],
);
