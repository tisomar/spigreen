<?php

$pageEvents = \Config::get('facebook_tracking.inicio_processo_pagamento.events');
$events = \QPress\Facebook\Tracking::getInstance()->getEvents($pageEvents);

include_once __DIR__ . '/fb.view.tracking.php';
