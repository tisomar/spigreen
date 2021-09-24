<?php

include QCOMMERCE_DIR . '/admin/relatorio/helpers/js.php';
include QCOMMERCE_DIR . '/admin/relatorio/helpers/functions.php';
include QCOMMERCE_DIR . '/admin/relatorio/helpers/config.php';

$con = Propel::getConnection();

$sql = "
    SELECT 
        pla.id PLANO_ID,
        pla.NOME PLANO,
        COUNT(VALOR_TOTAL_PEDIDO) QTD_PEDIDOS,
        SUM(VALOR_TOTAL_PEDIDO) VALOR_TOTAL_PLANO
      FROM (
            SELECT (SELECT SUM(pedite.VALOR_UNITARIO * pedite.QUANTIDADE)
                      FROM qp1_pedido_item pedite
                      JOIN qp1_produto_variacao provar ON provar.ID = pedite.PRODUTO_VARIACAO_ID
                      JOIN qp1_produto pro ON pro.ID = provar.PRODUTO_ID and pro.PLANO_ID IS NOT NULL
                     WHERE pedite.PEDIDO_ID = ped.ID
                    ) + COALESCE(ped.VALOR_ENTREGA, 0) - COALESCE(pfp.VALOR_DESCONTO, 0) - COALESCE(ped.VALOR_CUPOM_DESCONTO, 0) VALOR_TOTAL_PEDIDO,
                    cli.PLANO_ID PLANO_ID
              FROM qp1_pedido ped
              JOIN qp1_pedido_status_historico stahist ON stahist.PEDIDO_ID = ped.ID
              JOIN qp1_pedido_forma_pagamento pfp ON pfp.PEDIDO_ID = ped.ID
              JOIN qp1_cliente cli ON cli.ID = ped.CLIENTE_ID
             WHERE ped.STATUS <> 'CANCELADO'
               AND stahist.PEDIDO_STATUS_ID = 1
               AND stahist.IS_CONCLUIDO = 1
               AND pfp.STATUS = 'APROVADO'
               AND stahist.UPDATED_AT BETWEEN '{$startDate->format('Y-m-d 00:00:00')}' AND '{$endDate->format('Y-m-d 23:59:59')}'
      ) TABELA
      LEFT JOIN qp1_plano pla ON pla.ID = TABELA.PLANO_ID
     WHERE pla.ID IS NOT NULL
     GROUP BY pla.ID
";

$stmt = $con->prepare($sql);
$rs = $stmt->execute();

$totalizadores = [
    'valor_total_plano' => 0,
    'qtd_pedidos_plano' => 0,
];

$dataValorTotal = array();
$dataQtdPedidos = array();

while ($rs = $stmt->fetch(PDO::FETCH_OBJ)) :
    $dataValorTotal[] = [
        'label' => $rs->PLANO,
        'data' => $rs->VALOR_TOTAL_PLANO
    ];

    $dataQtdPedidos[] = [
        'label' => $rs->PLANO,
        'data' => $rs->QTD_PEDIDOS
    ];

    $totalizadores['valor_total_plano'] += $rs->VALOR_TOTAL_PLANO;
    $totalizadores['qtd_pedidos_plano'] += $rs->QTD_PEDIDOS;
endwhile;

include QCOMMERCE_DIR . '/admin/relatorio/helpers/menu.periodo.php';
?>

<div class="clearfix"></div>

<div class="col-xs-12 col-md-12">
    <a href="<?php echo get_url_admin() ?>/relatorio/volume-adesoes-plano/?exportar=1&<?php echo $url ?>&data_inicial=<?php echo $startDate->format('Y-m-d 00:00:00') ?>&data_final=<?php echo $endDate->format('Y-m-d 23:59:59') ?>"
       class="btn btn-primary export-newsletter">
        <i class="icon-cloud-download"></i> Exportar
    </a>
    <a href="#" class="btn btn-primary btn-print export-newsletter">
        <i class="icon-cloud-download"></i> Imprimir
    </a>
</div>

<br/>

<div class="col-xs-12 col-md-6 col-lg-4">
    <div class="panel panel-gray">
        <div class="panel-heading">
            <h4>Distribuição por valor de venda</h4>
        </div>
        <div class="panel-body">
            <div id="graph1" style="width: 100%; height: 380px;"></div>
        </div>
    </div>
</div>

