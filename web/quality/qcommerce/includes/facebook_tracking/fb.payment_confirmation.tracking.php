<?php

$purchaseArgs = array();
if (isset($objPedido)) {
    $purchaseArgs = array(
        'currency'  => 'BRL',
        'value'     => number_format($objPedido->getValorTotal(), 2, '.', '')
    );
}

$pageEvents = \Config::get('facebook_tracking.confirmacao_pagamento.events', $purchaseArgs);
$events = \QPress\Facebook\Tracking::getInstance()->getEvents($pageEvents);

include_once __DIR__ . '/fb.view.tracking.php';
