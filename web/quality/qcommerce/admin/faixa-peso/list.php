<?php

$pageTitle = 'Faixas<span class="hidden-xs"> de Peso</span>';
$_class = TransportadoraFaixaPesoPeer::OM_CLASS;

$preQuery = TransportadoraFaixaPesoQuery::create()->filterByTransportadoraRegiaoId($_GET['reference'])->orderByPeso();

include QCOMMERCE_DIR . '/admin/_2015/load.page.php';
