<?php

$pageTitle = 'Cupom de Desc<span class="hidden-xs">onto</span>';
$_class = CupomPeer::OM_CLASS;
$preQuery = CupomQuery::create()->orderByDataInicial(Criteria::DESC);

include QCOMMERCE_DIR . '/admin/_2015/load.page.php';
