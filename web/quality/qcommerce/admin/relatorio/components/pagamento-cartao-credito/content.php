<link rel='stylesheet' type='text/css' href='<?php echo asset('/admin/assets/plugins/DataTables-1.10.9/media/css/dataTables.bootstrap.css') ?>' />
<link rel='stylesheet' type='text/css' href='<?php echo asset('/admin/assets/plugins/DataTables-1.10.9/extensions/Buttons/css/buttons.bootstrap.css') ?>' />

<?php
echo sprintf("<script type='text/javascript' src='%s'></script>", asset('/admin/assets/plugins/DataTables-1.10.9/media/js/jquery.dataTables.min.js'));
echo sprintf("<script type='text/javascript' src='%s'></script>", asset('/admin/assets/plugins/DataTables-1.10.9/media/js/dataTables.bootstrap.min.js'));
echo sprintf("<script type='text/javascript' src='%s'></script>", asset('/admin/assets/plugins/DataTables-1.10.9/extensions/Buttons/js/dataTables.buttons.min.js'));
echo sprintf("<script type='text/javascript' src='%s'></script>", asset('/admin/assets/plugins/DataTables-1.10.9/extensions/Buttons/js/buttons.bootstrap.min.js'));
echo sprintf("<script type='text/javascript' src='%s'></script>", asset('/admin/assets/plugins/DataTables-1.10.9/extensions/Buttons/js/buttons.print.min.js'));
echo sprintf("<script type='text/javascript' src='%s'></script>", asset('/admin/assets/plugins/DataTables-1.10.9/extensions/Buttons/js/buttons.html5.min.js'));
echo sprintf("<script type='text/javascript' src='%s'></script>", asset('/admin/assets/plugins/DataTables-1.10.9/extensions/Buttons/js/jszip.min.js'));

include QCOMMERCE_DIR . '/admin/relatorio/helpers/config.php';
include QCOMMERCE_DIR . '/admin/relatorio/helpers/js.php';

$con = Propel::getConnection();

$sql = "
    SELECT
        ped.ID PEDIDO_ID,
        ped.CREATED_AT DATA,
        (ped.VALOR_ITENS +
         ped.VALOR_ENTREGA 
        ) VALOR_TOTAL,
        COD_AUTORIZACAO,
        IF(cli.CNPJ IS NULL, cli.NOME, cli.RAZAO_SOCIAL) CLIENTE,
        IF(ped.STATUS <> 'ANDAMENTO',
           ped.STATUS,
           (SELECT status.LABEL_PRE_CONFIRMACAO
              FROM qp1_pedido_status_historico his
              JOIN qp1_pedido_status status ON status.ID = his.PEDIDO_STATUS_ID
             WHERE his.PEDIDO_ID = ped.ID
               AND his.IS_CONCLUIDO = 0
             ORDER BY his.PEDIDO_STATUS_ID DESC
             LIMIT 1
           )
        ) STATUS
    FROM qp1_pedido ped
    JOIN qp1_cliente cli 
        ON cli.ID = ped.CLIENTE_ID
    JOIN qp1_pedido_forma_pagamento pf
        ON pf.PEDIDO_ID = ped.ID
        WHERE ped.CREATED_AT BETWEEN '{$startDate->format('Y-m-d 00:00:00')}' AND '{$endDate->format('Y-m-d 23:59:59')}'
        AND pf.FORMA_PAGAMENTO = 'CIELO_CARTAO_CREDITO'
        AND pf.status <> 'CANCELADO'
        AND (SELECT COUNT(PEDIDO_STATUS_ID)
            FROM qp1_pedido_status_historico
            WHERE PEDIDO_ID = ped.ID) > 0
        ORDER BY ped.CREATED_AT, ped.ID DESC 
";

$stmt = $con->prepare($sql);
$result = $stmt->execute();

include QCOMMERCE_DIR . '/admin/relatorio/helpers/menu.periodo.php';
?>

