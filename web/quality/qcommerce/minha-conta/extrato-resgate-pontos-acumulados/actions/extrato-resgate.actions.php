<?php
$cliente = ClientePeer::getClienteLogado(true);

if (!$cliente->isMensalidadeEmDia()) :
    exit_403();
endif;

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
    $query = ResgatePremiosAcumuladosQuery::create()
        ->filterByClienteId($cliente->getId())
        ->orderByData(Criteria::DESC);

    if ($dtInicio) :
        $query->filterByData($dtInicio, Criteria::GREATER_EQUAL);
    endif;

    if ($dtFim) :
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

    $totalPontosPeriodo = 0;
    foreach($query->find() as $ponto) :
        $totalPontosPeriodo += $ponto->getPontosResgate();
    endforeach;

} catch (\PropelException $pe) {
    $logger->error($pe->getMessage());
} catch (\Exception $e) {
    $logger->error($e->getMessage());
} catch (\Throwable $th) {
    $logger->error($th->getMessage());
}
