<?php

include QCOMMERCE_DIR . '/admin/relatorio/helpers/js.php';
include QCOMMERCE_DIR . '/admin/relatorio/helpers/functions.php';
include QCOMMERCE_DIR . '/admin/relatorio/helpers/config.php';

// Efetua a consulta dos pedidos e monta os totalizadores
$con = Propel::getConnection();

$sql = "
     SELECT
        SUM(VALOR_TOTAL) as TOTAL,
        CASE FORMA_PAGAMENTO
          WHEN 'BOLETO' THEN 'Boleto'
          WHEN 'CARTAO_CREDITO' THEN 'Cartão de Crédito'
          WHEN 'PAGSEGURO' THEN 'PagSeguro'
          WHEN 'PAGSEGURO_BOLETO' THEN 'PagSeguro - Boleto'
          WHEN 'PAGSEGURO_CARTAO_CREDITO' THEN 'PagSeguro - Cartão de Crédito'
          WHEN 'PAGSEGURO_DEBITO_ONLINE' THEN 'PagSeguro - Débito Online'
          WHEN 'ITAU_SHOPLINE' THEN 'Itaú Shopline'
          WHEN 'PAYPAL' THEN 'PayPal'
          WHEN 'FATURAMENTO_DIRETO' THEN 'Faturamento Direto'
          WHEN 'CIELO_CARTAO_CREDITO' THEN 'Cielo - Cartão de Crédito'
          WHEN 'CIELO_BOLETO_BB' THEN 'Boleto Banco do Brasil'
          WHEN 'CIELO_CARTAO_DEBITO' THEN 'Cielo - Cartão de Débito'
          ELSE 'N/I'
          END as FORMA_PAGAMENTO,
      COUNT(PEDIDO) as PEDIDOS
    FROM (
        	SELECT
                p.VALOR_ITENS + COALESCE(p.VALOR_ENTREGA, 0) - COALESCE(pfp.VALOR_DESCONTO, 0) as VALOR_TOTAL
                , pfp.FORMA_PAGAMENTO as FORMA_PAGAMENTO
                , pfp.BANDEIRA as BANDEIRA
                , p.ID as PEDIDO

		     FROM qp1_pedido p

		     JOIN qp1_pedido_item ip ON ip.PEDIDO_ID = p.ID

		     JOIN (
		         SELECT *
		         FROM qp1_pedido_status_historico
		         WHERE qp1_pedido_status_historico.PEDIDO_STATUS_ID = 1
		         AND qp1_pedido_status_historico.IS_CONCLUIDO = 1
		     ) as psh ON psh.PEDIDO_ID = p.ID

		     JOIN (
		         SELECT *
		         FROM (
		             SELECT *
		             FROM qp1_pedido_forma_pagamento
		             WHERE qp1_pedido_forma_pagamento.STATUS = 'APROVADO'
		             ORDER BY ID DESC
		         ) as qp1_pedido_forma_pagamento
		         GROUP BY qp1_pedido_forma_pagamento.PEDIDO_ID
		     ) as pfp ON pfp.PEDIDO_ID = p.ID

		     WHERE  p.CLASS_KEY = 1
		         AND p.STATUS <> 'CANCELADO'
		         AND p.CREATED_AT
		           BETWEEN '" . $startDate->format('Y-m-d 00:00:00') . "'
                    AND '" . $endDate->format('Y-m-d 23:59:59') . "'

		      GROUP BY p.ID

    )as relatorio

    GROUP BY relatorio.FORMA_PAGAMENTO

";
$stmt = $con->prepare($sql);
$rs = $stmt->execute();

$totalizadores = array(
    'valor_total_venda' => 0,
    'numero_total_pedidos' => 0,
);

$dataValorVenda = array();
$dataNumeroPedidoVenda = array();

