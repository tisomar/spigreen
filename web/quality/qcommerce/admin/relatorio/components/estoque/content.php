<link rel='stylesheet' type='text/css' href='<?php echo asset('/admin/assets/plugins/DataTables-1.10.9/media/css/dataTables.bootstrap.css') ?>' />
<link rel='stylesheet' type='text/css' href='<?php echo asset('/admin/assets/plugins/DataTables-1.10.9/extensions/Buttons/css/buttons.bootstrap.css') ?>' />

<?php
echo sprintf("<script type='text/javascript' src='%s'></script>", asset('/admin/assets/plugins/DataTables-1.10.9/media/js/jquery.dataTables.js'));
echo sprintf("<script type='text/javascript' src='%s'></script>", asset('/admin/assets/plugins/DataTables-1.10.9/media/js/dataTables.bootstrap.js'));
echo sprintf("<script type='text/javascript' src='%s'></script>", asset('/admin/assets/plugins/DataTables-1.10.9/extensions/Buttons/js/dataTables.buttons.js'));
echo sprintf("<script type='text/javascript' src='%s'></script>", asset('/admin/assets/plugins/DataTables-1.10.9/extensions/Buttons/js/buttons.bootstrap.min.js'));
echo sprintf("<script type='text/javascript' src='%s'></script>", asset('/admin/assets/plugins/DataTables-1.10.9/extensions/Buttons/js/buttons.print.js'));

// Efetua a consulta dos pedidos e monta os totalizadores
$con = Propel::getConnection();

$sql = "
     SELECT
        (SELECT SUM(_pi.QUANTIDADE)
            FROM qp1_pedido_item _pi
            WHERE _pi.PRODUTO_VARIACAO_ID = pv.ID
            AND 0 = (
                	SELECT COUNT(1)
                	FROM qp1_pedido_status_historico
                	WHERE qp1_pedido_status_historico.PEDIDO_STATUS_ID = 2
               	AND qp1_pedido_status_historico.IS_CONCLUIDO = 1
               	AND qp1_pedido_status_historico.PEDIDO_ID = _pi.PEDIDO_ID
            )
            AND _pi.PEDIDO_ID in (
                SELECT 
                    ID
                FROM qp1_pedido
                WHERE qp1_pedido.CLASS_KEY = 1
                                    )
        ) AS ESTOQUE_RESERVADO,
        p.NOME,
        (SELECT group_concat(' ', DESCRICAO, '') FROM qp1_produto_variacao_atributo pva WHERE pva.PRODUTO_VARIACAO_ID = pv.ID) as VARIACAO,
        pv.ID,
        pv.SKU,
        pv.VALOR_BASE,
        pv.VALOR_PROMOCIONAL,
        (
            (	
                SELECT 
                    coalesce(SUM(esp.QUANTIDADE),0)
               FROM qp1_estoque_produto esp
            WHERE
                    esp.OPERACAO = 'ENTRADA'
                and esp.PRODUTO_VARIACAO_ID = pv.ID
            ) - 
            (
                SELECT 
                    coalesce(SUM(esp.QUANTIDADE),0)
               FROM qp1_estoque_produto esp
            WHERE
                    esp.OPERACAO = 'SAIDA'
                and esp.PRODUTO_VARIACAO_ID = pv.ID
           )                         
        ) AS ESTOQUE_ATUAL,
        pv.ESTOQUE_MINIMO,
        pv.PRODUTO_ID
    FROM qp1_produto_variacao pv
    JOIN qp1_produto p ON pv.PRODUTO_ID = p.ID
    WHERE p.DATA_EXCLUSAO IS NULL
    AND pv.DATA_EXCLUSAO IS NULL
    AND
    (
        (
            pv.IS_MASTER = 1
            AND pv.PRODUTO_ID NOT IN(
                    SELECT pv2.PRODUTO_ID
                    FROM qp1_produto_variacao pv2
                    WHERE pv2.PRODUTO_ID = p.ID
                    AND pv2.IS_MASTER = 0
                    AND pv2.DATA_EXCLUSAO IS NULL
                )
        )
        OR
        (

            pv.ID IN (
                SELECT pv2.ID
                FROM qp1_produto_variacao pv2
                WHERE pv2.PRODUTO_ID = p.ID
                AND pv2.IS_MASTER = 0
                AND pv2.DATA_EXCLUSAO IS NULL
            )
        )
    )

";

$stmt = $con->prepare($sql);
$rs = $stmt->execute();

ob_start();
?>

