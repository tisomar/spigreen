<?php
$cliente = ClientePeer::getClienteLogado(true);

$clienteAtivo = ClientePeer::getClienteAtivoMensal($cliente->getId(),
    new DateTime(date('d-m-Y', strtotime('first day of this month'))),
    new DateTime(date('d-m-Y', strtotime('last day of this month'))));

$dtInicio = null;

if ($inicio = $request->query->get('inicio')) :
    $dtInicio = DateTime::createFromFormat('d/m/Y', $inicio);
    if (!$dtInicio) :
        FlashMsg::danger('Data inicial é inválida.');
    else :
        $dtInicio->setTime(0, 0, 0);
    endif;
endif;

$dtFim = null;

if ($fim = $request->query->get('fim')) :
    $dtFim = DateTime::createFromFormat('d/m/Y', $fim);

    if (!$dtFim) :
        FlashMsg::danger('Data final é inválida.');
    else :
        $dtFim->setTime(23, 59, 59);
    endif;
endif;

try {
    $query = ExtratoQuery::create()
        ->filterByTipo(Extrato::TIPO_CLIENTE_PREFERENCIAL)
        ->filterByCliente($cliente)
        ->filterByBloqueado(false)
        ->orderByData(Criteria::DESC);

    if ($dtInicio != null) :
        $query->filterByData($dtInicio, Criteria::GREATER_EQUAL);
    endif;

    if ($dtFim != null) :
        $query->filterByData($dtFim, Criteria::LESS_EQUAL);
    endif;

    $page = (int)$router->getArgument(0);

    if ($page < 1) :
        $page = 1;
    endif;

    $pager = $query->paginate($page, 10);

    $queryString = '';

    if ($qs = $request->getQueryString()) :
        $queryString = '?' . $qs;
    endif;

    $gerenciador = new GerenciadorPontos(Propel::getConnection(), $logger);

    $totalBonusPeriodo = $gerenciador->getTotalBonusClientePreferencial($cliente, $dtInicio, $dtFim);
} catch (\PropelException $e) {
    $logger->error($e->getMessage());
}
