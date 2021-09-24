<?php

$pageTitle = 'Ticket';
$_class = TicketPeer::OM_CLASS;
$_classQuery = 'TicketQuery';

$preQuery = $_classQuery::create()->orderByData(Criteria::DESC)->orderByCategoria();

include QCOMMERCE_DIR . '/admin/_2015/load.page.php';
