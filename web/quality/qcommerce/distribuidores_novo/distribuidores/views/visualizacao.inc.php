<?php /* @var $objCliente Cliente */ ?>
<div class="well" style="background: #fff;">
    <div class="row ">
        <div class="visible-sm visible-xs" style="    position: absolute;  top: 25px; right: 30px;">
            <div class=" pull-right menudrop-icon">
                <a href="#" class="dropdown-toggle" data-toggle="dropdown" data-hover="dropdown" data-close-others="true" title="">
                    <div class="menu-dots" id="js-menu-dots">
                        <span class="dot"></span>
                        <span class="dot"></span>
                        <span class="dot"></span>
                    </div>
                </a>
                <ul class="dropdown-menu menu-icons">
                    <li>
                        <a href="javascript:;" onclick="jQuery('#modal-criar-atividade').modal('show');">
                            <i class="entypo-newspaper"></i>
                            <?php echo escape(_trans('agenda.criar_agendamento')); ?>
                        </a>
                    </li>
                    <li>
                        <a href="javascript:;" onclick="jQuery('#enviar-sms').modal('show');">
                            <i class="entypo-mobile"></i>
                            <?php echo escape(_trans('agenda.enviar_sms')); ?>
                        </a>
                    </li>

                </ul>
            </div>
        </div>
        <div class="col-sm-8 col-md-4">
            <div class="profile-env">
                <div class="profile-info">
                    <h3><?php echo escape($objCliente->getNomeCompleto()); ?></h3>
                </div>
            </div>
        </div>
        <div class="col-sm-8" >
            <ul class="list-unstyled list-inline cabecalho-clientes text-right hidden-xs hidden-sm">
                <li>
                    <a href="javascript:;" onclick="jQuery('#enviar-email').modal('show');"
                       class="btn btn-default btn-icon icon-left" >
                        <i class="entypo-mail"></i>
                        <?php echo escape(_trans('agenda.enviar_email')); ?>
                    </a>
                </li>
                <li>
                    <a href="javascript:;" onclick="jQuery('#enviar-sms').modal('show');"
                       class="btn btn-default btn-icon icon-left" >
                        <i class="entypo-mobile"></i>
                        <?php echo escape(_trans('agenda.enviar_SMS')); ?>
                    </a>
                </li>
            </ul>
        </div>

    </div>
</div>
<div class="row">
    <div class="col-md-4">
        <div class="panel panel-primary">
            <div class="panel-heading">
                <h4><?php echo escape(_trans('agenda.detalhes')); ?></h4>
            </div>
            <br>
            <div class="">
                <ul class="list-unstyled dados-cliente">
                    <li>
                        <span class="pull-left "> <?php echo escape(_trans('agenda.telefone')); ?>:&nbsp;</span>
                        <b><a href="tel:<?php echo escape($objCliente->getTelefone()) ?>"><span><?php echo escape($objCliente->getTelefone()) ?></span></a></b><br>
                    </li>
                    <li>
                        <span class="pull-left "> <?php echo escape(_trans('agenda.email')); ?>:&nbsp;</span>
                        <b><span><?php echo escape($objCliente->getEmail()) ?></span></b><br>
                    </li>
