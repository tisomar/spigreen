<?php

$config['routes'] = array(
    'list' => get_url_admin() . '/' . $router->getModule() . '/list/?context=' . $_GET['context'] . '&reference=' . $_GET['reference'] . '&associacao_id=' . $_GET['associacao_id'],
    'registration' => get_url_admin() . '/' . $router->getModule() . '/registration/?context=' . $_GET['context'] . '&reference=' . $_GET['reference'] . '&associacao_id=' . $_GET['associacao_id'],
);