while ($rs = $stmt->fetch(PDO::FETCH_OBJ)) {
    $dataValorVenda[] = array(
        'label' => $rs->FORMA_PAGAMENTO,
        'data' => $rs->TOTAL
    );

    $dataNumeroPedidoVenda[] = array(
        'label' => $rs->FORMA_PAGAMENTO,
        'data' => $rs->PEDIDOS
    );

    $totalizadores['valor_total_venda'] += $rs->TOTAL;
    $totalizadores['numero_total_pedidos'] += $rs->PEDIDOS;
}

include QCOMMERCE_DIR . '/admin/relatorio/helpers/menu.periodo.php';
?>

<div class="clearfix"></div>

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
            Relatório de formas de pagamento
        </h1>
        <h4 class="print">
            Periodo:
            <?php echo $startDate->format('d/m/Y') . " à " . $endDate->format('d/m/Y')  ?>
        </h4>
        <div class="row">
            <div class="col-xs-12 col-lg-6">
                <h3 class="text-right"><?php echo $totalizadores['numero_total_pedidos'] ?><br>
                    <small class="text-muted">
                        pedidos feitos
                    </small>
                </h3>
            </div>
            <div class="col-xs-12 col-lg-6">
                <h3 class="text-right">
                    R$&nbsp;<?php echo format_money($totalizadores['valor_total_venda']) ?><br>
                    <small class="text-muted">vendas neste período</small>
                </h3>
            </div>
        </div>
        <hr/>
        <div class="table-responsive">
            <table class="table">
                <thead>
                <tr>
                    <th class="text-right text-muted">Forma de Pagamento</th>
                    <th class="text-right text-muted">Número de pedidos</th>
                    <th class="text-right text-muted">Valor</th>
                </tr>
                </thead>
                <tbody>
                <?php foreach ($dataValorVenda as $i => $data) : ?>
                    <tr>
                        <td data-title="Forma de Pagamento" class="text-right">
                            <h4><?php echo strip_tags($data['label']); ?></h4>
                        </td>
                        <td data-title="Número de pedidos" class="text-right">
                            <h4><?php echo $dataNumeroPedidoVenda[$i]['data'] ?> pedidos</h4>
                            <span class="text-muted">(<?php echo round((($dataNumeroPedidoVenda[$i]['data'] * 100) / $totalizadores['numero_total_pedidos'])) . '%' ?>)</span>
                        </td>
                        <td data-title="Valor" class="text-right">
                            <h4>R$&nbsp;<?php echo format_money($data['data']); ?></h4>
                            <span class="text-muted">(<?php echo round((($data['data'] * 100) / $totalizadores['valor_total_venda'])) . '%' ?>)</span>
                        </td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
</div>


<?php
echo sprintf("<script type='text/javascript' src='%s'></script>", asset('/admin/assets/plugins/charts-flot/jquery.flot.min.js'));
echo sprintf("<script type='text/javascript' src='%s'></script>", asset('/admin/assets/plugins/charts-flot/jquery.flot.resize.min.js'));
echo sprintf("<script type='text/javascript' src='%s'></script>", asset('/admin/assets/plugins/charts-flot/jquery.flot.orderBars.min.js'));
echo sprintf("<script type='text/javascript' src='%s'></script>", asset('/admin/assets/plugins/charts-flot/jquery.flot.stack.min.js'));
echo sprintf("<script type='text/javascript' src='%s'></script>", asset('/admin/assets/plugins/charts-flot/jquery.flot.pie.min.js'));

if (count($dataValorVenda) > 0) {
    ?>

    <script>
        $(window).load(function() {
            $(function() {

                function labelFormatter(label, series) {
                    return "<div style='font-size:8pt; text-align:center; padding:2px 4px; color:white;'>" + label + "<br/>" + Math.round(series.percent) + "%</div>";
                }

                $.plot($("#graph1"), <?php echo json_encode($dataValorVenda) ?>, {
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

                $.plot($("#graph2"), <?php echo json_encode($dataNumeroPedidoVenda) ?>, {
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
?>