<!--                    <li>-->
<!--                        <span class="pull-left">--><?php //echo escape(_trans('agenda.telefone_fixo')); ?><!--:&nbsp;</span>-->
<!--                        <b><a href="tel:--><?php //echo escape($objCliente->getEnderecoPrincipal()->getTelefone2()) ?><!--"><span>--><?php //echo escape($objCliente->getEnderecoPrincipal()->getTelefone2()); ?><!--</span></a></b><br>-->
<!--                    </li>-->
                    <li>
                        <span class="pull-left "><?php echo ($objCliente->isPessoaJuridica() ? escape(_trans('agenda.cnpj')) : escape(_trans('agenda.cpf'))); ?>:&nbsp;</span>
                        <span><b><?php echo escape($objCliente->getCpfCnpj()) ?></b></span><br>
                    </li>
                    <li>
                        <span class="pull-left "><?php echo ($objCliente->isPessoaJuridica() ? escape(_trans('agenda.ie')) : escape(_trans('agenda.rg'))); ?>:&nbsp;</span>
                        <span><b><?php echo escape($objCliente->getRgIe()) ?></span></b><br>
                    </li>
                    <li>
                        <span class="pull-left "><?php echo escape(_trans('agenda.data_nascimento')); ?>:&nbsp;</span>
                        <span><b><?php echo escape($objCliente->getDataNascimentoDataFundacao('d/m/Y')) ?></b></span>
                    </li><br>
                </ul>
            </div>
        </div>
    </div>
    <div class="col-md-8">
        <div class="panel panel-primary panel-timeline">

            <h4><?php echo escape(_trans('agenda.tomar_nota')); ?></h4><br>
            <div >
                
                <form role="form" method="post" style="margin-bottom: 50px;">
                    <div class="form-group">
                        <textarea class="form-control wysihtml5"
                                  data-stylesheet-url="assets/css/custom.css"
                                  name="observacao[OBSERVACAO]" id="observacao[OBSERVACAO]">
                        </textarea>
                    </div>
                    <button type="submit" class="btn btn-success"><?php echo escape(_trans('agenda.enviar')); ?></button>
                    <button type="button" class="btn btn-gold"><?php echo escape(_trans('agenda.cancelar')); ?></button>
                </form>
                
                <div id="historico-obs" style="margin-bottom: 50px;">
                    <h4 class="historico"><?php echo escape(_trans('agenda.historico_observacoes')); ?></h4>
                    <table class="table large-only table-striped table-clientes">
                        <thead>
                            <tr>
                                <th><?php echo escape(_trans('agenda.data')); ?></th>
                                <th><?php echo escape(_trans('agenda.observacao')); ?></th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody><?php
                        
                            $observacoesCliente = ClienteDistribuidorObservacaoQuery::create()
                                ->filterByClienteDistribuidor($objClienteDistribuidor)
                                ->orderByDataCadastro(Criteria::DESC)
                                ->find();
                            
                            /* @var $observacao ClienteDistribuidorObservacao */
                        foreach ($observacoesCliente as $observacao) {
                            ?><tr>
                                    <td><?php echo $observacao->getDataCadastro('d/m/Y'); ?></td>
                                    <td><?php echo nl2br($observacao->getObservacao()); ?></td>
                                    <td style="width: 50px; text-align: center;">
                                        <a href="<?php echo $root_path ?>/distribuidores_novo/clientes/actions/excluir_observacao.action.php?id=<?php echo $observacao->getId(); ?>" class="btn btn-danger"><i class="entypo-cancel"></i></a>
                                    </td>
                                </tr><?php
                        }
                        
                        ?></tbody>
                    </table>
                </div>
                
                <h4 class="historico"><?php echo escape(_trans('agenda.historico_agendamentos')); ?></h4>

                <?php
                $atividades = DistribuidorEventoQuery::create()
                        ->filterByCliente(ClientePeer::getClienteLogado())
                        ->filterByClienteDistribuidor($objClienteDistribuidor)
                        ->filterByData('now', '<')
                        ->orderByData(Criteria::DESC)
                        ->find();
                ?>

                <ul class="cbp_tmtimeline"><?php
                    /* @var $atividade DistribuidorEvento */
                foreach ($atividades as $row => $atividade) {
                    $finalizado = $atividade->getStatus() == DistribuidorEvento::STATUS_FINALIZADO;

                    if ($row == 0 && $atividade->getData('d/m/Y') != date('d/m/Y')) {
                        ?><li>
                                <time class="cbp_tmtime" datetime="<?php echo date('d/m/Y'); ?>">
                                    <span class="hidden"><?php echo date('d/m/Y'); ?></span>
                                    <span class="large"><?php echo escape(_trans('agenda.hoje')); ?></span>
                                </time>

                                <div class="cbp_tmicon">
                                    <i class="entypo-user"></i>
                                </div>

                                <div class="cbp_tmlabel empty">
                                    <span><?php echo escape(_trans('agenda.sem_agendamentos')); ?></span>
                                </div>
                            </li><?php
                    }
                    ?><li>
                            <time class="cbp_tmtime" datetime="<?php echo $atividade->getData('d/m/Y'); ?>">
                                <span style="padding-top: 10px;"><?php
                                if ($atividade->getData('d/m/Y') === date('d/m/Y')) {
                                    echo escape(_trans('agenda.hoje'));
                                } else {
                                    echo $atividade->getData('d/m/Y');
                                }
                                ?></span>
                                <span></span>
                            </time>

                            <?php
                            $subject = DistribuidorEventoPeer::getSubjectByText($atividade->getAssunto());
                            ?>

                            <div class="cbp_tmicon <?php echo $subject['class']; ?>">
                                <i class="<?php echo $subject['icon']; ?>"></i>
                            </div>

                            <div class="cbp_tmlabel<?php echo ($finalizado ? '' : ' emAndamento')?>" data-id="<?php echo $atividade->getId(); ?>" style="<?php
                            
                            if ($finalizado) {
                                ?>background:<?php echo ($atividade->getDistribuidorTemplateIdPerda() ? '#ee4749' : '#00a651'); ?><?php
                            } else {
                                ?>cursor: pointer;<?php
                            }
                            
                            ?>">
                                <h2<?php echo ($finalizado ? ' style="color: #FFF;"' : ''); ?>>
                                <?php echo ($atividade->getAssunto() != '' ? escape(_trans('agenda.' . $atividade->getAssunto())) : escape(_trans('agenda.sem_assunto')));?><small><?php echo ($finalizado ? '' : ' (' . escape(_trans('agenda.em_andamento')) . ')'); ?></small>
                                    <small class="h4 pull-right"<?php echo ($finalizado ? ' style="color: #FFF;"' : ''); ?>><?php
                                    
                                    $valor = '';
                                    
                                    if ($atividade->getValor()) {
                                        if ($atividade->getDistribuidorTemplateIdPerda()) {
                                            $valor = escape(_trans('agenda.valor_perdido')) . ': ';
                                        } else {
                                            $valor = escape(_trans('agenda.valor_ganho')) . ': ';
                                        }
                                            
                                        $valor .= 'R$ ' . $atividade->getValor();
                                    }
                                    
                                    echo $valor;
                                    ?></small>
                                </h2>
                                <p style="padding-top: 10px; <?php echo ($finalizado ? 'color: #FFF;' : ''); ?>"><?php echo $atividade->getDescricao(); ?></p><?php
                                
                                $produtos = DistribuidorEventoProdutoQuery::create()
                                    ->findByDistribuidorEventoId($atividade->getId());
                                    
                                if (count($produtos) > 0) {
                                    ?><br>
                                        <span style="color: #FFF"><?php echo escape(_trans('agenda.produtos')); ?>:</span>
                                        <ul style="color: #FFF"><?php
                                    
                                        /* @var $produto DistribuidorEventoProduto */
                                        /* @var $detalhes Produto */
                                        foreach ($produtos as $produto) {
                                            $detalhes = $produto->getProduto();

                                            ?><li><?php echo $detalhes->getNome(); ?></li><?php
                                        }
                                        
                                        ?></ul><?php
                                }
                                
                                ?></div>
                        </li><?php
                }
                ?></ul>

            </div>

        </div>
    </div>
