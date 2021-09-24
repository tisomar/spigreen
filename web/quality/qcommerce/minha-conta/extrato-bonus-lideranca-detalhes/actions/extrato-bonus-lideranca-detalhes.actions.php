<?php

$clienteLogado = ClientePeer::getClienteLogado(true);

$distribuicaoId = (int) $router->getArgument(0);

$query = BonusLiderancaQuery::create()
    ->filterByClienteId($clienteLogado->getId())
    ->filterByDistribuicaoId($distribuicaoId);

$page = (int) $router->getArgument(1);

$pager = $query->paginate($page, 20);

$queryString = '';

if ($qs = $request->getQueryString()) :
    $queryString = '?' . $qs;
endif;
