<div class="modal fade custom-width" id="modal-contatos-rede">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title"><?php echo escape(_trans('agenda.contatos_indicados')); ?></h4>
            </div>
            <div class="modal-body">
                <div class="small alert alert-warning" style="margin: 15px auto;">
                    <p>
                        <span class="entypo-info-circled" style="font-size: 16px"></span>
                        <?php echo _trans('agenda.contatos_indicados_info'); ?>
                    </p>
                </div>
                <table id="modal-criar-leads" class="table table-striped table-contatos">
                    <thead>
                        <tr>
                            <th><?php echo escape(_trans('agenda.nome')); ?></th>
                            <th><?php echo escape(_trans('agenda.telefone')); ?></th>
                            <th><?php echo escape(_trans('agenda.tipo_lead')); ?></th>
                            <th><?php echo escape(_trans('agenda.email')); ?></th>
                            <th class="text-center"><?php echo escape(_trans('agenda.prazo_contato')); ?></th>
                            <th width="10%"></th>
                            <th width="10%"></th>
                        </tr>
                    </thead>
                    <tbody><?php
                        $openModal = false;
                        /* @var $clienteP ClienteDistribuidor */
                    foreach ($clientesPendentes as $clienteP) {
                        $now = new DateTime('now');
                        $cadastro = $clienteP->getDataCadastro(null);

                        $qtAtividades = DistribuidorEventoQuery::create()
                            ->filterByClienteDistribuidorId($clienteP->getId())
                            ->count();
                        $intervalo = $now->diff($cadastro);
                        if ($clienteP->getTipoLeadDescricao() != 'C') {
                            $falta = 86400;                         // Segundos de 24 horas (tempo limite)
                            $falta -= $intervalo->d * 24 * 60 * 60; // Segundos por dia
                            $falta -= $intervalo->h * 60 * 60;      // Segundos por hora
                            $falta -= $intervalo->i * 60;           // Segundos por minuto
                            $falta -= $intervalo->s;                // Segundos
                        }

                        if ($qtAtividades <= 0) {
                            $openModal = true;

                            ?><tr>
                                    <td><b class="visible-xs"><?php echo escape(_trans('agenda.nome')); ?>: </b> <?php echo $clienteP->getNomeCompleto(); ?><i class="indicacao entypo-star"></i></td>
                                    <td><b class="visible-xs"><?php echo escape(_trans('agenda.telefone')); ?>: </b><?php echo $clienteP->getTelefoneCelular(); ?></td>
                                    <td><b class="visible-xs"><?php echo escape(_trans('agenda.tipo_lead')); ?>: </b><?php echo $clienteP->getTipoLeadDescricao(); ?></td>
                                    <td><b class="visible-xs"><?php echo escape(_trans('agenda.email')); ?>: </b><?php echo $clienteP->getEmail(); ?></td>
                                        <td>
                                            <b class="visible-xs"><?php echo escape(_trans('agenda.prazo_contato')); ?>: </b>
                                        <?php if ($clienteP->getTipoLead() != 'C') :?>
                                            <div class="cronometro" data-time="<?php echo $falta; ?>" style="text-align:center; color: red;">00:00:00</div>
                                        <?php endif;?>
                                        </td>
                                    <td>
                                        <a href="javascript:;" data-id="<?php echo $clienteP->getId();?>" class="btn btn-warning btn-icon hidden-xs hidden-sm icon-left pull-right btnAtividade">
                                            <i class="entypo-newspaper"></i>
                                        <?php echo escape(_trans('agenda.criar_agendamento')); ?>
                                        </a>
                                        <a href="javascript:;" data-id="<?php echo $clienteP->getId();?>" class="btn btn-warning btnAtividade visible-sm">
                                            <i class="entypo-newspaper"></i>
                                        </a>
                                        <a href="javascript:;" data-id="<?php echo $clienteP->getId();?>" class="btn btn-warning btnAtividade visible-xs">
                                            <i class="entypo-newspaper"></i>
                                        <?php echo escape(_trans('agenda.criar_agendamento')); ?>
                                        </a>
                                    </td>
                                    <td>
                                        <a class="btn btn-success btn-icon hidden-xs hidden-sm icon-left pull-right " href="<?php echo $root_path ?>/distribuidores_novo/clientes/visualizacao?id=<?php echo $clienteP->getId() ?>">
                                            <i class="entypo-eye"></i>
                                        <?php echo escape(_trans('agenda.ver_dados')); ?>
                                        </a>
                                        <a href="<?php echo $root_path ?>/distribuidores_novo/clientes/visualizacao?id=<?php echo $clienteP->getId() ?>" class="btn btn-success visible-sm">
                                            <i class="entypo-eye"></i>
                                        </a>
                                        <a href="<?php echo $root_path ?>/distribuidores_novo/clientes/visualizacao?id=<?php echo $clienteP->getId() ?>" class="btn btn-success visible-xs">
                                            <i class="entypo-eye"></i>
                                        <?php echo escape(_trans('agenda.ver_dados')); ?>
                                        </a>
                                        <div class="visible-xs"><br></div>
                                    </td>
                                </tr><?php
                        }
                    }

                    ?></tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- modal novo cliente -->