</div>


<div class="modal fade" id="enviar-sms">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="<?php echo $root_path ?>/distribuidores_novo/clientes/actions/sms.action.php?v=1" method="POST">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                    <h4 class="modal-title"><?php echo escape(_trans('agenda.confirmacao_envio_sms')); ?></h4>
                </div>
                <div class="modal-body">
                
                    <input type="hidden" name="cliente_id" value="<?php echo $objClienteDistribuidor->getId(); ?>">
                    <div class="form-group row">
                        <div class="col-sm-3">
                            <label class="control-label"><?php echo escape(_trans('agenda.cliente')); ?></label>
                        </div>
                        <div class="col-sm-8">
                            <label class="control-label nomeCliente"><?php echo escape($objClienteDistribuidor->getNomeCompleto()); ?></label>
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
                            <textarea name="sms[MENSAGEM]" id="mensagemSMS" class="form-control" cols="30" rows="10" maxlength="160"></textarea>
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
            <form action="<?php echo $root_path ?>/distribuidores_novo/clientes/actions/email.action.php?v=1" method="POST">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                    <h4 class="modal-title"><?php echo escape(_trans('agenda.confirmacao_envio_email')); ?></h4>
                </div>

                <div class="modal-body">
                    <input type="hidden" name="cliente_id" value="<?php echo $objClienteDistribuidor->getId(); ?>">
                    <div class="form-group row">
                        <div class="col-sm-3 label-atividade">
                            <label class="control-label"><?php echo escape(_trans('agenda.cliente')); ?></label>
                        </div>
                        <div class="col-sm-8">
                            <label class="control-label nomeCliente"><?php echo escape($objClienteDistribuidor->getNomeCompleto()); ?></label>
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
                            <textarea name="email[MENSAGEM]" class="form-control" cols="30" rows="10"></textarea>
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

