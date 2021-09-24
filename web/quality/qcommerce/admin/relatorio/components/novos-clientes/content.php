<?php

include QCOMMERCE_DIR . '/admin/relatorio/helpers/js.php';
include QCOMMERCE_DIR . '/admin/relatorio/helpers/functions.php';
include QCOMMERCE_DIR . '/admin/relatorio/helpers/config.php';

/**
 * Define o agrupamento.
 * Se a diferença de dias for menor ou igual a 30 dias, agrupa por dia.
 * Do contrário, efetua o agrupamento por mês.
 */

$groupByOptions = array(
    'month' => 'MES, ANO',
    'day' => 'DIA, MES, ANO',
    'hour' => 'HORA, DIA, MES, ANO',
);

if ($startDate->diff($endDate)->days == 0) {
    $groupBy = "hour";
} elseif ($startDate->diff($endDate)->days <= 30) {
    $groupBy = "day";
} else {
    $groupBy = 'month';
}

// Efetua a consulta dos pedidos e monta os totalizadores
$con = Propel::getConnection();

$sql = "
    SELECT
        HORA,
        MES,
        ANO,
        DIA,
        COUNT(CLIENTE) as CLIENTE

    FROM (
        SELECT
           YEAR(c.created_at) as ANO
            , MONTH(c.created_at) as MES
            , DAY(c.created_at) as DIA
            , HOUR(c.created_at) as HORA
            , c.ID as CLIENTE
        FROM qp1_cliente c
        WHERE  c.created_at
          BETWEEN '" . $startDate->format('Y-m-d 00:00:00') . "' AND '" . $endDate->format('Y-m-d 23:59:59') . "'
    )as relatorio

    GROUP BY " . $groupByOptions[$groupBy] . "
    ORDER BY ANO, MES, DIA, HORA

";

$stmt = $con->prepare($sql);
$rs = $stmt->execute();

$totalizadores = array(
    'numero_total_clientes' => 0,
);

while ($rs = $stmt->fetch(PDO::FETCH_OBJ)) {
    if ($groupBy == 'month') {
        $date = date("m/Y", mktime(0, 0, 0, $rs->MES, $rs->DIA, $rs->ANO));
    } elseif ($groupBy == 'day') {
        $date = date("d/m", mktime(0, 0, 0, $rs->MES, $rs->DIA, $rs->ANO));
    } elseif ($groupBy == 'hour') {
        $date = date("H\h", mktime($rs->HORA, 0, 0, $rs->MES, $rs->DIA, $rs->ANO));
    }

    $key = $map[$date];
    $dataValorVenda[$key][1] = $rs->CLIENTE;

    $totalizadores['numero_total_clientes'] += $rs->CLIENTE;
}

include QCOMMERCE_DIR . '/admin/relatorio/helpers/menu.periodo.php';
?>

<div class="report-printable">
    <h1 class="print">
        Relatório de novos clientes
    </h1>
    <h4 class="print">
        Periodo:
        <?php echo $startDate->format('d/m/Y') . " à " . $endDate->format('d/m/Y')  ?>
    </h4>
</div>

<div class="report-printable">
    <div class="col-xs-12 ">

        <div class="col-xs-12 col-md-6 col-lg-3">
            <h3><?php echo $totalizadores['numero_total_clientes'] ?><br>
                <small class="text-muted">
                    novos clientes no período
                </small>
            </h3>
        </div>
    </div>
</div>

<div class="clearfix"></div>

<div class="col-xs-12 hidden-xs">
    <div class="panel panel-gray">
        <div class="panel-heading">
            <h4>Clientes por período</h4>
        </div>
        <div class="panel-body">
            <div id="site-statistics" style="height:380px;"></div>
        </div>
    </div>
</div>

<div class="col-xs-12 visible-xs">
    <div class="table-responsive">
        <div class="report-printable">
            <table class="table">
                <thead>
                <tr>
                    <th class="text-right">Período</th>
                    <th class="text-right">Quantidade</th>
                </tr>
                </thead>
                <tbody>
                <?php foreach ($dataValorVenda as $i => $data) : ?>
                    <tr>
                        <td data-title="Período" class="text-right"><?php echo strip_tags($ticks[$i][1]); ?></td>
                        <td data-title="Valor" class="text-right"><?php echo ($data[1]); ?></td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
                <tfoot>
                <tr>
                    <td data-title="" class="text-right"><b>Total:</b></td>
                    <td data-title="" class="text-right"><b><?php echo ($totalizadores['numero_total_clientes']) ?></b></td>
                </tr>
                </tfoot>
            </table>
        </div>
    </div>
</div>

<?php
echo sprintf("<script type='text/javascript' src='%s'></script>", asset('/admin/assets/plugins/charts-flot/jquery.flot.min.js'));
echo sprintf("<script type='text/javascript' src='%s'></script>", asset('/admin/assets/plugins/charts-flot/jquery.flot.resize.min.js'));
echo sprintf("<script type='text/javascript' src='%s'></script>", asset('/admin/assets/plugins/charts-flot/jquery.flot.orderBars.min.js'));

?>
<script>
    $(function() {

        $(window).load(function() {
            var plot_statistics = $.plot($("#site-statistics"), [{
                data: <?php echo json_encode($dataValorVenda) ?>,
                label: "Novos clientes"
            }], {
                series: {
                    lines: {
                        steps: <?php echo $groupBy == 'hour' ? 'false' : 'false' ?>,
                        fill: 0.10,
                        lineWidth: 1.5,
                        show: <?php echo $groupBy == 'hour' ? 'true' : 'true' ?>
                    },
                    bars: {
                        show: <?php echo $groupBy == 'hour' ? 'false' : 'false' ?>,
                        barWidth: 0.9
                    },
                    points: {
                        show: <?php echo $groupBy == 'hour' ? 'true' : 'true' ?>
                    }
                },
                grid: {
                    labelMargin: 10,
                    hoverable: true,
                    borderWidth: 0
                },

                colors: ["#a6b0c2", "#71a5e7", "#aa73c2"],
                xaxis: {
                    tickColor: "transparent",
                    ticks: <?php echo json_encode($ticks) ?>,
                    font: {
                        color: '#8c8c8c',
                        size: 11
                    }
                },
                yaxis: {
                    min: 0,
                    ticks: 10,
                    tickSize: 1,
                    font: {
                        color: '#8c8c8c',
                        size: 11
                    },
                    tickFormatter: function (val, axis) {
                        return val;
                    }
                },
                legend : {
                    labelBoxBorderColor: 'transparent'
                }
            });

            var previousPoint = null;
            $("#site-statistics").bind("plothover", function (event, pos, item) {
                if (item) {
                    if (previousPoint != item.dataIndex) {
                        previousPoint = item.dataIndex;
                        $("#tooltip").remove();
                        showTooltip(item.pageX, item.pageY-7, item.datapoint[1]);
                    }
                } else {
                    $("#tooltip").remove();
                    previousPoint = null;
                }
            });
        });
    });


    function showTooltip(x, y, contents) {
        $('<div id="tooltip" class="tooltip top in"><div class="tooltip-inner">' + contents + '<\/div><\/div>').css({
            display: 'none',
            top: y - 40,
            left: x - 15
        }).appendTo("body").fadeIn(200);
    }
</script>
