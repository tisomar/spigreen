<!-- modal novo cliente -->
<div class="modal fade custom-width" id="modal-adicionar-clientes">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="<?php echo $root_path ?>/distribuidores_novo/clientes/actions/cadastro.action.php" method="POST" class="form-horizontal form-cadastro-clientes">
                <input type="hidden" name="id" value="-1">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                    <h4 class="modal-title"><?php echo escape(_trans('agenda.cadastro_cliente')); ?></h4>
                </div>
                <div class="modal-body">
                    <div class="container">
                        <?php include __DIR__ . '/../../../clientes/views/includes/form-cadastro.php'; ?>
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

<div class="modal fade" id="delete-item">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="<?php echo $root_path ?>/distribuidores_novo/clientes/actions/excluir.action.php" method="POST">
                <input type="hidden" name="id" value="-1"/>
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                    <h4 class="modal-title"><?php echo escape(_trans('agenda.confirmacao_exclusao')); ?></h4>
                </div>
                <div class="modal-body">
                    <div class="row text-center">
                        <h4 class="modal-title" style="margin: 15px 10px;">
                            <?php echo escape(_trans('agenda.confirmacao_exclusao_cliente')); ?> <span id="name"></span>?
                            <br>
                            <br>
                            <?php echo _trans('agenda.confirmacao_exclusao_cliente_atencao'); ?>
                            <br>
                        </h4>
                    </div>
                    <div class="row text-center">
                        <button type="button" class="btn btn-default" data-dismiss="modal"><?php echo escape(_trans('agenda.cancelar')); ?></button>
                        <button type="submit" class="btn btn-danger"><?php echo escape(_trans('agenda.excluir')); ?></button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade" id="enviar-sms">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="<?php echo $root_path ?>/distribuidores_novo/distribuidores/actions/sms.action.php" method="POST">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                    <h4 class="modal-title">Confirmação de envio de SMS</h4>
                </div>
                <div class="modal-body">
                
                    <input type="hidden" name="cliente_id">
                    <div class="form-group row">
                        <div class="col-sm-3">
                            <label class="control-label"><?php echo escape(_trans('agenda.cliente')); ?></label>
                        </div>
                        <div class="col-sm-8">
                            <label class="control-label nomeCliente"></label>
                        </div>
                    </div>
                    <div class="form-group row">
                        <div class="col-sm-3">
                            <label class="control-label"><?php echo escape(_trans('agenda.modelo_sms')); ?></label>
                        </div>
                        <div class="col-sm-8">
                            <select name="modeloSMS" class="select2" data-allow-clear="true" data-placeholder="<?php echo escape(_trans('agenda.escolha_modelo_mensagem')); ?>">
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
                        <div class="col-sm-3">
                            <label class="control-label"><?php echo escape(_trans('agenda.descricao')); ?></label>
                        </div>
                        <div class="col-sm-8">
                            <textarea name="sms[MENSAGEM]" id="descricao-modelo" class="form-control" cols="30" rows="10" maxlength="160"></textarea>
                            <div id="contador"><?php echo escape(_trans('agenda.limite_caracteres')) ?>: <span>160</span></div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal"><?php echo escape(_trans('agenda.cancelar')); ?></button>
                    <button type="submit" class="btn btn-green"><?php echo escape(_trans('agenda.enviar')); ?></button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade" id="enviar-email">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="<?php echo $root_path ?>/distribuidores_novo/distribuidores/actions/email.action.php" method="POST">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                    <h4 class="modal-title"><?php echo escape(_trans('agenda.confirmacao_envio_email')); ?></h4>
                </div>

                <div class="modal-body">
                    <input type="hidden" name="cliente_id">
                    <div class="form-group row">
                        <div class="col-sm-3 label-atividade">
                            <label class="control-label"><?php echo escape(_trans('agenda.cliente')); ?></label>
                        </div>
                        <div class="col-sm-8">
                            <label class="control-label nomeCliente"></label>
                        </div>
                    </div>
                    <div class="form-group row">
                        <div class="col-sm-3 label-atividade">
                            <label class="control-label"><?php echo escape(_trans('agenda.assunto')); ?></label>
                        </div>
                        <div class="col-sm-8">
                            <input type="text" class="form-control" name="email[ASSUNTO]">
                        </div>
                    </div>
                    <div class="form-group row">
                        <div class="col-sm-3 label-atividade">
                            <label class="control-label"><?php echo escape(_trans('agenda.modelo_email')); ?></label>
                        </div>
                        <div class="col-sm-8">
                            <select name="modeloEMAIL" class="select2" data-allow-clear="true" data-placeholder="<?php echo escape(_trans('agenda.escolha_modelo_mensagem')); ?>">
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
                        <div class="col-sm-8">
                            <textarea class="form-control wysihtml5"
                                data-stylesheet-url="<?php echo $root_path ?>/distribuidor_scripts/assets/css/custom.css"
                                name="email[MENSAGEM]" id="sample_wysiwyg_sms">
                            </textarea>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal"><?php echo escape(_trans('agenda.cancelar')); ?></button>
                    <button type="submit" class="btn btn-green"><?php echo escape(_trans('agenda.enviar')); ?></button>
                </div>
            </form>
        </div>
    </div>
</div>
