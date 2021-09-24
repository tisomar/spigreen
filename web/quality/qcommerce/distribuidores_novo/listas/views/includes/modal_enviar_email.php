<style>
    .form-email{
        padding-left: 0px;
        padding-right: 5px;
    }
</style>
<div class="modal fade custom-width" id="modal-enviar-email">
    <div class="modal-dialog" style="width: 100%; margin: 0">
        <div class="modal-content">
            <form id="enviar-email-form"
                  action="<?php echo $root_path ?>/distribuidores_novo/listas/actions/enviar_email.action.php"
                  method="POST">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                    <h4 class="modal-title"><?php echo escape(_trans('agenda.enviar_email')); ?></h4>
                </div>
                <div class="modal-body">
<!--                    <input type="hidden" name="id" value="-1">-->
                    <div class="row">
                        <div class="col-md-3" style="padding-right: 0px">
                            <div class="form-group row">
                                <div class="col-sm-3 label-atividade">
                                    <label class="control-label"><?php echo escape(_trans('agenda.assunto')) ?>*</label>
                                </div>
                                <div class="col-sm-9 form-email">
                                    <input type="text" id="assunto" name="assunto" class="form-control" value="" required>
                                </div>
                            </div>
                            <div class="form-group row">
                                <div class="col-sm-3 label-atividade">
                                    <label class="control-label"><?php echo escape(_trans('agenda.nome_remetente')) ?>*</label>
                                </div>
                                <div class="col-sm-9 form-email">
                                    <input type="text" id="nome_remetente" name="nome_remetente" class="form-control" value="" required>
                                </div>
                            </div>
                            <div class="form-group row">
                                <div class="col-sm-3 label-atividade">
                                    <label class="control-label"><?php echo escape(_trans('agenda.email_remetente')) ?>*</label>
                                </div>
                                <div class="col-sm-9 form-email">
                                    <div class="div-select">

                                        <select class="form-control" name="email_remetente" required>
                                            <option value="">Selecione</option>
                                            <?php foreach ($remetentes as $option) : /* @var $cliente ClienteDistribuidor */?>
                                                <option value="<?php echo escape($option['id']);?>"><?php echo escape($option['email']);?></option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group row">
                                <div class="col-sm-2 col-md-3 label-atividade">
                                    <label class="control-label"><?php echo escape(_trans('agenda.segmentos')) ?>*</label>
                                </div>
                                <div class="col-sm-9 form-email" hidden>
                                    <div class="div-select">

                                        <select class="form-control" name="segmento">
                                            <option value="">Selecione</option>
                                            <?php foreach ($segmentos as $option) : /* @var $cliente ClienteDistribuidor */?>
                                            <option value="<?php echo escape($option['nome']);?>"><?php echo escape($option['nome']);?></option>

                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group row">
                                <div class="col-sm-2 col-md-4 label-atividade">
                                    <input name="checkbox_sms" id="checkbox_sms" type="checkbox" class="checkbox_check" value="" />
                                    <label for="checkbox_sms"><?php echo escape(_trans('agenda.enviar_sms')) ?></label>
                                </div>
                                <div class="col-sm-8 form-email" id="text_sms" style="display: none; left: 5px">
                                    <textarea name="sms" id="" rows="4" cols="28" maxlength="110" placeholder="<?php echo escape(_trans('agenda.textarea_placeholder')) ?>"></textarea>
                                    <span id="chars">110</span> <?php echo escape(_trans('agenda.caracteres_restantes')) ?>
                                </div>    
                            </div>
                            <div class="form-group row">
                                <div class="col-sm-2 col-md-12 label-atividade">
                                    <input name="checkbox_agendar" id="checkbox_agendar" type="checkbox" class="checkbox_check" value="" />
                                    <label for="checkbox_agendar"><?php echo escape(_trans('agenda.agendar_envio')) ?></label>
                                </div>
                                    <div class="col-sm-12 form-email" id="actions" style="display: none; left: 5px">
                                            <p>( GMT-03:00 -  Brasilia )</p>

                                            <div class="form-group row">
                                                <div class="col-sm-2 col-md-3 label-atividade">
                                                    <p class="floatL"><b><?php echo escape(_trans('agenda.data')) ?></b></p>
                                                </div>
                                                <div class="col-sm-9 col-md-9 form-email">
                                                    <div class="input-group">
                                                        <span class="input-group-addon"><i class="entypo-calendar"></i></span>
                                                        <input type="text" class="form-control datepicker" data-format="dd/mm/yyyy"
                                                               name="data"  style="width: 215px">
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="form-group row">
                                                <div class="col-sm-2 col-md-3 label-atividade">
                                                    <p class="floatL"><b><?php echo escape(_trans('agenda.hora')) ?></b></p>
                                                </div>
                                                <div class="col-sm-9 col-md-9 form-email">
                                                    <div class="input-group">
                                                        <input type="time" id="appt-time" class="form-control" name="hora"  />
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="form-group row">
                                                <div class="col-sm-2 col-md-3 label-atividade">
                                                    <p class="floatL"><b><?php echo escape(_trans('agenda.recorrencia')) ?></b></p>
                                                </div>
                                                <div class="col-sm-9 col-md-9 form-email">
                                                        <select id="enviotype_recorrencia" name="recorrencia" class="form-control" style="display: inline; width: 258px">
                                                            <option value="">Não possui</option>
                                                            <option value="DIARIO" >Diário</option>
                                                            <option value="SEMANAL" >Semanal</option>
                                                            <option value="QUINZENAL" >Quinzenal</option>
                                                            <option value="MENSAL" >Mensal</option>
                                                            <option value="TRIMESTRAL" >Trimestral</option>
                                                            <option value="SEMESTRAL" >Semestral</option>
                                                            <option value="ANUAL" >Anual</option></select>
                                                </div>
                                            </div>
                                    </div>
                            </div>

                        </div>
                        <div style="padding-left: 27%;">
                            <p><b>Debe selecionar um template</b></p>
                        </div>
                        <div class="col-md-9" style="left: 12px">
                            <div class="locale-container locale-">
                                <div id="corpo_modal">
                                    <div
                                            id="corpo"
                                            class="drag_and_drop"
                                            data-name="corpo"
                                            data-namejson="json"
                                            data-post-usarhtml="hideDragDrop"
                                            name="corpo">

                                        <textarea name="corpo" id="drag_and_drop_corpo" required></textarea>

                                        <textarea name="json" id="drag_and_drop_json" required></textarea>

                                    </div>


                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <input id="hidden-id" type="hidden" name="id">
                <div class="modal-footer">
                    <button type="button" class="btn btn-default"
                            data-dismiss="modal" id="btnModalCancelar"><?php echo escape(_trans('agenda.cancelar')); ?></button>
                    <button  type="button"
                            class="btn btn-green"><?php echo escape(_trans('agenda.salvar_como_rasgunho')); ?></button>
                </div>
            </form>
        </div>
    </div>
