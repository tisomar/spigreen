<?php
$cliente = ClientePeer::getClienteLogado(true);

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
    $query = ExtratoClientePreferencialQuery::create()
        ->filterByCliente($cliente)
        ->orderBy('Id', Criteria::DESC);

    if ($dtInicio != null) :
        $query->filterByData($dtInicio, Criteria::GREATER_EQUAL);
    endif;

    if ($dtFim != null) :
        $query->filterByData($dtFim, Criteria::LESS_EQUAL);
    endif;

    $gerenciador = new GerenciadorPontosClientePreferencial();

    $totalPontosPeriodo = $gerenciador->getTotalPontosDisponiveis($cliente, $dtInicio, $dtFim);

    $page = (int)$router->getArgument(0);

    if ($page < 1) :
        $page = 1;
    endif;

    $pager = $query->paginate($page, 10);

    $queryString = '';

    if ($qs = $request->getQueryString()) :
        $queryString = '?' . $qs;
    endif;
} catch (\PropelException $e) {
    $logger->error($e->getMessage());
}