<div class="modal fade custom-width" id="modal-adicionar-clientes">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="<?php echo $root_path ?>/distribuidores_novo/clientes/actions/cadastro.action.php?pag=home" method="POST" class="form-horizontal form-cadastro-clientes">
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

<!-- modal seja VIP-->
<div class="modal fade custom-width" id="modal-seja-vip">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title"><?php echo escape(_trans('agenda.seja_vip')); ?></h4>
            </div>
            <div class="modal-body">
                <?php
                    $objConteudo = ConteudoPeer::retrieveByPK(9);
                ?>
                <p style="word-break: break-all"><?php echo $objConteudo->getDescricao(); ?></p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal"><?php echo escape(_trans('button.fechar')); ?></button>
            </div>
        </div>
    </div>
</div>

<!-- modal seja VIP-->
<div class="modal fade custom-width" id="modal-agenda-bloqueada">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title"><?php echo escape(_trans('agenda.agenda_bloqueada')); ?></h4>
            </div>
            <div class="modal-body">
                <?php
                $objConteudo = ConteudoPeer::retrieveByPK(10);
                ?>
                <p style="word-break: break-all"><?php echo $objConteudo->getDescricao(); ?></p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal"><?php echo escape(_trans('button.fechar')); ?></button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="enviar-sms">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="<?php echo $root_path ?>/distribuidores_novo/clientes/actions/sms.action.php?pag=home" method="POST">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                    <h4 class="modal-title"><?php echo escape(_trans('agenda.confirmacao_envio_sms')); ?></h4>
                </div>
                <div class="modal-body">
                    <div class="form-group row">
                        <div class="col-sm-2 col-md-3 label-atividade">
                            <label class="control-label"><?php echo escape(_trans('agenda.cliente')); ?></label>
                        </div>
                        <div class="col-sm-9 selectCliente">
                            <select name="cliente_id" class="select2" data-allow-clear="true" data-placeholder="Infor<?php echo escape(_trans('agenda.informe_cliente')); ?>">
                                <option></option><?php

                                    $clientes = ClientePeer::getClienteLogado()->getClienteDistribuidors();

                                foreach ($clientes as $clienteD) {
                                    ?><option value="<?php echo $clienteD->getId(); ?>"><?php echo $clienteD->getNomeCompleto(); ?></option><?php
                                }

                                ?></select>
                        </div>
                    </div>
                    <div class="form-group row">
                        <div class="col-sm-3">
                            <label class="control-label"><?php echo escape(_trans('agenda.creditos_sms')); ?></label>
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
                            <textarea name="sms[MENSAGEM]" id="descricao-modelo" class="form-control" cols="30" rows="10"></textarea>
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
            <form action="<?php echo $root_path ?>/distribuidores_novo/clientes/actions/email.action.php?pag=home" method="POST">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                    <h4 class="modal-title"><?php echo escape(_trans('agenda.confirmacao_envio_email')); ?></h4>
                </div>

                <div class="modal-body">
                    <div class="form-group row">
                        <div class="col-sm-2 col-md-3 label-atividade">
                            <label class="control-label"><?php echo escape(_trans('agenda.cliente')); ?></label>
                        </div>
                        <div class="col-sm-9 selectCliente">
                            <select name="cliente_id" class="select2" data-allow-clear="true" data-placeholder="<?php echo escape(_trans('agenda.informe_cliente')); ?>">
                                <option></option><?php

                                    $clientes = ClientePeer::getClienteLogado()->getClienteDistribuidors();

                                foreach ($clientes as $clienteD) {
                                    ?><option value="<?php echo $clienteD->getId(); ?>"><?php echo $clienteD->getNomeCompleto(); ?></option><?php
                                }

                                ?></select>
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
                                data-stylesheet-url="<?php echo $root_path ?>/distribuidores_novo/assets/css/custom.css?v=1"
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
