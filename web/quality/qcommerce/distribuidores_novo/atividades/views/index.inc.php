<div class="page">
    <div class="row hidden-xs hidden-sm hidden-md">
        <form action="<?php echo BASE_URL_ASSETS ?>/distribuidores_novo/atividades/" method="GET" class="form-horizontal form-periodo pull-right">
            <div class="col-xs-10">
                <div class="form-group">
                    <div class="row">
                        <label class="col-sm-2 control-label hidden-xs"><?php echo escape(_trans('agenda.periodo')); ?>:</label>
                        <label class="col-sm-4 control-label visible-xs"><?php echo escape(_trans('agenda.periodo_exibicao')); ?>:</label>
                        <div class="col-sm-4">
                            <div class="input-group" id="rel-date-initial">
                                <span class="input-group-addon"><?php echo escape(_trans('agenda.de')); ?></span>
                                <input type="text" class="form-control datepicker" data-format="dd/mm/yyyy" name="dataInicial" value="<?php echo (isset($dataInicial) && $dataInicial != null ? $dataInicial->format('d/m/Y') : ''); ?>">
                            </div>
                        </div>
                        <div class="col-sm-4">
                            <div class="input-group" id='rel-date-end'>
                                <span class="input-group-addon"><?php echo escape(_trans('agenda.ate')); ?></span>
                                <input type="text" class="form-control datepicker" data-format="dd/mm/yyyy" name="dataFinal" value="<?php echo (isset($dataFinal) && $dataFinal != null ? $dataFinal->format('d/m/Y') : ''); ?>">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-sm-2">
                <button type="submit" class="btn btn-default btn-icon icon-right pull-right">
                    <?php echo escape(_trans('agenda.filtrar')); ?>
                    <i class="fa fa-filter"></i>
                </button>
            </div>
        </form>
    </div>
    <div class="row">
        <div id="slideout" style="overflow-y: auto;">
            <div class="heading-slideout ">
                <h3 class="modal-title"><?php echo escape(_trans('agenda.escolha_filtro')); ?></h3>
            </div>
            <div class="content-slideout">
                <button type="button" class="btn btn-close">x</button>
                <form action="<?php echo BASE_URL_ASSETS ?>/distribuidores_novo/atividades/" method="GET" class="form-horizontal form-periodo pull-right">
                        <!-- <div class="form-group"> -->
                        <div class="row">                        
                            <label class="col-xs-2 control-label"><?php echo escape(_trans('agenda.periodo')); ?>:</label>
                            <div class="col-xs-10">
                                <div class="row">
                                    <div class="col-xs-6">
                                        <div class="input-group" id="rel-date-initial">
                                            <span class="input-group-addon"><?php echo escape(_trans('agenda.de')); ?></span>
                                            <input type="text" class="form-control datepicker" data-format="dd/mm/yyyy" name="dataInicial" value="<?php echo (isset($dataFinal) && $dataFinal != null ? $dataInicial->format('d/m/Y') : ''); ?>">
                                        </div>
                                    </div>
                                    <div class="col-xs-6">
                                        <div class="input-group" id='rel-date-end'>
                                            <span class="input-group-addon"><?php echo escape(_trans('agenda.ate')); ?></span>
                                            <input type="text" class="form-control datepicker" data-format="dd/mm/yyyy" name="dataFinal" value="<?php echo (isset($dataFinal) && $dataFinal != null ? $dataFinal->format('d/m/Y') : ''); ?>">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- </div> -->
                        <div class="row">                        
                            <div class="col-xs-offset-2 col-xs-4">
                            <button type="submit" class="btn btn-default btn-icon icon-right">
                                <?php echo escape(_trans('agenda.filtrar')); ?>
                                <i class="fa fa-filter"></i>
                            </button>
                            </div>
                        </div>
                </form>
                
                <p><?php echo escape(_trans('agenda.selecione_tipo_agendamento')); ?></p>
                <?php include __DIR__ . '/includes/filter.php'; ?>
            </div>
        </div>
        <div class="col-xs-4 col-sm-4 col-md-5">
            <div class="hidden-lg">
                <button type="button" class="btn btn-default btn-icon icon-left btn-filtrar" style="">
                    <?php echo escape(_trans('agenda.filtrar')); ?>
                    <i class="fa fa-filter"></i>
                </button>
            </div>
            <div class="visible-lg">
                <div class="group-filters-list">
                    <ul class="filters" style="padding-left: 0;">
                        <li class="active">
                            <div class="tooltip">
                                <a href="#" class="filtro-assunto" data-assunto="all"><?php echo escape(_trans('agenda.todas')); ?></a>
                                <span class="tooltiptext tooltip-bottom"><?php echo escape(_trans('agenda.todas')); ?></span>
                            </div>
                        </li><?php

                            $subjects = DistribuidorEventoPeer::getSubjects();

                        foreach ($subjects as $subject) {
                            ?><li>
                                    <div class="tooltip">
                                        <a href="#" class="filtro-assunto" data-assunto="<?php echo $subject['category']; ?>">
                                            <i class="<?php echo $subject['icon']; ?>"></i>
                                        </a>
                                        <span class="tooltiptext tooltip-bottom"><?php echo escape(_trans('agenda.' . $subject['text'])) ?></span>
                                    </div>
                                </li><?php
                        }
                            
                        ?></ul>
                </div>
            </div>
        </div>
        <div class="visible-lg col-md-7">
            <div class="group-filters-list">
                <ul class="filters not-margin" style="text-align: right; padding-right: 0; float: right">
                    <li<?php echo isset($_GET['filter']) && $_GET['filter'] == 'todas' ? ' class="active"' : ''; ?>>
                        <a href="<?php echo BASE_URL_ASSETS ?>/distribuidores_novo/atividades/?filter=todas"><?php echo escape(_trans('agenda.todas')); ?></a>
                    </li>
                    <li<?php echo !isset($_GET['filter']) ? ' class="active"' : ''; ?>>
                        <a href="<?php echo BASE_URL_ASSETS ?>/distribuidores_novo/atividades/"><?php echo escape(_trans('agenda.andamento')); ?></a>
                    </li>
                    <li<?php echo isset($_GET['filter']) && $_GET['filter'] == 'finalizadas' ? ' class="active"' : ''; ?>>
                        <a href="<?php echo BASE_URL_ASSETS ?>/distribuidores_novo/atividades/?filter=finalizadas"><?php echo escape(_trans('agenda.finalizadas')); ?></a>
                    </li>
                    <li<?php echo isset($_GET['filter']) && $_GET['filter'] == 'atrasadas' ? ' class="active"' : ''; ?>>
                        <a href="<?php echo BASE_URL_ASSETS ?>/distribuidores_novo/atividades/?filter=atrasadas"><?php echo escape(_trans('agenda.atrasadas')); ?></a>
                    </li>
                    <li<?php echo isset($_GET['filter']) && $_GET['filter'] == 'hoje' ? ' class="active"' : ''; ?>>
                        <a href="<?php echo BASE_URL_ASSETS ?>/distribuidores_novo/atividades/?filter=hoje"><?php echo escape(_trans('agenda.hoje')); ?></a>
                    </li>
                    <li<?php echo isset($_GET['filter']) && $_GET['filter'] == 'esta-semana' ? ' class="active"' : ''; ?>>
                        <a href="<?php echo BASE_URL_ASSETS ?>/distribuidores_novo/atividades/?filter=esta-semana"><?php echo escape(_trans('agenda.esta_semana')); ?></a>
                    </li>
                    <li<?php echo isset($_GET['filter']) && $_GET['filter'] == 'proxima-semana' ? ' class="active"' : ''; ?>>
                        <a href="<?php echo BASE_URL_ASSETS ?>/distribuidores_novo/atividades/?filter=proxima-semana"><?php echo escape(_trans('agenda.proxima_semana')); ?></a>
                    </li>
                </ul>
            </div>
        </div>
    </div>
    <div class="row">
        <table id="table-atividades" class="table large-only table-striped table-clientes"  style="margin-top: 0;">
            <thead>
                <tr>
                    <th></th>
                    <th class="text-center"><?php echo (isset($_GET['filter']) && $_GET['filter'] != 'finalizadas' ? escape(_trans('agenda.concluido')) : '')?></th>
                    <th><?php echo escape(_trans('agenda.tipo')) ?></th>
                    <th>
                        <div class="hidden-xs">
                            <?php echo escape(_trans('agenda.data')) ?>
                            <i class="glyphicon glyphicon-sort-by-attributes" style="float: left; margin-right: 9px;"></i>
                        </div>
                        <div class="visible-xs">
                            <i class="glyphicon glyphicon-sort-by-attributes" style="display: inline-block; margin-right: 9px;"></i>
                            <?php echo escape(_trans('agenda.data')) ?>
                        </div>
                    </th>
                    <th><?php echo escape(_trans('agenda.cliente')) ?></th>
                    <th><?php echo escape(_trans('agenda.interesse')) ?></th>
                    <th class="text-center"><?php echo escape(_trans('agenda.telefone')) ?></th>
                    <th class="text-right"><?php echo escape(_trans('agenda.valor_jogo')) ?></th>
                    <th class="text-center"><?php echo escape(_trans('agenda.status')) ?></th><?php
                
                    if (isset($_GET['filter']) && $_GET['filter'] == 'finalizadas') {
                        ?><th class="text-center"><?php echo escape(_trans('agenda.com_venda')) ?></th><?php
                    }
                
                    ?></tr>
            </thead>
            <tbody><?php

            if (count($pager) > 0) {
                /* @var $evento DistribuidorEvento */
                foreach ($pager as $evento) {
                    ?><tr<?php echo ($evento->isAtrasado() ? ' class="tr-atrasada"' : '');  ?> data-id="<?php echo $evento->getId(); ?>" data-cliente-id="<?php echo $evento->getClienteDistribuidorId(); ?>" data-valor="<?php echo $evento->getValor(); ?>" data-finalizado="<?php echo (escape($evento->getStatus()) == 'FINALIZADO' ? 1 : 0); ?>" data-category="<?php echo DistribuidorEventoPeer::getSubjectByText($evento->getAssunto())['category']; ?>" <?php echo (escape($evento->getStatus()) == 'FINALIZADO' ? '' : 'style="cursor: pointer;"'); ?>>
                            <td>
                                <div class="visible-xs pull-right menudrop-icon" data-id="<?php echo $evento->getid(); ?>">
                                    <a href="javascript:;" class="btnEditar">
                                        <i class="entypo-pencil" style="font-size: 1.2em"></i>
                                    </a>
                                </div>
                            </td><?php
                                
                            if (isset($_GET['filter']) && $_GET['filter'] != 'finalizadas') {
                                ?><td class="checkbox-item">
                                        <div class="hidden-xs text-center">
                                            <div class="checkbox checkbox-replace color-green">
                                                <input type="checkbox" class="check-itens atividadeCon" data-id="<?php echo $evento->getId(); ?>" data-cliente-id="<?php echo $evento->getClienteDistribuidorId(); ?>" data-valor="<?php echo $evento->getValor(); ?>">
                                            </div>
                                        </div>
                                        <div class="visible-xs" >
                                            <div class="checkbox checkbox-replace color-green">
                                                <input type="checkbox" class="check-itens atividadeCon" data-id="<?php echo $evento->getId(); ?>" data-cliente-id="<?php echo $evento->getClienteDistribuidorId(); ?>" data-valor="<?php echo $evento->getValor(); ?>">
                                            </div>
                                        </div>
                                    </td><?php
                            } else {
                                ?><td></td><?php
                            }
                                
                            ?><td>
                            <?php
                                
                            if ($evento->getAssunto()) {
                                echo escape(_trans('agenda.' . $evento->getAssunto()));
                            }
                                    
                            ?>
                            </td>
                            <td class="data">
                                <?php echo escape($evento->getData('d/m/Y')); ?>
                            </td>
                            <td><?php echo escape($evento->getClienteDistribuidor()->getNomeCompleto()) ?></td>
                            <td><?php echo escape($evento->getInteresse()) ?></td>
                            <td>
                                <div class="hidden-xs text-center"><?php
                                echo escape($evento->getClienteDistribuidor()->getTelefoneCelular());
                                ?></div>
                                <div class="visible-xs"><?php
                                echo escape($evento->getClienteDistribuidor()->getTelefoneCelular());
                                ?></div>
                            </td>
                            <td>
                                <div class="hidden-xs text-right"><?php
                                echo escape(format_number($evento->getValor(), UsuarioPeer::LINGUAGEM_PORTUGUES));
                                ?></div>
                                <div class="visible-xs"><?php
                                echo escape(format_number($evento->getValor(), UsuarioPeer::LINGUAGEM_PORTUGUES));
                                ?></div>
                            </td>

                            <td>
                                <div class="hidden-xs text-center"><?php
                                echo escape($evento->getStatus());
                                ?></div>
                                <div class="visible-xs"><?php
                                echo escape($evento->getStatus());
                                ?></div>
                            </td>
                            
                            <?php
                            
                            if (isset($_GET['filter']) && $_GET['filter'] == 'finalizadas') {
                                ?><td style="text-align: center;"><?php
                                    
if ($evento->getDistribuidorTemplateIdPerda()) {
    ?><i class="entypo-thumbs-down" style="color: #cc2424"></i><?php
} else {
    ?><i class="entypo-thumbs-up" style="color: #00a651"></i><?php
}
                                    
?></td><?php
                            }
                            
                            ?>
                            
                        </tr><?php
                }
            } else {
                ?><tr><td colspan="9"><?php echo escape(_trans('agenda.nenhum_agendamento')) ?></td></tr><?php
            }
                    
            ?></tbody>
        </table>      
    </div>
