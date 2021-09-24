<?php

/**
 * Eventos das páginas em geral
 */
$pageEvents = \Config::get('facebook_tracking.todas_as_paginas.events');
$events = \QPress\Facebook\Tracking::getInstance()->getEvents($pageEvents);

include_once __DIR__ . '/fb.view.tracking.php';
