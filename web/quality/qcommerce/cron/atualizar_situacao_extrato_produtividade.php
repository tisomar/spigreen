<?php

$dataInicial = new Datetime('first day of this month');
$dataInicial->setTime(0, 0, 0);
$dataFinal = new Datetime('last day of this month');
$dataFinal->setTime(23, 59, 59);

$clientes = ClienteQuery::create()
    ->filterByVago(0)
    ->usePlanoQuery()
        ->filterByPlanoClientePreferencial(0)
    ->endUse()
    ->find();

$bonificacaoProdutividade = new BonificacaoProdutividade();

foreach ($clientes as $cliente) :
    $bonificacaoProdutividade->atualizarExtratoCliente($cliente, $dataInicial, $dataFinal);
endforeach;