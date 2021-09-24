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

$pontuacao = ControlePontuacaoClientePeer::getPontuacaoRecompraClientes($startDate->format('Y-m-d H:i:s:u'), $endDate->format('Y-m-d H:i:s:u'));

include QCOMMERCE_DIR . '/admin/relatorio/helpers/menu.periodo.php';
?>

<div class="col-xs-12">
    <div class="table-responsive">
        <table cellpadding="0" cellspacing="0" border="0" class="table table-striped datatables">
            <thead>
            <tr>
                <th width="45%">Cliente</th>
                <th width="10%">Total Pontos</th>
                <th width="15%">Telefone</th>
                <th width="15%">Email</th>
                <th width="10%">Situação</th>
                <th width="10%">Data de Criação</th>
                <th width="10%">Data de Ativação</th>
            </tr>
            </thead>
            <tbody>
            <?php
            foreach($pontuacao as $result) :
            ?>
                <tr>
                    <td data-title="Produto">
                        <?= $result->NOME ?>
                    </td>
                    <td data-title="Pontos Recompra">
                        <?= $result->TOTAL_PONTOS ?>
                    </td>
                    <td data-title="Quantidade">
                        <?= $result->TELEFONE ?>
                    </td>
                    <td data-title="Quantidade">
                        <?= $result->EMAIL ?>
                    </td>
                    <td data-title="Situação">
                        <?=
                            ClientePeer::getClienteAtivoMensal(
                                $result->ID,
                                $startDate,
                                $endDate
                            ) ? 'Ativo' : 'Inativo';
                        ?>
                    </td>
                    <td data-title="Pontos Recompra">
                        <?= date('d/m/Y', strtotime($result->CREATED_AT))?>
                    </td>
                    <td data-title="Pontos Recompra">
                        <?= date('d/m/Y', strtotime($result->DATA_ATIVACAO))?>
                    </td>
                </tr>
                <?php
            endforeach;
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
                var valor = parseFloat( data[2] ) || 0;
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

        $('<label><input type="number" class="form-control input-sm" data-toggle="tooltip" data-placement="top" title="Pontos maior ou igual à..." placeholder="Pontos >= que..." id="filterMinValue"></label>').insertBefore($('.dataTables_filter label:first-child'));
        $('<label><input type="number" class="form-control input-sm" data-toggle="tooltip" data-placement="top" title="Pontos menor ou igual à..." placeholder="Pontos <= que..." id="filterMaxValue"></label>').insertBefore($('.dataTables_filter label:first-child'));

        $('#filterMinValue, #filterMaxValue').keyup(function() {
            $table.fnDraw();
        });

    });
</script>
