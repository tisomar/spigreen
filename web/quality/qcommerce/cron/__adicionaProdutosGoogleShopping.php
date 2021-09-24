<?php

set_time_limit(0);

include_once('../includes/include_propel.inc.php');

$arrProdutos = ProdutoQuery::create()
            ->find();

if (count($arrProdutos)) {
    foreach ($arrProdutos as $produto) {
        $objIntegracao = new Integracao();
        $objIntegracao->setProdutoId($produto->getId());
        $objIntegracao->setTipo('GOOGLE');
        $objIntegracao->save();
    }
}
