<div class="row">
    <div class="col-sm-12">
        <h2><?php echo $title; ?></h2>
        <br>
    </div>
</div>
<div class="row">
    <div class="col-sm-12">
        <table class="table large-only table-striped table-cadastro-sms">
            <thead>
            <tr>
                <th><?php
                
                switch ($txtTitle) {
                    case 'Agendamento':
                        $texto = escape(_trans('agenda.nome_agendamento'));
                        break;
                    case 'Perda':
                        $texto = escape(_trans('agenda.motivo'));
                        break;
                    default:
                        $texto = escape(_trans('agenda.assunto'));
                }
                
                    echo $texto;
                
                ?></th><?php
                
if ($txtTitle != 'Perda') {
    ?><th><?php echo escape(_trans('agenda.modelo')); ?></th><?php
?><th><?php echo escape(_trans('agenda.categoria')); ?></th><?php
}
                    
?><th class="text-center"><?php echo escape(_trans('agenda.ativo')); ?></th>
                <th></th>
            </tr>
            </thead>
            <tbody><?php
            
            if (count($pager) > 0) {
                /* @var $template DistribuidorTemplate */
                foreach ($pager as $template) {
                    ?><tr>
                            <td><?php echo $template->getAssunto(); ?></td><?php

                            if ($txtTitle != 'Perda') {
                                ?><td><?php echo $template->getMensagem(); ?></td><?php
?><td><?php echo $template->getCategoria(); ?></td><?php
                            }


                            if ($template->getPadrao() < 1) {
                                ?>
                                <td style="width: 7%" class="text-center">
                                    <div class="form-group">
                                        <div class="make-switch switch-mini ativaItem"
                                             data-id="<?php echo $template->getId(); ?>" data-on-label="<?php echo escape(_trans('agenda.sim')); ?>"
                                             data-off-label="<?php echo escape(_trans('agenda.nao')); ?>">
                                            <input type="checkbox"<?php echo($template->getAtivo() ? ' checked' : ''); ?>>
                                        </div>
                                    </div>
                                </td>
                                <td style="width: 15%" class="text-center">
                                    <div class="tooltip">
                                        <a href="#" class="btn btn-default editItem"
                                           data-id="<?php echo $template->getId(); ?>" style="padding: 6px 6px;">
                                            <i class="entypo-pencil"></i></a>
                                        <span class="tooltiptext tooltip-top"><?php echo escape(_trans('agenda.editar')); ?></span>
                                    </div>
                                    <div class="tooltip">
                                        <a href="#" class="btn btn-default deleteItem"
                                           data-id="<?php echo $template->getId(); ?>" style="padding: 6px 6px;">
                                            <i class="entypo-trash"></i> </a>
                                        <span class="tooltiptext tooltip-top"><?php echo escape(_trans('agenda.excluir')); ?></span>
                                    </div>
                                </td><?php
                            } else {
                                ?>
                                <td style="width: 7%" class="text-center">
                                    <?php echo escape(_trans('agenda.sim')); ?>
                                </td>
                                <td style="width: 15%" class="text-center"><?php echo escape(_trans('agenda.padrao')); ?>
                                </td><?php
                            }
                            ?>
                        </tr><?php
                }
            } else {
                ?><tr>
                        <td colspan="5"><?php
                        
                        switch ($txtTitle) {
                            case 'Perda':
                                echo escape(_trans('agenda.nenhum_motivo_perda'));
                                break;
                            case 'E-mail':
                                echo escape(_trans('agenda.nenhum_modelo_email'));
                                break;
                            case 'Agendamento':
                                echo escape(_trans('agenda.nenhum_modelo_agendamento'));
                                break;
                            case 'SMS':
                                echo escape(_trans('agenda.nenhum_modelo_sms'));
                                break;
                        }
                        
                        ?></td>
                    </tr><?php
            }
            
            ?></tbody>
        </table>

    </div>
</div>

<nav id="bt-menu" class="btn-menu">
    <a href="javascript:;" onclick="jQuery('#modal-cad-modelo-sms').modal('show');" class="bt-menu-trigger bt-item-page">
        <span><?php echo escape(_trans('agenda.menu')); ?></span>
    </a>
</nav>

