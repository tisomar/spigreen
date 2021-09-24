<style>
    .table-emails td{
        padding: 3px;
    }
</style>
<div class="page">
    <div id="slideout">
        <div class="heading-slideout ">
            <h3 class="modal-title"><?php echo escape(_trans('agenda.escolha_filtro')); ?></h3>
        </div>
        <div class="content-slideout">
            <button type="button" class="btn btn-close">x</button>
            <p><?php echo escape(_trans('agenda.informe_filtrar_listas')); ?></p>
            <?php include __DIR__ . '/includes/filter.php'; ?>
        </div>
    </div>
    <div class="row">
        <div class="col-xs-12 visible-xs visible-sm">
            <?php include __DIR__ . '/includes/search.php'; ?>
        </div>
    </div>
    <div class="row">
        <div class="col-sm-12">
            <div class="row">
                <div class="col-lg-9"></div>
                <div class="col-lg-3">
                    <div class=" col-lg-6 ">
                        <button data-id="<?php echo $lista['id']; ?>"
                                class="btn btn-info btn-icon icon-left btnEnviarEmail pull-right">
                            <i class="entypo-mail" style=""></i>
                            <?php echo escape(_trans('agenda.enviar_email')); ?>
                        </button>

                    </div>
                    <div class=" col-lg-6 ">
                        <a data-id="" href="<?php echo $root_path ?>/distribuidores_novo/distribuidores/"
                                class="btn btn-info btn-icon icon-left btnCriarLista pull-right">
                            <i class="entypo-mail"></i>
                            <?php echo escape(_trans('agenda.criar_lista')); ?>
                        </a>
                    </div>
                </div>



            </div>

            <table class="table large-only table-striped table-listas">
                <thead>
                <tr>
                    <th>

                    </th>
                    <th><?php echo escape(_trans('agenda.nome')); ?></th>
                    <th><?php echo escape(_trans('agenda.descricao')); ?></th>
                </tr>
                </thead>
                <tbody>

                <?php

                if (count($pager) > 0) {
                    foreach ($pager as $lista) : /* @var $cliente ClienteDistribuidor */ ?>
                        <tr>
                            <td>
                                <input name="checkbox" type="checkbox" class="checkbox_check" value="<?php echo $lista['id']; ?>" />
                            </td>
                            <td class="name">
                                <?php echo escape($lista['nome']); ?>
                            </td>
                            <td class="mail">
                                <?php echo escape($lista['descricao']) ?>
                            </td>
                            <td class="visible-lg btn-atividade" style="width: 10%">
                                <a href="javascript:;" data-id="<?php echo $lista['id']; ?>"
                                   class="btn btn-warning btn-icon icon-left pull-right btnAtividade">
                                    <i class="entypo-newspaper"></i>
                                    <?php echo escape(_trans('agenda.ver_contatos')); ?>
                                </a>
                            </td>
                        </tr>
                    <?php endforeach ?>
                    <?php
                } else {
                    ?>
                    <tr>
                    <td colspan="9"><?php echo escape(_trans('agenda.nenhum_cliente')); ?></td>
                    </tr><?php
                }
                ?><input type="hidden" name="redirect" id="redirect"
                         value="<?= $root_path . '/distribuidores_novo/listas/'; ?>">
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php

include __DIR__ . '/includes/modal_enviar_email.php';
include __DIR__ . '/../../home/views/includes/modais.php';
?>

<script>
    $(document).ready(function() {
        function makeTable(container, data) {
            var table = $("<table/>").addClass('table-emails');
            $.each(data, function(rowIndex, r) {
                var row = $("<tr/>");
                row.append($("<td/>").text(r));
                table.append(row);
            });
            return container.append(table);
        }

        $('.btnAtividade').on('click', function (e) {
            e.preventDefault();
            var id = $(this).data('id');
            $.ajax({
                url: '<?php echo $root_path ?>/distribuidores_novo/listas/actions/listar_contatos.action.php',
                type: 'GET',
                data: 'lista=' + id,
                success: function (response) {
                    var lista_contatos = $.parseJSON(response);
                    $('#modal-listar-contatos').modal('show');
                    var emails =[];
                    $.each(lista_contatos, function(index, obj) {
                        emails.push(obj['email']);
                        console.log(emails);
                    });
                    makeTable($('#modal-listar-contatos .modal-body'), emails);



                }

            });

        });



    });

    $('.btnEnviarEmail').on('click', function (e) {
        //Si hay algun check marcado
        var checkboxValue=$(".checkbox_check:checked");
        if(checkboxValue.length > 0){
            // console.info(checkboxValue.val());
            $('#modal-enviar-email').modal('show');
        } else
            swal({
                title: '<?php echo _trans('agenda.selecionar_lista')?>',
                type: 'warning',
                confirmButtonText: '<?php echo _trans('agenda.fechar')?>'
            });

    });

    function updateiCheckSkinandStyle() {
        var skin = $(".icheck-skins a.current").data('color-class'),
            style = $("#icheck-style").val();

        var cb_class = 'icheckbox_' + style + (skin.length ? ("-" + skin) : ''),
            rd_class = 'iradio_' + style + (skin.length ? ("-" + skin) : '');

        if (style == 'futurico' || style == 'polaris') {
            cb_class = cb_class.replace('-' + skin, '');
            rd_class = rd_class.replace('-' + skin, '');
        }

        $('input.icheck-2').iCheck('destroy');
        $('input.icheck-2').iCheck({
            checkboxClass: cb_class,
            radioClass: rd_class
        });
    }

</script>