<?php

    include __DIR__ . '/../../atividades/views/includes/modal_criar_atividade.php';
    
?>

<script>

    $(document).ready(function () {

        $('.emAndamento').on('click', function() {
            
            var id = $(this).data('id');
            
            $.ajax({
                url: '<?php echo $root_path ?>/distribuidores_novo/atividades/actions/busca_atividade.action.php?id='+id
            }).done(function( result ) {
                
                result = JSON.parse(result);
                
                console.log(result);
                
                $('#modal-criar-atividade input[name="id"]').val(id);
                $('#modal-criar-atividade select[name="evento[CLIENTE_DISTRIBUIDOR_ID]"]').val(result.CLIENTE_ID).trigger('change');
                $('#modal-criar-atividade select[name="evento[ASSUNTO]"]').val(result.ASSUNTO).trigger('change');
                $('#modal-criar-atividade input[name="evento[INTERESSE]"]').val(result.INTERESSE);
                $('#modal-criar-atividade input[name="evento[DATA]"]').val(result.DATA);
                $('#modal-criar-atividade textarea[name="evento[DESCRICAO]"]').val(result.DESCRICAO);
                $('#modal-criar-atividade input[name="evento[VALOR]"]').val(result.VALOR);
                $('#modal-criar-atividade textarea[name="evento[DESCRICAO_SMS]"]').val(result.DESCRICAO_SMS);
                $('#modal-criar-atividade textarea[name="evento[DESCRICAO_EMAIL]"]').val(result.DESCRICAO_EMAIL);
                $('#mensagemSMS').keydown();
                
                $('#modal-criar-atividade').modal('show');
            });
        });

        $('#mensagemSMS').on('keydown', function() {
            $('#contador span').text(160 - $(this).val().length);
        });

        $('#enviar-sms select[name="modeloSMS"]').on('change', function() {
            
            $('#enviar-sms textarea[name="sms[MENSAGEM]"]').attr('disabled', 'disabled');
            
            $.ajax({
                url: '<?php echo $root_path ?>/distribuidores_novo/clientes/actions/busca_modelo.action.php?id='+$(this).find('option:selected').val()
            }).done(function( result ) {
                
                result = JSON.parse(result);
                
                $('#enviar-sms textarea[name="sms[MENSAGEM]"]').val(result);
                $('#enviar-sms textarea[name="sms[MENSAGEM]"]').removeAttr('disabled');
                $('#mensagemSMS').keydown();
            });
            
        });
        
        $('#enviar-email select[name="modeloEMAIL"]').on('change', function() {
            
            $('#enviar-email textarea[name="email[MENSAGEM]"]').attr('disabled', 'disabled');
            
            $.ajax({
                url: '<?php echo $root_path ?>/distribuidores_novo/clientes/actions/busca_modelo.action.php?id='+$(this).find('option:selected').val()
            }).done(function( result ) {
                
                result = JSON.parse(result);
                
                $('#enviar-email textarea[name="email[MENSAGEM]"]').val(result);
                $('#enviar-email textarea[name="email[MENSAGEM]"]').removeAttr('disabled');
            });
            
        });
        
    });

</script>
