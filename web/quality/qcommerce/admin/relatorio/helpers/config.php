<?php
// Define a lista de filtros pré-definidos para auxiliar o filtro do cliente
$filters = array(
    'today' => 'Hoje',
    'yesterday' => 'Ontem',
    'last-week' => 'Últimos 7 dias',
    'last-month' => 'Últimos 30 dias',
    'last-year' => 'Último ano',
);

$range = 'last-month';

// Define as datas conforme o filtro selecionado
if ($container->getRequest()->query->has('range')) {
    if ($container->getRequest()->query->get('range') == 'custom' || isset($filters[$container->getRequest()->query->get('range')])) {
        $range = $container->getRequest()->query->get('range');
    }
}

$modulesNeedInterval = array(
    'volume-venda',
    'volume-faturamento',
    'novos-clientes',
    'venda-faturamento',
);

switch ($range) {
    // Hoje
    case 'today':
        $startDate = new DateTime(date('Y-m-d 00:00:00'));
        $endDate = new DateTime(date('Y-m-d 23:59:59'));

        if (in_array($router->getAction(), $modulesNeedInterval) || $router->getModule() == 'dashboard') {
            list($dataValorVenda, $ticks, $map, $dataValorVendaExport) = getIntervalDatesHour($startDate, $endDate);
        }

        break;

    // Últimos 2 dias
    case 'yesterday':
        $startDate = new DateTime(date('Y-m-d 00:00:00', strtotime('-1 day')));
        $endDate = new DateTime(date('Y-m-d 23:59:59', strtotime('-1 day')));

        if (in_array($router->getAction(), $modulesNeedInterval)) {
            list($dataValorVenda, $ticks, $map, $dataValorVendaExport) = getIntervalDatesHour($startDate);
        }

        break;

    // Última semana
    case 'last-week':
        $startDate = new DateTime('-7 days');
        $endDate = new DateTime();

        if (in_array($router->getAction(), $modulesNeedInterval) || $router->getModule() == 'dashboard') {
            list($dataValorVenda, $ticks, $map, $dataValorVendaExport) = getIntervalDatesDay($startDate, $endDate);
        }

        break;

    // Último mês (30 dias)
    case 'last-month':
        $startDate = new DateTime('-30 days');
        $endDate = new DateTime();

        if (in_array($router->getAction(), $modulesNeedInterval) || $router->getModule() == 'dashboard') {
            list($dataValorVenda, $ticks, $map, $dataValorVendaExport) = getIntervalDatesDay($startDate, $endDate);
        }

        break;

    // Último ano
    case 'last-year':
        $startDate = new DateTime('last year');
        $endDate = new DateTime();

        if (in_array($router->getAction(), $modulesNeedInterval) || $router->getModule() == 'dashboard') {
            list($dataValorVenda, $ticks, $map, $dataValorVendaExport) = getIntervalDatesMonth($startDate, $endDate);
        }

        break;

    // Data informada pelo cliente
    case 'custom':
        $startDate = new DateTime(format_data($container->getRequest()->query->get('startDate'), UsuarioPeer::LINGUAGEM_INGLES));
        $endDate = new DateTime(format_data($container->getRequest()->query->get('endDate'), UsuarioPeer::LINGUAGEM_INGLES));

        if (in_array($router->getAction(), $modulesNeedInterval) || $router->getModule() == 'dashboard') {
            if ($startDate->diff($endDate)->days == 0) {
                list($dataValorVenda, $ticks, $map, $dataValorVendaExport) = getIntervalDatesHour($startDate);
            } elseif ($startDate->diff($endDate)->days <= 30) {
                list($dataValorVenda, $ticks, $map, $dataValorVendaExport) = getIntervalDatesDay($startDate, $endDate);
            } else {
                list($dataValorVenda, $ticks, $map, $dataValorVendaExport) = getIntervalDatesMonth($startDate, $endDate);
            }
        }

        break;

    // Por padrão, o seleciona o último mês.
    default:
        $startDate = new DateTime();
        $startDate->modify("-30 days");
        $endDate = new DateTime();
        break;
}
