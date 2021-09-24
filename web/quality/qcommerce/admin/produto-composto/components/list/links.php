<?php

use PFBC\Element;

if (ProdutoVariacaoQuery::create()->filterByProdutoId($produto->getId())->filterByIsMaster(false)->count() == 0 && $pager->count() < 2) {
    $add = new \PFBC\Element\AddNewButton($config['routes']['registration']);
    $add->render();
}
