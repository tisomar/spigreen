<?php

include QCOMMERCE_DIR . '/admin/relatorio/helpers/js.php';

$showMenu = !isset($showMenu) ? true : $showMenu;
if ($showMenu) {
    include QCOMMERCE_DIR . '/admin/relatorio/helpers/menu.periodo.php';
}

?>

<div class="report-printable">
    <h1 class="print">
        Relatório de Faturamento
    </h1>
    <h4 class="print">
        Periodo:
        <?= $startDate->format('d/m/Y') . " à " . $endDate->format('d/m/Y')  ?>
    </h4>
</div>

<div class="col-xs-12">
    <div class="report-printable">
        <div class="col-xs-12 col-md-6 col-lg-3">
            <h3>
                R$ <?= format_money($totalizadores['valor_total_venda']) ?><br>
                <small class="text-muted">em pagamentos confirmados</small>
            </h3>
        </div>
        <div class="col-xs-12 col-md-6 col-lg-3">
            <h3><?= $totalizadores['numero_total_pedidos'] ?><br>
                <small class="text-muted">
                    pedidos pagos
                </small>
            </h3>
        </div>
        <div class="col-xs-12 col-md-6 col-lg-3">
            <h3><?= $totalizadores['numero_total_itens'] ?><br>
                <small class="text-muted">
                    itens comprados
                </small>
            </h3>
        </div>
        <div class="col-xs-12 col-md-6 col-lg-3">
            <h3> 
                R$ <?= format_money($totalizadores['valor_entrega']) ?><br>
                <small class="text-muted">total entrega</small>
            </h3>
        </div>
    </div>
</div>

<div class="clearfix"></div>

<div class="col-xs-12">
    <h5>* Neste relatório, os valores são agrupados pela data de confirmação do pagamento do pedido.</h5>
</div>

<div class="col-xs-12 hidden-xs">
    <div class="panel panel-gray">
        <div class="panel-heading">
            <h4>Pedidos por período</h4>
        </div>
        <div class="panel-body">
            <div id="site-statistics" style="height:380px;"></div>
        </div>
    </div>
</div>

<div class="col-xs-12 visible-xs">
    <div class="report-printable">
        <div class="table-responsive ">
            <table class="table">
                <thead>
                <tr>
                    <th class="text-left">Período</th>
                    <th class="text-right">Valor</th>
                </tr>
                </thead>
                <tbody>
                <?php foreach ($dataValorVenda as $i => $data) : ?>
                    <tr>
                        <td data-title="Período" class="text-left"><?= strip_tags($ticks[$i][1]); ?></td>
                        <td data-title="Valor" class="text-right">R$ <?= format_money($data[1]); ?></td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
                <tfoot>
                <tr>
                    <td data-title="" class="text-right"><b>Total:</b></td>
                    <td data-title="" class="text-right"><b>R$ <?= format_money($totalizadores['valor_total_venda']) ?></b></td>
                </tr>
                </tfoot>
            </table>
        </div>
    </div>
</div>

<script src="<?= asset('/admin/assets/plugins/charts-flot/jquery.flot.min.js') ?>"></script>
<script src="<?= asset('/admin/assets/plugins/charts-flot/jquery.flot.resize.min.js') ?>"></script>
<script src="<?= asset('/admin/assets/plugins/charts-flot/jquery.flot.orderBars.min.js') ?>"></script>

<script>
    $(window).load(function() {
        var plot_statistics = $.plot($("#site-statistics"), [{
            data: <?= json_encode($dataValorVenda) ?>,
            label: "Em pagamentos confirmados"
        }], {
            series: {
                lines: {
                    steps: <?= $groupBy == 'hour' ? 'false' : 'false' ?>,
                    fill: 0.10,
                    lineWidth: 1.5,
                    show: <?= $groupBy == 'hour' ? 'true' : 'true' ?>
                },
                bars: {
                    show: <?= $groupBy == 'hour' ? 'false' : 'false' ?>,
                    barWidth: 0.9
                },
                points: {
                    show: <?= $groupBy == 'hour' ? 'true' : 'true' ?>
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
                ticks: <?= json_encode($ticks) ?>,
                autoscaleMargin: 0,
                font: {
                    color: '#8c8c8c',
                    size: 11
                }
            },
            yaxis: {
                min: 0,
                ticks: 10,
                font: {
                    color: '#8c8c8c',
                    size: 11
                },
                tickFormatter: function (val, axis) {
                    return 'R$ ' + val.formatMoney(2, ',', '.');
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

                    showTooltip(item.pageX, item.pageY-7, "R$ " + parseFloat(item.datapoint[1]).formatMoney(2, ',', '.'));

                }
            } else {
                $("#tooltip").remove();
                previousPoint = null;
            }
        });
    });

    function showTooltip(x, y, contents) {
        $('<div id="tooltip" class="tooltip top in"><div class="tooltip-inner">' + contents + '<\/div><\/div>').css({
            display: 'none',
            top: y - 40,
            left: x - 55
        }).appendTo("body").fadeIn(200);
    }
</script>
