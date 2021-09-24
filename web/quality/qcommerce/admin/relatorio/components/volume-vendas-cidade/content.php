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

$startDate->setTime(0, 0, 0, 0);
$endDate->setTime(23, 59, 59, 999999);

$sql = "
    SELECT
        pro.ID PRODUTO_ID,
        pro.NOME PRODUTO,
        SUM(item.QUANTIDADE) QUANTIDADE,
        SUM(item.VALOR_UNITARIO * item.QUANTIDADE) VALOR_TOTAL,
        cid.NOME CIDADE,
        est.SIGLA ESTADO
      FROM qp1_pedido ped
      JOIN qp1_pedido_item item ON item.PEDIDO_ID = ped.ID
      JOIN qp1_produto_variacao provar ON provar.ID = item.PRODUTO_VARIACAO_ID
      JOIN qp1_produto pro ON pro.ID = provar.PRODUTO_ID
      JOIN qp1_pedido_status_historico stahist ON stahist.PEDIDO_ID = ped.ID
      JOIN qp1_cidade cid ON cid.ID = ped.CIDADE_ID
      JOIN qp1_estado est ON est.ID = cid.ESTADO_ID
     WHERE ped.STATUS <> 'CANCELADO'
       AND stahist.PEDIDO_STATUS_ID = 1
       AND stahist.IS_CONCLUIDO = 1
       AND item.PLANO_ID IS NULL
       AND stahist.UPDATED_AT BETWEEN '{$startDate->format('Y-m-d H:i:s:u')}' AND '{$endDate->format('Y-m-d H:i:s:u')}'
     GROUP BY
        pro.ID,
        cid.ID
     ORDER BY
        pro.NOME,
        cid.NOME
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
                <th width="30%">Produto</th>
                <th width="10%">Quantidade</th>
                <th width="10%">Valor Total</th>
                <th width="15%">Cidade</th>
                <th width="15%">Estado</th>
            </tr>
            </thead>
            <tbody>
            <?php
            while ($result = $stmt->fetch(PDO::FETCH_OBJ)) {
            ?>
                <tr>
                    <td data-title="Produto">
                        <?php echo $result->PRODUTO ?>
                    </td>
                    <td data-title="Quantidade">
                        <?php echo $result->QUANTIDADE ?>
                    </td>
                    <td data-title="Valor Total">
                        <?php echo 'R$ ' . $result->VALOR_TOTAL ?>
                    </td>
                    <td data-title="Cidade">
                        <?php echo $result->CIDADE ?>
                    </td>
                    <td data-title="Estado">
                        <?php echo $result->ESTADO ?>
                    </td>
                </tr>
                <?php
            }
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
                var valor = data[2].replace('R$ ', '');
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
            "order": [[ 0, "asc" ]],
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
