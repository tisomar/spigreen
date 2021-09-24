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
        REGISTROS.CENTRO_ID,
        REGISTROS.CENTRO_DESCRICAO,
        REGISTROS.PRODUTO_ID,
        pro.NOME PRODUTO_NOME,
        REGISTROS.PRODUTO_VARIACAO_ID,
        provar.SKU VARIACAO_REFERENCIA,
        SUM(REGISTROS.ESTOQUE_QTD) -
        (
            SELECT SUM(pedite.QUANTIDADE)
              FROM qp1_pedido_item pedite
             WHERE pedite.PRODUTO_VARIACAO_ID = REGISTROS.PRODUTO_VARIACAO_ID
               AND (0 = (
                            SELECT COUNT(1)
                              FROM qp1_pedido_status_historico
                             WHERE qp1_pedido_status_historico.PEDIDO_STATUS_ID = 2
                               AND qp1_pedido_status_historico.IS_CONCLUIDO = 1
                               AND qp1_pedido_status_historico.PEDIDO_ID = pedite.PEDIDO_ID
                        ) AND pedite.PEDIDO_ID in
                        (
                            SELECT
                                    ID
                              FROM qp1_pedido
                             WHERE qp1_pedido.CLASS_KEY = 1
                        )
                    )
        ) AS QTD_TOTAL
      FROM (
                SELECT 
                    cen.ID CENTRO_ID,
                    cen.DESCRICAO CENTRO_DESCRICAO,
                    est.PRODUTO_ID,
                    est.PRODUTO_VARIACAO_ID,
                    CASE est.OPERACAO
                        WHEN 'SAIDA' THEN 0 - SUM(est.QUANTIDADE)
                        WHEN 'ENTRADA' THEN SUM(est.QUANTIDADE)
                    END ESTOQUE_QTD
                 FROM qp1_centro_distribuicao cen
                 JOIN qp1_estoque_produto est ON est.CENTRO_DISTRIBUICAO_ID = cen.ID
                GROUP BY est.PRODUTO_ID, est.PRODUTO_VARIACAO_ID, est.OPERACAO
            ) REGISTROS
      JOIN qp1_produto pro ON pro.ID = REGISTROS.PRODUTO_ID
      JOIN qp1_produto_variacao provar ON provar.ID = REGISTROS.PRODUTO_VARIACAO_ID
     GROUP BY REGISTROS.PRODUTO_ID, REGISTROS.PRODUTO_VARIACAO_ID
     ORDER BY CENTRO_DESCRICAO, PRODUTO_NOME, VARIACAO_REFERENCIA
";

$stmt = $con->prepare($sql);
$result = $stmt->execute();
?>

<div class="col-xs-12">
    <div class="table-responsive">
        <table cellpadding="0" cellspacing="0" border="0" class="table table-striped datatables">
            <thead>
            <tr>
                <th width="5%">Centro de Distribuição</th>
                <th width="8%">Produto</th>
                <th width="22%">Variação</th>
                <th width="12%">Quantidade</th>
            </tr>
            </thead>
            <tbody>
            <?php
            while ($result = $stmt->fetch(PDO::FETCH_OBJ)) :
            ?>
                <tr>
                    <td data-title="Pedido">
                        <?= $result->CENTRO_DESCRICAO ?>
                    </td>
                    <td data-title="Data">
                        <?= $result->PRODUTO_NOME ?>
                    </td>
                    <td data-title="Cliente">
                        <?= $result->VARIACAO_REFERENCIA ?>
                    </td>
                    <td data-title="Valor Total">
                        <?= $result->QTD_TOTAL ? $result->QTD_TOTAL : 0 ?>
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