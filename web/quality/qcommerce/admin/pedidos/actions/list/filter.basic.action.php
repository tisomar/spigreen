<?php

if (!isset($preQuery)) {
    $preQuery = null;
}

$query = $_classQuery::create(null, $preQuery);

$status = false;

if ($request->query->get('is_filter') == 'true') {
    foreach ($request->query->get('filter') as $phpName => $value) {
        $value = trim($value);
        $methodName = 'filterBy' . $phpName;

        if ($phpName == 'StatusHistorico') {
            $status = true;
            continue;
        }

        if ($value === '' || $value === null || !method_exists($classQueryName, $methodName)) {
            continue;
        }

        $query->$methodName($value);
    }

    if($request->query->get('filter')['StatusHistorico'] !== '') :
        if($request->query->get('filter')['StatusHistorico'] === 'FINALIZADO' || $request->query->get('filter')['StatusHistorico'] === 'CANCELADO') :
            $query_builder
            ->filterByStatus($request->query->get('filter')['StatusHistorico'], Criteria::EQUAL);
        else:
            $query_builder
            ->filterByStatus('ANDAMENTO', Criteria::EQUAL)
            ->usePedidoStatusHistoricoQuery()
                ->filterByPedidoStatusId($request->query->get('filter')['StatusHistorico'])
                ->filterByIsConcluido(false)
            ->enduse();
        endif;
    endif;
}

if (!$status) {
    $methodName = 'filterByStatusHistorico';
    // $query->$methodName(1);
}
