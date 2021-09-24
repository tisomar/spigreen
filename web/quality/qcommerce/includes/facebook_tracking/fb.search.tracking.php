<?php

$pageEvents = \Config::get('facebook_tracking.busca.events');
$events = \QPress\Facebook\Tracking::getInstance()->getEvents($pageEvents);

include_once __DIR__ . '/fb.view.tracking.php';