<div class="col-xs-12">
    <div class="table-responsive">
        <table cellpadding="0" cellspacing="0" border="0" class="table table-striped datatables">
            <thead>
            <tr>
                <th width="5%">Pedido</th>
                <th width="8%">Data</th>
                <th width="22%">Cliente</th>
                <th width="12%">Valor Total</th>
                <th width="12%">Forma Pagamento</th>
                <th width="12%">N° Autorização</th>
                <th width="12%">Status</th>
            </tr>
            </thead>
            <tbody>
            <?php
            while ($result = $stmt->fetch(PDO::FETCH_OBJ)) :
                /** @var $pedido Pedido */
                $pedido = PedidoQuery::create()
                    ->filterById($result->PEDIDO_ID)
                    ->findOne();

                $forma_pagamento = '';
                foreach ($pedido->getPedidoFormaPagamentoLista() as $formaPagamento): 
                    $forma_pagamento .= '<b>R$ ' . format_money($formaPagamento->getValorPagamento() ?? $pedido->getValorTotal()) . '</b> - ' . $formaPagamento->getFormaPagamentoDescricaoCompletaAdminList(). '  ';
                endforeach;
                ?>
                <tr>
                    <td data-title="Pedido">
                        <?= $result->PEDIDO_ID ?>
                    </td>
                    <td data-title="Data">
                        <?= $pedido->getCreatedAt('d/m/Y') ?>
                    </td>
                    <td data-title="Cliente">
                        <?= $result->CLIENTE ?>
                    </td>
                    <td data-title="Valor Total">
                        R$ <?php echo format_money($pedido->getValorTotal())?>
                    </td>
                    <td data-title="Forma de pagamento">
                        <?= $forma_pagamento ?>
                    </td>
                    <td data-title="Numero autorização">
                        <?= $result->COD_AUTORIZACAO ?>
                    </td>
                    <td data-title="Status">
                        <?= ucfirst(strtolower($result->STATUS)) ?>
                    </td>
                </tr>
            <?php
            endwhile;
            ?>
            </tbody>
        </table>
    </div>
</div>

<?php
echo sprintf("<script type='text/javascript' src='%s'></script>", asset('/admin/assets/plugins/datatables/jquery.dataTables.js'));
echo sprintf("<script type='text/javascript' src='%s'></script>", asset('/admin/assets/plugins/datatables/dataTables.bootstrap.js'));
?>

<script>
    $(document).ready(function() {

        $.fn.dataTable.ext.search.push(
            function( settings, data, dataIndex ) {
                var returnFilter = false;

                var valorMin = parseInt( $('#filterMinValue').val(), 10 );
                var valorMax = parseInt( $('#filterMaxValue').val(), 10 );
                var valor = data[3].replace('R$ ', '');
                valor = parseFloat( valor ) || 0;
                if ( ( isNaN( valorMin ) && isNaN( valorMax ) ) ||
                    ( isNaN( valorMin ) && valor <= valorMax ) ||
                    ( valorMin <= valor   && isNaN( valorMax ) ) ||
                    ( valorMin <= valor   && valor <= valorMax ) )
                {
                    returnFilter = true;
                }

                return returnFilter;
            }
        );

        var $table = $('.datatables').dataTable({
            "dom": "<'row'<'col-xs-6'l><'col-xs-6'f>r>t<'row'<'col-xs-6'i><'col-xs-6'p>>",
            "lengthMenu": [ [30, 60, 90, -1], [30, 60, 90, "Todos"] ],
            "order": [[ 0, "desc" ]],
            "oLanguage":
                {
                    "sProcessing":   "Processando...",
                    "sLengthMenu": "_MENU_ registros por página",
                    "sZeroRecords":  "Não foram encontrados resultados",
                    "sInfo":         "Exibindo de _START_ a _END_ de _TOTAL_ registros",
                    "sInfoEmpty":    "Exibindo de 0 a 0 de 0 registros",
                    "sInfoFiltered": "(filtrado de _MAX_ registros no total)",
                    "sInfoPostFix":  "",
                    "sSearch":       "",
                    "sUrl":          "",
                    "oPaginate": {
                        "sFirst":    "",
                        "sPrevious": "Anterior",
                        "sNext":     "Próximo",
                        "sLast":     ""
                    }
                },
            dom: 'Bfrtip',
            buttons: [
                {
                    extend: 'excel',
                    text: "<i class='icon-cloud-download'/> Exportar",
                    className: 'btn btn-primary export-newsletter'
                },
                {
                    extend: 'print',
                    text: "<i class='icon-cloud-download'/> PDF",
                    className: 'btn btn-primary export-newsletter'
                }
            ]
        });

        $('.dataTables_filter input').addClass('form-control').attr('placeholder','Procurar...');
        $('.dataTables_length select').addClass('form-control');

        $('<label><input type="number" class="form-control input-sm" data-toggle="tooltip" data-placement="top" title="Valor maior ou igual à..." placeholder="Valor >= que..." id="filterMinValue"></label>').insertBefore($('.dataTables_filter label:first-child'));
        $('<label><input type="number" class="form-control input-sm" data-toggle="tooltip" data-placement="top" title="Valor menor ou igual à..." placeholder="Valor <= que..." id="filterMaxValue"></label>').insertBefore($('.dataTables_filter label:first-child'));

        $('#filterMinValue, #filterMaxValue').keyup(function() {
            $table.fnDraw();
        });

    });
</script>


