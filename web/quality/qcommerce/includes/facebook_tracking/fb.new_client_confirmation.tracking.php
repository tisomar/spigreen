<?php

$pageEvents = \Config::get('facebook_tracking.confirmacao_cliente.events');
$events = \QPress\Facebook\Tracking::getInstance()->getEvents($pageEvents);

include_once 'fb.view.tracking.php';
