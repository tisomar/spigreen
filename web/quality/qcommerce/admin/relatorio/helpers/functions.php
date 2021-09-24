<?php
function getIntervalDatesHour($_startDate)
{

    $date = clone $_startDate;

    $map = $dataValorVenda = $dataValorVendaExport = $ticks = array();

    for ($i = 0; $i <= 23; $i++) {
        $map[$date->format('H\h')] = $i;
        $dataValorVenda[] = array($i, 0);
        $dataValorVendaExport[] = array($i, 0);

        $ticks[] = array($i, $date->format('H\h'));

        $date->modify('+1 hour');
    }

    return array(
        $dataValorVenda,
        $ticks,
        $map,
        $dataValorVendaExport
    );
}

function getIntervalDatesDay($_startDate, $_endDate)
{

    $startDate = clone $_startDate;
    $endDate = clone $_endDate;

    $map = $dataValorVenda = $dataValorVendaExport = $ticks = array();

    $diff = $startDate->diff($endDate)->days;
    $diff++;
    $date = $startDate;

    for ($i = 1; $i <= $diff; $i++) {
        $map[$date->format('d/m')] = $i - 1;
        $dataValorVenda[] = array($i, 0);
        $dataValorVendaExport[] = array($i, 0);

        $br = "";
        if ($diff > 25 && $i % 2 == 0) {
            $br = "<br>";
        }
        $ticks[] = array($i, $br . $date->format('d/m'));

        $date->modify('+1 day');
    }

    return array(
        $dataValorVenda,
        $ticks,
        $map,
        $dataValorVendaExport
    );
}

function getIntervalDatesMonth($_startDate, $_endDate)
{

    $startDate = clone $_startDate;
    $endDate = clone $_endDate;

    $diffDate = $startDate->diff($endDate);
    $diff = ($diffDate->format('%y') * 12) + $diffDate->format('%m') + 1;

    $date = $startDate;

    $map = $dataValorVenda = $dataValorVendaExport = $ticks = array();

    for ($i = 1; $i <= $diff; $i++) {
        $map[$date->format('m/Y')] = $i - 1;
        $dataValorVenda[] = array($i, 0);
        $dataValorVendaExport[] = array($i, 0);

        $ticks[] = array($i, $date->format('m/Y'));

        $date->modify('+1 month');
    }

    return array(
        $dataValorVenda,
        $ticks,
        $map,
        $dataValorVendaExport
    );
}
