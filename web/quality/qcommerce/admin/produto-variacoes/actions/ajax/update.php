<?php

$con = Propel::getConnection();
$con->beginTransaction();

try {
    $objProdutoVariacao = ProdutoVariacaoPeer::retrieveByPK($container->getRequest()->request->get('produto_variacao_id'));
    if ($objProdutoVariacao) {
        ProdutoVariacaoQuery::create()
            ->filterByProdutoId($objProdutoVariacao->getProdutoId())
            ->update(array('IsPadrao' => false));

        $objProdutoVariacao->setIsPadrao(true);
        $objProdutoVariacao->save();
    }
    $con->commit();
} catch (Exception $e) {
    $con->rollBack();
}
