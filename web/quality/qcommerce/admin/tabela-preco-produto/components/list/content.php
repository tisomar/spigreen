<link rel='stylesheet' type='text/css' href='<?php echo asset('/admin/assets/plugins/DataTables-1.10.9/media/css/dataTables.bootstrap.css') ?>' />
<link rel='stylesheet' type='text/css' href='<?php echo asset('/admin/assets/plugins/DataTables-1.10.9/extensions/Buttons/css/buttons.bootstrap.css') ?>' />

<?php
echo sprintf("<script type='text/javascript' src='%s'></script>", asset('/admin/assets/plugins/DataTables-1.10.9/media/js/jquery.dataTables.js'));
echo sprintf("<script type='text/javascript' src='%s'></script>", asset('/admin/assets/plugins/DataTables-1.10.9/media/js/dataTables.bootstrap.js'));
echo sprintf("<script type='text/javascript' src='%s'></script>", asset('/admin/assets/plugins/DataTables-1.10.9/extensions/Buttons/js/dataTables.buttons.js'));
echo sprintf("<script type='text/javascript' src='%s'></script>", asset('/admin/assets/plugins/DataTables-1.10.9/extensions/Buttons/js/buttons.bootstrap.min.js'));
echo sprintf("<script type='text/javascript' src='%s'></script>", asset('/admin/assets/plugins/DataTables-1.10.9/extensions/Buttons/js/buttons.print.js'));

$con = Propel::getConnection();

$sql = "
     SELECT
        p.NOME,
        (SELECT group_concat(' ', DESCRICAO, '') FROM qp1_produto_variacao_atributo pva WHERE pva.PRODUTO_VARIACAO_ID = pv.ID) as VARIACAO,
        pv.ID,
        pv.SKU,
        pv.VALOR_BASE as PV_VALOR_BASE,
        pv.VALOR_PROMOCIONAL as PV_VALOR_PROMOCIONAL,
        tpv.VALOR_BASE,
        tpv.VALOR_PROMOCIONAL,
        tpv.ID as TABELA_PRECO_VARIACAO_ID,
        pv.PRODUTO_ID
    FROM qp1_produto_variacao pv
    JOIN qp1_tabela_preco_variacao tpv ON pv.ID = tpv.PRODUTO_VARIACAO_ID
      AND tpv.TABELA_PRECO_ID = " . $tabelaId . "
    JOIN qp1_produto p ON pv.PRODUTO_ID = p.ID
    WHERE p.DATA_EXCLUSAO IS NULL
    AND pv.DATA_EXCLUSAO IS NULL
    ORDER BY p.NOME

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
                <th width="50%">Produto / Referência</th>
                <th width="20%">Variação</th>
                <th width="15%">Preço Normal</th>
                <th width="15%">Preço Oferta</th>
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
                    <td data-title="Produto / Ref">
                        <a href="<?php echo $externalUrl ?>" target="_blank">
                            <span class="icon-external-link"></span>
                        </a>
                        <?php echo $rs->NOME ?>
                        <br>
                        <div class="text-muted">
                            <?php echo $rs->SKU != '' ? 'REF: ' . $rs->SKU : ''; ?>
                        </div>
                    </td>
                    <td data-title="Variação"><?php echo $rs->VARIACAO ?></td>
                    <td data-title="Preço normal">
                        <span data-toggle="tooltip" data-placement="left" title="Valor base: R$ <?php echo format_money($rs->PV_VALOR_BASE) ?>">
                            <?php echo edit_inline(escape('R$ ' . format_number($rs->VALOR_BASE, UsuarioPeer::LINGUAGEM_PORTUGUES)), TabelaPrecoVariacaoPeer::OM_CLASS, 'ValorBase', $rs->TABELA_PRECO_VARIACAO_ID, 'text', array('data-applymask' => 'maskMoney')); ?>
                        </span>
                    </td>
                    <td data-title="Preço oferta">
                        <span data-toggle="tooltip" data-placement="left" title="Valor base: R$ <?php echo format_money($rs->PV_VALOR_PROMOCIONAL) ?>">
                            <?php echo edit_inline(escape('R$ ' . format_number($rs->VALOR_PROMOCIONAL, UsuarioPeer::LINGUAGEM_PORTUGUES)), TabelaPrecoVariacaoPeer::OM_CLASS, 'ValorPromocional', $rs->TABELA_PRECO_VARIACAO_ID, 'text', array('data-applymask' => 'maskMoney')); ?>
                        </span>
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

        setTimeout(function() {
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

                            $(win.document.body).find('h1').text('Tabela: <?php echo $tabelas[$container->getRequest()->query->get('id')] ?>');

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

            $('.datatables').on('draw.dt', function () {
                initEditableInline();
                initModal();
            });

        }, 1000);

    });
</script>

<?php
$content = ob_get_clean();
echo $content;
