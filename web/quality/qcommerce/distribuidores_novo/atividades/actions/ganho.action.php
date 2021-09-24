<?php

$obj = DistribuidorEventoQuery::create()->findPk($_POST['id']);

$obj->setStatus(DistribuidorEvento::STATUS_FINALIZADO);
$obj->setValor($_POST['valor']);

$con = Propel::getConnection(DistribuidorEventoProdutoPeer::DATABASE_NAME);
$con->beginTransaction();

foreach ($_POST['produtos'] as $produto) {
    $produtoEvento = new DistribuidorEventoProduto();
    $produtoEvento->setDistribuidorEventoId($_POST['id']);
    $produtoEvento->setProdutoId($produto);
    $produtoEvento->save();
}

$con->commit();

if ($con->inTransaction()) {
    $con->rollBack();
    return false;
}

$obj->save();

return true;
