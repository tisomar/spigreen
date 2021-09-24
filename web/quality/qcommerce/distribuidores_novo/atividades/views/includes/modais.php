<div class="modal fade" id="atividade-concluida">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title"><?php echo escape(_trans('agenda.agendamento_concluido')); ?></h4>
            </div>

            <div class="modal-body inline">
                <div class="row text-center">
                    <h4 class="modal-title" style="margin: 15px auto"><?php echo escape(_trans('agenda.agendamento_concluido_descricao')); ?></h4>
                </div>
                <div class="row text-center">
                    <a href="javascript:;" onclick="jQuery('#atividade-ganho').modal('show');" class="btn btn-green" data-dismiss="modal"><?php echo escape(_trans('agenda.ganho')); ?></a>
                    <a href="javascript:;" onclick="jQuery('#atividade-perdido').modal('show');" class="btn btn-danger" data-dismiss="modal"><?php echo escape(_trans('agenda.perda')); ?></a>
                    <a href="javascript:;" onclick="jQuery('#atividade-prorrogar').modal('show');" class="btn btn-warning" data-dismiss="modal"><?php echo escape(_trans('agenda.prorrogar')); ?></a>
                </div>
            </div>

        </div>
    </div>
</div>

<div class="modal fade" id="atividade-ganho">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="<?php echo $root_path ?>/distribuidores_novo/atividades/actions/ganho.action.php" method="POST">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                    <h4 class="modal-title"><?php echo escape(_trans('agenda.agendamento_ganho')); ?></h4>
                </div>

                <div class="modal-body">
                    <input type="hidden" name="id">
                    <input type="hidden" name="cliente-id">
                    <div class="form-group row">
                        <div class="col-sm-3 label-atividade">
                            <label class="control-label"><?php echo escape(_trans('agenda.qual_valor_fechado')); ?></label>
                        </div>
                        <div class="col-sm-9">
                            <div class="input-group">
                                <span class="input-group-addon">R$</span>
                                <input type="number" name="valor-fechado" id="valor-fechado" class="form-control">
                            </div>
                        </div>
                    </div>
                    <div class="form-group row">
                        <div class="col-sm-3 label-atividade">
                            <label class="control-label"><?php echo escape(_trans('agenda.quais_produtos')); ?></label>
                        </div>
                        <div class="col-sm-9">
                            <select name="produtos[]" id="produtos" class="select2" multiple><?php

                                $produtos = ProdutoQuery::create()
                                    ->find();

                                /* @var $produto Produto */
                            foreach ($produtos as $produto) {
                                ?><option value="<?php echo $produto->getId(); ?>" ><?php echo $produto->getNome(); ?></option><?php
                            }

                            ?></select>
                        </div>
                    </div>
                    <div class="form-group row">
                        <div class="col-sm-3 label-atividade">
                            <label class="control-label" style="width: 109%; max-width: 110%;"><?php echo escape(_trans('agenda.abrir_agendamento_acompanhamento')); ?></label>
                        </div>
                        <div class="col-sm-9" >
                            <div class="radio radio-replace" style="display: inline-block; margin-right: 15px">
                                <input type="radio" id="rd-sim" name="abrirAtividade" value="1" checked>
                                <label><?php echo escape(_trans('agenda.sim')); ?></label>
                            </div>
                            <div class="radio radio-replace" style="display: inline-block">
                                <input type="radio" id="rd-nao" name="abrirAtividade" value="0">
                                <label><?php echo escape(_trans('agenda.nao')); ?></label>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal"><?php echo escape(_trans('agenda.cancelar')); ?></button>
                    <button type="submit" class="btn btn-green"><?php echo escape(_trans('agenda.salvar')); ?></button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade" id="atividade-perdido">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="<?php echo $root_path ?>/distribuidores_novo/atividades/actions/perdido.action.php" method="POST">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                    <h4 class="modal-title"><?php echo escape(_trans('agenda.agendamento_perdido')); ?></h4>
                </div>

                <div class="modal-body">
                    <input type="hidden" name="id">
                    <input type="hidden" name="cliente-id">
                    <div class="form-group row">
                        <div class="col-sm-3 label-atividade">
                            <label class="control-label"><?php echo escape(_trans('agenda.motivo_perda')); ?></label>
                        </div>
                        <div class="col-sm-9">                            

                            <select id="motivo-perda" name="motivo-perda" class="select2" data-allow-clear="true" data-placeholder="Escolha um motivo de perda" required>
                                <option></option><?php
                                
                                    $motivos = DistribuidorTemplateQuery::create()
                                        ->filterByCliente(ClientePeer::getClienteLogado())
                                        ->_or()
                                        ->filterByClienteId(null, Criteria::ISNULL)
                                        ->filterByTipo(DistribuidorTemplate::TIPO_PERDA)
                                        ->filterByAtivo(1)
                                        ->find();

                                    /* @var $motivo DistribuidorTemplate */
                                foreach ($motivos as $motivo) {
                                    ?><option value="<?php echo $motivo->getId(); ?>"><?php echo $motivo->getAssunto(); ?></option><?php
                                }
                                
                                ?></select>
                        </div>
                    </div>
                    <div class="form-group row">
                        <div class="col-sm-3 label-atividade">
                            <label class="control-label" style="width: 109%; max-width: 110%;"><?php echo escape(_trans('agenda.abrir_agendamento_acompanhamento')); ?></label>
                        </div>
                        <div class="col-sm-9" >
                            <div class="radio radio-replace" style="display: inline-block; margin-right: 15px">
                                <input type="radio" id="rd-sim" name="abrirAtividade" value="1" checked>
                                <label><?php echo escape(_trans('agenda.sim')); ?></label>
                            </div>
                            <div class="radio radio-replace" style="display: inline-block">
                                <input type="radio" id="rd-nao" name="abrirAtividade" value="0">
                                <label><?php echo escape(_trans('agenda.nao')); ?></label>
                            </div>
                        </div>
                    </div>

                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal"><?php echo escape(_trans('agenda.cancelar')); ?></button>
                    <button type="submit" class="btn btn-green"><?php echo escape(_trans('agenda.salvar')); ?></button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade" id="atividade-prorrogar">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="<?php echo $root_path ?>/distribuidores_novo/atividades/actions/prorrogar.action.php" method="POST">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                    <h4 class="modal-title"><?php echo escape(_trans('agenda.prorrogar_agendamento')); ?></h4>
                </div>

                <div class="modal-body">
                    <input type="hidden" name="id">
                    <div class="form-group row">
                        <div class="col-sm-2 col-md-3 label-atividade">
                            <label class="control-label"><?php echo escape(_trans('agenda.tipo')) ?></label>
                        </div>
                        <div class="col-sm-9 selectAssunto">
                            <div class="div-select ">
                                <select name="assunto" class="select2" data-allow-clear="true" data-placeholder="<?php echo escape(_trans('agenda.escolha_tipo')) ?>">
                                    <option></option><?php

                                        $subjects = DistribuidorEventoPeer::getSubjects();

                                    foreach ($subjects as $subject) {
                                        ?><option value="<?php echo $subject['text']; ?>"><?php echo escape(_trans('agenda.' . $subject['text'])) ?></option><?php
                                    }

                                    ?></select>
                            </div>
                        </div>
                    </div>
                    <div class="form-group row">
                        <div class="col-sm-3 label-atividade">
                            <label class="control-label"><?php echo escape(_trans('agenda.informe_nova_data')) ?></label>
                        </div>
                        <div class="col-sm-9">
                            <div class="input-group">
                                <span class="input-group-addon"><i class="entypo-calendar"></i></span>
                                <input type="text" class="form-control datepicker" data-format="dd/mm/yyyy" name="data">
                            </div>
                        </div>
                    </div>

                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal"><?php echo escape(_trans('agenda.cancelar')); ?></button>
                    <button type="submit" class="btn btn-green"><?php echo escape(_trans('agenda.salvar')); ?></button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    
    $(document).ready(function() {
        
        var listItem;
        
        $('#atividade-concluida, #atividade-ganho, #atividade-perdido, #atividade-prorrogar').on('hidden.bs.modal', function() {
            
            if($('#atividade-ganho').css('display') !== 'block' &&
                $('#atividade-perdido').css('display') !== 'block' &&
                $('#atividade-prorrogar').css('display') !== 'block') {
                $(listItem).click();
            }
            
        });
        
        // Abre modal se atividade concluida
        $('.atividadeCon').change(function () {
            
            listItem = $(this);
            
            if ($(this).is(':checked')) {
                console.log($(this).data('id'));
                //var id = $(this).parent().parent().parent().parent().parent().data('id');
                //var idCliente = $(this).parent().parent().parent().parent().parent().data('cliente-id');
                //var valor = $(this).parent().parent().parent().parent().parent().data('valor');
                var id = $(this).data('id');
                var idCliente = $(this).data('cliente-id');
                var valor = $(this).data('valor');
                $('#atividade-concluida').modal('show', {backdrop: 'static'});
                
                $('#atividade-ganho input[name="id"]').val(id);
                $('#atividade-ganho input[name="cliente-id"]').val(idCliente);
                $('#atividade-ganho input[name="valor-fechado"]').val(valor);
                $('#atividade-perdido input[name="id"]').val(id);
                $('#atividade-perdido input[name="cliente-id"]').val(idCliente);
                $('#atividade-prorrogar input[name="id"]').val(id);
            };
        });
        
        // Action Ganho
        $('#atividade-ganho form').on('submit', function(e) {
            e.preventDefault();
            
            var id = $(this).find('input[name="id"]').val();
            var idCliente = $(this).find('input[name="cliente-id"]').val();
            
            $.ajax({
                url: ''+$(this).attr('action')+'',
                method: 'POST',
                data: {
                    id: id,
                    valor: $('#atividade-ganho input[name="valor-fechado"]').val(),
                    produtos: $('#atividade-ganho select[name="produtos[]"]').val()
                }
            }).done(function( result ) {
                $('tr[data-id="'+id+'"]').hide();
                $('#atividade-ganho').modal('hide');
                
                console.log(result);
                
                if($('#atividade-ganho input[name="abrirAtividade"]:checked').val() === '1') {
                    $('#modal-criar-atividade select[name="evento[CLIENTE_DISTRIBUIDOR_ID]"]').val(idCliente).trigger('change');
                    $('#modal-criar-atividade').modal('show');
                }
            });
            
        });
        
        // Action Perda
        $('#atividade-perdido form').on('submit', function(e) {
            e.preventDefault();
            
            var id = $(this).find('input[name="id"]').val();
            var idCliente = $(this).find('input[name="cliente-id"]').val();
            
            $.ajax({
                url: ''+$(this).attr('action')+'',
                data: {
                    id: id,
                    motivo: $(this).find('select[name="motivo-perda"]').val()
                }
            }).done(function( result ) {
                $('#atividade-perdido').modal('hide');
                $('tr[data-id="'+id+'"]').hide();
                
                console.log(result);
                
                if($('#atividade-perdido input[name="abrirAtividade"]:checked').val() === '1') {
                    $('#modal-criar-atividade select[name="evento[CLIENTE_DISTRIBUIDOR_ID]"]').val(idCliente).trigger('change');
                    $('#modal-criar-atividade').modal('show');
                }
            });
            
        });
        
    });
    
</script>
