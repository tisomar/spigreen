<?php

$pageTitle = 'Preview Distribuição Bônus Produtos';
$_class = ExtratoBonusProdutosPeer::OM_CLASS;
$_classQuery = 'ExtratoBonusProdutosQuery';
$_classPeer = 'ExtratoBonusProdutosPeer';

$typeFilter = '.query';

$preQuery   = ExtratoBonusProdutosQuery::create()
                ->filterByDistribuicaoId($request->query->get('distribuicao_id'));

include QCOMMERCE_DIR . '/admin/_2015/load.page.php';