</div>

<nav id="bt-menu" class="bt-menu">
    <a href="javascript:;" onclick="$('#modal-criar-atividade').modal('show');" class="bt-menu-trigger">
        <span><?php echo escape(_trans('agenda.menu')) ?></span>
    </a>
</nav>

<?php
    
    include __DIR__ . '/includes/modais.php';
    include __DIR__ . '/includes/modal_criar_atividade.php';

?>

<script>

    $(document).ready(function() {
        
        $('label.filtro-assunto').on('click', function() {
            console.log('chegou aqui');
            
            $('label.filtro-assunto').parent().removeClass('active');
            $(this).parent().addClass('active');
            
            $('#table-atividades tbody tr').show();
            
            if($(this).data('assunto') !== 'all') {
                $('#table-atividades tbody tr[data-category!="'+$(this).data('assunto')+'"]').hide();
            }
            
        });
        
        // Filtra categoria
        $('a.filtro-assunto').on('click', function(e) {
            e.preventDefault();
            
            $('a.filtro-assunto').parent().parent().removeClass('active');
            $(this).parent().parent().addClass('active');
            
            $('#table-atividades tbody tr').show();
            
            if($(this).data('assunto') !== 'all') {
                $('#table-atividades tbody tr[data-category!="'+$(this).data('assunto')+'"]').hide();
            }
            
        });
        
        // Edita atividade
        $('#table-atividades tbody tr[data-finalizado="0"] td:not(.checkbox-item), a.btnEditar').on('click', function() {
            
            var id = $(this).parent().data('id');
            
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
        
    });



</script>
