<?php
    $query = DistribuidorEventoQuery::create()
        ->filterByCliente(ClientePeer::getClienteLogado())
        ->orderByData(Criteria::ASC);

if (isset($_GET['dataInicial']) && $_GET['dataInicial']) {
    $dataInicial = DateTime::createFromFormat('d/m/Y', $_GET['dataInicial']);
    $dataInicial->setTime(0, 0, 0);
} else {
    $dataInicial = new DateTime('now');
    $dataInicial->modify('first day of this month');
}
    $query->filterByData($dataInicial, Criteria::GREATER_EQUAL);


if (isset($_GET['dataFinal']) && $_GET['dataFinal']) {
    $dataFinal = DateTime::createFromFormat('d/m/Y', $_GET['dataFinal']);
    $dataFinal->setTime(23, 59, 59);
} else {
    $dataFinal = new DateTime('now');
    $dataFinal->modify('last day of this month');
}
    $query->filterByData($dataFinal, Criteria::LESS_EQUAL);

if (isset($_GET['filter'])) {
    switch ($_GET['filter']) {
        case 'todas':
            break;
        case 'finalizadas':
            $query->filterByStatus(DistribuidorEvento::STATUS_FINALIZADO);
            break;
        case 'atrasadas':
            $data = DateTime::createFromFormat('d/m/Y', date('d/m/Y'));
            $data->setTime(0, 0, 0);
            $query->filterByData($data, '<');
            $query->filterByStatus(DistribuidorEvento::STATUS_ANDAMENTO);
            break;
        case 'hoje':
            $data = DateTime::createFromFormat('d/m/Y', date('d/m/Y'));
            $data->setTime(0, 0, 0);
            $query->filterByData($data, '=');
            break;
        case 'esta-semana':
            $inicio = new DateTime();
            $fim = new DateTime();
            $inicio->modify('Monday this week');
            $fim->modify('Sunday this week');
            $inicio->setTime(0, 0, 0);
            $fim->setTime(23, 59, 59);
            $query->filterByData($inicio, Criteria::GREATER_EQUAL);
            $query->filterByData($fim, Criteria::LESS_EQUAL);
            break;
        case 'proxima-semana':
            $inicio = new DateTime();
            $fim = new DateTime('Sunday this week');
            $inicio->modify('next Monday');
            $fim->modify('next Sunday');

            $inicio->setTime(0, 0, 0);
            $fim->setTime(23, 59, 59);
            $query->filterByData($inicio, Criteria::GREATER_EQUAL);
            $query->filterByData($fim, Criteria::LESS_EQUAL);
            break;
        case 'agendamento_aberto':
            $query->filterByStatus(DistribuidorEvento::STATUS_ANDAMENTO);
            break;
        default:
            FlashMsg::erro('Filtro inválido.');
            break;
    }
} else {
    $query->filterByStatus(DistribuidorEvento::STATUS_ANDAMENTO);
}

    $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
    $pager = new QPropelPager($query, 'DistribuidorEventoPeer', 'doSelect', $page);
