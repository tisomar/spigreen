<?php

if (!$request->query->get('cliente')) {
    redirectTo(get_url_site() . '/minha-conta/visualizacao-clientes-preferencais-finais');
    exit;
}

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
    $clientePedido = ClientePeer::retrieveByPK($request->query->get('cliente'));

    $query = PedidoQuery::create()
        ->usePedidoStatusHistoricoQuery()
        ->filterByPedidoStatusId(1)
        ->filterByIsConcluido(1)
        ->endUse()
        ->where(
            sprintf(
                'IFNULL(%s, %s) = ?',
                PedidoPeer::HOTSITE_CLIENTE_ID,
                PedidoPeer::CLIENTE_ID
            ),
            $clientePedido->getId(),
            \PDO::PARAM_INT
        )
        ->filterByStatus('CANCELADO', Criteria::NOT_EQUAL)
        ->orderByCreatedAt(Criteria::DESC);

    if ($dtInicio != null) :
        $query->filterByCreatedAt($dtInicio, Criteria::GREATER_EQUAL);
    endif;

    if ($dtFim != null) :
        $query->filterByCreatedAt($dtFim, Criteria::LESS_EQUAL);
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
} catch (\PropelException $e) {
    $logger->error($e->getMessage());
}