<div class="col-xs-12 col-md-6 col-lg-4">
    <div class="panel panel-gray">
        <div class="panel-heading">
            <h4>Distribuição por número de pedidos</h4>
        </div>
        <div class="panel-body">
            <div id="graph2" style="width: 100%; height:380px;"></div>
        </div>
    </div>
</div>

<div class="col-xs-12 col-md-12 col-lg-4">
    <div class="report-printable">
        <h1 class="print">
            Relatório de volume de adesões por plano
        </h1>
        <h4 class="print">
            Período:
            <?php echo $startDate->format('d/m/Y') . " à " . $endDate->format('d/m/Y')  ?>
        </h4>
        <div class="row">
            <div class="col-xs-12 col-lg-6">
                <h3 class="text-right"><?php echo $totalizadores['qtd_pedidos_plano'] ?><br>
                    <small class="text-muted">
                        pedidos feitos
                    </small>
                </h3>
            </div>
            <div class="col-xs-12 col-lg-6">
                <h3 class="text-right">
                        R$&nbsp;<?php echo format_money($totalizadores['valor_total_plano']) ?><br>
                    <small class="text-muted">vendas neste período</small>
                </h3>
            </div>
        </div>
        <hr/>
        <div class="table-responsive">
            <table class="table">
                <thead>
                <tr>
                    <th class="text-right text-muted">Plano</th>
                    <th class="text-right text-muted">Número de pedidos</th>
                    <th class="text-right text-muted">Valor</th>
                </tr>
                </thead>
                <tbody>
                <?php foreach ($dataValorTotal as $i => $data) : ?>
                    <tr>
                        <td data-title="Plano" class="text-right">
                            <h5><?php echo strip_tags($data['label']); ?></h5>
                        </td>
                        <td data-title="Quantidade de pedidos" class="text-right">
                            <h5><?php echo $dataQtdPedidos[$i]['data'] ?> pedidos</h5>
                            <span class="text-muted">(<?php echo round((($dataQtdPedidos[$i]['data'] * 100) / $totalizadores['qtd_pedidos_plano'])) . '%' ?>)</span>
                        </td>
                        <td data-title="Valor" class="text-right">
                            <h5>R$&nbsp;<?php echo format_money($data['data']); ?></h5>
                            <span class="text-muted">(<?php echo round((($data['data'] * 100) / $totalizadores['valor_total_plano'])) . '%' ?>)</span>
                        </td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php
echo sprintf("<script type='text/javascript' src='%s'></script>", asset('/admin/assets/plugins/charts-flot/jquery.flot.min.js'));
echo sprintf("<script type='text/javascript' src='%s'></script>", asset('/admin/assets/plugins/charts-flot/jquery.flot.resize.min.js'));
echo sprintf("<script type='text/javascript' src='%s'></script>", asset('/admin/assets/plugins/charts-flot/jquery.flot.orderBars.min.js'));
echo sprintf("<script type='text/javascript' src='%s'></script>", asset('/admin/assets/plugins/charts-flot/jquery.flot.stack.min.js'));
echo sprintf("<script type='text/javascript' src='%s'></script>", asset('/admin/assets/plugins/charts-flot/jquery.flot.pie.min.js'));

if (count($dataValorTotal) > 0) {
    ?>

    <script>
        $(window).load(function() {
            $(function() {

                function labelFormatter(label, series) {
                    return "<div style='font-size:8pt; text-align:center; padding:2px 4px; color:white;'>" + label + "<br/>" + Math.round(series.percent) + "%</div>";
                }

                $.plot($("#graph1"), <?php echo json_encode($dataValorTotal) ?>, {
                    series: {
                        pie: {
                            show: true,
                            radius: 1,
                            label: {
                                show: true,
                                radius: 3/4,
                                formatter: labelFormatter,
                                background: {
                                    opacity: 0.5,
                                    color: '#000'
                                }
                            }
                        }
                    },
                    grid: {
                        hoverable: true
                    },
                    legend: {
                        show: false
                    }
                });

                $.plot($("#graph2"), <?php echo json_encode($dataQtdPedidos) ?>, {
                    series: {
                        pie: {
                            show: true,
                            radius: 1,
                            label: {
                                show: true,
                                radius: 3/4,
                                formatter: labelFormatter,
                                background: {
                                    opacity: 0.5,
                                    color: '#000'
                                }
                            }
                        }
                    },
                    grid: {
                        hoverable: true
                    },
                    legend: {
                        show: false
                    }
                });
            });
        });

    </script>
    <?php
}
