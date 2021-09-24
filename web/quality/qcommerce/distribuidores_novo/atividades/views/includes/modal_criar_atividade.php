<div class="modal fade custom-width" id="modal-criar-atividade">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="<?php echo $root_path ?>/distribuidores_novo/atividades/actions/cadastro.action.php" method="POST">

                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                    <h4 class="modal-title"><?php echo escape(_trans('agenda.novo_agendamento')); ?></h4>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="id" value="-1">
                    <div class="row">
                        <div class="col-md-5">
                            <div class="form-group row">
                                <div class="col-sm-2 col-md-3 label-atividade">
                                    <label class="control-label"><?php echo escape(_trans('agenda.cliente')); ?></label>
                                </div>
                                <div class="col-sm-9 selectCliente">
                                    <select name="evento[CLIENTE_DISTRIBUIDOR_ID]" class="select2" data-allow-clear="true" data-placeholder="<?php echo escape(_trans('agenda.informe_cliente')); ?>" required>
                                        <option></option><?php
                                            $criteria = ClienteDistribuidorQuery::create()->filterByClienteRedefacilId(null);
                                            $clientesDistribuidor = ClientePeer::getClienteLogado()->getClienteDistribuidors($criteria);

                                        if (count($clientesDistribuidor) > 0) {
                                            ?><optgroup label="Clientes"><?php
foreach ($clientesDistribuidor as $clienteD) {
    ?>
                                                    <option
                                                    value="<?php echo $clienteD->getId(); ?>">
        <?php echo $clienteD->getNomeCompleto(); ?></option><?php
}
?></optgroup><?php
                                        }

                                        $criteria = ClienteDistribuidorQuery::create()->filterByClienteRedefacilId(null, Criteria::NOT_EQUAL);
                                        $clientesDistribuidorRede = ClientePeer::getClienteLogado()->getClienteDistribuidors($criteria);

                                        if (count($clientesDistribuidorRede) > 0) {
                                            ?><optgroup label="Distribuidores"><?php
foreach ($clientesDistribuidorRede as $clienteD) {
    ?>
                                            <option
                                                    value="<?php echo $clienteD->getId(); ?>">
                                                <?php echo $clienteD->getNomeCompleto(); ?></option><?php
}
?></optgroup><?php
                                        }
                                            
                                        ?></select>
                                </div>
                            </div>
                            <div class="form-group row">
                                <div class="col-sm-2 col-md-3 label-atividade">
                                    <label class="control-label"><?php echo escape(_trans('agenda.tipo')) ?></label>
                                </div>
                                <div class="col-sm-9 selectAssunto">
                                    <div class="div-select ">
                                        <select name="evento[ASSUNTO]" class="select2" data-allow-clear="true" data-placeholder="<?php echo escape(_trans('agenda.escolha_tipo')); ?>">
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
                                    <label class="control-label"><?php echo escape(_trans('agenda.interesse')) ?></label>
                                </div>
                                <div class="col-sm-9">
                                    <input type="text" name="evento[INTERESSE]" class="form-control">
                                </div>
                            </div>
                            <div class="form-group row">
                                <div class="col-sm-2 col-md-3 label-atividade">
                                    <label class="control-label"><?php echo escape(_trans('agenda.data')) ?></label>
                                </div>
                                <div class="col-sm-9">
                                    <div class="input-group">
                                        <span class="input-group-addon"><i class="entypo-calendar"></i></span>
                                        <input type="text" class="form-control datepicker" data-format="dd/mm/yyyy" name="evento[DATA]" required>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group row">
                                <div class="col-sm-2 col-md-3 label-atividade">
                                    <label class="control-label"><?php echo escape(_trans('agenda.modelo')) ?></label>
                                </div>
                                <div class="col-sm-9">
                                    <div class="div-select ">
                                        <select name="modeloATIVIDADE" class="select2" data-allow-clear="true" data-placeholder="<?php echo escape(_trans('agenda.escolha_modelo_mensagem')) ?>">
                                            <option></option><?php
                                                
                                                    $modelosATIVIDADE = DistribuidorTemplateQuery::create()
                                                        ->filterByCliente(ClientePeer::getClienteLogado())
                                                        ->filterByTipo(DistribuidorTemplate::TIPO_ATIVIDADE)
                                                        ->filterByAtivo(1)
                                                        ->find();
                                                
                                                    /* @var $modelo DistribuidorTemplate */
                                            foreach ($modelosATIVIDADE as $modelo) {
                                                ?><option value="<?php echo $modelo->getId(); ?>"><?php echo $modelo->getAssunto(); ?></option><?php
                                            }
                                                    
                                            ?></select>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group row">
                                <div class="col-sm-2 col-md-3 label-atividade"
                                    <label class="control-label"><?php echo escape(_trans('agenda.descricao')); ?></label>
                                </div>
                                <div class="col-sm-9">
                                    <textarea name="evento[DESCRICAO]" style="overflow-x: hidden" id="descricao-modelo" class="form-control" cols="30" rows="10" required></textarea>
                                </div>
                            </div>
                            <div class="form-group row">
                                <div class="col-sm-3 label-atividade">
                                    <label class="control-label"><?php echo escape(_trans('agenda.valor_jogo')); ?></label>
                                </div>
                                <div class="col-sm-9">
                                    <input type="number" name="evento[VALOR]" class="form-control">
                                </div>
                            </div>
                        </div>
                        <div class="col-md-7">
                            <div class="panel panel-primary">
                                <div class="panel-heading" >
                                    <div class="panel-title text-uppercase">
                                        <?php echo escape(_trans('agenda.mensagens_automaticas')) ?>
                                    </div>
                                </div>
                                <div class="panel-body info-small">
                                    <p class="small alert alert-warning">
                                        <i class="entypo-info-circled" ></i>
                                        <span><?php echo escape(_trans('agenda.mensagens_automaticas_descricao')) ?></span></p>
                                    <div class="form-group row">
                                        <div class="col-sm-3 label-atividade">
                                            <label class="control-label"><?php echo escape(_trans('agenda.modelo_sms')) ?></label>
                                        </div>
                                        <div class="col-sm-9">
                                            <select name="modeloSMS" class="select2" data-allow-clear="true" data-placeholder="<?php echo escape(_trans('agenda.escolha_modelo_mensagem')) ?>">
                                                <option></option><?php
                                                
                                                    $modelosSMS = DistribuidorTemplateQuery::create()
                                                        ->filterByCliente(ClientePeer::getClienteLogado())
                                                        ->filterByTipo(DistribuidorTemplate::TIPO_SMS)
                                                        ->find();
                                                
                                                    /* @var $modelo DistribuidorTemplate */
                                                foreach ($modelosSMS as $modelo) {
                                                    ?><option value="<?php echo $modelo->getId(); ?>"><?php echo $modelo->getAssunto(); ?></option><?php
                                                }
                                                    
                                                ?></select>
                                        </div>
                                    </div>
                                    <div class="form-group row">
                                        <div class="col-sm-3 label-atividade">
                                            <label class="control-label"><?php echo escape(_trans('agenda.descricao')); ?></label>
                                        </div>
                                        <div class="col-sm-9">
                                            <textarea id="mensagemSMS" name="evento[DESCRICAO_SMS]" style="overflow-x: hidden" class="form-control" cols="30" rows="4" maxlength="160"></textarea>
                                            <div id="contador"><?php echo escape(_trans('agenda.limite_caracteres')) ?>: <span>160</span></div>
                                        </div>
                                    </div>
                                    <div class="form-group row">
                                        <div class="col-sm-3 label-atividade">
                                            <label class="control-label"><?php echo escape(_trans('agenda.modelo_email')); ?></label>
                                        </div>
                                        <div class="col-sm-9">
                                            <select name="modeloEMAIL" class="select2" data-allow-clear="true" data-placeholder="<?php echo escape(_trans('agenda.escolha_modelo_mensagem')) ?>">
                                                <option></option><?php
                                                
                                                    $modelosEMAIL = DistribuidorTemplateQuery::create()
                                                        ->filterByCliente(ClientePeer::getClienteLogado())
                                                        ->filterByTipo(DistribuidorTemplate::TIPO_EMAIL)
                                                        ->find();
                                                
                                                    /* @var $modelo DistribuidorTemplate */
                                                foreach ($modelosEMAIL as $modelo) {
                                                    ?><option value="<?php echo $modelo->getId(); ?>"><?php echo $modelo->getAssunto(); ?></option><?php
                                                }
                                                    
                                                ?></select>
                                        </div>
                                    </div>
                                    <div class="form-group row">
                                        <div class="col-sm-3 label-atividade">
                                            <label class="control-label"><?php echo escape(_trans('agenda.descricao')); ?></label>
                                        </div>
                                        <div class="col-sm-9">
                                            <textarea name="evento[DESCRICAO_EMAIL]" style="overflow-x: hidden" class="form-control" cols="30" rows="4"></textarea>
                                        </div>
                                    </div>
                                </div>

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
        
        $('#modal-criar-atividade').on('hidden.bs.modal', function() {
            $('#modal-criar-atividade #mensagemSMS').val('');
            $('#modal-criar-atividade #mensagemSMS').keydown();
        });
        
        $('#mensagemSMS').on('keydown', function() {
            $('#contador span').text(160 - $(this).val().length);
        });
        
        // Seleciona modelo de atividade
        $('#modal-criar-atividade select[name="modeloATIVIDADE"]').on('change', function() {
            
            $('#modal-criar-atividade textarea[name="evento[DESCRICAO]"]').attr('disabled', 'disabled');
            
            $.ajax({
                url: '<?php echo $root_path; ?>/distribuidores_novo/clientes/actions/busca_modelo.action.php?id='+$(this).find('option:selected').val()
            }).done(function( result ) {
                
                result = JSON.parse(result);
                
                $('#modal-criar-atividade textarea[name="evento[DESCRICAO]"]').val(result);
                $('#modal-criar-atividade textarea[name="evento[DESCRICAO]"]').removeAttr('disabled');
            });
            
        });
        
        // Seleciona modelo de sms
        $('#modal-criar-atividade select[name="modeloSMS"]').on('change', function() {
            
            $('#modal-criar-atividade textarea[name="evento[DESCRICAO_SMS]"]').attr('disabled', 'disabled');
            
            $.ajax({
                url: '<?php echo $root_path; ?>/distribuidores_novo/clientes/actions/busca_modelo.action.php?id='+$(this).find('option:selected').val()
            }).done(function( result ) {
                
                result = JSON.parse(result);
                
                $('#modal-criar-atividade textarea[name="evento[DESCRICAO_SMS]"]').val(result);
                $('#modal-criar-atividade textarea[name="evento[DESCRICAO_SMS]"]').removeAttr('disabled');
                $('#mensagemSMS').keydown();
            });
            
        });
        
        // Seleciona modelo de email
        $('#modal-criar-atividade select[name="modeloEMAIL"]').on('change', function() {
            
            $('#modal-criar-atividade textarea[name="evento[DESCRICAO_EMAIL]"]').attr('disabled', 'disabled');
            
            $.ajax({
                url: '<?php echo $root_path; ?>/distribuidores_novo/clientes/actions/busca_modelo.action.php?id='+$(this).find('option:selected').val()
            }).done(function( result ) {
                
                result = JSON.parse(result);
                
                $('#modal-criar-atividade textarea[name="evento[DESCRICAO_EMAIL]"]').val(result);
                $('#modal-criar-atividade textarea[name="evento[DESCRICAO_EMAIL]"]').removeAttr('disabled');
            });
            
        });
        
    });
    
</script>
