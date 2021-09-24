<link rel='stylesheet' type='text/css' href='<?php echo asset('/admin/assets/plugins/DataTables-1.10.9/media/css/dataTables.bootstrap.css') ?>' />
<link rel='stylesheet' type='text/css' href='<?php echo asset('/admin/assets/plugins/DataTables-1.10.9/extensions/Buttons/css/buttons.bootstrap.css') ?>' />

<?php
echo sprintf("<script type='text/javascript' src='%s'></script>", asset('/admin/assets/plugins/DataTables-1.10.9/media/js/jquery.dataTables.js'));
echo sprintf("<script type='text/javascript' src='%s'></script>", asset('/admin/assets/plugins/DataTables-1.10.9/media/js/dataTables.bootstrap.js'));
echo sprintf("<script type='text/javascript' src='%s'></script>", asset('/admin/assets/plugins/DataTables-1.10.9/extensions/Buttons/js/dataTables.buttons.js'));
echo sprintf("<script type='text/javascript' src='%s'></script>", asset('/admin/assets/plugins/DataTables-1.10.9/extensions/Buttons/js/buttons.bootstrap.min.js'));
echo sprintf("<script type='text/javascript' src='%s'></script>", asset('/admin/assets/plugins/DataTables-1.10.9/extensions/Buttons/js/buttons.print.js'));


include QCOMMERCE_DIR . '/admin/relatorio/helpers/config.php';
include QCOMMERCE_DIR . '/admin/relatorio/helpers/js.php';

// Efetua a consulta dos pedidos e monta os totalizadores
$con = Propel::getConnection();

$sql = "
     SELECT
        pv.ID,
        pv.PRODUTO_ID,
        pr.NOME,
        pv.SKU,
        (SELECT group_concat(' ', DESCRICAO, '') 
            FROM qp1_produto_variacao_atributo pva 
            WHERE pva.PRODUTO_VARIACAO_ID = pv.ID
        ) as VARIACAO,
        SUM(pit.QUANTIDADE) as TOTAL_VENDAS
    FROM qp1_pedido_item pit
    JOIN qp1_pedido pe ON pit.PEDIDO_ID = pe.ID
    JOIN qp1_produto_variacao pv ON pit.PRODUTO_VARIACAO_ID = pv.ID
    JOIN qp1_produto pr ON pv.PRODUTO_ID = pr.ID
    JOIN qp1_pedido_status_historico psh ON psh.PEDIDO_ID = pe.ID
    WHERE pe.`STATUS` <> 'CANCELADO'
      AND psh.PEDIDO_STATUS_ID = 1
      AND psh.IS_CONCLUIDO = 1
          AND pe.CREATED_AT
              BETWEEN '" . $startDate->format('Y-m-d 00:00:00') . "'
              AND '" . $endDate->format('Y-m-d 23:59:59') . "'
    GROUP BY pv.ID
    ORDER BY TOTAL_VENDAS DESC
";

$stmt = $con->prepare($sql);
$rs = $stmt->execute();

include QCOMMERCE_DIR . '/admin/relatorio/helpers/menu.periodo.php';
?>

<div class="col-xs-12">
    <div class="table-responsive">
        <table cellpadding="0" cellspacing="0" border="0" class="table table-striped datatables">
            <thead>
            <tr>
                <th width="35%">Produto</th>
                <th width="20%">Variação</th>
                <th width="20%">Referência</th>
                <th width="15%">Total Itens Vendidos</th>
            </tr>
            </thead>
            <tbody>
            <?php
            while ($rs = $stmt->fetch(PDO::FETCH_OBJ)) {
                $externalUrl = $rs->VARIACAO != null
                    ? get_url_admin() . '/produto-variacoes/list/?context=Produto&reference=' . $rs->PRODUTO_ID . '#list-variations'
                    : get_url_admin() . '/produtos/registration/?id=' . $rs->PRODUTO_ID
                ?>
                <tr>
                    <td data-title="Produto">
                        <a href="<?php echo $externalUrl ?>" target="_blank">
                            <span class="icon-external-link"></span>
                        </a>
                        <?php echo $rs->NOME ?>
                    </td>
                    <td><?php echo $rs->VARIACAO ?></td>
                    <td data-title="SKU">
                        <?php echo $rs->SKU ?>
                    </td>
                    <td data-title="Total Vendas">
                        <?php echo $rs->TOTAL_VENDAS ?>
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
                var min = parseInt( $('#filterMinStock').val(), 10 );
                var max = parseInt( $('#filterMaxStock').val(), 10 );
                var estoque = parseFloat( data[3] ) || 0;
                if ( ( isNaN( min ) && isNaN( max ) ) ||
                    ( isNaN( min ) && estoque <= max ) ||
                    ( min <= estoque   && isNaN( max ) ) ||
                    ( min <= estoque   && estoque <= max ) )
                {
                    return true;
                }
                return false;
            }
        );

        var $table = $('.datatables').dataTable({
            "dom": "<'row'<'col-xs-6'l><'col-xs-6'f>r>t<'row'<'col-xs-6'i><'col-xs-6'p>>",
            "lengthMenu": [ [30, 60, 90, -1], [30, 60, 90, "Todos"] ],
            "order": [[ 3, "desc" ]],
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
                    extend: 'print',
                    text: 'Imprimir',
                    exportOptions: {
                        columns: ':visible'
                    },
                    customize: function ( win ) {
                        $(win.document.body)
                            .css( 'font-size', '10pt' );

                        $(win.document.body).find( 'table' )
                            .addClass( 'compact table-condensed table-striped' )
                            .css( 'font-size', 'inherit' );

                        $(win.document.body).find('.hidden-on-print')
                            .css('display', 'none');
                    }
                }
            ]
        });

        $('.dataTables_filter input').addClass('form-control').attr('placeholder','Procurar...');
        $('.dataTables_length select').addClass('form-control');

        $('<label><input type="number" class="form-control input-sm" data-toggle="tooltip" data-placement="top" title="Vendas maior ou igual à..." placeholder="Vendas >= que..." id="filterMinStock"></label>').insertBefore($('.dataTables_filter label:first-child'));
        $('<label><input type="number" class="form-control input-sm" data-toggle="tooltip" data-placement="top" title="Vendas menor ou igual à..." placeholder="Vendas <= que..." id="filterMaxStock"></label>').insertBefore($('.dataTables_filter label:first-child'));

        $('#filterMaxStock, #filterMinStock').keyup(function() {
            $table.fnDraw();
        });



    });
</script>
