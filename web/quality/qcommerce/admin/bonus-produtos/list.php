<?php

$pageTitle = 'Distribuições Bônus Produtos';
$_class = DistribuicaoBonusProdutosPeer::OM_CLASS;
$_classQuery = 'DistribuicaoBonusProdutosQuery';
$_classPeer = 'DistribuicaoBonusProdutosPeer';

$preQuery   = DistribuicaoBonusProdutosQuery::create()->orderByData(Criteria::DESC);

include QCOMMERCE_DIR . '/admin/_2015/load.page.php';