</div>
<script src="https://qsforweb.com.br/mailforweb_email_dragdrop/web/assets/dragdrop.js"></script>
<script>

        $("#enviar-email-form").submit(function (e) {
            e.preventDefault();
            $('.drag_and_drop').emaildragdrop('html', function (html, json) {
                // console.info(html);
                // console.info(json);
                var ids = [];
                $.each($('.checkbox_check:checkbox:checked'), function (index, obj) {
                    ids.push(obj.value);
                });
                var postData = $("#enviar-email-form").serializeArray();
                postData.push(
                    {
                        'name': 'listas_id',
                        'value': ids
                    }, {
                        'name': 'html',
                        'value': html
                    },
                    {
                        'name': 'json',
                        'value': json
                    }
                );

                    $.post(
                        "<?php echo $root_path . '/distribuidores_novo/listas/actions/enviar_email.action.php'?>",
                        postData
                    );

                $("#modal-enviar-email").modal('hide');
                $.each($('.checkbox_check:checkbox:checked'), function () {
                    $('.checkbox_check').prop('checked', false);
                });

            });


        });
        $('#btnModalCancelar').click(function() {
            $.each($('.checkbox_check:checkbox:checked'), function () {
                $('.checkbox_check').prop('checked', false);
            });
        });


    $(document).ready(function () {
        $(".drag_and_drop").emaildragdrop({
            external: $('#drag_and_drop_json').val() == '' && $('#drag_and_drop_corpo').val() == '' ? false : true,
            json: $('#drag_and_drop_json').val(),
            html: $('#drag_and_drop_corpo').val(),
            site: "mailforweb",
            campos_dinamicos: [],
            cliente: "<?php echo $email_conta['email']?>",
            privateKey: "aaaaa",
            role: 'selector'
        });

        $(".drag_and_drop").css({
            "position": "absolute",
            "top": "0",
            "left": "0",
            "width": "100%",
            "height": "400px",
            "zoon": "0.8",
        });


        $(window).resize(AjustarTamnahoDoModal);
        AjustarTamnahoDoModal();

        function AjustarTamnahoDoModal() {
            console.info($('html').height());
            $('#modal-enviar-email').css({
                left: 0,
                right: 0,
            });
            $('#modal-enviar-email').css({
                "padding": 0
            });
            $('#modal-enviar-email').find('.modal-body').css({
                left: 0,
                right: 0,
                width: ($('html').width() - 10) + "px",
                height: ($('html').height() - 130) + "px",
                overflow: 'auto',
            });
            $('#modal-enviar-email').find('.modal-body iframe').css({
                height: ($('html').height() - 150) + "px",

            });
        }

        $('#mensagemSMS').on('keydown', function () {
            $('#contador span').text(160 - $(this).val().length);
        });

        // Seleciona modelo de atividade
        $('#modal-enviar-email select[name="modeloATIVIDADE"]').on('change', function () {

            $('#modal-enviar-email textarea[name="evento[DESCRICAO]"]').attr('disabled', 'disabled');

            $.ajax({
                url: '<?php echo $root_path ?>/distribuidores_novo/clientes/actions/busca_modelo.action.php?id=' + $(this).find('option:selected').val()
            }).done(function (result) {

                result = JSON.parse(result);

                $('#modal-enviar-email textarea[name="evento[DESCRICAO]"]').val(result);
                $('#modal-enviar-email textarea[name="evento[DESCRICAO]"]').removeAttr('disabled');
            });

        });

        // Seleciona modelo de sms
        $('#modal-enviar-email select[name="modeloSMS"]').on('change', function () {

            $('#modal-enviar-email textarea[name="evento[DESCRICAO_SMS]"]').attr('disabled', 'disabled');

            $.ajax({
                url: '<?php echo $root_path ?>/distribuidores_novo/clientes/actions/busca_modelo.action.php?id=' + $(this).find('option:selected').val()
            }).done(function (result) {

                result = JSON.parse(result);

                $('#modal-enviar-email textarea[name="evento[DESCRICAO_SMS]"]').val(result);
                $('#modal-enviar-email textarea[name="evento[DESCRICAO_SMS]"]').removeAttr('disabled');
                $('#mensagemSMS').keydown();
            });

        });

        // Seleciona modelo de email
        $('#modal-enviar-email select[name="modeloEMAIL"]').on('change', function () {

            $('#modal-enviar-email textarea[name="evento[DESCRICAO_EMAIL]"]').attr('disabled', 'disabled');

            $.ajax({
                url: '<?php echo $root_path ?>/distribuidores_novo/clientes/actions/busca_modelo.action.php?id=' + $(this).find('option:selected').val()
            }).done(function (result) {

                result = JSON.parse(result);

                $('#modal-enviar-email textarea[name="evento[DESCRICAO_EMAIL]"]').val(result);
                $('#modal-enviar-email textarea[name="evento[DESCRICAO_EMAIL]"]').removeAttr('disabled');
            });

        });

        // $('#modal-enviar-email').on('shown.bs.modal', function() {
        //     var ids = [];
        //     $.each($('.checkbox_check:checkbox:checked'), function (index, obj) {
        //         ids.push(obj.value);
        //     });
        //     $('#hidden-id').val(ids);
        //     console.info($('#hidden-id').val());
        //
        // });


    });
    var maxLength = 100;
    $('textarea').keyup(function() {
        var length = $(this).val().length;
        var length = maxLength-length;
        $('#chars').text(length);
    });
    $('#checkbox_agendar').click(function () {
        if ($(this).is(":checked")) {
            $("#actions").show();
        } else  $("#actions").hide();

    });
    $('#checkbox_sms').click(function () {
        if ($(this).is(":checked")) {
            $("#text_sms").show();
        } else  $("#text_sms").hide();

    });

</script>

<div class="modal fade custom-width" id="modal-listar-contatos">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title"><?php echo escape(_trans('agenda.contatos')); ?></h4>
            </div>
            <div class="modal-body">
                <input type="hidden" name="id" value="-1">
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default"
                        data-dismiss="modal"><?php echo escape(_trans('agenda.fechar')); ?></button>
            </div>
        </div>
    </div>
</div>

