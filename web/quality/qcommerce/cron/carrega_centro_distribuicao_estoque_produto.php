<?php

$estoqueProduto = EstoqueProdutoQuery::create()
    ->filterByCentroDistribuicaoId(null, Criteria::EQUAL)
    ->filterByPedidoId(null, Criteria::NOT_EQUAL)
    ->find();

/** @var $estoque EstoqueProduto */
foreach ($estoqueProduto as $estoque) :
    $pedido = PedidoPeer::retrieveByPK($estoque->getPedidoId());

    if ($pedido->getCentroDistribuicao()) :
        $estoque->setCentroDistribuicaoId($pedido->getCentroDistribuicaoId());
        $estoque->save();
    endif;
endforeach;
