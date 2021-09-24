<?php
$tabelas = TabelaPrecoQuery::create()->select(array('Id', 'Nome'))->orderByNome()->find()->toArray();
$tabelas = array_column($tabelas, 'Nome', 'Id');

if ($container->getRequest()->query->has('id') && $tabelas[$container->getRequest()->query->get('id')]) {
    $tabelaId = $container->getRequest()->query->get('id');
} else {
    $slice = array_slice(array_keys($tabelas), 0, 1);
    $tabelaId = array_shift($slice);
}

$pageTitle      = 'Valores de produtos por tabela';
include_once QCOMMERCE_DIR . '/admin/_2015/load.page.php';
