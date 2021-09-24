<?php

if ($container->getRequest()->isXmlHttpRequest()) {
    header("Content-Type: application/json");
}

$quantidade_pv = $container->getRequest()->query->get('quantidade_pv', array());

if (count($quantidade_pv)) {
    $response = array('status' => 'success');
} else {
    FlashMsg::danger('Você precisa selecionar as variações dos produtos para comprá-los juntos!');
    $response = array('status' => 'error');
}

if ($container->getRequest()->isXmlHttpRequest()) {
    die(json_encode($response));
}
