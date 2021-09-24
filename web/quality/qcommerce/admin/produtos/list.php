<?php
$pageTitle = 'Produtos';

$_class = ProdutoPeer::OM_CLASS;
$_classQuery = $_class .  'Query';
$_classPeer = $_class .  'Peer';

$preQuery = ProdutoQuery::create()->groupById();

include_once QCOMMERCE_DIR . '/admin/_2015/load.page.php';