<div class="col-xs-12" id="tabela-container">
    <div class="carregando">
        <div class="jumbotron">
            <h3><div class="icon-spinner icon-spin"></div> Por favor, aguarde alguns instantes enquando a lista é criada.</h3>
        </div>
    </div>
    <div class="table-responsive" style="display: none;">
        <table cellpadding="0" cellspacing="0" border="0" class="table table-striped datatables">
            <thead>
            <tr>
                <th width="1%"></th>
                <th>Produto</th>
                <th width="15%"><abbr data-toggle="tooltip" data-placement="left" title="Estoque disponível para venda">Disponível</abbr></th>
                <th width="15%"><abbr data-toggle="tooltip" data-placement="left" title="Estoque reservado por pedidos que ainda não tiveram seus itens despachados">Reservado</abbr></th>
                <th width="15%"><abbr data-toggle="tooltip" data-placement="left" title="Estoque total é a soma do disponível e reservado">Total</abbr></th>
                <th width="15%"><abbr data-toggle="tooltip" data-placement="left" title="Estoque mínimo para receber alertas">Mínimo</abbr></th>
                <th width="0%"></th>
            </tr>
            </thead>
            <tbody>
            <?php
            while ($rs = $stmt->fetch(PDO::FETCH_OBJ)) {
                $externalUrl = $rs->VARIACAO != null
                    ? get_url_admin() . '/produto-variacoes/list/?context=Produto&reference=' . $rs->PRODUTO_ID . '#list-variations'
                    : get_url_admin() . '/produtos/registration/?id=' . $rs->PRODUTO_ID
                ?>
                <tr class="<?php $class = $rs->ESTOQUE_ATUAL == 0 ? 'danger' : ($rs->ESTOQUE_ATUAL < $rs->ESTOQUE_MINIMO ? 'warning' : '');
                echo $class;  ?>">
                    <td data-title="Ver em outra janela">
                        <a href="<?php echo $externalUrl ?>" target="_blank">
                            <span class="icon-external-link"></span>
                        </a>
                    </td>
                    <td data-title="Produto">
                        <?php echo $rs->NOME ?> <?php echo $rs->VARIACAO != '' ? '<b><small>(' . $rs->VARIACAO . ' )</small></b>' : '' ?>
                        <br>
                        <small class="text-muted"><abbr title="Referência">Ref.</abbr>: <?php echo $rs->SKU ?></small>
                        <span class="hidden-on-print">
                            <?php if ($class == 'danger') : ?>
                                <br><span class="text-danger"><i class="icon-exclamation-sign"></i> Produto sem estoque</span>
                            <?php elseif ($class == 'warning') : ?>
                                <br><span class="text-warning"><i class="icon-warning-sign"></i> Produto com estoque atual abaixo do mínimo</span>
                            <?php endif; ?>
                        </span>
                    </td>
                    <td class="dt-center" data-title="Estoque Disponível para venda" data-search="<?php echo $rs->ESTOQUE_ATUAL ?>" data-order="<?php echo $rs->ESTOQUE_ATUAL ?>">
                        <?php echo edit_inline(escape($rs->ESTOQUE_ATUAL), 'ProdutoVariacao', 'EstoqueAtual', $rs->ID, 'number'); ?>
                    </td>
                    <td class="dt-center" data-title="Estoque Reservado" data-search="<?php echo $rs->ESTOQUE_RESERVADO ?>" data-order="<?php echo $rs->ESTOQUE_RESERVADO ?>">
                        <?php echo (int) $rs->ESTOQUE_RESERVADO ?>
                    </td>
                    <?php $total = $rs->ESTOQUE_ATUAL + $rs->ESTOQUE_RESERVADO ?>
                    <td class="dt-center" data-title="Estoque Total" data-search="<?php echo $total ?>" data-order="<?php echo $total ?>">
                        <b><?php echo (int) $total; ?></b>
                    </td>
                    <td class="dt-center" data-title="Estoque Mínimo">
                        <?php echo edit_inline(escape($rs->ESTOQUE_MINIMO), 'ProdutoVariacao', 'EstoqueMinimo', $rs->ID, 'number'); ?>
                    </td>
                    <td data-order="<?php echo $rs->ESTOQUE_ATUAL - $rs->ESTOQUE_MINIMO ?>">
                        <?php echo $rs->ESTOQUE_ATUAL - $rs->ESTOQUE_MINIMO ?>
                    </td>
                </tr>
                <?php
            }
            ?>
            </tbody>
        </table>
    </div>
</div>

<script>
    $(document).ready(function() {

        $.fn.dataTable.ext.search.push(
            function( settings, data, dataIndex ) {
                var min = parseInt( $('#filterMinStock').val(), 10 );
                var max = parseInt( $('#filterMaxStock').val(), 10 );
                var estoque = parseFloat( data[2] ) || 0;
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
            "columnDefs": [
                {
                    "targets": [6],
                    "visible": false
                },
                {   "orderable": false,
                    "targets": 0
                }
            ],
            "order": [[ 2, "asc" ], [ 6, "asc" ]],
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
            "initComplete": function( settings, json ) {
                $('#tabela-container .carregando').fadeOut();
                $('#tabela-container .table-responsive').fadeIn();
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

                        $(win.document.body).find('h1').text('Relatório de estoque: <?php echo date('d/m/Y H:i') ?>');

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

        $('.datatables').on('draw.dt', function () {
            initEditableInline();
            initModal();
        });

        $('.dataTables_filter input').addClass('form-control').attr('placeholder','Procurar...');
        $('.dataTables_length select').addClass('form-control');

        $('<label><input type="number" class="form-control input-sm" placeholder="Estoque maior que..." id="filterMinStock"></label>').insertBefore($('.dataTables_filter label:first-child'));
        $('<label><input type="number" class="form-control input-sm" placeholder="Estoque menor que..." id="filterMaxStock"></label>').insertBefore($('.dataTables_filter label:first-child'));

        $('#filterMaxStock, #filterMinStock').keyup(function() {
            $table.fnDraw();
        });



    });
</script>

<?php
$content = ob_get_clean();
echo $content;