<div class="modal fade" id="modal-cad-modelo-sms">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title"><?php
                        
                switch ($txtTitle) {
                    case 'Perda':
                        echo escape(_trans('agenda.cadastro_motivo_perda'));
                        break;
                    case 'E-mail':
                        echo escape(_trans('agenda.cadastro_modelo_email'));
                        break;
                    case 'Agendamento':
                        echo escape(_trans('agenda.cadastro_modelo_agendamento'));
                        break;
                    case 'SMS':
                        echo escape(_trans('agenda.cadastro_modelo_sms'));
                        break;
                }

                ?></h4>
            </div>
            
            <form action="" method="POST">
                <div class="modal-body">
                    <div class="form-group row">
                        <div class="col-sm-3 label-atividade">
                            <label class="control-label"><?php echo $texto; ?></label>
                        </div>
                        <div class="col-sm-9">
                            <input type="text" class="form-control" name="template_sms[ASSUNTO]">
                        </div>
                    </div><?php
                    
                    if ($txtTitle == 'Agendamento') {
                        ?><div class="form-group row">
                            <div class="col-sm-3 label-atividade">
                                <label class="control-label"><?php echo escape(_trans('agenda.categoria')); ?></label>
                            </div>
                            <div class="col-sm-9">
                                <select name="template_sms[CATEGORIA]" class="select2" data-allow-clear="true" data-placeholder="Categoria">
                                     <option value="ABERTURA"><?php echo escape(_trans('agenda.abertura')); ?></option>
                                     <option value="FECHAMENTO"><?php echo escape(_trans('agenda.fechamento')); ?></option>
                               </select>
                            </div>
                        </div><?php
                    }
                    
                    if ($txtTitle != 'Perda') {
                        ?><div class="form-group row">
                            <div class="col-sm-3 label-atividade">
                                <label class="control-label"><?php echo escape(_trans('agenda.descricao_mensagem')); ?></label>
                            </div>
                            <div class="col-sm-9"><?php
                            
                            if ($txtTitle == 'E-mail') {
                                ?><textarea class="form-control wysihtml5"
                                        data-stylesheet-url="<?php echo $root_path ?>/distribuidor_scripts/assets/css/custom.css"
                                        name="template_sms[MENSAGEM]" id="sample_wysiwyg_sms">
                                    </textarea><?php
                            } else {
                                ?><textarea id="mensagemSMS" class="form-control" name="template_sms[MENSAGEM]" maxlength="160" rows="10"></textarea>
                                    <div id="contador"><?php echo escape(_trans('agenda.limite_caracteres')); ?>: <span>160</span></div><?php
                            }
                            
                            ?></div>
                        </div><?php
                    } else {
                        ?><input type="hidden" name="template_sms[MENSAGEM]" value="..."/><?php
                    }
                    
                    ?><input  type="hidden" name="id" value="-1"><?php
                    
if ($txtTitle != 'Agendamento') {
    ?><input  type="hidden" name="template_sms[CATEGORIA]" value="ABERTURA"><?php
}
                    
?><input  type="hidden" name="template_sms[TIPO]" value="<?php echo escape($arrTemplateSms['TIPO']); ?>">
                </div>
                
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal"><?php echo escape(_trans('agenda.cancelar')) ?></button>
                    <button type="submit" class="btn btn-green"><?php echo escape(_trans('agenda.salvar')) ?></button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade" id="delete-item">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title"><?php echo escape(_trans('agenda.confirmacao_exclusao')); ?></h4>
            </div>
            <div class="modal-body">
                <div class="row text-center">
                    <h4 class="modal-title" style="margin: 15px auto"><?php echo escape(_trans('agenda.confirmacao_exclusao_item')); ?></h4>
                </div>
                <div class="row text-center">
                    <form action="<?php echo $root_path ?>/distribuidores_novo/modelos/actions/excluir.action.php?tpTemplate=<?php echo $tipoTemplate; ?>" method="POST">
                        <input type="hidden" name="id"/>
                        <button type="button" class="btn btn-default" data-dismiss="modal"><?php echo escape(_trans('agenda.cancelar')); ?></button>
                        <button type="submit" class="btn btn-danger"><?php echo escape(_trans('agenda.excluir')); ?></button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script type="text/javascript">

    $('#mensagemSMS').on('keydown', function() {
        $('#contador span').text(160 - $(this).val().length);
    });


    $('.ativaItem').on('click', function(e) {
        e.preventDefault();

        $.ajax({
            url: '<?php echo $root_path ?>/distribuidores_novo/modelos/actions/ativa.action.php?id='+$(this).data('id')
        }).done(function( result ) {
            console.log(result);
        });

    });

    $(document).ready(function() {
        
        $('a.editItem').on('click', function(e) {
            e.preventDefault();
            
            var id = $(this).data('id');
            
            $.ajax({
                url: '<?php echo $root_path ?>/distribuidores_novo/modelos/actions/visualiza.action.php?id='+id
            }).done(function( result ) {
                
                result = JSON.parse(result);

                $('#modal-cad-modelo-sms input[name="id"]').val(id);
                $('#modal-cad-modelo-sms input[name="template_sms[ASSUNTO]"]').val(result.ASSUNTO);
                $('#modal-cad-modelo-sms select[name="template_sms[CATEGORIA]"]').val(result.CATEGORIA).trigger('change');
                
                <?php
                
                if ($txtTitle == 'E-mail') {
                    ?>$('#modal-cad-modelo-sms iframe').contents().find('.wysihtml5-editor').html(result.MENSAGEM);<?php
                } else {
                    ?>$('#modal-cad-modelo-sms textarea[name="template_sms[MENSAGEM]"]').val(result.MENSAGEM);<?php
                }
                
                ?>
                
                $('#modal-cad-modelo-sms').modal('show');
            });
            
        });
        
        $('#modal-cad-modelo-sms').on('hidden.bs.modal', function() {
            $('#modal-cad-modelo-sms input[name="id').val('-1');
            $('#modal-cad-modelo-sms input[name="template_sms[ASSUNTO]"]').val('');
            <?php
            if ($txtTitle == 'Agendamento') {
                ?>$('#modal-cad-modelo-sms select[name="template_sms[CATEGORIA]"]').val('ABERTURA');
                    $('#modal-cad-modelo-sms #select2-chosen-2').text('');<?php
            }
            ?>
            $('#modal-cad-modelo-sms iframe').contents().find('.wysihtml5-editor').html('');
        });
        
        $('a.deleteItem').on('click', function(e) {
            e.preventDefault();
            $('#delete-item input[name="id"]').val($(this).data('id'));
            $('#delete-item').modal('show');
        });
         
     });

</script>
