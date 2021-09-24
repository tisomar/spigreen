<?php

$clienteLogado = ClientePeer::getClienteLogado(true);

$query = DistribuicaoClienteQuery::create()
    ->distinct()
    ->filterByClienteId($clienteLogado->getId())
    ->useDistribuicaoQuery()
        ->filterByStatus(Distribuicao::STATUS_DISTRIBUIDO)
        ->useBonusLiderancaQuery()
            ->filterByClienteId($clienteLogado->getId())
        ->endUse()
    ->endUse()
    ->orderByData(Criteria::DESC);

$page = (int) $router->getArgument(0);

$pager = $query->paginate($page, 10);

$queryString = '';

if ($qs = $request->getQueryString()) :
    $queryString = '?' . $qs;
endif;
